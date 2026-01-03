<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\TransaksiPembayaran;
use App\Models\Beasiswa;
use App\Models\PenerimaBeasiswa;
use App\Models\Cicilan;
use App\Models\DetailCicilan;
use App\Models\PembayaranPmb;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class KeuanganDashboardController extends Controller
{
    // Cache duration in seconds (5 menit)
    private const CACHE_DURATION = 300;

    public function index(Request $request)
    {
        // Cek tahun akademik aktif
        $tahunAkademikAktif = TahunAkademik::where('is_aktif', true)->first();
        
        // Decode hashid jika ada
        $tahunAkademikId = null;
        if ($request->has('tahun_akademik_id') && $request->get('tahun_akademik_id')) {
            $tahunAkademikId = TahunAkademik::decodeHashid($request->get('tahun_akademik_id'));
        } else {
            $tahunAkademikId = $tahunAkademikAktif?->id;
        }
        
        // Cache key berdasarkan tahun akademik
        $cacheKey = 'keuangan_dashboard_' . ($tahunAkademikId ?? 'all');

        // Ambil data dari cache atau generate baru
        $dashboardData = Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($tahunAkademikId) {
            return $this->generateDashboardData($tahunAkademikId);
        });

        // Data yang tidak di-cache (realtime)
        $transaksiTerbaru = $this->getTransaksiTerbaru();
        $tagihanJatuhTempo = $this->getTagihanJatuhTempo();
        $tahunAkademiks = TahunAkademik::orderBy('tahun', 'desc')->get();

        return view('keuangan.dashboard', array_merge($dashboardData, [
            'transaksiTerbaru' => $transaksiTerbaru,
            'tagihanJatuhTempo' => $tagihanJatuhTempo,
            'tahunAkademiks' => $tahunAkademiks,
            'tahunAkademikId' => $tahunAkademikId,
            'tahunAkademikAktif' => TahunAkademik::where('is_aktif', true)->first(),
        ]));
    }

    /**
     * Generate all dashboard data - optimized with single queries where possible
     */
    private function generateDashboardData($tahunAkademikId)
    {
        // === STATISTIK TAGIHAN (Single optimized query) ===
        $tagihanStats = $this->getTagihanStatsOptimized($tahunAkademikId);

        // === STATISTIK TRANSAKSI (Single optimized query) ===
        $transaksiStats = $this->getTransaksiStatsOptimized();

        // === STATISTIK BEASISWA ===
        $beasiswaStats = $this->getBeasiswaStats();

        // === STATISTIK PMB ===
        $pmbStats = $this->getPmbStatsOptimized();

        // === STATISTIK CICILAN ===
        $cicilanStats = $this->getCicilanStats();

        // === GRAFIK: Pendapatan per Bulan (Optimized single query) ===
        $pendapatanBulanan = $this->getPendapatanBulananOptimized();

        // === GRAFIK: Pendapatan per Hari (Optimized single query) ===
        $pendapatanHarian = $this->getPendapatanHarianOptimized();

        // === GRAFIK: Status Tagihan ===
        $statusTagihan = [
            ['status' => 'Belum Bayar', 'jumlah' => $tagihanStats['belum_bayar'], 'color' => '#dc3545'],
            ['status' => 'Cicilan', 'jumlah' => $tagihanStats['cicilan'], 'color' => '#ffc107'],
            ['status' => 'Lunas', 'jumlah' => $tagihanStats['lunas'], 'color' => '#28a745'],
        ];

        // === GRAFIK: Metode Pembayaran ===
        $metodePembayaran = $this->getMetodePembayaran();

        // === GRAFIK: Tagihan per Jenis ===
        $tagihanPerJenis = $this->getTagihanPerJenis($tahunAkademikId);

        // === GRAFIK: Tunggakan per Prodi ===
        $tunggakanPerProdi = $this->getTunggakanPerProdi($tahunAkademikId);

        return compact(
            'tagihanStats',
            'transaksiStats',
            'beasiswaStats',
            'pmbStats',
            'cicilanStats',
            'pendapatanBulanan',
            'pendapatanHarian',
            'statusTagihan',
            'metodePembayaran',
            'tagihanPerJenis',
            'tunggakanPerProdi'
        );
    }

    /**
     * Optimized: Get all tagihan stats in single query
     */
    private function getTagihanStatsOptimized($tahunAkademikId)
    {
        try {
            $stats = DB::table('tagihan')
                ->when($tahunAkademikId, fn($q) => $q->where('tahun_akademik_id', $tahunAkademikId))
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "Belum Bayar" THEN 1 ELSE 0 END) as belum_bayar,
                    SUM(CASE WHEN status = "Cicilan" THEN 1 ELSE 0 END) as cicilan,
                    SUM(CASE WHEN status = "Lunas" THEN 1 ELSE 0 END) as lunas,
                    COALESCE(SUM(total_bayar), 0) as total_nominal,
                    COALESCE(SUM(jumlah_dibayar), 0) as total_terbayar,
                    COALESCE(SUM(CASE WHEN status != "Lunas" THEN sisa_tagihan ELSE 0 END), 0) as total_tunggakan,
                    SUM(CASE WHEN status != "Lunas" AND tanggal_jatuh_tempo IS NOT NULL AND tanggal_jatuh_tempo < CURDATE() THEN 1 ELSE 0 END) as jatuh_tempo
                ')
                ->first();

            return [
                'total' => (int) ($stats->total ?? 0),
                'belum_bayar' => (int) ($stats->belum_bayar ?? 0),
                'cicilan' => (int) ($stats->cicilan ?? 0),
                'lunas' => (int) ($stats->lunas ?? 0),
                'total_nominal' => (float) ($stats->total_nominal ?? 0),
                'total_terbayar' => (float) ($stats->total_terbayar ?? 0),
                'total_tunggakan' => (float) ($stats->total_tunggakan ?? 0),
                'jatuh_tempo' => (int) ($stats->jatuh_tempo ?? 0),
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0, 'belum_bayar' => 0, 'cicilan' => 0, 'lunas' => 0,
                'total_nominal' => 0, 'total_terbayar' => 0, 'total_tunggakan' => 0, 'jatuh_tempo' => 0
            ];
        }
    }

    /**
     * Optimized: Get all transaksi stats in single query
     */
    private function getTransaksiStatsOptimized()
    {
        try {
            $now = now();
            $stats = DB::table('transaksi_pembayaran')
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "Pending" THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = "Verified" THEN 1 ELSE 0 END) as verified,
                    SUM(CASE WHEN status = "Rejected" THEN 1 ELSE 0 END) as rejected,
                    COALESCE(SUM(CASE WHEN status = "Verified" AND MONTH(tanggal_bayar) = ? AND YEAR(tanggal_bayar) = ? THEN jumlah ELSE 0 END), 0) as total_bulan_ini,
                    COALESCE(SUM(CASE WHEN status = "Verified" AND DATE(tanggal_bayar) = CURDATE() THEN jumlah ELSE 0 END), 0) as total_hari_ini
                ', [$now->month, $now->year])
                ->first();

            return [
                'total' => (int) ($stats->total ?? 0),
                'pending' => (int) ($stats->pending ?? 0),
                'verified' => (int) ($stats->verified ?? 0),
                'rejected' => (int) ($stats->rejected ?? 0),
                'total_bulan_ini' => (float) ($stats->total_bulan_ini ?? 0),
                'total_hari_ini' => (float) ($stats->total_hari_ini ?? 0),
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0, 'pending' => 0, 'verified' => 0, 'rejected' => 0,
                'total_bulan_ini' => 0, 'total_hari_ini' => 0
            ];
        }
    }

    private function getBeasiswaStats()
    {
        try {
            $stats = DB::table('beasiswa')
                ->selectRaw('COUNT(CASE WHEN is_active = 1 THEN 1 END) as total_beasiswa')
                ->first();

            $penerimaStats = DB::table('penerima_beasiswa')
                ->selectRaw('
                    SUM(CASE WHEN status = "Disetujui" THEN 1 ELSE 0 END) as total_penerima,
                    SUM(CASE WHEN status = "Diajukan" THEN 1 ELSE 0 END) as pengajuan_pending
                ')
                ->first();

            $totalNilaiBeasiswa = DB::table('penerima_beasiswa')
                ->join('beasiswa', 'penerima_beasiswa.beasiswa_id', '=', 'beasiswa.id')
                ->where('penerima_beasiswa.status', 'Disetujui')
                ->where('beasiswa.tipe_potongan', 'Nominal')
                ->sum('beasiswa.nilai_potongan') ?? 0;

            return [
                'total_beasiswa' => (int) ($stats->total_beasiswa ?? 0),
                'total_penerima' => (int) ($penerimaStats->total_penerima ?? 0),
                'pengajuan_pending' => (int) ($penerimaStats->pengajuan_pending ?? 0),
                'total_nilai_beasiswa' => (float) $totalNilaiBeasiswa,
            ];
        } catch (\Exception $e) {
            return [
                'total_beasiswa' => 0, 'total_penerima' => 0,
                'pengajuan_pending' => 0, 'total_nilai_beasiswa' => 0
            ];
        }
    }

    /**
     * Optimized: Get PMB stats in single query
     */
    private function getPmbStatsOptimized()
    {
        try {
            $stats = DB::table('pembayaran_pmb')
                ->selectRaw('
                    SUM(CASE WHEN jenis_pembayaran = "pendaftaran" THEN 1 ELSE 0 END) as total_pendaftaran,
                    SUM(CASE WHEN jenis_pembayaran = "daftar_ulang" THEN 1 ELSE 0 END) as total_daftar_ulang,
                    COALESCE(SUM(CASE WHEN jenis_pembayaran = "pendaftaran" AND status = "terverifikasi" THEN jumlah ELSE 0 END), 0) as pendapatan_pendaftaran,
                    COALESCE(SUM(CASE WHEN jenis_pembayaran = "daftar_ulang" AND status = "terverifikasi" THEN jumlah ELSE 0 END), 0) as pendapatan_daftar_ulang
                ')
                ->first();

            return [
                'total_pendaftaran' => (int) ($stats->total_pendaftaran ?? 0),
                'total_daftar_ulang' => (int) ($stats->total_daftar_ulang ?? 0),
                'pendapatan_pendaftaran' => (float) ($stats->pendapatan_pendaftaran ?? 0),
                'pendapatan_daftar_ulang' => (float) ($stats->pendapatan_daftar_ulang ?? 0),
            ];
        } catch (\Exception $e) {
            return [
                'total_pendaftaran' => 0, 'total_daftar_ulang' => 0,
                'pendapatan_pendaftaran' => 0, 'pendapatan_daftar_ulang' => 0
            ];
        }
    }

    private function getCicilanStats()
    {
        try {
            $totalCicilanAktif = DB::table('cicilan')->where('status', 'Aktif')->count();
            
            $totalAngsuranTertunggak = 0;
            if (Schema::hasTable('detail_cicilan')) {
                $totalAngsuranTertunggak = DB::table('detail_cicilan')
                    ->where('status', 'Belum Bayar')
                    ->whereNotNull('jatuh_tempo')
                    ->whereDate('jatuh_tempo', '<', now())
                    ->count();
            }

            return [
                'total_cicilan_aktif' => $totalCicilanAktif,
                'total_angsuran_tertunggak' => $totalAngsuranTertunggak,
            ];
        } catch (\Exception $e) {
            return ['total_cicilan_aktif' => 0, 'total_angsuran_tertunggak' => 0];
        }
    }

    /**
     * Optimized: Get monthly revenue with single query using GROUP BY
     */
    private function getPendapatanBulananOptimized()
    {
        try {
            $startDate = now()->subMonths(11)->startOfMonth();
            
            $data = DB::table('transaksi_pembayaran')
                ->where('status', 'Verified')
                ->whereNotNull('tanggal_bayar')
                ->where('tanggal_bayar', '>=', $startDate)
                ->selectRaw('YEAR(tanggal_bayar) as tahun, MONTH(tanggal_bayar) as bulan, COALESCE(SUM(jumlah), 0) as pendapatan')
                ->groupByRaw('YEAR(tanggal_bayar), MONTH(tanggal_bayar)')
                ->get()
                ->keyBy(fn($item) => $item->tahun . '-' . str_pad($item->bulan, 2, '0', STR_PAD_LEFT));

            $result = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $key = $date->format('Y-m');
                $result[] = [
                    'bulan' => $date->translatedFormat('M Y'),
                    'pendapatan' => (float) ($data[$key]->pendapatan ?? 0),
                ];
            }

            return $result;
        } catch (\Exception $e) {
            return array_map(fn($i) => [
                'bulan' => now()->subMonths($i)->translatedFormat('M Y'),
                'pendapatan' => 0
            ], range(11, 0));
        }
    }

    /**
     * Optimized: Get daily revenue with single query using GROUP BY
     */
    private function getPendapatanHarianOptimized()
    {
        try {
            $startDate = now()->subDays(29)->startOfDay();
            
            $data = DB::table('transaksi_pembayaran')
                ->where('status', 'Verified')
                ->whereNotNull('tanggal_bayar')
                ->where('tanggal_bayar', '>=', $startDate)
                ->selectRaw('DATE(tanggal_bayar) as tanggal, COALESCE(SUM(jumlah), 0) as pendapatan')
                ->groupByRaw('DATE(tanggal_bayar)')
                ->get()
                ->keyBy('tanggal');

            $result = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $key = $date->format('Y-m-d');
                $result[] = [
                    'tanggal' => $date->format('d/m'),
                    'pendapatan' => (float) ($data[$key]->pendapatan ?? 0),
                ];
            }

            return $result;
        } catch (\Exception $e) {
            return array_map(fn($i) => [
                'tanggal' => now()->subDays($i)->format('d/m'),
                'pendapatan' => 0
            ], range(29, 0));
        }
    }

    private function getMetodePembayaran()
    {
        try {
            return DB::table('transaksi_pembayaran')
                ->where('status', 'Verified')
                ->whereNotNull('metode_pembayaran')
                ->selectRaw('metode_pembayaran, COUNT(*) as jumlah, COALESCE(SUM(jumlah), 0) as total')
                ->groupBy('metode_pembayaran')
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    private function getTagihanPerJenis($tahunAkademikId)
    {
        try {
            return DB::table('tagihan')
                ->when($tahunAkademikId, fn($q) => $q->where('tahun_akademik_id', $tahunAkademikId))
                ->whereNotNull('jenis_tagihan')
                ->selectRaw('jenis_tagihan, COUNT(*) as jumlah, COALESCE(SUM(total_bayar), 0) as total')
                ->groupBy('jenis_tagihan')
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    private function getTunggakanPerProdi($tahunAkademikId)
    {
        try {
            return DB::table('tagihan')
                ->join('mahasiswa', 'tagihan.mahasiswa_id', '=', 'mahasiswa.id')
                ->join('program_studi', 'mahasiswa.program_studi_id', '=', 'program_studi.id')
                ->when($tahunAkademikId, fn($q) => $q->where('tagihan.tahun_akademik_id', $tahunAkademikId))
                ->where('tagihan.status', '!=', 'Lunas')
                ->selectRaw('program_studi.nama as prodi, COALESCE(SUM(tagihan.sisa_tagihan), 0) as total_tunggakan')
                ->groupBy('program_studi.id', 'program_studi.nama')
                ->orderByDesc('total_tunggakan')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    private function getTransaksiTerbaru()
    {
        try {
            return TransaksiPembayaran::with(['tagihan:id,no_tagihan,mahasiswa_id', 'tagihan.mahasiswa:id,nama,nim'])
                ->select('id', 'no_transaksi', 'tagihan_id', 'jumlah', 'status', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    private function getTagihanJatuhTempo()
    {
        try {
            return Tagihan::with(['mahasiswa:id,nama,nim,program_studi_id', 'mahasiswa.programStudi:id,nama'])
                ->select('id', 'no_tagihan', 'mahasiswa_id', 'jenis_tagihan', 'sisa_tagihan', 'tanggal_jatuh_tempo', 'status')
                ->where('status', '!=', 'Lunas')
                ->whereNotNull('tanggal_jatuh_tempo')
                ->whereDate('tanggal_jatuh_tempo', '<', now())
                ->orderBy('tanggal_jatuh_tempo', 'asc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Clear dashboard cache - call this when financial data changes
     */
    public static function clearCache($tahunAkademikId = null)
    {
        if ($tahunAkademikId) {
            Cache::forget('keuangan_dashboard_' . $tahunAkademikId);
        }
        Cache::forget('keuangan_dashboard_all');
    }
}
