<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\TransaksiPembayaran;
use App\Models\Beasiswa;
use App\Models\PenerimaBeasiswa;
use App\Models\TahunAkademik;
use App\Models\ProgramStudi;
use App\Models\Mahasiswa;
use App\Exports\LaporanPendapatanExport;
use App\Exports\LaporanTunggakanExport;
use App\Exports\LaporanBeasiswaExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanKeuanganController extends Controller
{
    /**
     * Dashboard Keuangan
     */
    public function dashboard()
    {
        // Statistik Utama
        $stats = [
            'total_tagihan' => Tagihan::sum('nominal'),
            'total_terbayar' => Tagihan::sum('jumlah_dibayar'),
            'total_tunggakan' => Tagihan::sum('sisa_tagihan'),
            'total_transaksi_hari_ini' => TransaksiPembayaran::whereDate('tanggal_bayar', today())->where('status', 'Verified')->sum('jumlah'),
            'total_transaksi_bulan_ini' => TransaksiPembayaran::whereMonth('tanggal_bayar', now()->month)
                ->whereYear('tanggal_bayar', now()->year)
                ->where('status', 'Verified')->sum('jumlah'),
            'menunggu_verifikasi' => TransaksiPembayaran::where('status', 'Pending')->count(),
            'total_penerima_beasiswa' => PenerimaBeasiswa::where('status', 'Disetujui')->count(),
        ];

        // Pendapatan 12 bulan terakhir
        $pendapatanBulanan = TransaksiPembayaran::where('status', 'Verified')
            ->where('tanggal_bayar', '>=', now()->subMonths(11)->startOfMonth())
            ->select(
                DB::raw('YEAR(tanggal_bayar) as tahun'),
                DB::raw('MONTH(tanggal_bayar) as bulan'),
                DB::raw('SUM(jumlah) as total')
            )
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        // Format untuk chart dengan nama bulan Indonesia
        $namaBulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartLabels = [];
        $chartData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $chartLabels[] = $namaBulan[$date->month] . ' ' . $date->year;
            $found = $pendapatanBulanan->first(function ($item) use ($date) {
                return $item->tahun == $date->year && $item->bulan == $date->month;
            });
            $chartData[] = $found ? (int) $found->total : 0;
        }

        // Tunggakan per Program Studi
        $tunggakanPerProdi = Tagihan::join('mahasiswa', 'tagihan.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('program_studi', 'mahasiswa.program_studi_id', '=', 'program_studi.id')
            ->where('tagihan.sisa_tagihan', '>', 0)
            ->select(
                'program_studi.nama as prodi',
                DB::raw('COUNT(DISTINCT tagihan.mahasiswa_id) as jumlah_mahasiswa'),
                DB::raw('SUM(tagihan.sisa_tagihan) as total_tunggakan')
            )
            ->groupBy('program_studi.id', 'program_studi.nama')
            ->orderByDesc('total_tunggakan')
            ->limit(10)
            ->get();

        // Pembayaran per Metode
        $pembayaranPerMetode = TransaksiPembayaran::where('status', 'Verified')
            ->whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year)
            ->select('metode_pembayaran', DB::raw('SUM(jumlah) as total'), DB::raw('COUNT(*) as jumlah'))
            ->groupBy('metode_pembayaran')
            ->get();

        // Top 10 Mahasiswa Tunggakan
        $topTunggakan = Tagihan::with(['mahasiswa.programStudi'])
            ->where('sisa_tagihan', '>', 0)
            ->select('mahasiswa_id', DB::raw('SUM(sisa_tagihan) as total_tunggakan'))
            ->groupBy('mahasiswa_id')
            ->orderByDesc('total_tunggakan')
            ->limit(10)
            ->get();

        // Transaksi terbaru
        $transaksiTerbaru = TransaksiPembayaran::with(['mahasiswa', 'tagihan'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('keuangan.laporan.dashboard', compact(
            'stats',
            'chartLabels',
            'chartData',
            'tunggakanPerProdi',
            'pembayaranPerMetode',
            'topTunggakan',
            'transaksiTerbaru'
        ));
    }

    /**
     * Laporan Pendapatan
     */
    public function pendapatan(Request $request)
    {
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();
        $query = TransaksiPembayaran::with(['mahasiswa.programStudi', 'tagihan'])
            ->where('status', 'Verified');

        // Filters
        if ($request->tahun_akademik_id) {
            $query->whereHas('tagihan', function ($q) use ($request) {
                $q->where('tahun_akademik_id', $request->tahun_akademik_id);
            });
        }

        if ($request->tanggal_mulai) {
            $query->whereDate('tanggal_bayar', '>=', $request->tanggal_mulai);
        }

        if ($request->tanggal_selesai) {
            $query->whereDate('tanggal_bayar', '<=', $request->tanggal_selesai);
        }

        if ($request->metode_pembayaran) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }

        $transaksi = $query->orderBy('tanggal_bayar', 'desc')->paginate(20);

        // Summary
        $summaryQuery = clone $query;
        $summary = [
            'total_transaksi' => $summaryQuery->count(),
            'total_pendapatan' => $summaryQuery->sum('jumlah'),
        ];

        return view('keuangan.laporan.pendapatan', compact('transaksi', 'tahunAkademik', 'summary'));
    }

    /**
     * Export Pendapatan Excel
     */
    public function exportPendapatanExcel(Request $request)
    {
        return Excel::download(new LaporanPendapatanExport($request), 'laporan-pendapatan-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Export Pendapatan PDF
     */
    public function exportPendapatanPdf(Request $request)
    {
        $query = TransaksiPembayaran::with(['mahasiswa.programStudi', 'tagihan.tahunAkademik'])
            ->where('status', 'Verified');

        if ($request->tahun_akademik_id) {
            $query->whereHas('tagihan', fn($q) => $q->where('tahun_akademik_id', $request->tahun_akademik_id));
        }
        if ($request->tanggal_mulai) {
            $query->whereDate('tanggal_bayar', '>=', $request->tanggal_mulai);
        }
        if ($request->tanggal_selesai) {
            $query->whereDate('tanggal_bayar', '<=', $request->tanggal_selesai);
        }

        $transaksi = $query->orderBy('tanggal_bayar', 'desc')->get();
        $total = $transaksi->sum('jumlah');

        $pdf = Pdf::loadView('keuangan.laporan.pdf.pendapatan', compact('transaksi', 'total', 'request'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('laporan-pendapatan-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Laporan Tunggakan
     */
    public function tunggakan(Request $request)
    {
        $programStudi = ProgramStudi::orderBy('nama')->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();

        $query = Tagihan::with(['mahasiswa.programStudi', 'tahunAkademik', 'tarif'])
            ->where('sisa_tagihan', '>', 0);

        if ($request->program_studi_id) {
            $query->whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $request->program_studi_id));
        }

        if ($request->tahun_akademik_id) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }

        if ($request->angkatan) {
            $query->whereHas('mahasiswa', fn($q) => $q->where('angkatan', $request->angkatan));
        }

        $tagihan = $query->orderBy('sisa_tagihan', 'DESC')->paginate(20);

        // Summary
        $summaryQuery = Tagihan::where('sisa_tagihan', '>', 0);
        if ($request->program_studi_id) {
            $summaryQuery->whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $request->program_studi_id));
        }
        if ($request->tahun_akademik_id) {
            $summaryQuery->where('tahun_akademik_id', $request->tahun_akademik_id);
        }
        if ($request->angkatan) {
            $summaryQuery->whereHas('mahasiswa', fn($q) => $q->where('angkatan', $request->angkatan));
        }
        
        $summary = [
            'total_mahasiswa' => $summaryQuery->distinct('mahasiswa_id')->count('mahasiswa_id'),
            'total_tunggakan' => $summaryQuery->sum('sisa_tagihan'),
        ];

        // Angkatan list
        $angkatanList = Mahasiswa::distinct()->orderBy('angkatan', 'desc')->pluck('angkatan');

        return view('keuangan.laporan.tunggakan', compact('tagihan', 'programStudi', 'tahunAkademik', 'angkatanList', 'summary'));
    }

    /**
     * Export Tunggakan Excel
     */
    public function exportTunggakanExcel(Request $request)
    {
        return Excel::download(new LaporanTunggakanExport($request), 'laporan-tunggakan-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Export Tunggakan PDF
     */
    public function exportTunggakanPdf(Request $request)
    {
        $query = Tagihan::with(['mahasiswa.programStudi', 'tahunAkademik'])
            ->where('sisa_tagihan', '>', 0);

        if ($request->program_studi_id) {
            $query->whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $request->program_studi_id));
        }
        if ($request->tahun_akademik_id) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }

        $tagihan = $query->orderBy('sisa_tagihan', 'DESC')->get();
        $total = $tagihan->sum('sisa_tagihan');

        $pdf = Pdf::loadView('keuangan.laporan.pdf.tunggakan', compact('tagihan', 'total', 'request'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('laporan-tunggakan-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Laporan Beasiswa
     */
    public function beasiswa(Request $request)
    {
        $beasiswaList = Beasiswa::orderBy('nama')->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();

        $query = PenerimaBeasiswa::with(['beasiswa', 'mahasiswa.programStudi', 'tahunAkademik']);

        if ($request->beasiswa_id) {
            $query->where('beasiswa_id', $request->beasiswa_id);
        }

        if ($request->tahun_akademik_id) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $penerima = $query->orderBy('created_at', 'desc')->paginate(20);

        // Summary
        $summaryQuery = clone $query;
        $summary = [
            'total_penerima' => $summaryQuery->count(),
            'total_disetujui' => (clone $summaryQuery)->where('status', 'Disetujui')->count(),
            'total_diajukan' => (clone $summaryQuery)->where('status', 'Diajukan')->count(),
        ];

        // Total nilai beasiswa (estimasi)
        $totalNilai = PenerimaBeasiswa::with('beasiswa')
            ->where('status', 'Disetujui')
            ->get()
            ->sum(function ($p) {
                return $p->beasiswa->tipe_potongan === 'Nominal' ? $p->beasiswa->nilai_potongan : 0;
            });
        $summary['total_nilai'] = $totalNilai;

        return view('keuangan.laporan.beasiswa', compact('penerima', 'beasiswaList', 'tahunAkademik', 'summary'));
    }

    /**
     * Export Beasiswa Excel
     */
    public function exportBeasiswaExcel(Request $request)
    {
        return Excel::download(new LaporanBeasiswaExport($request), 'laporan-beasiswa-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Export Beasiswa PDF
     */
    public function exportBeasiswaPdf(Request $request)
    {
        $query = PenerimaBeasiswa::with(['beasiswa', 'mahasiswa.programStudi', 'tahunAkademik']);

        if ($request->beasiswa_id) {
            $query->where('beasiswa_id', $request->beasiswa_id);
        }
        if ($request->tahun_akademik_id) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $penerima = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('keuangan.laporan.pdf.beasiswa', compact('penerima', 'request'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('laporan-beasiswa-' . now()->format('Y-m-d') . '.pdf');
    }
}
