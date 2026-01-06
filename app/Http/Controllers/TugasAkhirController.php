<?php

namespace App\Http\Controllers;

use App\Models\TugasAkhir;
use App\Models\BimbinganTA;
use App\Models\SeminarProposal;
use App\Models\SidangTA;
use App\Models\RevisiTA;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TugasAkhirController extends Controller
{
    // ========== ADMIN ROUTES ==========

    /**
     * Daftar semua tugas akhir
     */
    public function index(Request $request)
    {
        $query = TugasAkhir::with(['mahasiswa.programStudi', 'pembimbing1', 'pembimbing2'])
            ->orderBy('created_at', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_ta', 'like', "%{$search}%")
                    ->orWhere('judul', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($q2) use ($search) {
                        $q2->where('nama', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
            });
        }

        $tugasAkhirs = $query->paginate(15);

        return view('admin.tugas-akhir.index', compact('tugasAkhirs'));
    }

    /**
     * Detail tugas akhir
     */
    public function show(TugasAkhir $tugasAkhir)
    {
        $tugasAkhir->load([
            'mahasiswa.programStudi', 
            'pembimbing1', 
            'pembimbing2',
            'bimbingan.dosen',
            'seminarProposal.penguji1',
            'seminarProposal.penguji2',
            'sidang.ketuaPenguji',
            'sidang.penguji1',
            'sidang.penguji2',
            'sidang.revisi.dosen'
        ]);

        $dosens = Dosen::orderBy('nama')->get();

        return view('admin.tugas-akhir.show', compact('tugasAkhir', 'dosens'));
    }

    /**
     * Approval judul
     */
    public function approvalJudul(Request $request, TugasAkhir $tugasAkhir)
    {
        $request->validate([
            'status' => 'required|in:judul_disetujui,judul_ditolak',
            'pembimbing_1_id' => 'required_if:status,judul_disetujui|nullable|exists:dosen,id',
            'pembimbing_2_id' => 'nullable|exists:dosen,id|different:pembimbing_1_id',
            'catatan_pembimbing' => 'nullable|string',
        ]);

        $tugasAkhir->update([
            'status' => $request->status,
            'pembimbing_1_id' => $request->pembimbing_1_id,
            'pembimbing_2_id' => $request->pembimbing_2_id,
            'catatan_pembimbing' => $request->catatan_pembimbing,
            'tanggal_approval_judul' => now(),
        ]);

        return back()->with('success', 'Judul berhasil ' . ($request->status == 'judul_disetujui' ? 'disetujui' : 'ditolak') . '!');
    }

    /**
     * Update status
     */
    public function updateStatus(Request $request, TugasAkhir $tugasAkhir)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(TugasAkhir::getStatusOptions())),
            'catatan_pembimbing' => 'nullable|string',
        ]);

        $tugasAkhir->update([
            'status' => $request->status,
            'catatan_pembimbing' => $request->catatan_pembimbing,
        ]);

        if ($request->status == 'selesai') {
            $tugasAkhir->tanggal_lulus = now();
            $tugasAkhir->save();
        }

        return back()->with('success', 'Status berhasil diupdate!');
    }

    // ========== SEMINAR PROPOSAL ==========

    /**
     * Daftar seminar proposal
     */
    public function seminarIndex(Request $request)
    {
        $query = SeminarProposal::with(['tugasAkhir.mahasiswa.programStudi', 'tugasAkhir.pembimbing1', 'tugasAkhir.pembimbing2', 'penguji1', 'penguji2'])
            ->orderBy('tanggal', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $seminars = $query->paginate(15);
        $dosens = Dosen::orderBy('nama')->get();

        return view('admin.tugas-akhir.seminar.index', compact('seminars', 'dosens'));
    }

    /**
     * Jadwalkan seminar
     */
    public function seminarJadwalkan(Request $request, TugasAkhir $tugasAkhir)
    {
        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'ruangan' => 'required|string|max:50',
            'penguji_1_id' => 'required|exists:dosen,id',
            'penguji_2_id' => 'required|exists:dosen,id|different:penguji_1_id',
        ]);

        $seminar = SeminarProposal::updateOrCreate(
            ['tugas_akhir_id' => $tugasAkhir->id],
            [
                'tanggal' => $request->tanggal,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'ruangan' => $request->ruangan,
                'penguji_1_id' => $request->penguji_1_id,
                'penguji_2_id' => $request->penguji_2_id,
                'status' => 'dijadwalkan',
            ]
        );

        $tugasAkhir->status = 'proposal_diajukan';
        $tugasAkhir->save();

        return back()->with('success', 'Seminar proposal berhasil dijadwalkan!');
    }

    /**
     * Input nilai seminar
     */
    public function seminarNilai(Request $request, SeminarProposal $seminar)
    {
        $request->validate([
            'nilai_pembimbing_1' => 'required|numeric|min:0|max:100',
            'nilai_pembimbing_2' => 'nullable|numeric|min:0|max:100',
            'nilai_penguji_1' => 'required|numeric|min:0|max:100',
            'nilai_penguji_2' => 'required|numeric|min:0|max:100',
            'hasil' => 'required|in:lulus,lulus_revisi,tidak_lulus',
            'catatan_revisi' => 'nullable|string',
            'deadline_revisi' => 'nullable|date|after:today',
        ]);

        $seminar->fill($request->all());
        $seminar->hitungNilaiAkhir();
        $seminar->status = 'selesai';
        $seminar->save();

        // Update status TA
        $tugasAkhir = $seminar->tugasAkhir;
        if ($request->hasil == 'lulus') {
            $tugasAkhir->status = 'penelitian';
        } elseif ($request->hasil == 'lulus_revisi') {
            $tugasAkhir->status = 'proposal_revisi';
        } else {
            $tugasAkhir->status = 'judul_disetujui'; // Kembali ke tahap awal
        }
        $tugasAkhir->save();

        return back()->with('success', 'Nilai seminar berhasil disimpan!');
    }

    // ========== SIDANG TA ==========

    /**
     * Daftar sidang
     */
    public function sidangIndex(Request $request)
    {
        $query = SidangTA::with(['tugasAkhir.mahasiswa.programStudi', 'tugasAkhir.pembimbing1', 'tugasAkhir.pembimbing2', 'ketuaPenguji', 'penguji1', 'penguji2'])
            ->orderBy('tanggal', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_sidang', 'like', "%{$search}%")
                    ->orWhereHas('tugasAkhir', function ($q2) use ($search) {
                        $q2->where('judul', 'like', "%{$search}%")
                            ->orWhereHas('mahasiswa', function ($q3) use ($search) {
                                $q3->where('nim', 'like', "%{$search}%")
                                    ->orWhere('nama', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $sidangs = $query->paginate(15);
        $dosens = Dosen::orderBy('nama')->get();

        return view('admin.tugas-akhir.sidang.index', compact('sidangs', 'dosens'));
    }

    /**
     * Jadwalkan sidang
     */
    public function sidangJadwalkan(Request $request, TugasAkhir $tugasAkhir)
    {
        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'ruangan' => 'required|string|max:50',
            'ketua_penguji_id' => 'required|exists:dosen,id',
            'penguji_1_id' => 'required|exists:dosen,id|different:ketua_penguji_id',
            'penguji_2_id' => 'required|exists:dosen,id|different:ketua_penguji_id|different:penguji_1_id',
        ]);

        $sidang = SidangTA::updateOrCreate(
            ['tugas_akhir_id' => $tugasAkhir->id],
            [
                'tanggal' => $request->tanggal,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'ruangan' => $request->ruangan,
                'ketua_penguji_id' => $request->ketua_penguji_id,
                'penguji_1_id' => $request->penguji_1_id,
                'penguji_2_id' => $request->penguji_2_id,
                'status' => 'dijadwalkan',
            ]
        );

        $tugasAkhir->status = 'sidang_dijadwalkan';
        $tugasAkhir->save();

        return back()->with('success', 'Sidang berhasil dijadwalkan!');
    }

    /**
     * Input nilai sidang
     */
    public function sidangNilai(Request $request, SidangTA $sidang)
    {
        $request->validate([
            'nilai_ketua' => 'required|numeric|min:0|max:100',
            'nilai_penguji_1' => 'required|numeric|min:0|max:100',
            'nilai_penguji_2' => 'required|numeric|min:0|max:100',
            'nilai_pembimbing_1' => 'required|numeric|min:0|max:100',
            'nilai_pembimbing_2' => 'nullable|numeric|min:0|max:100',
            'hasil' => 'required|in:lulus,lulus_revisi,tidak_lulus',
            'catatan_revisi' => 'nullable|string',
            'deadline_revisi' => 'nullable|date|after:today',
        ]);

        $sidang->fill($request->all());
        $sidang->hitungNilaiAkhir();
        $sidang->status = 'selesai';
        $sidang->save();

        // Update status TA
        $tugasAkhir = $sidang->tugasAkhir;
        if ($request->hasil == 'lulus') {
            $tugasAkhir->status = 'lulus';
            $tugasAkhir->tanggal_lulus = now();
        } elseif ($request->hasil == 'lulus_revisi') {
            $tugasAkhir->status = 'lulus_revisi';
        } else {
            $tugasAkhir->status = 'tidak_lulus';
        }
        $tugasAkhir->save();

        return back()->with('success', 'Nilai sidang berhasil disimpan!');
    }

    /**
     * Tambah revisi
     */
    public function revisiStore(Request $request, SidangTA $sidang)
    {
        $request->validate([
            'dosen_id' => 'required|exists:dosen,id',
            'catatan_revisi' => 'required|string',
        ]);

        RevisiTA::create([
            'sidang_ta_id' => $sidang->id,
            'dosen_id' => $request->dosen_id,
            'catatan_revisi' => $request->catatan_revisi,
        ]);

        return back()->with('success', 'Revisi berhasil ditambahkan!');
    }

    /**
     * Tandai revisi selesai (oleh dosen)
     */
    public function revisiSelesai(RevisiTA $revisi)
    {
        $revisi->sudah_diperbaiki = true;
        $revisi->tanggal_perbaikan = now();
        $revisi->save();

        // Cek apakah semua revisi sudah selesai
        $sidang = $revisi->sidangTA;
        if ($sidang->cekRevisiSelesai()) {
            $sidang->revisi_selesai = true;
            $sidang->tanggal_revisi_selesai = now();
            $sidang->save();

            // Update status TA
            $tugasAkhir = $sidang->tugasAkhir;
            $tugasAkhir->status = 'selesai';
            $tugasAkhir->save();
        }

        return back()->with('success', 'Revisi ditandai selesai!');
    }

    // ========== BIMBINGAN ==========

    /**
     * Tambah jadwal bimbingan
     */
    public function bimbinganStore(Request $request, TugasAkhir $tugasAkhir)
    {
        $request->validate([
            'dosen_id' => 'required|exists:dosen,id',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'nullable',
            'waktu_selesai' => 'nullable',
            'tempat' => 'nullable|string|max:100',
            'materi_bimbingan' => 'required|string',
        ]);

        BimbinganTA::create([
            'tugas_akhir_id' => $tugasAkhir->id,
            'dosen_id' => $request->dosen_id,
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'tempat' => $request->tempat,
            'materi_bimbingan' => $request->materi_bimbingan,
            'status' => 'dijadwalkan',
        ]);

        return back()->with('success', 'Jadwal bimbingan berhasil ditambahkan!');
    }

    /**
     * Update hasil bimbingan
     */
    public function bimbinganUpdate(Request $request, BimbinganTA $bimbingan)
    {
        $request->validate([
            'hasil_bimbingan' => 'required|string',
            'catatan_dosen' => 'nullable|string',
            'rencana_selanjutnya' => 'nullable|string',
            'persentase_progress' => 'required|integer|min:0|max:100',
            'status' => 'required|in:dijadwalkan,selesai,dibatalkan',
        ]);

        $bimbingan->update($request->all());

        return back()->with('success', 'Bimbingan berhasil diupdate!');
    }
}
