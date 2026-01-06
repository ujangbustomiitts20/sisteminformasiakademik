<?php

use App\Models\Setting;
use App\Models\KonfigurasiCetak;

if (!function_exists('setting')) {
    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return Setting::getValue($key, $default);
    }
}

if (!function_exists('settings')) {
    /**
     * Get all settings as array
     *
     * @return array
     */
    function settings(): array
    {
        return Setting::getAll();
    }
}

if (!function_exists('konfigurasi_cetak')) {
    /**
     * Get print configuration by code
     *
     * @param string $kode
     * @return KonfigurasiCetak|null
     */
    function konfigurasi_cetak(string $kode): ?KonfigurasiCetak
    {
        return KonfigurasiCetak::getByKode($kode);
    }
}

if (!function_exists('format_rupiah')) {
    /**
     * Format number to Indonesian Rupiah
     *
     * @param float|int $number
     * @param bool $withPrefix
     * @return string
     */
    function format_rupiah($number, bool $withPrefix = true): string
    {
        $formatted = number_format($number, 0, ',', '.');
        return $withPrefix ? 'Rp ' . $formatted : $formatted;
    }
}

if (!function_exists('format_tanggal')) {
    /**
     * Format date to Indonesian format
     *
     * @param mixed $date
     * @param string $format
     * @return string
     */
    function format_tanggal($date, string $format = 'd F Y'): string
    {
        if (!$date) return '-';
        
        $bulan = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember',
        ];
        
        $date = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);
        $formatted = $date->format($format);
        
        return strtr($formatted, $bulan);
    }
}

if (!function_exists('pejabat')) {
    /**
     * Get pejabat penandatangan by kode
     *
     * @param string $kode
     * @return \App\Models\PejabatPenandatangan|null
     */
    function pejabat(string $kode): ?\App\Models\PejabatPenandatangan
    {
        return \App\Models\PejabatPenandatangan::getByKode($kode);
    }
}

if (!function_exists('pejabat_for_dokumen')) {
    /**
     * Get pejabat penandatangan untuk dokumen tertentu
     *
     * @param string $dokumenKode
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function pejabat_for_dokumen(string $dokumenKode)
    {
        return \App\Models\PejabatPenandatangan::getForDokumen($dokumenKode);
    }
}
