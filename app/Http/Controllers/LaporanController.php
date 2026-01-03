<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Krs;
use App\Models\Nilai;
use App\Models\Pembayaran;
use App\Models\TahunAkademik;
use App\Models\ProgramStudi;
use App\Models\Absensi;
use App\Models\JadwalKuliah;
use App\Models\MataKuliah;
use App\Models\Yudisium;
use App\Models\PeriodeWisuda;
use App\Models\PendaftaranWisuda;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();
        $programStudi = ProgramStudi::with('fakultas')->get();
        
        return view('laporan.index', compact('tahunAkademik', 'programStudi'));
    }

    /**
     * Laporan Mahasiswa per Program Studi
     */
    public function mahasiswa(Request $request)
    {
        $query = Mahasiswa::with(['programStudi.fakultas', 'user']);
        
        if ($request->program_studi_id) {
            $query->where('program_studi_id', $request->program_studi_id);
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->angkatan) {
            $query->where('angkatan', $request->angkatan);
        }

        $mahasiswa = $query->orderBy('nim')->get();
        $programStudi = ProgramStudi::all();
        
        if ($request->export == 'pdf') {
            $pdf = Pdf::loadView('laporan.mahasiswa-pdf', compact('mahasiswa', 'request'));
            $pdf->setPaper('a4', 'landscape');
            return $pdf->stream('Laporan_Mahasiswa.pdf');
        }

        return view('laporan.mahasiswa', compact('mahasiswa', 'programStudi'));
    }

    /**
     * Laporan Nilai per Mata Kuliah
     */
    public function nilai(Request $request)
    {
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();
        $tahunAkademikAktif = $request->tahun_akademik_id 
            ? TahunAkademik::find($request->tahun_akademik_id)
            : TahunAkademik::getAktif();
        
        $nilai = collect();
        
        if ($tahunAkademikAktif) {
            $nilai = Krs::with(['mahasiswa', 'jadwalKuliah.mataKuliah', 'nilai'])
                ->whereHas('jadwalKuliah', function($q) use ($tahunAkademikAktif) {
                    $q->where('tahun_akademik_id', $tahunAkademikAktif->id);
                })
                ->where('status', 'Disetujui')
                ->get()
                ->groupBy('jadwalKuliah.mataKuliah.nama');
        }

        if ($request->export == 'pdf') {
            $pdf = Pdf::loadView('laporan.nilai-pdf', compact('nilai', 'tahunAkademikAktif'));
            $pdf->setPaper('a4', 'portrait');
            return $pdf->stream('Laporan_Nilai.pdf');
        }

        return view('laporan.nilai', compact('nilai', 'tahunAkademik', 'tahunAkademikAktif'));
    }

    /**
     * Laporan Keuangan / Pembayaran
     */
    public function keuangan(Request $request)
    {
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();
        $tahunAkademikAktif = $request->tahun_akademik_id 
            ? TahunAkademik::find($request->tahun_akademik_id)
            : TahunAkademik::getAktif();
        
        $pembayaran = Pembayaran::with(['mahasiswa.programStudi', 'tahunAkademik'])
            ->when($tahunAkademikAktif, function($q) use ($tahunAkademikAktif) {
                $q->where('tahun_akademik_id', $tahunAkademikAktif->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total' => $pembayaran->sum('jumlah'),
            'lunas' => $pembayaran->where('status', 'Lunas')->sum('jumlah'),
            'belum_lunas' => $pembayaran->where('status', 'Belum Lunas')->sum('jumlah'),
            'count_lunas' => $pembayaran->where('status', 'Lunas')->count(),
            'count_belum' => $pembayaran->where('status', 'Belum Lunas')->count(),
        ];

        if ($request->export == 'pdf') {
            $pdf = Pdf::loadView('laporan.keuangan-pdf', compact('pembayaran', 'summary', 'tahunAkademikAktif'));
            $pdf->setPaper('a4', 'landscape');
            return $pdf->stream('Laporan_Keuangan.pdf');
        }

        return view('laporan.keuangan', compact('pembayaran', 'tahunAkademik', 'tahunAkademikAktif', 'summary'));
    }

    /**
     * Laporan Kehadiran/Absensi
     */
    public function absensi(Request $request)
    {
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();
        $tahunAkademikAktif = $request->tahun_akademik_id 
            ? TahunAkademik::find($request->tahun_akademik_id)
            : TahunAkademik::getAktif();

        $absensi = collect();
        
        if ($tahunAkademikAktif) {
            $absensi = Krs::with(['mahasiswa', 'jadwalKuliah.mataKuliah', 'absensi'])
                ->whereHas('jadwalKuliah', function($q) use ($tahunAkademikAktif) {
                    $q->where('tahun_akademik_id', $tahunAkademikAktif->id);
                })
                ->where('status', 'Disetujui')
                ->get()
                ->map(function($krs) {
                    $total = $krs->absensi->count();
                    $hadir = $krs->absensi->where('status', 'Hadir')->count();
                    $izin = $krs->absensi->where('status', 'Izin')->count();
                    $sakit = $krs->absensi->where('status', 'Sakit')->count();
                    $alpha = $krs->absensi->where('status', 'Alpha')->count();
                    $persentase = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;
                    
                    return [
                        'mahasiswa' => $krs->mahasiswa,
                        'mata_kuliah' => $krs->jadwalKuliah->mataKuliah->nama,
                        'total' => $total,
                        'hadir' => $hadir,
                        'izin' => $izin,
                        'sakit' => $sakit,
                        'alpha' => $alpha,
                        'persentase' => $persentase,
                    ];
                });
        }

        if ($request->export == 'pdf') {
            $pdf = Pdf::loadView('laporan.absensi-pdf', compact('absensi', 'tahunAkademikAktif'));
            $pdf->setPaper('a4', 'landscape');
            return $pdf->stream('Laporan_Absensi.pdf');
        }

        return view('laporan.absensi', compact('absensi', 'tahunAkademik', 'tahunAkademikAktif'));
    }

    /**
     * Statistik Akademik
     */
    public function statistik()
    {
        $tahunAkademikAktif = TahunAkademik::getAktif();
        
        // Statistik Mahasiswa per Status
        $mahasiswaPerStatus = Mahasiswa::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Statistik Mahasiswa per Program Studi
        $mahasiswaPerProdi = Mahasiswa::where('status', 'Aktif')
            ->selectRaw('program_studi_id, count(*) as total')
            ->groupBy('program_studi_id')
            ->with('programStudi')
            ->get();

        // IPK Rata-rata per Program Studi
        $ipkPerProdi = ProgramStudi::with(['mahasiswa' => function($q) {
            $q->where('status', 'Aktif');
        }])->get()->map(function($prodi) {
            $totalIpk = 0;
            $count = 0;
            foreach ($prodi->mahasiswa as $mhs) {
                $ipk = $mhs->hitungIPK();
                if ($ipk > 0) {
                    $totalIpk += $ipk;
                    $count++;
                }
            }
            return [
                'prodi' => $prodi->nama,
                'ipk_rata' => $count > 0 ? round($totalIpk / $count, 2) : 0,
                'jumlah_mhs' => $prodi->mahasiswa->count(),
            ];
        });

        return view('laporan.statistik', compact('mahasiswaPerStatus', 'mahasiswaPerProdi', 'ipkPerProdi', 'tahunAkademikAktif'));
    }

    /**
     * Laporan Wisuda & Yudisium
     */
    public function wisuda(Request $request)
    {
        $periodeWisuda = PeriodeWisuda::orderBy('tanggal_wisuda', 'desc')->get();
        $periodeAktif = $request->periode_wisuda_id 
            ? PeriodeWisuda::find($request->periode_wisuda_id)
            : $periodeWisuda->first();

        $yudisium = collect();
        $summary = [];

        if ($periodeAktif) {
            $yudisium = Yudisium::with(['mahasiswa.programStudi', 'pendaftaranWisuda'])
                ->whereHas('pendaftaranWisuda', function($q) use ($periodeAktif) {
                    $q->where('periode_wisuda_id', $periodeAktif->id);
                })
                ->where('status', 'Disetujui')
                ->orderBy('predikat')
                ->get();

            $summary = [
                'total_lulusan' => $yudisium->count(),
                'cum_laude' => $yudisium->where('predikat', 'Cum Laude')->count(),
                'sangat_memuaskan' => $yudisium->where('predikat', 'Sangat Memuaskan')->count(),
                'memuaskan' => $yudisium->where('predikat', 'Memuaskan')->count(),
                'cukup' => $yudisium->where('predikat', 'Cukup')->count(),
                'ipk_rata' => $yudisium->avg('ipk_akhir') ?? 0,
                'masa_studi_rata' => $yudisium->avg('masa_studi_bulan') ?? 0,
            ];
        }

        if ($request->export == 'pdf') {
            $pdf = Pdf::loadView('laporan.wisuda-pdf', compact('yudisium', 'summary', 'periodeAktif'));
            $pdf->setPaper('a4', 'landscape');
            return $pdf->stream('Laporan_Wisuda.pdf');
        }

        return view('laporan.wisuda', compact('yudisium', 'periodeWisuda', 'periodeAktif', 'summary'));
    }

    /**
     * Laporan Distribusi IPK
     */
    public function ipk(Request $request)
    {
        $programStudi = ProgramStudi::all();
        $prodiFilter = $request->program_studi_id;
        $angkatanFilter = $request->angkatan;

        $query = Mahasiswa::where('status', 'Aktif')
            ->with(['programStudi', 'krs.nilai', 'krs.jadwalKuliah.mataKuliah']);

        if ($prodiFilter) {
            $query->where('program_studi_id', $prodiFilter);
        }

        if ($angkatanFilter) {
            $query->where('angkatan', $angkatanFilter);
        }

        $mahasiswa = $query->get()->map(function($mhs) {
            $ipk = $mhs->hitungIPK();
            $totalSks = $mhs->totalSksLulus();
            return [
                'mahasiswa' => $mhs,
                'ipk' => $ipk,
                'total_sks' => $totalSks,
                'kategori' => $this->getKategoriIPK($ipk),
            ];
        })->sortByDesc('ipk');

        // Distribusi IPK
        $distribusi = [
            'cumlaude' => $mahasiswa->where('ipk', '>=', 3.51)->count(),
            'sangat_memuaskan' => $mahasiswa->whereBetween('ipk', [3.01, 3.50])->count(),
            'memuaskan' => $mahasiswa->whereBetween('ipk', [2.76, 3.00])->count(),
            'cukup' => $mahasiswa->whereBetween('ipk', [2.00, 2.75])->count(),
            'kurang' => $mahasiswa->where('ipk', '<', 2.00)->count(),
        ];

        $summary = [
            'total' => $mahasiswa->count(),
            'ipk_tertinggi' => $mahasiswa->max('ipk') ?? 0,
            'ipk_terendah' => $mahasiswa->min('ipk') ?? 0,
            'ipk_rata' => $mahasiswa->avg('ipk') ?? 0,
        ];

        // Angkatan untuk filter
        $angkatanList = Mahasiswa::select('angkatan')
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan');

        if ($request->export == 'pdf') {
            $pdf = Pdf::loadView('laporan.ipk-pdf', compact('mahasiswa', 'distribusi', 'summary'));
            $pdf->setPaper('a4', 'portrait');
            return $pdf->stream('Laporan_IPK.pdf');
        }

        return view('laporan.ipk', compact('mahasiswa', 'programStudi', 'distribusi', 'summary', 'angkatanList'));
    }

    /**
     * Laporan KRS (Pengambilan Mata Kuliah)
     */
    public function krs(Request $request)
    {
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();
        $tahunAkademikAktif = $request->tahun_akademik_id 
            ? TahunAkademik::find($request->tahun_akademik_id)
            : TahunAkademik::getAktif();

        $krsData = collect();
        $summary = [];

        if ($tahunAkademikAktif) {
            // Data per mata kuliah
            $krsData = JadwalKuliah::with(['mataKuliah', 'dosen', 'krs' => function($q) {
                    $q->where('status', 'Disetujui');
                }])
                ->where('tahun_akademik_id', $tahunAkademikAktif->id)
                ->get()
                ->map(function($jadwal) {
                    return [
                        'mata_kuliah' => $jadwal->mataKuliah->nama ?? '-',
                        'kode' => $jadwal->mataKuliah->kode ?? '-',
                        'sks' => $jadwal->mataKuliah->sks ?? 0,
                        'dosen' => $jadwal->dosen->nama ?? '-',
                        'kelas' => $jadwal->kelas ?? '-',
                        'peserta' => $jadwal->krs->count(),
                        'kapasitas' => $jadwal->kapasitas ?? 40,
                    ];
                })
                ->sortByDesc('peserta');

            $summary = [
                'total_mk' => $krsData->count(),
                'total_peserta' => $krsData->sum('peserta'),
                'rata_peserta' => $krsData->count() > 0 ? round($krsData->avg('peserta'), 1) : 0,
                'mk_penuh' => $krsData->filter(fn($k) => $k['peserta'] >= $k['kapasitas'])->count(),
            ];
        }

        if ($request->export == 'pdf') {
            $pdf = Pdf::loadView('laporan.krs-pdf', compact('krsData', 'summary', 'tahunAkademikAktif'));
            $pdf->setPaper('a4', 'portrait');
            return $pdf->stream('Laporan_KRS.pdf');
        }

        return view('laporan.krs', compact('krsData', 'tahunAkademik', 'tahunAkademikAktif', 'summary'));
    }

    /**
     * Laporan Dosen & Beban Mengajar
     */
    public function dosen(Request $request)
    {
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();
        $tahunAkademikAktif = $request->tahun_akademik_id 
            ? TahunAkademik::find($request->tahun_akademik_id)
            : TahunAkademik::getAktif();

        $dosenData = Dosen::with(['programStudi'])
            ->where('status', 'Aktif')
            ->get()
            ->map(function($dosen) use ($tahunAkademikAktif) {
                $jadwal = $tahunAkademikAktif 
                    ? JadwalKuliah::where('dosen_id', $dosen->id)
                        ->where('tahun_akademik_id', $tahunAkademikAktif->id)
                        ->with('mataKuliah')
                        ->get()
                    : collect();

                $totalSks = $jadwal->sum(fn($j) => $j->mataKuliah->sks ?? 0);
                $jumlahMk = $jadwal->count();
                $jumlahMahasiswa = $jadwal->sum(fn($j) => $j->krs()->where('status', 'Disetujui')->count());
                $mahasiswaBimbingan = Mahasiswa::where('dosen_wali_id', $dosen->id)->where('status', 'Aktif')->count();

                return [
                    'dosen' => $dosen,
                    'total_sks' => $totalSks,
                    'jumlah_mk' => $jumlahMk,
                    'jumlah_mahasiswa' => $jumlahMahasiswa,
                    'mahasiswa_bimbingan' => $mahasiswaBimbingan,
                    'jadwal' => $jadwal,
                ];
            })
            ->sortByDesc('total_sks');

        $summary = [
            'total_dosen' => $dosenData->count(),
            'total_sks' => $dosenData->sum('total_sks'),
            'rata_sks' => $dosenData->count() > 0 ? round($dosenData->avg('total_sks'), 1) : 0,
            'dosen_overload' => $dosenData->filter(fn($d) => $d['total_sks'] > 16)->count(), // Lebih dari 16 SKS
        ];

        if ($request->export == 'pdf') {
            $pdf = Pdf::loadView('laporan.dosen-pdf', compact('dosenData', 'summary', 'tahunAkademikAktif'));
            $pdf->setPaper('a4', 'landscape');
            return $pdf->stream('Laporan_Dosen.pdf');
        }

        return view('laporan.dosen', compact('dosenData', 'tahunAkademik', 'tahunAkademikAktif', 'summary'));
    }

    /**
     * Laporan Kelulusan Tahunan
     */
    public function kelulusan(Request $request)
    {
        $tahunFilter = $request->tahun ?? date('Y');
        
        // Data kelulusan per tahun
        $kelulusanPerTahun = Yudisium::where('status', 'Disetujui')
            ->selectRaw('YEAR(tanggal_lulus) as tahun, COUNT(*) as total')
            ->groupBy(DB::raw('YEAR(tanggal_lulus)'))
            ->orderBy('tahun', 'desc')
            ->get();

        // Detail kelulusan tahun terpilih
        $detailKelulusan = Yudisium::with(['mahasiswa.programStudi'])
            ->where('status', 'Disetujui')
            ->whereYear('tanggal_lulus', $tahunFilter)
            ->get();

        // Per prodi
        $kelulusanPerProdi = $detailKelulusan->groupBy('mahasiswa.programStudi.nama')
            ->map(function($group, $prodi) {
                return [
                    'prodi' => $prodi ?? 'Tidak Diketahui',
                    'total' => $group->count(),
                    'cum_laude' => $group->where('predikat', 'Cum Laude')->count(),
                    'sangat_memuaskan' => $group->where('predikat', 'Sangat Memuaskan')->count(),
                    'memuaskan' => $group->where('predikat', 'Memuaskan')->count(),
                    'cukup' => $group->where('predikat', 'Cukup')->count(),
                    'ipk_rata' => round($group->avg('ipk_akhir'), 2),
                ];
            });

        $summary = [
            'total_lulusan' => $detailKelulusan->count(),
            'cum_laude' => $detailKelulusan->where('predikat', 'Cum Laude')->count(),
            'ipk_rata' => round($detailKelulusan->avg('ipk_akhir'), 2),
            'masa_studi_rata' => round($detailKelulusan->avg('masa_studi_bulan') / 12, 1),
        ];

        $tahunList = Yudisium::selectRaw('DISTINCT YEAR(tanggal_lulus) as tahun')
            ->whereNotNull('tanggal_lulus')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        if ($request->export == 'pdf') {
            $pdf = Pdf::loadView('laporan.kelulusan-pdf', compact('detailKelulusan', 'kelulusanPerProdi', 'summary', 'tahunFilter'));
            $pdf->setPaper('a4', 'portrait');
            return $pdf->stream('Laporan_Kelulusan.pdf');
        }

        return view('laporan.kelulusan', compact('kelulusanPerTahun', 'detailKelulusan', 'kelulusanPerProdi', 'summary', 'tahunFilter', 'tahunList'));
    }

    /**
     * Helper: Get kategori IPK
     */
    private function getKategoriIPK($ipk)
    {
        if ($ipk >= 3.51) return 'Cum Laude';
        if ($ipk >= 3.01) return 'Sangat Memuaskan';
        if ($ipk >= 2.76) return 'Memuaskan';
        if ($ipk >= 2.00) return 'Cukup';
        return 'Kurang';
    }
}
