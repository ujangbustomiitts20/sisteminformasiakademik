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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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
}
