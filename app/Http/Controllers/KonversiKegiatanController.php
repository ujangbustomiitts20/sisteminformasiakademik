<?php

namespace App\Http\Controllers;

use App\Models\PengajuanKonversiKegiatan;
use App\Models\DetailKonversiKegiatan;
use App\Models\MataKuliah;
use App\Models\TahunAkademik;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class KonversiKegiatanController extends Controller
{
    /**
     * Index untuk mahasiswa - melihat pengajuan sendiri
     */
    public function mahasiswaIndex()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        $pengajuans = PengajuanKonversiKegiatan::with(['tahunAkademik', 'details'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('mahasiswa.konversi-kegiatan.index', compact('pengajuans', 'mahasiswa'));
    }

    /**
     * Form buat pengajuan baru
     */
    public function create()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        // Cek apakah ada pengajuan draft
        $draftPengajuan = PengajuanKonversiKegiatan::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'Draft')
            ->first();

        if ($draftPengajuan) {
            return redirect()->route('konversi-kegiatan.show', $draftPengajuan)
                ->with('info', 'Anda memiliki pengajuan draft. Silakan lanjutkan atau ajukan.');
        }

        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();
        $jenisKegiatan = DetailKonversiKegiatan::getJenisKegiatanOptions();

        // MK dari prodi mahasiswa
        $mataKuliahs = MataKuliah::where('program_studi_id', $mahasiswa->program_studi_id)
            ->orderBy('semester')
            ->orderBy('nama')
            ->get();

        return view('mahasiswa.konversi-kegiatan.create', compact('mahasiswa', 'tahunAkademik', 'jenisKegiatan', 'mataKuliahs'));
    }

    /**
     * Simpan pengajuan baru
     */
    public function store(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        $request->validate([
            'catatan_mahasiswa' => 'nullable|string|max:1000',
        ]);

        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();

        $pengajuan = PengajuanKonversiKegiatan::create([
            'mahasiswa_id' => $mahasiswa->id,
            'tahun_akademik_id' => $tahunAkademik->id,
            'status' => 'Draft',
            'catatan_mahasiswa' => $request->catatan_mahasiswa,
        ]);

        return redirect()->route('konversi-kegiatan.show', $pengajuan)
            ->with('success', 'Pengajuan berhasil dibuat. Silakan tambahkan kegiatan yang akan dikonversi.');
    }

    /**
     * Tampilkan detail pengajuan
     */
    public function show(PengajuanKonversiKegiatan $pengajuanKonversiKegiatan)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        // Validasi kepemilikan
        if ($mahasiswa && $pengajuanKonversiKegiatan->mahasiswa_id != $mahasiswa->id) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
        }

        $pengajuanKonversiKegiatan->load(['mahasiswa.programStudi', 'tahunAkademik', 'details.mataKuliah', 'diprosesOleh']);

        $jenisKegiatan = DetailKonversiKegiatan::getJenisKegiatanOptions();
        $mataKuliahs = MataKuliah::where('program_studi_id', $pengajuanKonversiKegiatan->mahasiswa->program_studi_id)
            ->orderBy('semester')
            ->orderBy('nama')
            ->get();

        return view('mahasiswa.konversi-kegiatan.show', compact('pengajuanKonversiKegiatan', 'jenisKegiatan', 'mataKuliahs'));
    }

    /**
     * Tambah detail kegiatan
     */
    public function storeDetail(Request $request, PengajuanKonversiKegiatan $pengajuanKonversiKegiatan)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if ($mahasiswa && $pengajuanKonversiKegiatan->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        if (!$pengajuanKonversiKegiatan->canEdit()) {
            return redirect()->back()->with('error', 'Pengajuan tidak dapat diubah.');
        }

        $request->validate([
            'jenis_kegiatan' => 'required|in:Sertifikasi,Lomba,Magang,Kursus,Pelatihan,Pengalaman Kerja,Organisasi,Lainnya',
            'nama_kegiatan' => 'required|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'no_sertifikat' => 'nullable|string|max:100',
            'durasi_jam' => 'nullable|integer|min:1',
            'deskripsi_kegiatan' => 'nullable|string|max:1000',
            'bukti_dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
        ]);

        $data = $request->except('bukti_dokumen');

        // Upload bukti dokumen
        if ($request->hasFile('bukti_dokumen')) {
            $file = $request->file('bukti_dokumen');
            $filename = 'konversi_' . time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/konversi-kegiatan', $filename);
            $data['bukti_dokumen'] = $path;
        }

        $data['pengajuan_konversi_kegiatan_id'] = $pengajuanKonversiKegiatan->id;
        $data['status_detail'] = 'Pending';

        DetailKonversiKegiatan::create($data);

        return redirect()->back()->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    /**
     * Hapus detail kegiatan
     */
    public function destroyDetail(PengajuanKonversiKegiatan $pengajuanKonversiKegiatan, DetailKonversiKegiatan $detail)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if ($mahasiswa && $pengajuanKonversiKegiatan->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        if (!$pengajuanKonversiKegiatan->canEdit()) {
            return redirect()->back()->with('error', 'Pengajuan tidak dapat diubah.');
        }

        // Hapus file jika ada
        if ($detail->bukti_dokumen) {
            Storage::delete($detail->bukti_dokumen);
        }

        $detail->delete();

        return redirect()->back()->with('success', 'Kegiatan berhasil dihapus.');
    }

    /**
     * Submit pengajuan
     */
    public function submit(PengajuanKonversiKegiatan $pengajuanKonversiKegiatan)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if ($mahasiswa && $pengajuanKonversiKegiatan->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        if (!$pengajuanKonversiKegiatan->canSubmit()) {
            return redirect()->back()->with('error', 'Pengajuan tidak dapat diajukan. Pastikan ada minimal 1 kegiatan.');
        }

        $pengajuanKonversiKegiatan->update([
            'status' => 'Diajukan',
            'tanggal_pengajuan' => now(),
        ]);

        return redirect()->route('konversi-kegiatan.index')
            ->with('success', 'Pengajuan berhasil diajukan. Silakan tunggu proses verifikasi.');
    }

    /**
     * Download bukti dokumen
     */
    public function downloadBukti(DetailKonversiKegiatan $detail)
    {
        if (!$detail->bukti_dokumen || !Storage::exists($detail->bukti_dokumen)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        return Storage::download($detail->bukti_dokumen);
    }

    // ========== ADMIN METHODS ==========

    /**
     * Index untuk admin
     */
    public function adminIndex(Request $request)
    {
        $query = PengajuanKonversiKegiatan::with(['mahasiswa.programStudi', 'tahunAkademik', 'details']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('prodi')) {
            $query->whereHas('mahasiswa', function ($q) use ($request) {
                $q->where('program_studi_id', $request->prodi);
            });
        }

        if ($request->filled('tahun_akademik')) {
            $query->where('tahun_akademik_id', $request->tahun_akademik);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_pengajuan', 'like', "%{$search}%")
                  ->orWhereHas('mahasiswa', function ($q2) use ($search) {
                      $q2->where('nama', 'like', "%{$search}%")
                        ->orWhere('nim', 'like', "%{$search}%");
                  });
            });
        }

        $pengajuans = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total' => PengajuanKonversiKegiatan::count(),
            'diajukan' => PengajuanKonversiKegiatan::where('status', 'Diajukan')->count(),
            'diproses' => PengajuanKonversiKegiatan::where('status', 'Diproses')->count(),
            'disetujui' => PengajuanKonversiKegiatan::where('status', 'Disetujui')->count(),
            'ditolak' => PengajuanKonversiKegiatan::where('status', 'Ditolak')->count(),
        ];

        $prodis = ProgramStudi::orderBy('nama')->get();
        $tahunAkademiks = TahunAkademik::orderBy('tahun', 'desc')->get();

        return view('admin.konversi-kegiatan.index', compact('pengajuans', 'stats', 'prodis', 'tahunAkademiks'));
    }

    /**
     * Detail untuk admin
     */
    public function adminShow(PengajuanKonversiKegiatan $pengajuanKonversiKegiatan)
    {
        $pengajuanKonversiKegiatan->load(['mahasiswa.programStudi', 'tahunAkademik', 'details.mataKuliah', 'diprosesOleh']);

        return view('admin.konversi-kegiatan.show', compact('pengajuanKonversiKegiatan'));
    }

    /**
     * Proses verifikasi per detail
     */
    public function verifyDetail(Request $request, DetailKonversiKegiatan $detail)
    {
        $request->validate([
            'status_detail' => 'required|in:Disetujui,Ditolak',
            'sks_diakui' => 'nullable|integer|min:0|max:6',
            'nilai_huruf' => 'nullable|string|max:2',
            'nilai_angka' => 'nullable|numeric|min:0|max:4',
            'catatan_verifikasi' => 'nullable|string|max:500',
        ]);

        $detail->update([
            'status_detail' => $request->status_detail,
            'sks_diakui' => $request->sks_diakui,
            'nilai_huruf' => $request->nilai_huruf,
            'nilai_angka' => $request->nilai_angka,
            'catatan_verifikasi' => $request->catatan_verifikasi,
        ]);

        return redirect()->back()->with('success', 'Verifikasi kegiatan berhasil disimpan.');
    }

    /**
     * Finalisasi pengajuan (approve/reject)
     */
    public function finalize(Request $request, PengajuanKonversiKegiatan $pengajuanKonversiKegiatan)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
            'catatan_admin' => 'nullable|string|max:1000',
        ]);

        $pengajuanKonversiKegiatan->update([
            'status' => $request->status,
            'catatan_admin' => $request->catatan_admin,
            'diproses_oleh' => Auth::id(),
            'tanggal_diproses' => now(),
        ]);

        // Jika disetujui, buat record nilai untuk MK yang disetujui
        if ($request->status === 'Disetujui') {
            $this->createNilaiFromApprovedDetails($pengajuanKonversiKegiatan);
        }

        return redirect()->route('admin.konversi-kegiatan.index')
            ->with('success', 'Pengajuan berhasil ' . strtolower($request->status) . '.');
    }

    /**
     * Buat nilai dari detail yang disetujui
     * 
     * Alur:
     * 1. Cari detail kegiatan yang disetujui
     * 2. Untuk setiap detail, buat KRS dengan flag is_konversi = true
     * 3. Buat Nilai dengan nilai huruf dan bobot dari hasil konversi
     */
    private function createNilaiFromApprovedDetails(PengajuanKonversiKegiatan $pengajuan)
    {
        $approvedDetails = $pengajuan->details()
            ->where('status_detail', 'Disetujui')
            ->whereNotNull('mata_kuliah_id')
            ->whereNotNull('nilai_huruf')
            ->get();
        
        // Ambil tahun akademik aktif
        $tahunAkademik = \App\Models\TahunAkademik::getAktif();
        if (!$tahunAkademik) {
            return;
        }

        foreach ($approvedDetails as $detail) {
            // Cek apakah sudah ada KRS konversi untuk detail ini
            $existingKrs = \App\Models\Krs::where('detail_konversi_kegiatan_id', $detail->id)->first();
            if ($existingKrs) {
                continue; // Skip jika sudah ada
            }

            // Cari jadwal kuliah yang sesuai dengan mata kuliah
            // Jika tidak ada jadwal, buat KRS tanpa jadwal (khusus konversi)
            $jadwalKuliah = \App\Models\JadwalKuliah::where('mata_kuliah_id', $detail->mata_kuliah_id)
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->first();

            // Jika tidak ada jadwal di semester aktif, cari di semester manapun
            if (!$jadwalKuliah) {
                $jadwalKuliah = \App\Models\JadwalKuliah::where('mata_kuliah_id', $detail->mata_kuliah_id)
                    ->latest('tahun_akademik_id')
                    ->first();
            }

            // Jika masih tidak ada jadwal, skip
            if (!$jadwalKuliah) {
                continue;
            }

            // Buat KRS dengan flag konversi
            $krs = \App\Models\Krs::create([
                'mahasiswa_id' => $pengajuan->mahasiswa_id,
                'tahun_akademik_id' => $tahunAkademik->id,
                'jadwal_kuliah_id' => $jadwalKuliah->id,
                'status' => 'Disetujui',
                'tanggal_pengajuan' => now(),
                'tanggal_persetujuan' => now(),
                'is_konversi' => true,
                'detail_konversi_kegiatan_id' => $detail->id,
            ]);

            // Buat Nilai
            $bobot = $this->konversiHurufKeBobot($detail->nilai_huruf);
            \App\Models\Nilai::create([
                'krs_id' => $krs->id,
                'tugas' => null,
                'kehadiran' => null,
                'uts' => null,
                'uas' => null,
                'nilai_akhir' => $detail->nilai_angka ?? ($bobot * 25), // Estimasi nilai akhir dari bobot
                'huruf' => $detail->nilai_huruf,
                'bobot' => $bobot,
            ]);
        }
    }

    /**
     * Konversi nilai huruf ke bobot
     */
    private function konversiHurufKeBobot($huruf)
    {
        $bobotMap = [
            'A' => 4.00,
            'AB' => 3.50,
            'B' => 3.00,
            'BC' => 2.50,
            'C' => 2.00,
            'D' => 1.00,
            'E' => 0.00,
        ];

        return $bobotMap[$huruf] ?? 0.00;
    }
}
