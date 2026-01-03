<?php
/**
 * Migrate Nilai by Matching MK Name
 * 
 * Masalah: Kode MK di source tidak konsisten dengan prodi mahasiswa
 * Solusi: Match nilai dengan nama MK bukan kode
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Connect to source database
$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
if ($source->connect_error) {
    die("Connection failed: " . $source->connect_error);
}
$source->set_charset('utf8mb4');

echo "=== MIGRATE NILAI BY MK NAME ===\n\n";

// Step 1: Build MK name mapping from source
echo "Step 1: Building source MK name mapping...\n";
$sourceMkMap = []; // IDMAKUL => NAMA
$result = $source->query("SELECT ID, NAMA FROM makul");
while ($row = $result->fetch_assoc()) {
    $sourceMkMap[$row['ID']] = $row['NAMA'];
}
echo "  Source MK loaded: " . count($sourceMkMap) . "\n";

// Step 2: Build local MK name mapping (nama => id, grouped by prodi)
echo "Step 2: Building local MK name mapping...\n";
$localMkByName = []; // nama => [prodi_id => mk_id]
$localMk = DB::table('mata_kuliah')->get(['id', 'kode', 'nama', 'program_studi_id']);
foreach ($localMk as $mk) {
    $namaNorm = strtolower(trim($mk->nama));
    if (!isset($localMkByName[$namaNorm])) {
        $localMkByName[$namaNorm] = [];
    }
    $localMkByName[$namaNorm][$mk->program_studi_id] = $mk->id;
    // Also store without prodi for fallback
    $localMkByName[$namaNorm]['any'] = $mk->id;
}
echo "  Local MK names loaded: " . count($localMkByName) . "\n";

// Step 3: Build mahasiswa NIM => (local_id, prodi_id) mapping
echo "Step 3: Building mahasiswa mapping...\n";
$mhsMap = []; // nim => [local_id, prodi_id]
$mahasiswas = DB::table('mahasiswa')->get(['id', 'nim', 'program_studi_id']);
foreach ($mahasiswas as $mhs) {
    $mhsMap[$mhs->nim] = ['id' => $mhs->id, 'prodi_id' => $mhs->program_studi_id];
}
echo "  Mahasiswa loaded: " . count($mhsMap) . "\n";

// Step 4: Build KRS mapping (mahasiswa_id + mk_id + tahun_akademik_id => krs_id)
echo "Step 4: Building KRS mapping...\n";
$krsMap = []; // "mhs_id-mk_id-ta_id" => krs_id
$krsList = DB::table('krs as k')
    ->join('jadwal_kuliah as j', 'k.jadwal_kuliah_id', '=', 'j.id')
    ->select('k.id as krs_id', 'k.mahasiswa_id', 'j.mata_kuliah_id', 'k.tahun_akademik_id')
    ->get();
foreach ($krsList as $krs) {
    $key = $krs->mahasiswa_id . '-' . $krs->mata_kuliah_id . '-' . $krs->tahun_akademik_id;
    $krsMap[$key] = $krs->krs_id;
}
echo "  KRS loaded: " . count($krsMap) . "\n";

// Step 5: Build tahun akademik mapping
echo "Step 5: Building tahun akademik mapping...\n";
$taMap = []; // "2025-1" => ta_id
$taList = DB::table('tahun_akademik')->get(['id', 'tahun', 'semester']);
foreach ($taList as $ta) {
    $semNum = match($ta->semester) {
        'Ganjil' => 1,
        'Genap' => 2,
        'Pendek' => 4,
        default => 0
    };
    $key = $ta->tahun . $semNum;
    $taMap[$key] = $ta->id;
}
echo "  Tahun Akademik loaded: " . count($taMap) . "\n";

// Step 6: Get existing nilai krs_ids
echo "Step 6: Getting existing nilai...\n";
$existingNilai = DB::table('nilai')->pluck('krs_id')->toArray();
$existingNilaiSet = array_flip($existingNilai);
echo "  Existing nilai: " . count($existingNilai) . "\n";

// Step 7: Query source nilai
echo "\nStep 7: Migrating nilai...\n";
$result = $source->query("
    SELECT n.IDMAHASISWA, n.IDMAKUL, n.TAHUN, n.SEMESTER, 
           n.NILAIUTS, n.NILAIUAS, n.NILAITUGAS, n.NILAIPRAKTIKUM,
           n.NILAIAKHIR, n.NILAIAKHIR2, n.HURUF, n.BOBOT, n.STATUS,
           n.CREATOR, n.DATECREATED
    FROM nilai n
");

$created = 0;
$skipped = 0;
$noKrs = 0;
$noMhs = 0;
$noMk = 0;
$noTa = 0;
$duplicate = 0;
$errors = [];

$batchSize = 500;
$batch = [];

while ($row = $result->fetch_assoc()) {
    $nim = $row['IDMAHASISWA'];
    $idmakul = $row['IDMAKUL'];
    $tahun = $row['TAHUN'];
    $semester = $row['SEMESTER'];
    
    // Get mahasiswa
    if (!isset($mhsMap[$nim])) {
        $noMhs++;
        continue;
    }
    $mhsId = $mhsMap[$nim]['id'];
    $prodiId = $mhsMap[$nim]['prodi_id'];
    
    // Get MK name from source, then find local MK
    if (!isset($sourceMkMap[$idmakul])) {
        $noMk++;
        continue;
    }
    $mkNama = strtolower(trim($sourceMkMap[$idmakul]));
    
    if (!isset($localMkByName[$mkNama])) {
        $noMk++;
        continue;
    }
    
    // Prefer MK from same prodi, fallback to any
    $localMkId = $localMkByName[$mkNama][$prodiId] ?? $localMkByName[$mkNama]['any'] ?? null;
    if (!$localMkId) {
        $noMk++;
        continue;
    }
    
    // Get tahun akademik
    $taKey = $tahun . $semester;
    if (!isset($taMap[$taKey])) {
        $noTa++;
        continue;
    }
    $taId = $taMap[$taKey];
    
    // Find KRS
    $krsKey = $mhsId . '-' . $localMkId . '-' . $taId;
    if (!isset($krsMap[$krsKey])) {
        $noKrs++;
        continue;
    }
    $krsId = $krsMap[$krsKey];
    
    // Check duplicate
    if (isset($existingNilaiSet[$krsId])) {
        $duplicate++;
        continue;
    }
    
    // Prepare nilai data
    $nilaiAkhir = $row['NILAIAKHIR'] ?: $row['NILAIAKHIR2'] ?: 0;
    $huruf = $row['HURUF'] ?: '';
    $bobot = $row['BOBOT'] ?: 0;
    
    $batch[] = [
        'krs_id' => $krsId,
        'nilai_tugas' => $row['NILAITUGAS'] ?: null,
        'nilai_uts' => $row['NILAIUTS'] ?: null,
        'nilai_uas' => $row['NILAIUAS'] ?: null,
        'nilai_praktikum' => $row['NILAIPRAKTIKUM'] ?: null,
        'nilai_akhir' => $nilaiAkhir,
        'grade' => $huruf,
        'bobot' => $bobot,
        'status' => 'final',
        'created_at' => $row['DATECREATED'] ?: now(),
        'updated_at' => now(),
    ];
    $existingNilaiSet[$krsId] = true; // Prevent duplicate in same batch
    
    // Insert batch
    if (count($batch) >= $batchSize) {
        try {
            DB::table('nilai')->insert($batch);
            $created += count($batch);
            echo "  Inserted batch: $created created\r";
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
        $batch = [];
    }
}

// Insert remaining
if (!empty($batch)) {
    try {
        DB::table('nilai')->insert($batch);
        $created += count($batch);
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}

echo "\n\n=== MIGRATION RESULTS ===\n";
echo "Created: $created\n";
echo "Skipped (duplicate): $duplicate\n";
echo "Skipped (no mahasiswa): $noMhs\n";
echo "Skipped (no MK match): $noMk\n";
echo "Skipped (no tahun akademik): $noTa\n";
echo "Skipped (no KRS): $noKrs\n";

if (!empty($errors)) {
    echo "\nErrors:\n";
    foreach (array_slice($errors, 0, 5) as $err) {
        echo "  - $err\n";
    }
}

$source->close();
echo "\nDone!\n";
