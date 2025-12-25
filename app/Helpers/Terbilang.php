<?php

namespace App\Helpers;

class Terbilang
{
    private static $angka = [
        '',
        'satu',
        'dua',
        'tiga',
        'empat',
        'lima',
        'enam',
        'tujuh',
        'delapan',
        'sembilan',
        'sepuluh',
        'sebelas'
    ];

    public static function make($nilai)
    {
        $nilai = abs($nilai);
        
        if ($nilai < 12) {
            return self::$angka[$nilai];
        } elseif ($nilai < 20) {
            return self::$angka[$nilai - 10] . ' belas';
        } elseif ($nilai < 100) {
            return self::$angka[floor($nilai / 10)] . ' puluh ' . self::$angka[$nilai % 10];
        } elseif ($nilai < 200) {
            return 'seratus ' . self::make($nilai - 100);
        } elseif ($nilai < 1000) {
            return self::$angka[floor($nilai / 100)] . ' ratus ' . self::make($nilai % 100);
        } elseif ($nilai < 2000) {
            return 'seribu ' . self::make($nilai - 1000);
        } elseif ($nilai < 1000000) {
            return self::make(floor($nilai / 1000)) . ' ribu ' . self::make($nilai % 1000);
        } elseif ($nilai < 1000000000) {
            return self::make(floor($nilai / 1000000)) . ' juta ' . self::make($nilai % 1000000);
        } elseif ($nilai < 1000000000000) {
            return self::make(floor($nilai / 1000000000)) . ' milyar ' . self::make($nilai % 1000000000);
        } elseif ($nilai < 1000000000000000) {
            return self::make(floor($nilai / 1000000000000)) . ' trilyun ' . self::make($nilai % 1000000000000);
        }
        
        return '';
    }
}
