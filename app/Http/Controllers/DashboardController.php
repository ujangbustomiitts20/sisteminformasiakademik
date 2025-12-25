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
            $dosen = $user->dosen;
            $data['dosen'] = $dosen;
            
            if ($dosen && $data['tahunAkademikAktif']) {
                $data['jadwalMengajar'] = $dosen->jadwalKuliah()
                    ->where('tahun_akademik_id', $data['tahunAkademikAktif']->id)
                    ->with(['mataKuliah', 'ruangan'])
                    ->get();
                
                $data['mahasiswaWali'] = $dosen->mahasiswaWali()->where('status', 'Aktif')->count();
            }

            return view('dashboard.dosen', $data);
        }

        if ($user->isMahasiswa()) {
            $mahasiswa = $user->mahasiswa;
            $data['mahasiswa'] = $mahasiswa;
            
            if ($mahasiswa && $data['tahunAkademikAktif']) {
                // Akademik
                $data['krsSemesterIni'] = Krs::where('mahasiswa_id', $mahasiswa->id)
                    ->where('tahun_akademik_id', $data['tahunAkademikAktif']->id)
                    ->where('status', 'Disetujui')
                    ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'jadwalKuliah.ruangan'])
                    ->get();
                
                $data['ipk'] = $mahasiswa->hitungIPK();
                $data['totalSks'] = $mahasiswa->totalSksLulus();
                
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
