<?php
/**
 * Fix Migrasi Presensi - Match berdasarkan mahasiswa dan MK
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Source database
$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
if ($source->connect_error) {
    die("Connection failed: " . $source->connect_error);
}
$source->set_charset('utf8');

echo "=== Fix Migrasi Presensi ===\n\n";

// Build maps
echo "Building maps...\n";

// Mahasiswa map (NIM => id)
$mahasiswaMap = [];
$mahasiswaRes = DB::table('mahasiswa')->select('id', 'nim')->get();
foreach ($mahasiswaRes as $m) {
    $mahasiswaMap[$m->nim] = $m->id;
}
echo "  Mahasiswa: " . count($mahasiswaMap) . "\n";

// Tahun Akademik map
$taMap = [];
$taRes = DB::table('tahun_akademik')->select('id', 'tahun', 'semester')->get();
foreach ($taRes as $ta) {
    $sem = $ta->semester == 'Ganjil' ? 1 : ($ta->semester == 'Genap' ? 2 : 3);
    $key = $ta->tahun . '-' . $sem;
    $taMap[$key] = $ta->id;
}
echo "  Tahun Akademik: " . count($taMap) . "\n";

// Build KRS map - (mahasiswa_id, tahun_akademik_id, kode_mk) => krs_id
$krsMap = [];
$krsRes = DB::table('krs as k')
    ->join('jadwal_kuliah as j', 'k.jadwal_kuliah_id', '=', 'j.id')
    ->join('mata_kuliah as mk', 'j.mata_kuliah_id', '=', 'mk.id')
    ->select('k.id', 'k.mahasiswa_id', 'k.tahun_akademik_id', 'mk.kode')
    ->get();
foreach ($krsRes as $k) {
    $key = $k->mahasiswa_id . '-' . $k->tahun_akademik_id . '-' . $k->kode;
    $krsMap[$key] = $k->id;
}
echo "  KRS (mhs-ta-mk): " . count($krsMap) . "\n\n";

// Status mapping
$statusMap = [
    'H' => 'Hadir',
    'I' => 'Izin',
    'S' => 'Sakit',
    'A' => 'Alpha',
    'T' => 'Alpha',
];

// Truncate absensi dan insert ulang
DB::table('absensi')->truncate();
echo "Truncated existing absensi\n\n";

// Process presensi
echo "Processing presensi...\n";

$presensiQuery = "SELECT * FROM kelaskuliah_presensimahasiswa ORDER BY TAHUN, SEMESTER, TANGGAL";
$presensiResult = $source->query($presensiQuery);

$count = 0;
$skip = 0;
$batch = [];
$batchSize = 500;

$skipReasons = [
    'mhs_not_found' => 0,
    'ta_not_found' => 0,
    'krs_not_found' => 0,
];

while ($row = $presensiResult->fetch_assoc()) {
    $nim = $row['IDMAHASISWA'];
    $kodeMk = $row['IDMAKUL'];
    $tahun = $row['TAHUN'];
    $semester = $row['SEMESTER'];
    $tanggal = $row['TANGGAL'];
    $pertemuan = $row['IDPERTEMUAN'] ?: 1;
    $status = $statusMap[$row['PRESENSI']] ?? 'Alpha';
    
    // Find mahasiswa
    if (!isset($mahasiswaMap[$nim])) {
        $skip++;
        $skipReasons['mhs_not_found']++;
        continue;
    }
    $mahasiswaId = $mahasiswaMap[$nim];
    
    // Find tahun akademik
    $taKey = $tahun . '-' . $semester;
    if (!isset($taMap[$taKey])) {
        $skip++;
        $skipReasons['ta_not_found']++;
        continue;
    }
    $taId = $taMap[$taKey];
    
    // Find KRS by mahasiswa + tahun_akademik + kode_mk
    $krsKey = $mahasiswaId . '-' . $taId . '-' . $kodeMk;
    if (!isset($krsMap[$krsKey])) {
        $skip++;
        $skipReasons['krs_not_found']++;
        continue;
    }
    $krsId = $krsMap[$krsKey];
    
    // Skip invalid dates
    if ($tanggal == '0000-00-00' || empty($tanggal)) {
        $skip++;
        continue;
    }
    
    $batch[] = [
        'krs_id' => $krsId,
        'pertemuan_id' => null,
        'tanggal' => $tanggal,
        'pertemuan' => $pertemuan,
        'status' => $status,
        'keterangan' => null,
        'materi' => null,
        'created_at' => $row['DATECREATED'] ?? now(),
        'updated_at' => $row['LASTUPDATE'] ?? now(),
    ];
    
    if (count($batch) >= $batchSize) {
        DB::table('absensi')->insert($batch);
        $count += count($batch);
        $batch = [];
        echo "  Progress: $count inserted...\r";
    }
}

// Insert remaining
if (count($batch) > 0) {
    DB::table('absensi')->insert($batch);
    $count += count($batch);
}

$source->close();

echo "\n\n=== Hasil ===\n";
echo "Inserted: $count\n";
echo "Skipped: $skip\n";
echo "  - Mahasiswa not found: " . $skipReasons['mhs_not_found'] . "\n";
echo "  - TA not found: " . $skipReasons['ta_not_found'] . "\n";
echo "  - KRS not found: " . $skipReasons['krs_not_found'] . "\n";

// Show sample
echo "\n=== Sample Absensi ===\n";
$samples = DB::table('absensi as a')
    ->join('krs as k', 'a.krs_id', '=', 'k.id')
    ->join('mahasiswa as m', 'k.mahasiswa_id', '=', 'm.id')
    ->join('jadwal_kuliah as j', 'k.jadwal_kuliah_id', '=', 'j.id')
    ->join('mata_kuliah as mk', 'j.mata_kuliah_id', '=', 'mk.id')
    ->select('m.nim', 'mk.kode', 'a.tanggal', 'a.pertemuan', 'a.status')
    ->limit(5)
    ->get();
foreach ($samples as $s) {
    echo sprintf("  %s | %s | %s | P%d | %s\n", $s->nim, $s->kode, $s->tanggal, $s->pertemuan, $s->status);
}
