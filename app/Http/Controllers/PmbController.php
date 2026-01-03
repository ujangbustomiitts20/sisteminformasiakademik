<?php

namespace App\Http\Controllers;

use App\Models\PeriodePmb;
use App\Models\GelombangPmb;
use App\Models\JalurSeleksi;
use App\Models\CalonMahasiswa;
use App\Models\HasilSeleksi;
use App\Models\DaftarUlang;
use App\Models\PembayaranPmb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PmbController extends Controller
{
    /**
     * Dashboard PMB untuk Admin
     */
    public function dashboard()
    {
        $periodeAktif = PeriodePmb::active()->first();
        $gelombangAktif = GelombangPmb::active()->first();

        // Statistik Calon Mahasiswa
        $stats = [
            'total_pendaftar' => CalonMahasiswa::when($gelombangAktif, function($q) use ($gelombangAktif) {
                return $q->where('gelombang_pmb_id', $gelombangAktif->id);
            })->count(),
            'pendaftar_terdaftar' => CalonMahasiswa::when($gelombangAktif, function($q) use ($gelombangAktif) {
                return $q->where('gelombang_pmb_id', $gelombangAktif->id);
            })->where('status_pendaftaran', 'terdaftar')->count(),
            'menunggu_bayar' => CalonMahasiswa::when($gelombangAktif, function($q) use ($gelombangAktif) {
                return $q->where('gelombang_pmb_id', $gelombangAktif->id);
            })->whereIn('status_pendaftaran', ['menunggu_bayar', 'pending'])->count(),
            'lulus' => HasilSeleksi::when($gelombangAktif, function($q) use ($gelombangAktif) {
                return $q->where('gelombang_pmb_id', $gelombangAktif->id);
            })->where('status', 'lulus')->count(),
            'tidak_lulus' => HasilSeleksi::when($gelombangAktif, function($q) use ($gelombangAktif) {
                return $q->where('gelombang_pmb_id', $gelombangAktif->id);
            })->where('status', 'tidak_lulus')->count(),
            'daftar_ulang' => CalonMahasiswa::when($gelombangAktif, function($q) use ($gelombangAktif) {
                return $q->where('gelombang_pmb_id', $gelombangAktif->id);
            })->where('status_pendaftaran', 'daftar_ulang')->count(),
            'menjadi_mahasiswa' => CalonMahasiswa::when($gelombangAktif, function($q) use ($gelombangAktif) {
                return $q->where('gelombang_pmb_id', $gelombangAktif->id);
            })->where('status_pendaftaran', 'menjadi_mahasiswa')->count(),
        ];

        // Statistik Daftar Ulang
        $statsDaftarUlang = [
            'total' => DaftarUlang::count(),
            'pending' => DaftarUlang::where('status', 'pending')->count(),
            'lunas' => DaftarUlang::where('status', 'lunas')->count(),
            'selesai' => DaftarUlang::where('status', 'selesai')->count(),
        ];

        // Statistik Pembayaran
        $statsPembayaran = [
            'total_pendaftaran' => PembayaranPmb::where('jenis_pembayaran', 'pendaftaran')->count(),
            'terverifikasi_pendaftaran' => PembayaranPmb::where('jenis_pembayaran', 'pendaftaran')
                ->where('status', 'terverifikasi')->count(),
            'total_daftar_ulang' => PembayaranPmb::where('jenis_pembayaran', 'daftar_ulang')->count(),
            'terverifikasi_daftar_ulang' => PembayaranPmb::where('jenis_pembayaran', 'daftar_ulang')
                ->where('status', 'terverifikasi')->count(),
            'total_pendapatan' => PembayaranPmb::where('status', 'terverifikasi')->sum('jumlah'),
        ];

        // Pendaftar per prodi
        $pendaftarPerProdi = CalonMahasiswa::select('program_studi_id', DB::raw('count(*) as total'))
            ->when($gelombangAktif, function($q) use ($gelombangAktif) {
                return $q->where('gelombang_pmb_id', $gelombangAktif->id);
            })
            ->groupBy('program_studi_id')
            ->with('programStudi')
            ->get();

        // Pendaftar per jalur
        $pendaftarPerJalur = CalonMahasiswa::select('jalur_seleksi_id', DB::raw('count(*) as total'))
            ->when($gelombangAktif, function($q) use ($gelombangAktif) {
                return $q->where('gelombang_pmb_id', $gelombangAktif->id);
            })
            ->groupBy('jalur_seleksi_id')
            ->with('jalurSeleksi')
            ->get();

        // Pendaftar 7 hari terakhir
        $pendaftarHarian = CalonMahasiswa::select(DB::raw('DATE(created_at) as tanggal'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('tanggal')
            ->get();

        return view('pmb.dashboard', compact(
            'periodeAktif',
            'gelombangAktif',
            'stats',
            'statsDaftarUlang',
            'statsPembayaran',
            'pendaftarPerProdi',
            'pendaftarPerJalur',
            'pendaftarHarian'
        ));
    }
}
