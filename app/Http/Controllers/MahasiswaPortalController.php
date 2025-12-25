<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\PotonganMahasiswa;
use App\Models\RiwayatPotongan;
use App\Models\PeriodeDiskon;
use App\Models\TransaksiPembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class MahasiswaPortalController extends Controller
{
    /**
     * Halaman Profil Mahasiswa
     */
    public function profil()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }
        
        $mahasiswa->load(['programStudi.fakultas', 'dosenWali', 'user']);
        
        // Statistik
        $totalSks = $mahasiswa->totalSksLulus();
        $ipk = $mahasiswa->hitungIPK();
        $totalTagihan = Tagihan::where('mahasiswa_id', $mahasiswa->id)->sum('total_bayar');
        $totalDibayar = TransaksiPembayaran::where('mahasiswa_id', $mahasiswa->id)->sum('jumlah');
        
        return view('mahasiswa.profil', compact('mahasiswa', 'totalSks', 'ipk', 'totalTagihan', 'totalDibayar'));
    }
    
    /**
     * Halaman Potongan & Diskon Mahasiswa
     */
    public function potongan()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }
        
        // Potongan Aktif (disetujui dan masih berlaku)
        $potonganAktif = PotonganMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'disetujui')
            ->where(function($q) {
                $q->whereNull('tanggal_selesai')
                  ->orWhere('tanggal_selesai', '>=', now());
            })
            ->with('jenisPotongan')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Potongan Pending
        $potonganPending = PotonganMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'pending')
            ->with('jenisPotongan')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Riwayat Penggunaan Potongan
        $riwayatPotongan = RiwayatPotongan::where('mahasiswa_id', $mahasiswa->id)
            ->with(['tagihan', 'potonganMahasiswa.jenisPotongan'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Total Potongan yang sudah digunakan
        $totalPotonganDigunakan = RiwayatPotongan::where('mahasiswa_id', $mahasiswa->id)
            ->sum('nominal_potongan');
        
        // Promo yang tersedia
        $promoTersedia = PeriodeDiskon::active()
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->where(function($q) {
                $q->whereNull('kuota')
                  ->orWhereRaw('kuota > kuota_terpakai');
            })
            ->with('jenisPotongan')
            ->get();
        
        return view('mahasiswa.potongan', compact(
            'potonganAktif',
            'potonganPending',
            'riwayatPotongan',
            'totalPotonganDigunakan',
            'promoTersedia'
        ));
    }
    
    /**
     * Download Kartu Tagihan
     */
    public function downloadKartuTagihan()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }
        
        $mahasiswa->load(['programStudi.fakultas']);
        
        $tagihan = Tagihan::where('mahasiswa_id', $mahasiswa->id)
            ->with('tahunAkademik')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $pdf = Pdf::loadView('mahasiswa.pdf.kartu-tagihan', compact('mahasiswa', 'tagihan'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('kartu-tagihan-' . $mahasiswa->nim . '.pdf');
    }
    
    /**
     * Download Riwayat Pembayaran
     */
    public function downloadRiwayatPembayaran()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }
        
        $mahasiswa->load(['programStudi.fakultas']);
        
        $transaksi = TransaksiPembayaran::where('mahasiswa_id', $mahasiswa->id)
            ->with(['tagihan.tahunAkademik'])
            ->orderBy('tanggal_bayar', 'desc')
            ->get();
        
        $totalDibayar = $transaksi->sum('jumlah');
        
        $pdf = Pdf::loadView('mahasiswa.pdf.riwayat-pembayaran', compact('mahasiswa', 'transaksi', 'totalDibayar'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('riwayat-pembayaran-' . $mahasiswa->nim . '.pdf');
    }
    
    /**
     * Download Kartu Mahasiswa Digital
     */
    public function downloadKartuMahasiswa()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }
        
        $mahasiswa->load(['programStudi.fakultas']);
        
        $pdf = Pdf::loadView('mahasiswa.pdf.kartu-mahasiswa', compact('mahasiswa'));
        $pdf->setPaper([0, 0, 243, 153], 'landscape'); // ID Card size (85.6mm x 53.98mm)
        
        return $pdf->download('kartu-mahasiswa-' . $mahasiswa->nim . '.pdf');
    }
}
