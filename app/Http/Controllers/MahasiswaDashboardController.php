<?php

namespace App\Http\Controllers;

use App\Models\Krs;
use App\Models\Tagihan;
use App\Models\TransaksiPembayaran;
use App\Models\PotonganMahasiswa;
use App\Models\Cicilan;
use App\Models\PeriodeDiskon;
use App\Models\KalenderAkademik;
use App\Models\Absensi;
use App\Models\TahunAkademik;
use App\Models\Pengumuman;
use Illuminate\Http\Request;

class MahasiswaDashboardController extends Controller
{
    /**
     * Dashboard Utama Mahasiswa (ringkas)
     */
    public function index()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }
        
        $mahasiswa->load(['programStudi.fakultas', 'dosenWali']);
        $tahunAkademikAktif = TahunAkademik::getAktif();
        
        // Data Ringkas Akademik
        $ipk = $mahasiswa->hitungIPK();
        $totalSks = $mahasiswa->totalSksLulus();
        
        // Data Ringkas Keuangan
        $totalTagihan = Tagihan::where('mahasiswa_id', $mahasiswa->id)->belumLunas()->sum('sisa_tagihan');
        $tagihanJatuhTempo = Tagihan::where('mahasiswa_id', $mahasiswa->id)
            ->belumLunas()
            ->where('tanggal_jatuh_tempo', '<', now())
            ->count();
        
        // Jadwal Hari Ini
        $hariIni = now()->locale('id')->isoFormat('dddd');
        $jadwalHariIni = collect();
        if ($tahunAkademikAktif) {
            $krsSemesterIni = Krs::where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $tahunAkademikAktif->id)
                ->where('status', 'Disetujui')
                ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'jadwalKuliah.ruangan'])
                ->get();
            
            $jadwalHariIni = $krsSemesterIni
                ->filter(fn($krs) => $krs->jadwalKuliah && $krs->jadwalKuliah->hari === $hariIni)
                ->sortBy(fn($krs) => $krs->jadwalKuliah->jam_mulai);
        }
        
        // Event Mendatang (7 hari)
        $eventMendatang = KalenderAkademik::where('tanggal_mulai', '>=', now())
            ->where('tanggal_mulai', '<=', now()->addDays(7))
            ->orderBy('tanggal_mulai', 'asc')
            ->take(3)
            ->get();
        
        // Pengumuman
        $pengumuman = Pengumuman::aktif()->latest()->take(3)->get();
        
        return view('dashboard.mahasiswa', compact(
            'mahasiswa', 
            'tahunAkademikAktif',
            'ipk', 
            'totalSks', 
            'totalTagihan',
            'tagihanJatuhTempo',
            'jadwalHariIni',
            'eventMendatang',
            'pengumuman'
        ));
    }
    
    /**
     * Dashboard Akademik Mahasiswa
     */
    public function akademik()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }
        
        $mahasiswa->load(['programStudi.fakultas', 'dosenWali']);
        $tahunAkademikAktif = TahunAkademik::getAktif();
        
        // Check if mahasiswa has KRS in active tahun akademik
        $hasKrsInActiveTa = false;
        if ($tahunAkademikAktif) {
            $hasKrsInActiveTa = Krs::where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $tahunAkademikAktif->id)
                ->exists();
        }
        
        // If no KRS in active TA, fallback to latest TA with KRS
        $displayTahunAkademik = $tahunAkademikAktif;
        if (!$hasKrsInActiveTa) {
            $latestTaWithKrs = TahunAkademik::whereIn('id', 
                Krs::where('mahasiswa_id', $mahasiswa->id)->pluck('tahun_akademik_id')
            )->orderBy('id', 'desc')->first();
            
            if ($latestTaWithKrs) {
                $displayTahunAkademik = $latestTaWithKrs;
            }
        }
        
        $data = [
            'mahasiswa' => $mahasiswa,
            'tahunAkademikAktif' => $tahunAkademikAktif,
            'displayTahunAkademik' => $displayTahunAkademik,
            'hasKrsInActiveTa' => $hasKrsInActiveTa,
            'ipk' => $mahasiswa->hitungIPK(),
            'totalSks' => $mahasiswa->totalSksLulus(),
            'targetSks' => $mahasiswa->programStudi?->total_sks ?? 144,
            'krsSemesterIni' => collect(),
            'sksSemesterIni' => 0,
            'jadwalHariIni' => collect(),
            'rekapKehadiran' => ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0, 'total' => 0],
            'persentaseKehadiran' => 100,
            'eventMendatang' => collect(),
            'pengumuman' => Pengumuman::aktif()->where('kategori', 'Akademik')->latest()->take(5)->get(),
        ];
        
        $data['progressSks'] = $data['targetSks'] > 0 ? round(($data['totalSks'] / $data['targetSks']) * 100) : 0;
        
        if ($displayTahunAkademik) {
            // KRS Semester Ini (use displayTahunAkademik)
            $data['krsSemesterIni'] = Krs::where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $displayTahunAkademik->id)
                ->where('status', 'Disetujui')
                ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'jadwalKuliah.ruangan'])
                ->get();
            
            $data['sksSemesterIni'] = $data['krsSemesterIni']->sum(fn($krs) => $krs->jadwalKuliah?->mataKuliah?->sks ?? 0);
            
            // Jadwal Hari Ini (only if displaying active TA)
            if ($hasKrsInActiveTa || $displayTahunAkademik->id === $tahunAkademikAktif?->id) {
                $hariIni = now()->locale('id')->isoFormat('dddd');
                $data['jadwalHariIni'] = $data['krsSemesterIni']
                    ->filter(fn($krs) => $krs->jadwalKuliah && $krs->jadwalKuliah->hari === $hariIni)
                    ->sortBy(fn($krs) => $krs->jadwalKuliah->jam_mulai);
            }
            
            // Rekap Kehadiran
            $krsIds = $data['krsSemesterIni']->pluck('id')->filter();
            if ($krsIds->count() > 0) {
                $absensi = Absensi::whereIn('krs_id', $krsIds)->get();
                $data['rekapKehadiran'] = [
                    'hadir' => $absensi->where('status', 'Hadir')->count(),
                    'izin' => $absensi->where('status', 'Izin')->count(),
                    'sakit' => $absensi->where('status', 'Sakit')->count(),
                    'alpha' => $absensi->where('status', 'Alpha')->count(),
                    'total' => $absensi->count(),
                ];
                $totalHadir = $data['rekapKehadiran']['hadir'] + $data['rekapKehadiran']['izin'] + $data['rekapKehadiran']['sakit'];
                $data['persentaseKehadiran'] = $data['rekapKehadiran']['total'] > 0 
                    ? round(($totalHadir / $data['rekapKehadiran']['total']) * 100) 
                    : 100;
            }
        }
        
        // Event Akademik Mendatang
        $data['eventMendatang'] = KalenderAkademik::where('tanggal_mulai', '>=', now())
            ->where('tanggal_mulai', '<=', now()->addDays(30))
            ->orderBy('tanggal_mulai', 'asc')
            ->take(5)
            ->get();
        
        return view('dashboard.mahasiswa-akademik', $data);
    }
    
    /**
     * Dashboard Keuangan Mahasiswa
     */
    public function keuangan()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }
        
        $mahasiswa->load(['programStudi']);
        $tahunAkademikAktif = TahunAkademik::getAktif();
        
        // Tagihan
        $tagihanBelumLunas = Tagihan::where('mahasiswa_id', $mahasiswa->id)
            ->belumLunas()
            ->with('tahunAkademik')
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->get();
        
        $totalTagihan = $tagihanBelumLunas->sum('sisa_tagihan');
        $tagihanJatuhTempo = $tagihanBelumLunas->filter(fn($t) => $t->tanggal_jatuh_tempo && \Carbon\Carbon::parse($t->tanggal_jatuh_tempo)->isPast())->count();
        
        // Transaksi Pembayaran
        $transaksiTerbaru = TransaksiPembayaran::where('mahasiswa_id', $mahasiswa->id)
            ->with('tagihan')
            ->orderBy('tanggal_bayar', 'desc')
            ->take(10)
            ->get();
        
        $totalDibayarTahunIni = TransaksiPembayaran::where('mahasiswa_id', $mahasiswa->id)
            ->whereYear('tanggal_bayar', now()->year)
            ->sum('jumlah');
        
        $totalDibayarSemua = TransaksiPembayaran::where('mahasiswa_id', $mahasiswa->id)->sum('jumlah');
        
        // Potongan Aktif
        $potonganAktif = PotonganMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'disetujui')
            ->where(function($q) {
                $q->whereNull('tanggal_selesai')
                  ->orWhere('tanggal_selesai', '>=', now());
            })
            ->with('jenisPotongan')
            ->get();
        
        // Cicilan Aktif
        $cicilanAktif = Cicilan::whereHas('tagihan', function($q) use ($mahasiswa) {
                $q->where('mahasiswa_id', $mahasiswa->id);
            })
            ->whereIn('status', ['Aktif'])
            ->with(['tagihan', 'detailCicilan' => function($q) {
                $q->orderBy('jatuh_tempo', 'asc');
            }])
            ->orderBy('tanggal_mulai', 'desc')
            ->get();
        
        // Promo Tersedia
        $promoTersedia = PeriodeDiskon::active()
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->where(function($q) {
                $q->whereNull('kuota')
                  ->orWhereRaw('kuota > kuota_terpakai');
            })
            ->with('jenisPotongan')
            ->get();
        
        // Pengumuman Keuangan
        $pengumuman = Pengumuman::aktif()->where('kategori', 'Keuangan')->latest()->take(5)->get();
        
        return view('dashboard.mahasiswa-keuangan', compact(
            'mahasiswa',
            'tahunAkademikAktif',
            'tagihanBelumLunas',
            'totalTagihan',
            'tagihanJatuhTempo',
            'transaksiTerbaru',
            'totalDibayarTahunIni',
            'totalDibayarSemua',
            'potonganAktif',
            'cicilanAktif',
            'promoTersedia',
            'pengumuman'
        ));
    }
}
