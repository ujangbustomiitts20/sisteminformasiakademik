<?php
/**
 * Migrate Daftar Wisuda Data from Legacy Database
 * Source: kelulusan_mhs @ 192.168.120.121
 * Target: periode_wisuda & pendaftaran_wisuda @ local
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

echo "=== MIGRASI DATA WISUDA ===\n";
echo "Source: kelulusan_mhs @ 192.168.120.121\n";
echo str_repeat("=", 60) . "\n\n";

// Build mahasiswa map (NIM => ID)
$mahasiswaMap = [];
$mahasiswas = DB::table('mahasiswa')->select('id', 'nim')->get();
foreach ($mahasiswas as $mhs) {
    $mahasiswaMap[$mhs->nim] = $mhs->id;
}
echo "Mahasiswa map count: " . count($mahasiswaMap) . "\n";

// Build tahun akademik map for lookup
$tahunAkademikMap = [];
$tahunAkademiks = DB::table('tahun_akademik')->get();
foreach ($tahunAkademiks as $ta) {
    $key = $ta->tahun . '_' . $ta->semester;
    $tahunAkademikMap[$key] = $ta->id;
}
echo "Tahun akademik map count: " . count($tahunAkademikMap) . "\n\n";

// Function to get tahun_akademik_id from date
function getTahunAkademikId($date, $map) {
    $year = (int)date('Y', strtotime($date));
    $month = (int)date('n', strtotime($date));
    
    // Ganjil: Sept-Feb (year/year+1), Genap: Mar-Aug (year-1/year)
    if ($month >= 9) {
        // Ganjil semester (Sept-Dec)
        $taYear = $year;
        $taSem = 'Ganjil';
    } elseif ($month >= 3 && $month <= 8) {
        // Genap semester (Mar-Aug)
        $taYear = $year - 1;
        $taSem = 'Genap';
    } else {
        // Jan-Feb = still Ganjil from previous year
        $taYear = $year - 1;
        $taSem = 'Ganjil';
    }
    
    $key = $taYear . '_' . $taSem;
    return $map[$key] ?? null;
}

// Step 1: Create Periode Wisuda from unique SK Yudisium
echo "=== STEP 1: MIGRASI PERIODE WISUDA ===\n";

// First, get data WITH SK Yudisium
$query = "SELECT NOSKYUDISIUM, TANGGALSKYUDISIUM, COUNT(*) as cnt 
    FROM kelulusan_mhs 
    WHERE STATUSKELUAR = 'L' AND NOSKYUDISIUM IS NOT NULL AND NOSKYUDISIUM != ''
    GROUP BY NOSKYUDISIUM, TANGGALSKYUDISIUM
    ORDER BY TANGGALSKYUDISIUM";
$result = $source->query($query);

$periodeMap = []; // Map SK Yudisium => periode_wisuda_id
$periodeSuccess = 0;
$periodeSkipped = 0;

while ($row = $result->fetch_assoc()) {
    $noSk = $row['NOSKYUDISIUM'];
    $tanggal = ($row['TANGGALSKYUDISIUM'] && $row['TANGGALSKYUDISIUM'] != '0000-00-00') 
        ? $row['TANGGALSKYUDISIUM'] : date('Y-m-d');
    
    // Check if already exists
    $existing = DB::table('periode_wisuda')->where('nama', $noSk)->first();
    if ($existing) {
        $periodeMap[$noSk] = $existing->id;
        $periodeSkipped++;
        continue;
    }
    
    // Extract year from date for naming
    $year = date('Y', strtotime($tanggal));
    $month = date('n', strtotime($tanggal));
    $periode = $month <= 6 ? 'Genap' : 'Ganjil';
    
    // Get correct tahun_akademik_id based on wisuda date
    $taId = getTahunAkademikId($tanggal, $tahunAkademikMap);
    echo "  > Wisuda $tanggal -> tahun_akademik_id: $taId\n";
    
    $data = [
        'tahun_akademik_id' => $taId,
        'nama' => $noSk,
        'tanggal_wisuda' => $tanggal,
        'tanggal_buka_pendaftaran' => date('Y-m-d', strtotime($tanggal . ' -2 months')),
        'tanggal_tutup_pendaftaran' => date('Y-m-d', strtotime($tanggal . ' -1 month')),
        'tanggal_yudisium' => date('Y-m-d', strtotime($tanggal . ' -1 week')),
        'lokasi' => 'Kampus IT Telkom Surabaya',
        'kuota' => 200,
        'biaya_wisuda' => 1500000,
        'persyaratan' => "1. Lulus semua mata kuliah\n2. Bebas administrasi keuangan\n3. Bebas perpustakaan\n4. Sudah mengisi SKPI\n5. Sudah upload foto wisuda",
        'status' => 'Selesai', // Already completed
        'created_at' => now(),
        'updated_at' => now(),
    ];
    
    try {
        $id = DB::table('periode_wisuda')->insertGetId($data);
        $periodeMap[$noSk] = $id;
        $periodeSuccess++;
        echo "  + Periode: $noSk ($tanggal)\n";
    } catch (Exception $e) {
        echo "  ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\nPeriode Wisuda: $periodeSuccess added, $periodeSkipped skipped\n\n";

// Step 1b: Create default periode for graduates WITHOUT SK Yudisium
echo "=== STEP 1b: PERIODE DEFAULT UNTUK DATA TANPA SK ===\n";

// Check if there are graduates without SK
$queryNoSk = "SELECT TANGGALKELUAR, COUNT(*) as cnt 
    FROM kelulusan_mhs 
    WHERE STATUSKELUAR = 'L' AND (NOSKYUDISIUM IS NULL OR NOSKYUDISIUM = '')
    GROUP BY TANGGALKELUAR
    ORDER BY TANGGALKELUAR";
$resultNoSk = $source->query($queryNoSk);

$defaultPeriodeId = null;
$noSkCount = 0;
while ($rowNoSk = $resultNoSk->fetch_assoc()) {
    $noSkCount += $rowNoSk['cnt'];
}

if ($noSkCount > 0) {
    echo "  Found $noSkCount graduates without SK Yudisium\n";
    
    // Create or get default periode for data without SK
    $defaultPeriodeName = 'Wisuda-Pending-SK';
    $existingDefault = DB::table('periode_wisuda')->where('nama', $defaultPeriodeName)->first();
    
    if ($existingDefault) {
        $defaultPeriodeId = $existingDefault->id;
        echo "  Using existing default periode: $defaultPeriodeName (ID: $defaultPeriodeId)\n";
    } else {
        // Get tahun akademik for 2025 Ganjil (most recent)
        $taId = $tahunAkademikMap['2025_Ganjil'] ?? $tahunAkademikMap['2024_Ganjil'] ?? null;
        
        $defaultData = [
            'tahun_akademik_id' => $taId,
            'nama' => $defaultPeriodeName,
            'tanggal_wisuda' => '2025-09-09', // Default to next wisuda date
            'tanggal_buka_pendaftaran' => '2025-07-01',
            'tanggal_tutup_pendaftaran' => '2025-08-15',
            'tanggal_yudisium' => '2025-09-02',
            'lokasi' => 'Kampus IT Telkom Surabaya',
            'kuota' => 200,
            'biaya_wisuda' => 1500000,
            'persyaratan' => "1. Lulus semua mata kuliah\n2. Bebas administrasi keuangan\n3. Bebas perpustakaan",
            'status' => 'Dibuka', // Pending
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        $defaultPeriodeId = DB::table('periode_wisuda')->insertGetId($defaultData);
        $periodeSuccess++;
        echo "  + Created default periode: $defaultPeriodeName (ID: $defaultPeriodeId)\n";
    }
}

// Step 2: Create Pendaftaran Wisuda for graduates
echo "=== STEP 2: MIGRASI PENDAFTARAN WISUDA ===\n";

// Include ALL graduates (with and without SK)
$query = "SELECT * FROM kelulusan_mhs 
    WHERE STATUSKELUAR = 'L'
    ORDER BY TANGGALSKYUDISIUM, IDMAHASISWA";
$result = $source->query($query);

$pendaftaranSuccess = 0;
$pendaftaranSkipped = 0;
$noMahasiswa = 0;
$failed = 0;

while ($row = $result->fetch_assoc()) {
    $nim = $row['IDMAHASISWA'];
    $noSk = $row['NOSKYUDISIUM'];
    
    // Skip if mahasiswa not found
    if (!isset($mahasiswaMap[$nim])) {
        $noMahasiswa++;
        continue;
    }
    
    $mahasiswaId = $mahasiswaMap[$nim];
    
    // Get periode_wisuda_id (from SK or default)
    $periodeId = null;
    if ($noSk && isset($periodeMap[$noSk])) {
        $periodeId = $periodeMap[$noSk];
    } elseif ($defaultPeriodeId) {
        $periodeId = $defaultPeriodeId;
    }
    
    if (!$periodeId) {
        $failed++;
        continue;
    }
    
    // Check if already registered
    $existing = DB::table('pendaftaran_wisuda')
        ->where('mahasiswa_id', $mahasiswaId)
        ->where('periode_wisuda_id', $periodeId)
        ->first();
    if ($existing) {
        $pendaftaranSkipped++;
        continue;
    }
    
    // Parse dates
    $tanggalSk = ($row['TANGGALSKYUDISIUM'] && $row['TANGGALSKYUDISIUM'] != '0000-00-00') 
        ? $row['TANGGALSKYUDISIUM'] : date('Y-m-d');
    $tanggalDaftar = date('Y-m-d', strtotime($tanggalSk . ' -1 month'));
    
    // Generate no_pendaftaran based on periode
    $year = date('Y', strtotime($tanggalSk));
    // Use unique counter per periode
    $counter = DB::table('pendaftaran_wisuda')
        ->where('periode_wisuda_id', $periodeId)
        ->count() + 1;
    // Include periode ID in no_pendaftaran to ensure uniqueness
    $noPendaftaran = "WSD/{$periodeId}/{$year}/" . str_pad($counter, 4, '0', STR_PAD_LEFT);
    
    // Determine status based on SK
    $status = $noSk ? 'Lulus' : 'Lolos Yudisium'; // Pending SK = Lolos Yudisium (waiting for wisuda)
    
    $data = [
        'no_pendaftaran' => $noPendaftaran,
        'mahasiswa_id' => $mahasiswaId,
        'periode_wisuda_id' => $periodeId,
        'tanggal_daftar' => $tanggalDaftar,
        'ipk' => round(floatval($row['IPK']), 2),
        'total_sks' => intval($row['SKSTOTAL']) ?: 0,
        'judul_skripsi' => $row['JUDULTA'] ?? null,
        'tanggal_lulus_sidang' => ($row['TANGGALKELUAR'] && $row['TANGGALKELUAR'] != '0000-00-00') 
            ? $row['TANGGALKELUAR'] : null,
        'status' => $status,
        'foto_formal' => null,
        'bukti_bebas_pustaka' => null,
        'bukti_bebas_keuangan' => null,
        'bukti_pembayaran_wisuda' => null,
        'catatan_verifikasi' => 'Data migrasi dari sistem lama',
        'diverifikasi_oleh' => null,
        'tanggal_verifikasi' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ];
    
    try {
        DB::table('pendaftaran_wisuda')->insert($data);
        $pendaftaranSuccess++;
        
        if ($pendaftaranSuccess <= 10 || $pendaftaranSuccess % 10 == 0) {
            echo "  + $nim: $noPendaftaran (IPK: " . number_format($row['IPK'], 2) . ")\n";
        }
    } catch (Exception $e) {
        $failed++;
        echo "  ERROR $nim: " . $e->getMessage() . "\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "HASIL MIGRASI WISUDA:\n";
echo "  Periode Wisuda:\n";
echo "    - Added: $periodeSuccess\n";
echo "    - Skipped: $periodeSkipped\n";
echo "  Pendaftaran Wisuda:\n";
echo "    - Success: $pendaftaranSuccess\n";
echo "    - Skipped (exists): $pendaftaranSkipped\n";
echo "    - No Mahasiswa: $noMahasiswa\n";
echo "    - Failed: $failed\n";
echo "\nTotal periode_wisuda: " . DB::table('periode_wisuda')->count() . "\n";
echo "Total pendaftaran_wisuda: " . DB::table('pendaftaran_wisuda')->count() . "\n";

$source->close();
echo "\n=== MIGRASI SELESAI ===\n";
