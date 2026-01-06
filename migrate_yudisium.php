<?php
/**
 * Migrate Yudisium/Kelulusan Data from Legacy Database
 * Source: kelulusan_mhs @ 192.168.120.121
 * Target: yudisium @ local
 */

$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
if ($source->connect_error) {
    die('Connection failed: ' . $source->connect_error);
}

// Load Laravel
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== MIGRASI DATA YUDISIUM/KELULUSAN ===\n";
echo "Source: kelulusan_mhs @ 192.168.120.121\n";
echo str_repeat("=", 60) . "\n\n";

// Build mahasiswa map (NIM => ID)
$mahasiswaMap = [];
$mahasiswas = DB::table('mahasiswa')->select('id', 'nim', 'angkatan')->get();
foreach ($mahasiswas as $mhs) {
    $mahasiswaMap[$mhs->nim] = ['id' => $mhs->id, 'angkatan' => $mhs->angkatan];
}
echo "Mahasiswa map count: " . count($mahasiswaMap) . "\n";

// Get already migrated
$alreadyMigrated = DB::table('yudisium')->whereNotNull('legacy_id')->pluck('legacy_id')->toArray();
echo "Already migrated: " . count($alreadyMigrated) . "\n\n";

// Status mapping: Legacy => Local
// L = Lulus, D = DO, C = Cuti, K = Keluar, N = Non-Aktif, E = ?
$statusMap = [
    'L' => 'L', // Lulus
    'D' => 'D', // DO
    'K' => 'K', // Keluar
    'C' => 'C', // Cuti
    'N' => 'N', // Non-Aktif
    'E' => 'D', // Assume E = DO/Exit
];

// Fetch all kelulusan_mhs
$query = "SELECT * FROM kelulusan_mhs ORDER BY IDMAHASISWA";
$result = $source->query($query);

$success = 0;
$skipped = 0;
$failed = 0;
$noMahasiswa = 0;

