<?php

namespace App\Services;

use App\Models\KonfigurasiCetak;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class CetakService
{
    /**
     * Generate PDF with configuration
     *
     * @param string $kode Kode konfigurasi cetak
     * @param string $view View path
     * @param array $data Data to pass to view
     * @param string|null $filename Custom filename
     * @return \Barryvdh\DomPDF\PDF
     */
    public static function generatePdf(string $kode, string $view, array $data = [], ?string $filename = null)
    {
        $config = KonfigurasiCetak::getByKode($kode);
        
        // If config not found, use defaults
        if (!$config) {
            $config = new KonfigurasiCetak([
                'ukuran_kertas' => 'a4',
                'orientasi' => 'portrait',
                'margin_top' => 15,
                'margin_bottom' => 20,
                'margin_left' => 15,
                'margin_right' => 15,
                'tampilkan_kop' => true,
                'tampilkan_logo' => true,
                'tampilkan_ttd' => true,
                'tampilkan_footer' => true,
            ]);
        }
        
        // Add config to data
        $data['config'] = $config;
        
        // Generate PDF
        $pdf = Pdf::loadView($view, $data);
        
        // Set paper size and orientation
        $paperSize = $config->ukuran_kertas;
        if ($paperSize === 'f4') {
            // F4 is not standard, use custom dimensions (in points: 612 x 936)
            $pdf->setPaper([0, 0, 612, 936], $config->orientasi);
        } else {
            $pdf->setPaper($paperSize, $config->orientasi);
        }
        
        return $pdf;
    }

    /**
     * Stream PDF to browser
     */
    public static function stream(string $kode, string $view, array $data = [], ?string $filename = null)
    {
        $pdf = self::generatePdf($kode, $view, $data, $filename);
        $filename = $filename ?? $kode . '_' . date('Ymd_His') . '.pdf';
        
        return $pdf->stream($filename);
    }

    /**
     * Download PDF
     */
    public static function download(string $kode, string $view, array $data = [], ?string $filename = null)
    {
        $pdf = self::generatePdf($kode, $view, $data, $filename);
        $filename = $filename ?? $kode . '_' . date('Ymd_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Get configuration for a document type
     */
    public static function getConfig(string $kode): ?KonfigurasiCetak
    {
        return KonfigurasiCetak::getByKode($kode);
    }

    /**
     * Check if configuration exists and is active
     */
    public static function isConfigured(string $kode): bool
    {
        $config = KonfigurasiCetak::where('kode', $kode)->first();
        return $config && $config->is_active;
    }

    /**
     * Get all available document types
     */
    public static function getDocumentTypes(): array
    {
        return [
            'krs' => 'Kartu Rencana Studi (KRS)',
            'khs' => 'Kartu Hasil Studi (KHS)',
            'transkrip' => 'Transkrip Nilai',
            'invoice' => 'Invoice Tagihan',
            'kwitansi' => 'Kwitansi Pembayaran',
            'surat_cuti' => 'Surat Cuti Akademik',
            'surat_aktif' => 'Surat Keterangan Aktif',
            'kartu_ujian' => 'Kartu Peserta Ujian',
            'yudisium' => 'Surat Keterangan Yudisium',
            'laporan' => 'Laporan Umum',
            'daftar_hadir' => 'Daftar Hadir',
        ];
    }

    /**
     * Get CSS for margins
     */
    public static function getMarginCss(string $kode): string
    {
        $config = self::getConfig($kode);
        if (!$config) {
            return '15mm 15mm 20mm 15mm';
        }
        return $config->getMarginCss();
    }
}
