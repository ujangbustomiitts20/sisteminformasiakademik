<?php

namespace App\Http\Controllers;

use App\Models\TugasAkhir;
use App\Models\BimbinganTA;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TugasAkhirMahasiswaController extends Controller
{
    /**
     * Dashboard TA mahasiswa
     */
    public function index()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        $tugasAkhir = TugasAkhir::with([
            'pembimbing1', 
            'pembimbing2', 
            'bimbingan' => function ($query) {
                $query->orderBy('tanggal', 'desc');
            },
            'seminarProposal.penguji1',
            'seminarProposal.penguji2',
            'sidang.ketuaPenguji',
            'sidang.penguji1',
            'sidang.penguji2',
            'sidang.revisi.dosen'
        ])->where('mahasiswa_id', $mahasiswa->id)->first();

        return view('mahasiswa.tugas-akhir.index', compact('tugasAkhir', 'mahasiswa'));
    }

    /**
     * Form pengajuan judul
     */
    public function create()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        // Cek apakah sudah ada TA
        $existingTA = TugasAkhir::where('mahasiswa_id', $mahasiswa->id)->first();
        if ($existingTA) {
            return redirect()->route('mahasiswa.tugas-akhir.index')
                ->with('error', 'Anda sudah memiliki pengajuan tugas akhir!');
        }

        return view('mahasiswa.tugas-akhir.create', compact('mahasiswa'));
    }

    /**
     * Simpan pengajuan judul
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:500',
            'bidang_ilmu' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'latar_belakang' => 'nullable|string',
            'rumusan_masalah' => 'nullable|string',
            'tujuan_penelitian' => 'nullable|string',
            'dokumen_proposal' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $mahasiswa = Auth::user()->mahasiswa;

        // Cek apakah sudah ada TA
        $existingTA = TugasAkhir::where('mahasiswa_id', $mahasiswa->id)->first();
        if ($existingTA) {
            return back()->with('error', 'Anda sudah memiliki pengajuan tugas akhir!');
        }

        $data = [
            'mahasiswa_id' => $mahasiswa->id,
            'tahun_akademik_id' => \App\Models\TahunAkademik::where('is_aktif', true)->first()?->id,
            'judul' => $request->judul,
            'bidang_kajian' => $request->bidang_ilmu,
            'abstrak' => $request->deskripsi,
            'latar_belakang' => $request->latar_belakang,
            'rumusan_masalah' => $request->rumusan_masalah,
            'metodologi' => $request->tujuan_penelitian,
            'status' => 'draft',
            'tanggal_pengajuan' => now(),
        ];

        // Upload dokumen proposal
        if ($request->hasFile('dokumen_proposal')) {
            $data['dokumen_proposal'] = $request->file('dokumen_proposal')
                ->store('tugas-akhir/proposal', 'public');
        }

        TugasAkhir::create($data);

        return redirect()->route('mahasiswa.tugas-akhir.index')
            ->with('success', 'Pengajuan judul berhasil disimpan!');
    }

    /**
     * Update pengajuan (hanya draft)
     */
    public function update(Request $request, TugasAkhir $tugasAkhir)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if ($tugasAkhir->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        if (!in_array($tugasAkhir->status, ['draft', 'judul_ditolak'])) {
            return back()->with('error', 'Pengajuan tidak dapat diubah pada status ini!');
        }

        $request->validate([
            'judul' => 'required|string|max:500',
            'bidang_ilmu' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'latar_belakang' => 'nullable|string',
            'rumusan_masalah' => 'nullable|string',
            'tujuan_penelitian' => 'nullable|string',
            'dokumen_proposal' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $data = [
            'judul' => $request->judul,
            'bidang_kajian' => $request->bidang_ilmu,
            'abstrak' => $request->deskripsi,
            'latar_belakang' => $request->latar_belakang,
            'rumusan_masalah' => $request->rumusan_masalah,
            'metodologi' => $request->tujuan_penelitian,
        ];

        // Upload dokumen proposal baru
        if ($request->hasFile('dokumen_proposal')) {
            // Hapus file lama
            if ($tugasAkhir->dokumen_proposal) {
                Storage::disk('public')->delete($tugasAkhir->dokumen_proposal);
            }
            $data['dokumen_proposal'] = $request->file('dokumen_proposal')
                ->store('tugas-akhir/proposal', 'public');
        }

        $tugasAkhir->update($data);

        return back()->with('success', 'Pengajuan berhasil diupdate!');
    }

    /**
     * Ajukan judul
     */
    public function ajukan(TugasAkhir $tugasAkhir)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if ($tugasAkhir->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        if (!in_array($tugasAkhir->status, ['draft', 'judul_ditolak'])) {
            return back()->with('error', 'Pengajuan tidak dapat diajukan pada status ini!');
        }

        $tugasAkhir->status = 'diajukan';
        $tugasAkhir->tanggal_pengajuan = now();
        $tugasAkhir->save();

        return back()->with('success', 'Judul berhasil diajukan untuk review!');
    }

    /**
     * Upload dokumen TA
     */
    public function uploadDokumen(Request $request, TugasAkhir $tugasAkhir)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if ($tugasAkhir->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        $request->validate([
            'jenis_dokumen' => 'required|in:proposal,bab1,bab2,bab3,bab4,bab5,full_draft,final',
            'dokumen' => 'required|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $folder = 'tugas-akhir/' . $request->jenis_dokumen;
        $path = $request->file('dokumen')->store($folder, 'public');

        // Simpan ke kolom yang sesuai
        $kolom = 'dokumen_' . $request->jenis_dokumen;
        if ($request->jenis_dokumen == 'full_draft') {
            $kolom = 'dokumen_draft';
        } elseif ($request->jenis_dokumen == 'final') {
            $kolom = 'dokumen_final';
        } elseif (in_array($request->jenis_dokumen, ['bab1', 'bab2', 'bab3', 'bab4', 'bab5'])) {
            // Simpan di metadata atau buat field tersendiri
            // Untuk sekarang, simpan sebagai draft
            $kolom = 'dokumen_draft';
        }

        // Hapus file lama jika ada
        if (isset($tugasAkhir->$kolom) && $tugasAkhir->$kolom) {
            Storage::disk('public')->delete($tugasAkhir->$kolom);
        }

        $tugasAkhir->$kolom = $path;
        $tugasAkhir->save();

        return back()->with('success', 'Dokumen berhasil diupload!');
    }

    /**
     * Ajukan seminar proposal
     */
    public function ajukanSeminar(TugasAkhir $tugasAkhir)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if ($tugasAkhir->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        if ($tugasAkhir->status != 'judul_disetujui') {
            return back()->with('error', 'Status tugas akhir tidak valid untuk mengajukan seminar!');
        }

        if (!$tugasAkhir->dokumen_proposal) {
            return back()->with('error', 'Dokumen proposal harus diupload terlebih dahulu!');
        }

        // Cek jumlah bimbingan minimal
        $jumlahBimbingan = $tugasAkhir->bimbingan()->where('status', 'selesai')->count();
        if ($jumlahBimbingan < 3) {
            return back()->with('error', 'Minimal harus memiliki 3 kali bimbingan sebelum mengajukan seminar!');
        }

        $tugasAkhir->status = 'proposal_diajukan';
        $tugasAkhir->save();

        return back()->with('success', 'Pengajuan seminar proposal berhasil disubmit!');
    }

    /**
     * Ajukan sidang
     */
    public function ajukanSidang(TugasAkhir $tugasAkhir)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if ($tugasAkhir->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        if (!in_array($tugasAkhir->status, ['penelitian', 'penulisan'])) {
            return back()->with('error', 'Status tugas akhir tidak valid untuk mengajukan sidang!');
        }

        if (!$tugasAkhir->dokumen_draft) {
            return back()->with('error', 'Dokumen draft harus diupload terlebih dahulu!');
        }

        // Cek jumlah bimbingan minimal setelah seminar
        $jumlahBimbingan = $tugasAkhir->bimbingan()
            ->where('status', 'selesai')
            ->count();
        if ($jumlahBimbingan < 8) {
            return back()->with('error', 'Minimal harus memiliki 8 kali bimbingan sebelum mengajukan sidang!');
        }

        $tugasAkhir->status = 'sidang_diajukan';
        $tugasAkhir->save();

        return back()->with('success', 'Pengajuan sidang berhasil disubmit!');
    }

    // ========== BIMBINGAN ==========

    /**
     * Daftar bimbingan
     */
    public function bimbinganIndex(TugasAkhir $tugasAkhir)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if ($tugasAkhir->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        $bimbingans = $tugasAkhir->bimbingan()
            ->with('dosen')
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('mahasiswa.tugas-akhir.bimbingan', compact('tugasAkhir', 'bimbingans'));
    }

    /**
     * Request bimbingan
     */
    public function bimbinganRequest(Request $request, TugasAkhir $tugasAkhir)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if ($tugasAkhir->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        $request->validate([
            'dosen_id' => 'required|exists:dosen,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'nullable',
            'materi_bimbingan' => 'required|string',
        ]);

        // Validasi dosen adalah pembimbing
        if (!in_array($request->dosen_id, [$tugasAkhir->pembimbing_1_id, $tugasAkhir->pembimbing_2_id])) {
            return back()->with('error', 'Dosen yang dipilih bukan pembimbing Anda!');
        }

        BimbinganTA::create([
            'tugas_akhir_id' => $tugasAkhir->id,
            'dosen_id' => $request->dosen_id,
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $request->waktu_mulai,
            'materi_bimbingan' => $request->materi_bimbingan,
            'status' => 'dijadwalkan',
        ]);

        return back()->with('success', 'Permintaan bimbingan berhasil diajukan!');
    }

    /**
     * Upload revisi sidang
     */
    public function uploadRevisi(Request $request, TugasAkhir $tugasAkhir)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if ($tugasAkhir->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        $request->validate([
            'dokumen_revisi' => 'required|file|mimes:pdf,doc,docx|max:20480',
            'keterangan' => 'nullable|string',
        ]);

        $path = $request->file('dokumen_revisi')
            ->store('tugas-akhir/revisi', 'public');

        // Hapus file lama jika ada
        if ($tugasAkhir->dokumen_final) {
            Storage::disk('public')->delete($tugasAkhir->dokumen_final);
        }

        $tugasAkhir->dokumen_final = $path;
        $tugasAkhir->save();

        return back()->with('success', 'Dokumen revisi berhasil diupload!');
    }

    /**
     * Cetak kartu bimbingan
     */
    public function cetakKartuBimbingan(TugasAkhir $tugasAkhir)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if ($tugasAkhir->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        $tugasAkhir->load(['mahasiswa.programStudi', 'pembimbing1', 'pembimbing2', 'bimbingan.dosen']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('mahasiswa.tugas-akhir.cetak-kartu-bimbingan', [
            'tugasAkhir' => $tugasAkhir
        ]);

        return $pdf->download('kartu-bimbingan-' . $mahasiswa->nim . '.pdf');
    }
}
