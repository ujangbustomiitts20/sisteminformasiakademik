<?php
/**
 * Fix Nilai Kosong - Final
 * Update nilai yang masih kosong hurufnya dari transkrip_detil
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIX NILAI KOSONG - FINAL ===\n\n";

// Koneksi ke source database
$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
$source->set_charset('utf8mb4');

if ($source->connect_error) {
    die("Connection failed: " . $source->connect_error);
}

// Mapping bobot
$bobotMap = [
    'A' => 4.00,
    'A-' => 3.75,
    'B+' => 3.50,
    'B' => 3.00,
    'B-' => 2.75,
    'C+' => 2.50,
    'C' => 2.00,
    'D' => 1.00,
    'E' => 0.00,
];

// Ambil semua nilai kosong
$nilaiKosong = DB::table('nilai as n')
    ->join('krs as k', 'n.krs_id', '=', 'k.id')
    ->join('mahasiswa as m', 'k.mahasiswa_id', '=', 'm.id')
    ->join('jadwal_kuliah as j', 'k.jadwal_kuliah_id', '=', 'j.id')
    ->join('mata_kuliah as mk', 'j.mata_kuliah_id', '=', 'mk.id')
    ->where(function ($q) {
        $q->whereNull('n.huruf')->orWhere('n.huruf', '');
    })
    ->select('n.id', 'm.nim', 'mk.kode', 'mk.nama')
    ->get();

echo "Total nilai kosong: " . $nilaiKosong->count() . "\n\n";

// Load semua transkrip ke memory
$transkripMap = [];
$result = $source->query("SELECT * FROM transkrip_detil WHERE SIMBOL IS NOT NULL AND SIMBOL != ''");
while ($row = $result->fetch_assoc()) {
    $key = $row['IDMAHASISWA'] . '_' . $row['IDMAKUL'];
    $transkripMap[$key] = $row;
    
    // Juga simpan by nama
    $namaNormalized = strtolower(trim($row['NAMA']));
    $keyNama = $row['IDMAHASISWA'] . '_' . $namaNormalized;
    if (!isset($transkripMap[$keyNama])) {
        $transkripMap[$keyNama] = $row;
    }
}
echo "Loaded " . count($transkripMap) . " transkrip entries\n\n";

$updated = 0;
$notFound = 0;

foreach ($nilaiKosong as $n) {
    // Cari by IDMAKUL (kode MK)
    $key = $n->nim . '_' . $n->kode;
    $transkrip = $transkripMap[$key] ?? null;
    
    // Kalau tidak ketemu, coba by nama
    if (!$transkrip) {
        $namaNormalized = strtolower(trim($n->nama));
        $keyNama = $n->nim . '_' . $namaNormalized;
        $transkrip = $transkripMap[$keyNama] ?? null;
    }
    
    if ($transkrip && !empty($transkrip['SIMBOL'])) {
        $huruf = $transkrip['SIMBOL'];
        $bobot = $bobotMap[$huruf] ?? $transkrip['BOBOT'] ?? 0;
        $nilaiAkhir = $transkrip['NILAI'] ?? 0;
        
        DB::table('nilai')->where('id', $n->id)->update([
            'huruf' => $huruf,
            'bobot' => $bobot,
            'nilai_akhir' => $nilaiAkhir,
            'updated_at' => now(),
        ]);
        
        echo "✓ {$n->nim} | {$n->kode} -> {$huruf} ({$bobot})\n";
        $updated++;
    } else {
        echo "✗ NOT FOUND: {$n->nim} | {$n->kode} - {$n->nama}\n";
        $notFound++;
    }
}

echo "\n=== SUMMARY ===\n";
echo "Updated: $updated\n";
echo "Not found: $notFound\n";

// Verifikasi
echo "\n=== VERIFIKASI ===\n";
$stats = DB::table('nilai')
    ->selectRaw("
        COUNT(*) as total,
        SUM(CASE WHEN huruf IS NOT NULL AND huruf != '' THEN 1 ELSE 0 END) as terisi,
        SUM(CASE WHEN huruf IS NULL OR huruf = '' THEN 1 ELSE 0 END) as kosong
    ")
    ->first();

echo "Total Nilai: $stats->total\n";
echo "Terisi (punya huruf): $stats->terisi (" . round($stats->terisi / $stats->total * 100, 2) . "%)\n";
echo "Belum Dinilai: $stats->kosong\n";

$source->close();
