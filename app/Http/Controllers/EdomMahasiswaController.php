<?php

namespace App\Http\Controllers;

use App\Models\PeriodeEdom;
use App\Models\PertanyaanEdom;
use App\Models\JawabanEdom;
use App\Models\KomentarEdom;
use App\Models\RekapEdom;
use App\Models\Krs;
use App\Models\JadwalKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EdomMahasiswaController extends Controller
{
    /**
     * Display daftar mata kuliah yang perlu dievaluasi
     */
    public function index()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            return back()->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        // Cari periode EDOM yang aktif
        $periodeAktif = PeriodeEdom::aktif()
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->first();

        if (!$periodeAktif) {
            return view('mahasiswa.edom.index', [
                'periodeAktif' => null,
                'mataKuliahList' => collect(),
            ]);
        }

        // Ambil KRS mahasiswa di tahun akademik periode EDOM
        $krsList = Krs::where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $periodeAktif->tahun_akademik_id)
            ->where('status', 'Disetujui')
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen'])
            ->get();

        // Cek mana yang sudah diisi
        $sudahDiisi = JawabanEdom::where('periode_edom_id', $periodeAktif->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->pluck('jadwal_kuliah_id')
            ->unique()
            ->toArray();

        $mataKuliahList = $krsList->map(function ($krs) use ($sudahDiisi) {
            return [
                'krs' => $krs,
                'jadwal_kuliah' => $krs->jadwalKuliah,
                'sudah_diisi' => in_array($krs->jadwal_kuliah_id, $sudahDiisi),
            ];
        });

        return view('mahasiswa.edom.index', compact('periodeAktif', 'mataKuliahList'));
    }

    /**
     * Form pengisian EDOM untuk mata kuliah tertentu
     */
    public function create(JadwalKuliah $jadwalKuliah)
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;

        // Validasi periode aktif
        $periodeAktif = PeriodeEdom::aktif()
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->first();

        if (!$periodeAktif) {
            return back()->with('error', 'Tidak ada periode EDOM yang aktif saat ini!');
        }

        // Validasi mahasiswa terdaftar di kelas ini
        $krs = Krs::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_kuliah_id', $jadwalKuliah->id)
            ->where('status', 'Disetujui')
            ->first();

        if (!$krs) {
            return back()->with('error', 'Anda tidak terdaftar di kelas ini!');
        }

        // Cek apakah sudah mengisi
        $sudahDiisi = JawabanEdom::where('periode_edom_id', $periodeAktif->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_kuliah_id', $jadwalKuliah->id)
            ->exists();

        if ($sudahDiisi) {
            return back()->with('warning', 'Anda sudah mengisi evaluasi untuk mata kuliah ini!');
        }

        // Ambil pertanyaan aktif
        $pertanyaans = PertanyaanEdom::active()
            ->orderBy('kategori')
            ->orderBy('urutan')
            ->get()
            ->groupBy('kategori');

        $kategoris = PertanyaanEdom::getKategoriOptions();
        $nilaiOptions = JawabanEdom::getNilaiOptions();

        $jadwalKuliah->load(['mataKuliah', 'dosen']);

        return view('mahasiswa.edom.create', compact(
            'periodeAktif',
            'jadwalKuliah',
            'pertanyaans',
            'kategoris',
            'nilaiOptions'
        ));
    }

    /**
     * Simpan jawaban EDOM
     */
    public function store(Request $request, JadwalKuliah $jadwalKuliah)
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;

        $periodeAktif = PeriodeEdom::aktif()
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->first();

        if (!$periodeAktif) {
            return back()->with('error', 'Tidak ada periode EDOM yang aktif saat ini!');
        }

        // Validasi
        $request->validate([
            'jawaban' => 'required|array',
            'jawaban.*' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
            'saran' => 'nullable|string|max:1000',
        ]);

        // Cek apakah sudah mengisi
        $sudahDiisi = JawabanEdom::where('periode_edom_id', $periodeAktif->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_kuliah_id', $jadwalKuliah->id)
            ->exists();

        if ($sudahDiisi) {
            return back()->with('warning', 'Anda sudah mengisi evaluasi untuk mata kuliah ini!');
        }

        DB::beginTransaction();
        try {
            // Simpan jawaban
            foreach ($request->jawaban as $pertanyaanId => $nilai) {
                JawabanEdom::create([
                    'periode_edom_id' => $periodeAktif->id,
                    'mahasiswa_id' => $mahasiswa->id,
                    'jadwal_kuliah_id' => $jadwalKuliah->id,
                    'dosen_id' => $jadwalKuliah->dosen_id,
                    'pertanyaan_edom_id' => $pertanyaanId,
                    'nilai' => $nilai,
                ]);
            }

            // Simpan komentar/saran jika ada
            if ($request->filled('komentar') || $request->filled('saran')) {
                KomentarEdom::create([
                    'periode_edom_id' => $periodeAktif->id,
                    'mahasiswa_id' => $mahasiswa->id,
                    'jadwal_kuliah_id' => $jadwalKuliah->id,
                    'dosen_id' => $jadwalKuliah->dosen_id,
                    'komentar' => $request->komentar,
                    'saran' => $request->saran,
                ]);
            }

            // Hitung ulang rekap
            RekapEdom::hitungRekap(
                $periodeAktif->id,
                $jadwalKuliah->dosen_id,
                $jadwalKuliah->id
            );

            DB::commit();

            return redirect()->route('mahasiswa.edom.index')
                ->with('success', 'Evaluasi dosen berhasil disimpan! Terima kasih atas partisipasi Anda.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Riwayat pengisian EDOM
     */
    public function riwayat()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;

        $riwayat = JawabanEdom::where('mahasiswa_id', $mahasiswa->id)
            ->select('periode_edom_id', 'jadwal_kuliah_id', 'dosen_id', DB::raw('MIN(created_at) as tanggal_isi'))
            ->groupBy('periode_edom_id', 'jadwal_kuliah_id', 'dosen_id')
            ->with(['periodeEdom', 'jadwalKuliah.mataKuliah', 'dosen'])
            ->orderBy('tanggal_isi', 'desc')
            ->paginate(10);

        return view('mahasiswa.edom.riwayat', compact('riwayat'));
    }
}
