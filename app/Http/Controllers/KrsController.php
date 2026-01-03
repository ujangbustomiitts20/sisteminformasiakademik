<?php

namespace App\Http\Controllers;

use App\Models\Krs;
use App\Models\JadwalKuliah;
use App\Models\TahunAkademik;
use App\Models\Mahasiswa;
use App\Models\KurikulumMataKuliah;
use App\Models\Kurikulum;
use Illuminate\Http\Request;

class KrsController extends Controller
{
    // Untuk mahasiswa mengambil KRS
    public function index()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        $tahunAkademikAktif = TahunAkademik::getAktif();

        if (!$mahasiswa || !$tahunAkademikAktif) {
            return view('krs.index', ['error' => 'Data tidak ditemukan']);
        }

        $krsSemesterIni = Krs::where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $tahunAkademikAktif->id)
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'jadwalKuliah.ruangan', 'nilai'])
            ->get();

        $totalSks = $krsSemesterIni->where('status', '!=', 'Ditolak')
            ->sum(fn($krs) => $krs->jadwalKuliah->mataKuliah->sks ?? 0);

        $isPeriodeKrs = $tahunAkademikAktif->isPeriodeKrs();
        $modeKrs = setting('mode_krs', 'pilihan'); // paket atau pilihan

        return view('krs.index', compact('mahasiswa', 'tahunAkademikAktif', 'krsSemesterIni', 'totalSks', 'isPeriodeKrs', 'modeKrs'));
    }

    // Form pengambilan KRS
    public function create()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        $tahunAkademikAktif = TahunAkademik::getAktif();

        if (!$mahasiswa || !$tahunAkademikAktif) {
            return back()->with('error', 'Data tidak ditemukan');
        }

        if (!$tahunAkademikAktif->isPeriodeKrs()) {
            return back()->with('error', 'Periode pengisian KRS belum dibuka!');
        }

        $modeKrs = setting('mode_krs', 'pilihan');

        // Mode KRS Paket - ambil berdasarkan kurikulum dan semester mahasiswa
        if ($modeKrs === 'paket') {
            return $this->createPaket($mahasiswa, $tahunAkademikAktif);
        }

        // Mode KRS Pilihan - mahasiswa memilih sendiri
        return $this->createPilihan($mahasiswa, $tahunAkademikAktif);
    }

    // KRS Mode Paket
    private function createPaket($mahasiswa, $tahunAkademikAktif)
    {
        // Hitung semester mahasiswa saat ini
        $semesterMahasiswa = $mahasiswa->semester ?? $this->hitungSemester($mahasiswa);
        
        // Cari kurikulum aktif untuk program studi mahasiswa
        $kurikulum = Kurikulum::where('program_studi_id', $mahasiswa->program_studi_id)
            ->where('is_aktif', true)
            ->first();

        if (!$kurikulum) {
            return back()->with('error', 'Kurikulum tidak ditemukan untuk program studi Anda!');
        }

        // Ambil mata kuliah paket untuk semester ini
        $mataKuliahPaket = KurikulumMataKuliah::where('kurikulum_id', $kurikulum->id)
            ->where('semester_rekomendasi', $semesterMahasiswa)
            ->with('mataKuliah')
            ->get();

        // Cari jadwal untuk mata kuliah paket
        $mataKuliahIds = $mataKuliahPaket->pluck('mata_kuliah_id');
        
        $jadwalPaket = JadwalKuliah::where('tahun_akademik_id', $tahunAkademikAktif->id)
            ->whereIn('mata_kuliah_id', $mataKuliahIds)
            ->with(['mataKuliah', 'dosen', 'ruangan'])
            ->get();

        // Filter jadwal yang belum diambil
        $krsIds = Krs::where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $tahunAkademikAktif->id)
            ->pluck('jadwal_kuliah_id')
            ->toArray();

        $jadwalPaket = $jadwalPaket->filter(function($jadwal) use ($krsIds) {
            return !in_array($jadwal->id, $krsIds);
        });

        // Group by mata kuliah untuk pilihan kelas
        $jadwalGrouped = $jadwalPaket->groupBy('mata_kuliah_id');

        return view('krs.create-paket', compact(
            'mahasiswa', 
            'tahunAkademikAktif', 
            'mataKuliahPaket',
            'jadwalGrouped',
            'semesterMahasiswa',
            'kurikulum'
        ));
    }

    // KRS Mode Pilihan
    private function createPilihan($mahasiswa, $tahunAkademikAktif)
    {
        // Ambil jadwal yang tersedia untuk program studi mahasiswa
        $jadwalTersedia = JadwalKuliah::where('tahun_akademik_id', $tahunAkademikAktif->id)
            ->whereHas('mataKuliah', function($q) use ($mahasiswa) {
                $q->where('program_studi_id', $mahasiswa->program_studi_id);
            })
            ->with(['mataKuliah', 'dosen', 'ruangan'])
            ->get();

        // Filter jadwal yang belum diambil
        $krsIds = Krs::where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $tahunAkademikAktif->id)
            ->pluck('jadwal_kuliah_id')
            ->toArray();

        $jadwalTersedia = $jadwalTersedia->filter(function($jadwal) use ($krsIds) {
            return !in_array($jadwal->id, $krsIds) && $jadwal->sisaKuota() > 0;
        });

        return view('krs.create', compact('mahasiswa', 'tahunAkademikAktif', 'jadwalTersedia'));
    }

    // Hitung semester mahasiswa berdasarkan tahun masuk/angkatan
    private function hitungSemester($mahasiswa)
    {
        $tahunMasuk = $mahasiswa->tahun_masuk ?? $mahasiswa->angkatan ?? now()->year;
        $tahunAkademik = TahunAkademik::getAktif();
        
        if (!$tahunAkademik) {
            return 1;
        }

        // Parse tahun akademik (format: 2024/2025)
        $tahunAwal = (int) substr($tahunAkademik->tahun, 0, 4);
        $semester = $tahunAkademik->semester; // Ganjil atau Genap
        
        // Hitung selisih tahun
        $selisihTahun = $tahunAwal - $tahunMasuk;
        
        // Hitung semester: tahun * 2 + (1 jika genap, 0 jika ganjil)
        $semesterMahasiswa = ($selisihTahun * 2) + ($semester === 'Genap' ? 2 : 1);
        
        return max(1, $semesterMahasiswa);
    }

    // Simpan KRS Paket (ambil semua paket sekaligus)
    public function storePaket(Request $request)
    {
        $request->validate([
            'jadwal_kuliah_id' => 'required|array',
            'jadwal_kuliah_id.*' => 'exists:jadwal_kuliah,id',
        ]);

        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        $tahunAkademikAktif = TahunAkademik::getAktif();

        if (!$tahunAkademikAktif->isPeriodeKrs()) {
            return back()->with('error', 'Periode pengisian KRS belum dibuka!');
        }

        $errors = [];
        $berhasil = 0;

        foreach ($request->jadwal_kuliah_id as $jadwalId) {
            // Check apakah sudah diambil
            $exists = Krs::where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $tahunAkademikAktif->id)
                ->where('jadwal_kuliah_id', $jadwalId)
                ->exists();

            if ($exists) {
                continue;
            }

            // Cek kuota
            $jadwal = JadwalKuliah::with('mataKuliah')->find($jadwalId);
            if ($jadwal && $jadwal->sisaKuota() <= 0) {
                $errors[] = "Kuota kelas {$jadwal->mataKuliah->nama} ({$jadwal->kelas}) sudah penuh!";
                continue;
            }

            Krs::create([
                'mahasiswa_id' => $mahasiswa->id,
                'tahun_akademik_id' => $tahunAkademikAktif->id,
                'jadwal_kuliah_id' => $jadwalId,
                'status' => 'Disetujui', // KRS Paket langsung disetujui
                'tanggal_pengajuan' => now(),
                'tanggal_persetujuan' => now(),
            ]);
            $berhasil++;
        }

        if (!empty($errors)) {
            $errorMsg = implode("<br>", $errors);
            if ($berhasil > 0) {
                return redirect()->route('krs.index')
                    ->with('warning', "{$berhasil} mata kuliah berhasil ditambahkan. Beberapa gagal:<br>" . $errorMsg);
            }
            return back()->with('error', $errorMsg);
        }

        return redirect()->route('krs.index')->with('success', "KRS Paket berhasil! {$berhasil} mata kuliah telah ditambahkan.");
    }

    // Simpan KRS
    public function store(Request $request)
    {
        $request->validate([
            'jadwal_kuliah_id' => 'required|array',
            'jadwal_kuliah_id.*' => 'exists:jadwal_kuliah,id',
        ]);

        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        $tahunAkademikAktif = TahunAkademik::getAktif();

        if (!$tahunAkademikAktif->isPeriodeKrs()) {
            return back()->with('error', 'Periode pengisian KRS belum dibuka!');
        }

        $errors = [];
        $berhasil = 0;

        foreach ($request->jadwal_kuliah_id as $jadwalId) {
            // Check apakah sudah diambil
            $exists = Krs::where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $tahunAkademikAktif->id)
                ->where('jadwal_kuliah_id', $jadwalId)
                ->exists();

            if ($exists) {
                continue;
            }

            // Cek prasyarat mata kuliah
            $jadwal = JadwalKuliah::with('mataKuliah')->find($jadwalId);
            if ($jadwal && $jadwal->mataKuliah) {
                $cekPrasyarat = $jadwal->mataKuliah->cekPrasyarat($mahasiswa->id);
                
                if (!$cekPrasyarat['terpenuhi']) {
                    $mkNama = $jadwal->mataKuliah->nama;
                    $prasyaratBelumTerpenuhi = collect($cekPrasyarat['detail'])
                        ->filter(fn($d) => !$d['terpenuhi'])
                        ->map(fn($d) => $d['mata_kuliah'])
                        ->implode(', ');
                    
                    $errors[] = "Mata kuliah <strong>{$mkNama}</strong> memiliki prasyarat yang belum terpenuhi: {$prasyaratBelumTerpenuhi}";
                    continue;
                }
            }

            Krs::create([
                'mahasiswa_id' => $mahasiswa->id,
                'tahun_akademik_id' => $tahunAkademikAktif->id,
                'jadwal_kuliah_id' => $jadwalId,
                'status' => 'Pending',
                'tanggal_pengajuan' => now(),
            ]);
            $berhasil++;
        }

        if (!empty($errors)) {
            $errorMsg = "Beberapa mata kuliah tidak dapat diambil karena prasyarat belum terpenuhi:<br><ul><li>" . implode("</li><li>", $errors) . "</li></ul>";
            if ($berhasil > 0) {
                return redirect()->route('krs.index')
                    ->with('warning', "{$berhasil} mata kuliah berhasil diajukan. " . $errorMsg);
            }
            return back()->with('error', $errorMsg);
        }

        return redirect()->route('krs.index')->with('success', 'KRS berhasil diajukan! Menunggu persetujuan dosen wali.');
    }

    // Hapus KRS
    public function destroy(Krs $krs)
    {
        $user = auth()->user();
        $tahunAkademikAktif = TahunAkademik::getAktif();

        if ($krs->mahasiswa_id != $user->mahasiswa->id) {
            return back()->with('error', 'Akses ditolak!');
        }

        if (!$tahunAkademikAktif->isPeriodeKrs()) {
            return back()->with('error', 'Periode pengisian KRS sudah ditutup!');
        }

        if ($krs->status == 'Disetujui') {
            return back()->with('error', 'KRS yang sudah disetujui tidak dapat dihapus!');
        }

        $krs->delete();
        return back()->with('success', 'KRS berhasil dihapus!');
    }

    // Untuk dosen wali menyetujui KRS
    public function persetujuan(Request $request)
    {
        $user = auth()->user();
        $dosen = $user->dosen;
        $tahunAkademikAktif = TahunAkademik::getAktif();

        if (!$dosen) {
            return back()->with('error', 'Data dosen tidak ditemukan');
        }

        $mahasiswaWali = Mahasiswa::where('dosen_wali_id', $dosen->id)
            ->where('status', 'Aktif')
            ->with(['krs' => function($q) use ($tahunAkademikAktif) {
                if ($tahunAkademikAktif) {
                    $q->where('tahun_akademik_id', $tahunAkademikAktif->id);
                }
                $q->with(['jadwalKuliah.mataKuliah']);
            }])
            ->get();

        return view('krs.persetujuan', compact('mahasiswaWali', 'tahunAkademikAktif'));
    }

    // Setujui KRS
    public function approve(Krs $krs)
    {
        $user = auth()->user();
        $dosen = $user->dosen;

        if ($krs->mahasiswa->dosen_wali_id != $dosen->id) {
            return back()->with('error', 'Anda bukan dosen wali mahasiswa ini!');
        }

        $krs->update([
            'status' => 'Disetujui',
            'tanggal_persetujuan' => now(),
        ]);

        return back()->with('success', 'KRS berhasil disetujui!');
    }

    // Tolak KRS
    public function reject(Krs $krs)
    {
        $user = auth()->user();
        $dosen = $user->dosen;

        if ($krs->mahasiswa->dosen_wali_id != $dosen->id) {
            return back()->with('error', 'Anda bukan dosen wali mahasiswa ini!');
        }

        $krs->update([
            'status' => 'Ditolak',
            'tanggal_persetujuan' => now(),
        ]);

        return back()->with('success', 'KRS ditolak!');
    }

    // Approve semua KRS mahasiswa
    public function approveAll(Mahasiswa $mahasiswa)
    {
        $user = auth()->user();
        $dosen = $user->dosen;
        $tahunAkademikAktif = TahunAkademik::getAktif();

        if ($mahasiswa->dosen_wali_id != $dosen->id) {
            return back()->with('error', 'Anda bukan dosen wali mahasiswa ini!');
        }

        Krs::where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $tahunAkademikAktif->id)
            ->where('status', 'Pending')
            ->update([
                'status' => 'Disetujui',
                'tanggal_persetujuan' => now(),
            ]);

        return back()->with('success', 'Semua KRS mahasiswa berhasil disetujui!');
    }
}
