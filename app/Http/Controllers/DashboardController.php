<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\TahunAkademik;
use App\Models\Pengumuman;
use App\Models\Krs;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\TransaksiPembayaran;
use App\Models\PotonganMahasiswa;
use App\Models\Cicilan;
use App\Models\PeriodeDiskon;
use App\Models\KalenderAkademik;
use App\Models\Absensi;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $data = [];

        // Data untuk semua role
        $data['pengumuman'] = Pengumuman::aktif()->latest()->take(5)->get();
        $data['tahunAkademikAktif'] = TahunAkademik::getAktif();

        if ($user->isAdmin()) {
            $data['totalMahasiswa'] = Mahasiswa::where('status', 'Aktif')->count();
            $data['totalDosen'] = Dosen::where('status', 'Aktif')->count();
            $data['totalMataKuliah'] = MataKuliah::count();
            $data['totalPembayaranBulanIni'] = Pembayaran::where('status', 'Lunas')
                ->whereMonth('tanggal_bayar', now()->month)
                ->whereYear('tanggal_bayar', now()->year)
                ->sum('jumlah');
            
            // Statistik mahasiswa per fakultas
            $data['mahasiswaPerProdi'] = Mahasiswa::where('status', 'Aktif')
                ->selectRaw('program_studi_id, count(*) as total')
                ->groupBy('program_studi_id')
                ->with('programStudi')
                ->get();

            return view('dashboard.admin', $data);
        }

        if ($user->isDosen()) {
            // Redirect ke unified dashboard dosen
            return redirect()->route('dosen.dashboard');
        }

        if ($user->isMahasiswa()) {
            $mahasiswa = $user->mahasiswa;
            $data['mahasiswa'] = $mahasiswa;
            
            if ($mahasiswa && $data['tahunAkademikAktif']) {
                // Load relasi mahasiswa
                $mahasiswa->load(['programStudi.fakultas', 'dosenWali']);
                
                // Akademik
                $data['krsSemesterIni'] = Krs::where('mahasiswa_id', $mahasiswa->id)
                    ->where('tahun_akademik_id', $data['tahunAkademikAktif']->id)
                    ->where('status', 'Disetujui')
                    ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'jadwalKuliah.ruangan'])
                    ->get();
                
                $data['ipk'] = $mahasiswa->hitungIPK();
                $data['totalSks'] = $mahasiswa->totalSksLulus();
                
                // SKS semester ini
                $data['sksSemesterIni'] = $data['krsSemesterIni']->sum(fn($krs) => $krs->jadwalKuliah?->mataKuliah?->sks ?? 0);
                
                // Target SKS (asumsi 144 SKS untuk S1)
                $data['targetSks'] = $mahasiswa->programStudi?->total_sks ?? 144;
                $data['progressSks'] = $data['targetSks'] > 0 ? round(($data['totalSks'] / $data['targetSks']) * 100) : 0;
                
                // Jadwal Hari Ini
                $hariIni = now()->locale('id')->isoFormat('dddd');
                $data['jadwalHariIni'] = $data['krsSemesterIni']
                    ->filter(fn($krs) => $krs->jadwalKuliah && $krs->jadwalKuliah->hari === $hariIni)
                    ->sortBy(fn($krs) => $krs->jadwalKuliah->jam_mulai);
                
                // Kalender Akademik - Event mendatang 14 hari
                $data['eventMendatang'] = KalenderAkademik::where('tanggal_mulai', '>=', now())
                    ->where('tanggal_mulai', '<=', now()->addDays(14))
                    ->orderBy('tanggal_mulai', 'asc')
                    ->take(5)
                    ->get();
                
                // Rekap Kehadiran Semester Ini
                $krsIds = $data['krsSemesterIni']->pluck('id')->filter();
                $data['rekapKehadiran'] = [
                    'hadir' => 0,
                    'izin' => 0,
                    'sakit' => 0,
                    'alpha' => 0,
                    'total' => 0,
                ];
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
                } else {
                    $data['persentaseKehadiran'] = 100;
                }
                
                // Pembayaran lama (untuk backward compatibility)
                $data['pembayaranTerakhir'] = Pembayaran::where('mahasiswa_id', $mahasiswa->id)
                    ->latest()
                    ->first();
                
                // ========== KEUANGAN BARU ==========
                // Tagihan
                $data['tagihanBelumLunas'] = Tagihan::where('mahasiswa_id', $mahasiswa->id)
                    ->belumLunas()
                    ->with('tahunAkademik')
                    ->orderBy('tanggal_jatuh_tempo', 'asc')
                    ->get();
                
                $data['totalTagihan'] = $data['tagihanBelumLunas']->sum('sisa_tagihan');
                $data['tagihanJatuhTempo'] = $data['tagihanBelumLunas']->filter(fn($t) => $t->tanggal_jatuh_tempo && \Carbon\Carbon::parse($t->tanggal_jatuh_tempo)->isPast())->count();
                
                // Transaksi Pembayaran Terbaru
                $data['transaksiTerbaru'] = TransaksiPembayaran::where('mahasiswa_id', $mahasiswa->id)
                    ->with('tagihan')
                    ->orderBy('tanggal_bayar', 'desc')
                    ->take(5)
                    ->get();
                
                $data['totalDibayarTahunIni'] = TransaksiPembayaran::where('mahasiswa_id', $mahasiswa->id)
                    ->whereYear('tanggal_bayar', now()->year)
                    ->sum('jumlah');
                
                // Potongan/Diskon Aktif
                $data['potonganAktif'] = PotonganMahasiswa::where('mahasiswa_id', $mahasiswa->id)
                    ->where('status', 'disetujui')
                    ->where(function($q) {
                        $q->whereNull('tanggal_selesai')
                          ->orWhere('tanggal_selesai', '>=', now());
                    })
                    ->with('jenisPotongan')
                    ->get();
                
                $data['totalPotongan'] = $data['potonganAktif']->sum('nilai');
                
                // Cicilan Aktif
                $data['cicilanAktif'] = Cicilan::whereHas('tagihan', function($q) use ($mahasiswa) {
                        $q->where('mahasiswa_id', $mahasiswa->id);
                    })
                    ->whereIn('status', ['Aktif'])
                    ->with(['tagihan', 'detailCicilan' => function($q) {
                        $q->where('status', 'Belum Bayar')->orderBy('jatuh_tempo', 'asc');
                    }])
                    ->orderBy('tanggal_mulai', 'desc')
                    ->take(3)
                    ->get();
                
                // Promo/Diskon yang Tersedia
                $data['promoTersedia'] = PeriodeDiskon::active()
                    ->where('tanggal_mulai', '<=', now())
                    ->where('tanggal_selesai', '>=', now())
                    ->where(function($q) {
                        $q->whereNull('kuota')
                          ->orWhereRaw('kuota > kuota_terpakai');
                    })
                    ->with('jenisPotongan')
                    ->take(3)
                    ->get();
            }

            return view('dashboard.mahasiswa', $data);
        }

        return view('dashboard.index', $data);
    }
}