while ($row = $result->fetch_assoc()) {
    $nim = $row['IDMAHASISWA'];
    
    // Skip if already migrated
    if (in_array($nim, $alreadyMigrated)) {
        $skipped++;
        continue;
    }
    
    // Skip if mahasiswa not found
    if (!isset($mahasiswaMap[$nim])) {
        $noMahasiswa++;
        continue;
    }
    
    $mahasiswaId = $mahasiswaMap[$nim]['id'];
    $angkatan = $mahasiswaMap[$nim]['angkatan'];
    
    // Parse dates
    $tanggalLulus = ($row['TANGGALKELUAR'] && $row['TANGGALKELUAR'] != '0000-00-00') 
        ? $row['TANGGALKELUAR'] : null;
    $tanggalSkYudisium = ($row['TANGGALSKYUDISIUM'] && $row['TANGGALSKYUDISIUM'] != '0000-00-00') 
        ? $row['TANGGALSKYUDISIUM'] : null;
    $tanggalSkRektor = ($row['TANGGALSKREKTOR'] && $row['TANGGALSKREKTOR'] != '0000-00-00') 
        ? $row['TANGGALSKREKTOR'] : null;
    
    // Calculate tanggal_masuk from angkatan (assume September entry)
    $tanggalMasuk = $angkatan . '-09-01';
    
    // Calculate masa studi in months
    $masaStudiBulan = null;
    if ($tanggalMasuk && $tanggalLulus) {
        $masuk = new DateTime($tanggalMasuk);
        $lulus = new DateTime($tanggalLulus);
        $diff = $masuk->diff($lulus);
        $masaStudiBulan = ($diff->y * 12) + $diff->m;
    }
    
    // Determine predikat based on IPK
    $ipk = floatval($row['IPK']);
    $predikat = null;
    if ($row['STATUSKELUAR'] == 'L') { // Only for graduates
        if ($ipk >= 3.76) {
            $predikat = 'Dengan Pujian';
        } elseif ($ipk >= 3.51) {
            $predikat = 'Sangat Memuaskan';
        } elseif ($ipk >= 2.76) {
            $predikat = 'Memuaskan';
        } else {
            $predikat = 'Cukup';
        }
    } else {
        // Non-graduate (DO/Keluar/etc.) still need predikat
        $predikat = '-';
    }
    
    // Ensure required fields have default values
    $ipkAkhir = $ipk > 0 ? round($ipk, 2) : 0;
    $totalSks = intval($row['SKSTOTAL']) ?: 0;
    
    // Map status
    $statusKeluar = $statusMap[$row['STATUSKELUAR']] ?? $row['STATUSKELUAR'];
    
    // Generate no_yudisium from NIM and tahun_semester
    $tahunSem = $row['TAHUNSEMESTER'] ?: date('Ym');
    $noYudisium = "YUD/{$nim}/{$tahunSem}";
    
    // Determine tanggal_yudisium (required field): use SK date, or tanggal_lulus, or tanggal keluar
    $tanggalYudisium = $tanggalSkYudisium ?: $tanggalLulus ?: date('Y-m-d');
    
    // Prepare data
    $data = [
        'mahasiswa_id' => $mahasiswaId,
        'no_yudisium' => $noYudisium,
        'no_sk_yudisium' => $row['NOSKYUDISIUM'] ?: null,
        'tanggal_sk_yudisium' => $tanggalSkYudisium,
        'tanggal_yudisium' => $tanggalYudisium, // Required - fallback to tanggal_lulus or today
        'tanggal_lulus' => $tanggalLulus,
        'tanggal_masuk' => $tanggalMasuk,
        'ipk_akhir' => $ipkAkhir, // Required - use calculated value or 0
        'total_sks_lulus' => $totalSks, // Required - use calculated value or 0
        'masa_studi_bulan' => $masaStudiBulan,
        'predikat' => $predikat,
        'no_ijazah' => ($row['NOIJAZAH'] && $row['NOIJAZAH'] != '-') ? $row['NOIJAZAH'] : null,
        'no_transkrip' => $row['NOTRANSKRIP'] ?: null,
        'no_sk_rektor' => ($row['NOSKREKTOR'] && $row['NOSKREKTOR'] != '-') ? $row['NOSKREKTOR'] : null,
        'tanggal_sk_rektor' => $tanggalSkRektor,
        'no_blanko' => intval($row['NOBLANKO']) ?: null,
        'no_pin' => $row['NOPIN'] ?: null,
        'no_nirl' => $row['NONIRL'] ?: null,
        'url_pddikti' => $row['URLPDDIKTI'] ?: null,
        'nilai_kompre' => null,
        'nilai_uap_tulis' => floatval($row['NILAIUAPTULIS']) ?: null,
        'nilai_uap_praktek' => floatval($row['NILAIUAPPRAKTEK']) ?: null,
        'simbol_uap_tulis' => $row['SIMBOLUAPTULIS'] ?: null,
        'simbol_uap_praktek' => $row['SIMBOLUAPPRAKTEK'] ?: null,
        'peminatan' => $row['PEMINATAN'] ?: null,
        'status_keluar' => $statusKeluar,
        'tahun_semester' => $row['TAHUNSEMESTER'] ?: null,
        'status' => 'Disetujui', // Already graduated/processed
        'legacy_id' => $nim,
        'created_at' => now(),
        'updated_at' => now(),
    ];
    
    try {
        DB::table('yudisium')->insert($data);
        $success++;
        
        $statusLabel = match($row['STATUSKELUAR']) {
            'L' => 'Lulus',
            'D' => 'DO',
            'K' => 'Keluar',
            'E' => 'Exit/DO',
            default => $row['STATUSKELUAR']
        };
        
        if ($success <= 20 || $success % 50 == 0) {
            echo "  + $nim: $statusLabel (IPK: " . number_format($ipk, 2) . ")\n";
        }
    } catch (Exception $e) {
        $failed++;
        echo "  ERROR $nim: " . $e->getMessage() . "\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "HASIL MIGRASI YUDISIUM:\n";
echo "  - Success: $success\n";
echo "  - Skipped (exists): $skipped\n";
echo "  - No Mahasiswa: $noMahasiswa\n";
echo "  - Failed: $failed\n";
echo "\nTotal yudisium now: " . DB::table('yudisium')->count() . "\n";

$source->close();
echo "\n=== MIGRASI SELESAI ===\n";
