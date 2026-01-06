<?php

namespace App\Http\Controllers;

use App\Models\Krs;
use App\Models\Nilai;
use App\Models\Absensi;
use App\Models\JadwalKuliah;
use App\Models\MataKuliah;
use App\Models\Kurikulum;
use App\Models\TahunAkademik;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\ProgramStudi;
use App\Models\PeriodeUjian;
use App\Models\TugasAkhir;
use App\Models\Wisuda;
use App\Models\Yudisium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AkademikDashboardController extends Controller
{
    public function index()
    {
        $tahunAkademikAktif = TahunAkademik::getAktif();
        
        // Statistik Mahasiswa
        $mahasiswaStats = [
            'total' => Mahasiswa::count(),
            'aktif' => Mahasiswa::where('status', 'Aktif')->count(),
            'cuti' => Mahasiswa::where('status', 'Cuti')->count(),
            'do' => Mahasiswa::where('status', 'DO')->count(),
            'lulus' => Mahasiswa::where('status', 'Lulus')->count(),
            'non_aktif' => Mahasiswa::where('status', 'Non-Aktif')->count(),
        ];
        
        // Statistik Dosen
        $dosenStats = [
            'total' => Dosen::count(),
            'aktif' => Dosen::where('status', 'Aktif')->count(),
            'tidak_aktif' => Dosen::where('status', '!=', 'Aktif')->count(),
        ];
        
        // === KPI STRATEGIS ===
        // Rasio Dosen : Mahasiswa
        $rasioDosenMahasiswa = $dosenStats['aktif'] > 0 
            ? round($mahasiswaStats['aktif'] / $dosenStats['aktif'], 1) 
            : 0;
        
        // Hitung IPK dari tabel nilai (karena kolom ipk tidak ada di mahasiswa)
        // IPK = SUM(bobot * sks) / SUM(sks)
        $ipkData = DB::table('nilai')
            ->join('krs', 'nilai.krs_id', '=', 'krs.id')
            ->join('jadwal_kuliah', 'krs.jadwal_kuliah_id', '=', 'jadwal_kuliah.id')
            ->join('mata_kuliah', 'jadwal_kuliah.mata_kuliah_id', '=', 'mata_kuliah.id')
            ->join('mahasiswa', 'krs.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('krs.status', 'disetujui')
            ->where('mahasiswa.status', 'Aktif')
            ->whereNotNull('nilai.bobot')
            ->select(
                'mahasiswa.id as mahasiswa_id',
                'mahasiswa.program_studi_id',
                DB::raw('SUM(nilai.bobot * mata_kuliah.sks) as total_bobot_sks'),
                DB::raw('SUM(mata_kuliah.sks) as total_sks')
            )
            ->groupBy('mahasiswa.id', 'mahasiswa.program_studi_id')
            ->havingRaw('SUM(mata_kuliah.sks) > 0')
            ->get();
        
        // IPK Rata-rata Institusi
        $totalIpk = 0;
        $countMhs = 0;
        $ipkPerMahasiswa = [];
        foreach ($ipkData as $data) {
            if ($data->total_sks > 0) {
                $ipk = $data->total_bobot_sks / $data->total_sks;
                $ipkPerMahasiswa[$data->mahasiswa_id] = [
                    'ipk' => $ipk,
                    'prodi_id' => $data->program_studi_id
                ];
                $totalIpk += $ipk;
                $countMhs++;
            }
        }
        $ipkRataRata = $countMhs > 0 ? round($totalIpk / $countMhs, 2) : 0;
        
        // IPK per Program Studi
        $ipkByProdi = [];
        foreach ($ipkPerMahasiswa as $mhsId => $data) {
            $prodiId = $data['prodi_id'];
            if (!isset($ipkByProdi[$prodiId])) {
                $ipkByProdi[$prodiId] = ['total' => 0, 'count' => 0];
            }
            $ipkByProdi[$prodiId]['total'] += $data['ipk'];
            $ipkByProdi[$prodiId]['count']++;
        }
        
        $prodiNames = ProgramStudi::pluck('nama', 'id')->toArray();
        $ipkPerProdiArray = [];
        foreach ($ipkByProdi as $prodiId => $data) {
            if ($data['count'] > 0) {
                $ipkPerProdiArray[] = (object)[
                    'id' => $prodiId,
                    'nama' => $prodiNames[$prodiId] ?? 'Unknown',
                    'ipk_rata' => round($data['total'] / $data['count'], 2)
                ];
            }
        }
        usort($ipkPerProdiArray, fn($a, $b) => $b->ipk_rata <=> $a->ipk_rata);
        $ipkPerProdi = collect($ipkPerProdiArray);
        
        // === EARLY WARNING ===
        // Mahasiswa IPK < 2.0
        $mahasiswaIpkRendah = collect($ipkPerMahasiswa)->filter(fn($d) => $d['ipk'] < 2.0)->count();
        
        // Mahasiswa dengan kehadiran rendah (< 75%)
        $mahasiswaKehadiranRendah = 0;
        if ($tahunAkademikAktif) {
            $kehadiranData = DB::table('krs')
                ->join('absensi', 'krs.id', '=', 'absensi.krs_id')
                ->where('krs.tahun_akademik_id', $tahunAkademikAktif->id)
                ->where('krs.status', 'disetujui')
                ->select(
                    'krs.mahasiswa_id',
                    DB::raw('SUM(CASE WHEN absensi.status = "Hadir" THEN 1 ELSE 0 END) as hadir'),
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('krs.mahasiswa_id')
                ->get();
            
            $mahasiswaKehadiranRendah = $kehadiranData->filter(function($d) {
                return $d->total > 0 && ($d->hadir / $d->total) < 0.75;
            })->count();
        }
        
        // Daftar mahasiswa bermasalah (IPK rendah)
        $mahasiswaIdIpkRendah = collect($ipkPerMahasiswa)
            ->filter(fn($d) => $d['ipk'] < 2.0)
            ->sortBy('ipk')
            ->take(10)
            ->keys()
            ->toArray();
        
        $listMahasiswaBermasalah = Mahasiswa::with('programStudi')
            ->whereIn('id', $mahasiswaIdIpkRendah)
            ->get()
            ->map(function($mhs) use ($ipkPerMahasiswa) {
                $mhs->ipk = $ipkPerMahasiswa[$mhs->id]['ipk'] ?? 0;
                return $mhs;
            })
            ->sortBy('ipk')
            ->values();
        
        // === PROGRESS KELULUSAN ===
        // Statistik Tugas Akhir
        $taStats = [
            'total' => TugasAkhir::count(),
            'pengajuan' => TugasAkhir::where('status', 'pengajuan')->count(),
            'bimbingan' => TugasAkhir::where('status', 'bimbingan')->count(),
            'sidang' => TugasAkhir::where('status', 'sidang')->count(),
            'selesai' => TugasAkhir::where('status', 'selesai')->count(),
            'revisi' => TugasAkhir::where('status', 'revisi')->count(),
        ];
        
        // Calon Wisudawan (yang sudah yudisium disetujui)
        $calonWisudawan = Yudisium::where('status', 'disetujui')->count();
        
        // Mahasiswa semester akhir (>= semester 8)
        $mahasiswaSemesterAkhir = Mahasiswa::where('status', 'Aktif')
            ->where('semester_aktif', '>=', 8)
            ->count();
        
        // Tingkat kelulusan tahun ini
        $lulusTahunIni = Mahasiswa::where('status', 'Lulus')
            ->whereYear('updated_at', now()->year)
            ->count();
        
        // Mahasiswa per Program Studi
        $mahasiswaPerProdi = Mahasiswa::where('status', 'Aktif')
            ->join('program_studi', 'mahasiswa.program_studi_id', '=', 'program_studi.id')
            ->select('program_studi.nama', 'program_studi.id', DB::raw('COUNT(*) as total'))
            ->groupBy('program_studi.id', 'program_studi.nama')
            ->orderByDesc('total')
            ->get();
        
        // Statistik KRS
        $krsStats = [
            'total' => Krs::when($tahunAkademikAktif, fn($q) => $q->where('tahun_akademik_id', $tahunAkademikAktif->id))->count(),
            'pending' => Krs::when($tahunAkademikAktif, fn($q) => $q->where('tahun_akademik_id', $tahunAkademikAktif->id))
                ->where('status', 'diajukan')->count(),
            'approved' => Krs::when($tahunAkademikAktif, fn($q) => $q->where('tahun_akademik_id', $tahunAkademikAktif->id))
                ->where('status', 'disetujui')->count(),
            'rejected' => Krs::when($tahunAkademikAktif, fn($q) => $q->where('tahun_akademik_id', $tahunAkademikAktif->id))
                ->where('status', 'ditolak')->count(),
        ];
        
        // Statistik Nilai
        $nilaiStats = [
            'total_krs' => Krs::when($tahunAkademikAktif, fn($q) => $q->where('tahun_akademik_id', $tahunAkademikAktif->id))
                ->where('status', 'disetujui')->count(),
            'sudah_dinilai' => Nilai::whereHas('krs', function($q) use ($tahunAkademikAktif) {
                $q->where('status', 'disetujui');
                if ($tahunAkademikAktif) {
                    $q->where('tahun_akademik_id', $tahunAkademikAktif->id);
                }
            })->whereNotNull('huruf')->count(),
            'belum_dinilai' => 0,
        ];
        $nilaiStats['belum_dinilai'] = $nilaiStats['total_krs'] - $nilaiStats['sudah_dinilai'];
        
        // Distribusi Nilai
        $distribusiNilai = Nilai::whereHas('krs', function($q) use ($tahunAkademikAktif) {
                $q->where('status', 'disetujui');
                if ($tahunAkademikAktif) {
                    $q->where('tahun_akademik_id', $tahunAkademikAktif->id);
                }
            })
            ->whereNotNull('huruf')
            ->select('huruf', DB::raw('COUNT(*) as total'))
            ->groupBy('huruf')
            ->orderByRaw("FIELD(huruf, 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E')")
            ->get();
        
        // Statistik Jadwal Kuliah
        $jadwalStats = [
            'total' => JadwalKuliah::when($tahunAkademikAktif, fn($q) => $q->where('tahun_akademik_id', $tahunAkademikAktif->id))->count(),
            'hari_ini' => JadwalKuliah::when($tahunAkademikAktif, fn($q) => $q->where('tahun_akademik_id', $tahunAkademikAktif->id))
                ->where('hari', now()->locale('id')->dayName)->count(),
        ];
        
        // Statistik Mata Kuliah
        $mkStats = [
            'total' => MataKuliah::count(),
            'wajib' => MataKuliah::where('jenis', 'Wajib')->count(),
            'pilihan' => MataKuliah::where('jenis', 'Pilihan')->count(),
        ];
        
        // Periode Ujian Aktif
        $periodeUjianAktif = PeriodeUjian::when($tahunAkademikAktif, fn($q) => $q->where('tahun_akademik_id', $tahunAkademikAktif->id))
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->first();
        
        // Jadwal hari ini
        $jadwalHariIni = JadwalKuliah::with(['mataKuliah', 'dosen', 'ruangan'])
            ->when($tahunAkademikAktif, fn($q) => $q->where('tahun_akademik_id', $tahunAkademikAktif->id))
            ->where('hari', now()->locale('id')->dayName)
            ->orderBy('jam_mulai')
            ->take(10)
            ->get();
        
        // Mahasiswa belum KRS
        $mahasiswaBelumKrs = 0;
        if ($tahunAkademikAktif) {
            $mahasiswaSudahKrs = Krs::where('tahun_akademik_id', $tahunAkademikAktif->id)
                ->distinct('mahasiswa_id')
                ->count('mahasiswa_id');
            $totalMahasiswaAktif = Mahasiswa::where('status', 'Aktif')->count();
            $mahasiswaBelumKrs = $totalMahasiswaAktif - $mahasiswaSudahKrs;
        }
        
        // Recent KRS pending approval
        $recentPendingKrs = Krs::with(['mahasiswa', 'jadwalKuliah.mataKuliah'])
            ->when($tahunAkademikAktif, fn($q) => $q->where('tahun_akademik_id', $tahunAkademikAktif->id))
            ->where('status', 'diajukan')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
        
        return view('admin.akademik.dashboard', compact(
            'tahunAkademikAktif',
            'mahasiswaStats',
            'dosenStats',
            'mahasiswaPerProdi',
            'krsStats',
            'nilaiStats',
            'distribusiNilai',
            'jadwalStats',
            'mkStats',
            'periodeUjianAktif',
            'jadwalHariIni',
            'mahasiswaBelumKrs',
            'recentPendingKrs',
            // KPI Strategis
            'rasioDosenMahasiswa',
            'ipkRataRata',
            'ipkPerProdi',
            // Early Warning
            'mahasiswaIpkRendah',
            'mahasiswaKehadiranRendah',
            'listMahasiswaBermasalah',
            // Progress Kelulusan
            'taStats',
            'calonWisudawan',
            'mahasiswaSemesterAkhir',
            'lulusTahunIni'
        ));
    }
}
