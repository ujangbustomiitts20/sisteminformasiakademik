<?php

/**
 * Script untuk memperbaiki tanggal_masuk dan masa_studi_bulan pada data yudisium
 * yang salah karena menggunakan created_at mahasiswa (tanggal seeding)
 * 
 * Jalankan dengan: php fix_masa_studi_yudisium.php
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== FIX MASA STUDI YUDISIUM ===" . PHP_EOL;
echo str_repeat("=", 50) . PHP_EOL;

// Get all yudisium with wrong tanggal_masuk (year >= 2025 which is seeding date)
$yudisiumList = DB::table('yudisium')
    ->join('mahasiswa', 'yudisium.mahasiswa_id', '=', 'mahasiswa.id')
    ->select(
        'yudisium.id',
        'yudisium.tanggal_masuk',
        'yudisium.tanggal_lulus',
        'yudisium.masa_studi_bulan',
        'mahasiswa.id as mhs_id',
        'mahasiswa.nim',
        'mahasiswa.angkatan'
    )
    ->get();

echo "Found " . $yudisiumList->count() . " yudisium records to check" . PHP_EOL;

$updated = 0;
$errors = 0;

foreach ($yudisiumList as $y) {
    // Determine tanggal_masuk from angkatan
    $tahunMasuk = $y->angkatan;
    
    if (!$tahunMasuk) {
        echo "  SKIP: Yudisium ID {$y->id} - No angkatan/tahun_masuk for mahasiswa {$y->nim}" . PHP_EOL;
        continue;
    }
    
    // Tanggal masuk is September 1st of the angkatan year
    // This is typical academic year start in Indonesia
    $newTanggalMasuk = Carbon::create($tahunMasuk, 9, 1);
    
    // Calculate masa studi
    $tanggalLulus = Carbon::parse($y->tanggal_lulus);
    
    // Validate dates
    if ($tanggalLulus < $newTanggalMasuk) {
        echo "  WARN: Yudisium ID {$y->id} - tanggal_lulus ({$y->tanggal_lulus}) is before tanggal_masuk ({$newTanggalMasuk->format('Y-m-d')})" . PHP_EOL;
        // Set masa studi to 0 if dates are invalid
        $masaStudiBulan = 0;
    } else {
        $masaStudiBulan = $newTanggalMasuk->diffInMonths($tanggalLulus);
    }
    
    // Old values for comparison
    $oldMasaStudi = $y->masa_studi_bulan;
    $oldTanggalMasuk = $y->tanggal_masuk;
    
    // Update
    try {
        DB::table('yudisium')
            ->where('id', $y->id)
            ->update([
                'tanggal_masuk' => $newTanggalMasuk->format('Y-m-d'),
                'masa_studi_bulan' => $masaStudiBulan,
            ]);
        
        $updated++;
        
        // Show sample of changes (first 10)
        if ($updated <= 10) {
            echo "  Updated ID {$y->id} (NIM: {$y->nim}):" . PHP_EOL;
            echo "    Angkatan: {$tahunMasuk}" . PHP_EOL;
            echo "    tanggal_masuk: {$oldTanggalMasuk} -> {$newTanggalMasuk->format('Y-m-d')}" . PHP_EOL;
            echo "    tanggal_lulus: {$y->tanggal_lulus}" . PHP_EOL;
            echo "    masa_studi_bulan: {$oldMasaStudi} -> {$masaStudiBulan} (" . floor($masaStudiBulan / 12) . " tahun " . ($masaStudiBulan % 12) . " bulan)" . PHP_EOL;
        }
    } catch (\Exception $e) {
        $errors++;
        echo "  ERROR ID {$y->id}: " . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL . str_repeat("-", 50) . PHP_EOL;
echo "Summary:" . PHP_EOL;
echo "  Total checked: " . $yudisiumList->count() . PHP_EOL;
echo "  Updated: $updated" . PHP_EOL;
echo "  Errors: $errors" . PHP_EOL;

// Verification
echo PHP_EOL . "Verification (sample 5 records):" . PHP_EOL;
$samples = DB::table('yudisium')
    ->select('id', 'tanggal_masuk', 'tanggal_lulus', 'masa_studi_bulan')
    ->limit(5)
    ->get();

foreach ($samples as $s) {
    $tahun = floor($s->masa_studi_bulan / 12);
    $bulan = $s->masa_studi_bulan % 12;
    echo "  ID {$s->id}: masuk {$s->tanggal_masuk}, lulus {$s->tanggal_lulus}, masa_studi: {$s->masa_studi_bulan} bulan ({$tahun} tahun {$bulan} bulan)" . PHP_EOL;
}

echo PHP_EOL . "=== FIX SELESAI ===" . PHP_EOL;
