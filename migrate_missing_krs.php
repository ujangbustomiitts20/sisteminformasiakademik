<?php
/**
 * Migrasi KRS yang missing dari database lama
 * Untuk nilai yang ada tapi KRS-nya tidak ada di krss,
 * kita buat KRS berdasarkan data nilai + jadwal kuliah
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

echo "=== MIGRASI KRS YANG MISSING ===\n\n";

// 1. Build mapping mahasiswa
echo "1. Building mahasiswa map...\n";
$mhsMap = []; // nim => local_id
$mhs = DB::table('mahasiswa')->select('id', 'nim')->get();
foreach ($mhs as $m) {
    $mhsMap[$m->nim] = $m->id;
}
echo "   - " . count($mhsMap) . " mahasiswa\n";

// 2. Build mapping MK
echo "2. Building MK map...\n";
$mkMap = []; // kode => local_id
$mkNamaMap = []; // nama => local_id (fallback)
$mks = DB::table('mata_kuliah')->select('id', 'kode', 'nama')->get();
foreach ($mks as $mk) {
    $mkMap[$mk->kode] = $mk->id;
    $mkNamaMap[strtolower(trim($mk->nama))] = $mk->id;
}
echo "   - " . count($mkMap) . " MK\n";

// 3. Build source MK nama map
echo "3. Building source MK nama map...\n";
$sourceMkNama = []; // kode => nama
$result = $source->query("SELECT ID, NAMA FROM makul");
while ($row = $result->fetch_assoc()) {
    $sourceMkNama[$row['ID']] = trim($row['NAMA']);
}

// 4. Build mapping jadwal kuliah lokal
echo "4. Building jadwal kuliah map...\n";
$jadwalMap = []; // "mk_id-ta_id" => jadwal_id
$jadwals = DB::table('jadwal_kuliah as j')
    ->join('tahun_akademik as ta', 'j.tahun_akademik_id', '=', 'ta.id')
    ->select('j.id', 'j.mata_kuliah_id', 'ta.id as ta_id', 'ta.tahun', 'ta.semester')
    ->get();
foreach ($jadwals as $j) {
    $key = "{$j->mata_kuliah_id}-{$j->ta_id}";
    $jadwalMap[$key] = $j->id;
}
echo "   - " . count($jadwalMap) . " jadwal\n";

// 5. Build tahun akademik map
echo "5. Building tahun akademik map...\n";
$taMap = []; // "2025-1" => ta_id
$tas = DB::table('tahun_akademik')->get();
foreach ($tas as $ta) {
    $semNum = match($ta->semester) {
        'Ganjil' => 1,
        'Genap' => 2,
        'Pendek' => 4,
        default => 0
    };
    $taMap["{$ta->tahun}{$semNum}"] = $ta->id;
}
echo "   - " . count($taMap) . " tahun akademik\n";

// 6. Get existing KRS
echo "6. Getting existing KRS...\n";
$existingKrs = [];
$krs = DB::table('krs as k')
    ->join('jadwal_kuliah as j', 'k.jadwal_kuliah_id', '=', 'j.id')
    ->select('k.mahasiswa_id', 'j.mata_kuliah_id', 'k.tahun_akademik_id')
    ->get();
foreach ($krs as $k) {
    $key = "{$k->mahasiswa_id}-{$k->mata_kuliah_id}-{$k->tahun_akademik_id}";
    $existingKrs[$key] = true;
}
echo "   - " . count($existingKrs) . " existing KRS\n";

// 7. Query nilai tanpa KRS di source
echo "\n7. Querying nilai tanpa KRS...\n";
$result = $source->query("
    SELECT DISTINCT n.IDMAHASISWA, n.IDMAKUL, n.TAHUN, n.SEMESTER
    FROM nilai n
    LEFT JOIN krss k ON n.IDMAHASISWA = k.IDMAHASISWA 
        AND n.IDMAKUL = k.IDMAKUL 
        AND n.TAHUN = k.TAHUN 
        AND n.SEMESTER = k.SEMESTER
    WHERE k.IDMAHASISWA IS NULL
");

$totalMissing = $result->num_rows;
echo "   - Found $totalMissing nilai tanpa KRS\n";

// 8. Create missing KRS
echo "\n8. Creating missing KRS...\n";
$created = 0;
$skipped = 0;
$noMhs = 0;
$noMk = 0;
$noTa = 0;
$noJadwal = 0;
$alreadyExists = 0;

while ($row = $result->fetch_assoc()) {
    // Get mahasiswa
    $mhsId = $mhsMap[$row['IDMAHASISWA']] ?? null;
    if (!$mhsId) {
        $noMhs++;
        continue;
    }
    
    // Get MK - try direct first, then by nama
    $mkId = $mkMap[$row['IDMAKUL']] ?? null;
    if (!$mkId) {
        $mkNama = $sourceMkNama[$row['IDMAKUL']] ?? null;
        if ($mkNama) {
            $mkId = $mkNamaMap[strtolower(trim($mkNama))] ?? null;
        }
    }
    if (!$mkId) {
        $noMk++;
        continue;
    }
    
    // Get tahun akademik
    $taKey = "{$row['TAHUN']}{$row['SEMESTER']}";
    $taId = $taMap[$taKey] ?? null;
    if (!$taId) {
        $noTa++;
        continue;
    }
    
    // Get jadwal
    $jadwalKey = "{$mkId}-{$taId}";
    $jadwalId = $jadwalMap[$jadwalKey] ?? null;
    if (!$jadwalId) {
        $noJadwal++;
        continue;
    }
    
    // Check if already exists
    $krsKey = "{$mhsId}-{$mkId}-{$taId}";
    if (isset($existingKrs[$krsKey])) {
        $alreadyExists++;
        continue;
    }
    
    // Create KRS
    DB::table('krs')->insert([
        'mahasiswa_id' => $mhsId,
        'jadwal_kuliah_id' => $jadwalId,
        'tahun_akademik_id' => $taId,
        'status' => 'disetujui',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    $existingKrs[$krsKey] = true;
    $created++;
    
    if ($created % 100 == 0) {
        echo "   Created: $created\r";
    }
}

echo "\n\n=== HASIL ===\n";
echo "KRS Created: $created\n";
echo "Already exists: $alreadyExists\n";
echo "Skipped (no mahasiswa): $noMhs\n";
echo "Skipped (no MK): $noMk\n";
echo "Skipped (no tahun akademik): $noTa\n";
echo "Skipped (no jadwal): $noJadwal\n";

$source->close();
echo "\nDone!\n";
