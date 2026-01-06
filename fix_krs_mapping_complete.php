<?php
/**
 * Fix KRS Mapping - Perbaiki mapping semester yang tercampur
 * 
 * Mapping yang BENAR berdasarkan konvensi source:
 * - Source 2026/1 (Sep-Des 2025) => Local 2025 Ganjil (ID: 2) - AKTIF
 * - Source 2025/2 (Feb-Jul 2025) => Local 2024 Genap (ID: 11)  
 * - Source 2025/1 (Sep-Des 2024) => Local 2024 Ganjil (ID: 10)
 * - Source 2025/4 (Pendek Aug 2025) => Local 2025 Pendek (ID: 13)
 * - Source 2024/2 (Feb-Jul 2024) => Local 2023 Genap (ID: 9)
 * - Source 2024/1 (Sep-Des 2023) => Local 2023 Ganjil (ID: 8)
 * 
 * Pola: Source Year/Sem=1 => Local (Year-1) Ganjil
 *       Source Year/Sem=2 => Local (Year-1) Genap
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
if ($source->connect_error) {
    die("Koneksi gagal: " . $source->connect_error);
}
$source->set_charset('utf8mb4');

echo "=== FIX KRS MAPPING ===\n\n";

// Build tahun akademik map - local
$taMap = []; // "year-semester" => id
$tas = DB::table('tahun_akademik')->get();
foreach ($tas as $ta) {
    $key = "{$ta->tahun}-{$ta->semester}";
    $taMap[$key] = $ta->id;
}

// Mapping source to local
// Source menggunakan tahun akhir semester: 2026/1 = Ganjil yang berakhir di 2026 = Local 2025 Ganjil
$sourceToLocal = [
    '2026-1' => $taMap['2025-Ganjil'] ?? null,  // Sep-Des 2025
    '2025-2' => $taMap['2024-Genap'] ?? null,   // Feb-Jul 2025
    '2025-1' => $taMap['2024-Ganjil'] ?? null,  // Sep-Des 2024
    '2025-4' => $taMap['2025-Pendek'] ?? null,  // Pendek Aug 2025
    '2024-2' => $taMap['2023-Genap'] ?? null,   // Feb-Jul 2024
    '2024-1' => $taMap['2023-Ganjil'] ?? null,  // Sep-Des 2023
    '2023-2' => $taMap['2022-Genap'] ?? null,   // Feb-Jul 2023
    '2023-1' => $taMap['2022-Ganjil'] ?? null,  // Sep-Des 2022
];

echo "Mapping Source -> Local:\n";
foreach ($sourceToLocal as $src => $localId) {
    $localTa = DB::table('tahun_akademik')->where('id', $localId)->first();
    echo "  Source {$src} => Local " . ($localTa ? "{$localTa->tahun} {$localTa->semester} (ID:{$localId})" : "NULL") . "\n";
}

// Build MK map
echo "\n1. Building MK map...\n";
$mkMap = [];
$mks = DB::table('mata_kuliah')->select('id', 'kode')->get();
foreach ($mks as $mk) {
    $mkMap[$mk->kode] = $mk->id;
}

// Build mahasiswa map
echo "2. Building mahasiswa map...\n";
$mhsMap = [];
$mhsList = DB::table('mahasiswa')->select('id', 'nim')->get();
foreach ($mhsList as $m) {
    $mhsMap[$m->nim] = $m->id;
}

// Get source MK kode map
echo "3. Building source MK kode map...\n";
$sourceMkKode = [];
$result = $source->query("SELECT ID, KODEMAKUL FROM makul");
while ($row = $result->fetch_assoc()) {
    $sourceMkKode[$row['ID']] = $row['KODEMAKUL'];
}

// STEP 1: Clear all KRS data and rebuild from source
echo "\n4. Clearing existing KRS...\n";
$deletedKrs = DB::table('krs')->delete();
echo "   Deleted {$deletedKrs} KRS records\n";

// STEP 2: Build jadwal map
echo "5. Building jadwal map...\n";
$jadwalMap = []; // "mk_id-ta_id" => jadwal_id
$jadwals = DB::table('jadwal_kuliah')->select('id', 'mata_kuliah_id', 'tahun_akademik_id', 'kelas')->get();
foreach ($jadwals as $j) {
    $key = "{$j->mata_kuliah_id}-{$j->tahun_akademik_id}";
    if (!isset($jadwalMap[$key])) {
        $jadwalMap[$key] = $j->id;
    }
    // Also with kelas
    $keyKelas = "{$j->mata_kuliah_id}-{$j->tahun_akademik_id}-{$j->kelas}";
    $jadwalMap[$keyKelas] = $j->id;
}

// STEP 3: Query all KRS from source presensi
echo "6. Querying source presensi data...\n";
$result = $source->query("
    SELECT DISTINCT 
        kpm.IDMAHASISWA, 
        kpm.IDMAKUL, 
        kpm.TAHUN, 
        kpm.SEMESTER,
        kpm.IDKELAS
    FROM kelaskuliah_presensimahasiswa kpm
    ORDER BY kpm.TAHUN, kpm.SEMESTER
");

$sourceData = [];
while ($row = $result->fetch_assoc()) {
    $sourceData[] = $row;
}
echo "   Found " . count($sourceData) . " unique KRS entries\n";

// STEP 4: Insert KRS with correct mapping
echo "\n7. Inserting KRS with correct mapping...\n";
$inserted = 0;
$skipped = 0;
$noMhs = 0;
$noMk = 0;
$noTa = 0;
$noJadwal = 0;
$batchInsert = [];
$existingKrs = [];

foreach ($sourceData as $idx => $row) {
    // Find mahasiswa
    if (!isset($mhsMap[$row['IDMAHASISWA']])) {
        $noMhs++;
        continue;
    }
    $mhsId = $mhsMap[$row['IDMAHASISWA']];
    
    // Find local tahun akademik using correct mapping
    $sourceKey = "{$row['TAHUN']}-{$row['SEMESTER']}";
    if (!isset($sourceToLocal[$sourceKey]) || !$sourceToLocal[$sourceKey]) {
        $noTa++;
        continue;
    }
    $taId = $sourceToLocal[$sourceKey];
    
    // Find mata kuliah
    $kode = $sourceMkKode[$row['IDMAKUL']] ?? null;
    if (!$kode || !isset($mkMap[$kode])) {
        $noMk++;
        continue;
    }
    $mkId = $mkMap[$kode];
    
    // Check duplicate
    $krsKey = "{$mhsId}-{$mkId}-{$taId}";
    if (isset($existingKrs[$krsKey])) {
        $skipped++;
        continue;
    }
    $existingKrs[$krsKey] = true;
    
    // Find jadwal
    $kelas = $row['IDKELAS'] ?? 'A';
    $jadwalKey = "{$mkId}-{$taId}-{$kelas}";
    $jadwalKeySimple = "{$mkId}-{$taId}";
    $jadwalId = $jadwalMap[$jadwalKey] ?? $jadwalMap[$jadwalKeySimple] ?? null;
    
    if (!$jadwalId) {
        // Create jadwal
        $jadwalId = DB::table('jadwal_kuliah')->insertGetId([
            'mata_kuliah_id' => $mkId,
            'tahun_akademik_id' => $taId,
            'dosen_id' => 1,
            'kelas' => $kelas,
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'ruangan_id' => 1,
            'kuota' => 40,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $jadwalMap[$jadwalKey] = $jadwalId;
        $jadwalMap[$jadwalKeySimple] = $jadwalId;
    }
    
    $batchInsert[] = [
        'mahasiswa_id' => $mhsId,
        'jadwal_kuliah_id' => $jadwalId,
        'tahun_akademik_id' => $taId,
        'status' => 'disetujui',
        'created_at' => now(),
        'updated_at' => now(),
    ];
    $inserted++;
    
    // Batch insert
    if (count($batchInsert) >= 500) {
        DB::table('krs')->insert($batchInsert);
        echo "   Inserted batch: {$inserted} total\n";
        $batchInsert = [];
    }
}

// Insert remaining
if (count($batchInsert) > 0) {
    DB::table('krs')->insert($batchInsert);
}

echo "\n=== HASIL ===\n";
echo "Inserted: {$inserted}\n";
echo "Skipped (duplicate): {$skipped}\n";
echo "No mahasiswa: {$noMhs}\n";
echo "No mata kuliah: {$noMk}\n";
echo "No tahun akademik: {$noTa}\n";

// Verifikasi
echo "\n=== VERIFIKASI MHS 1003230001 ===\n";
$mhs = DB::table('mahasiswa')->where('nim', '1003230001')->first();
if ($mhs) {
    $semesters = [2 => '2025 Ganjil', 11 => '2024 Genap', 10 => '2024 Ganjil'];
    foreach ($semesters as $taId => $taName) {
        $krs = DB::table('krs as k')
            ->join('jadwal_kuliah as j', 'k.jadwal_kuliah_id', '=', 'j.id')
            ->join('mata_kuliah as mk', 'j.mata_kuliah_id', '=', 'mk.id')
            ->where('k.mahasiswa_id', $mhs->id)
            ->where('k.tahun_akademik_id', $taId)
            ->select('mk.kode', 'mk.nama')
            ->get();
        
        echo "\n{$taName} (ID:{$taId}):\n";
        foreach ($krs as $k) {
            echo "  {$k->kode} | {$k->nama}\n";
        }
        echo "Total: " . $krs->count() . " MK\n";
    }
}

$source->close();
echo "\nDone!\n";
