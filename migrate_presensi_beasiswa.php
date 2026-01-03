<?php
/**
 * Migrasi Presensi/Absensi Mahasiswa dan Beasiswa dari source database
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

echo "=== Migrasi Presensi & Beasiswa ===\n\n";

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

// KRS map (mahasiswa_id-jadwal_kuliah_id-tahun_akademik_id => id)
$krsMap = [];
$krsRes = DB::table('krs')->select('id', 'mahasiswa_id', 'jadwal_kuliah_id', 'tahun_akademik_id')->get();
foreach ($krsRes as $k) {
    $key = $k->mahasiswa_id . '-' . $k->jadwal_kuliah_id . '-' . $k->tahun_akademik_id;
    $krsMap[$key] = $k->id;
}
echo "  KRS: " . count($krsMap) . "\n";

// Jadwal map (kode_mk-tahun-semester-kelas => id)
$jadwalMap = [];
$jadwalRes = DB::table('jadwal_kuliah as j')
    ->join('mata_kuliah as mk', 'j.mata_kuliah_id', '=', 'mk.id')
    ->join('tahun_akademik as ta', 'j.tahun_akademik_id', '=', 'ta.id')
    ->select('j.id', 'mk.kode', 'ta.tahun', 'ta.semester', 'j.kelas')
    ->get();
foreach ($jadwalRes as $j) {
    $sem = $j->semester == 'Ganjil' ? 1 : ($j->semester == 'Genap' ? 2 : 3);
    // Multiple key formats untuk matching
    $key1 = $j->kode . '-' . $j->tahun . '-' . $sem . '-' . $j->kelas;
    $key2 = $j->kode . '-' . $j->tahun . '-' . $sem; // without kelas
    $jadwalMap[$key1] = $j->id;
    if (!isset($jadwalMap[$key2])) {
        $jadwalMap[$key2] = $j->id;
    }
}
echo "  Jadwal: " . count($jadwalMap) . "\n";

// Beasiswa map (kode => id)
$beasiswaMap = [];
$beasiswaRes = DB::table('beasiswa')->select('id', 'kode')->get();
foreach ($beasiswaRes as $b) {
    $beasiswaMap[$b->kode] = $b->id;
}
echo "  Beasiswa: " . count($beasiswaMap) . "\n";

echo "\n";

// ============================================
// 1. MIGRASI PENERIMA BEASISWA
// ============================================
echo "=== 1. Migrasi Penerima Beasiswa ===\n";

$beasiswaQuery = "SELECT * FROM mahasiswa_beasiswaoperator ORDER BY TAHUN, SEMESTER";
$beasiswaResult = $source->query($beasiswaQuery);
$beasiswaCount = 0;
$beasiswaSkip = 0;

while ($row = $beasiswaResult->fetch_assoc()) {
    $nim = $row['IDMAHASISWA'];
    $tahun = $row['TAHUN'];
    $semester = $row['SEMESTER'];
    $jenisId = $row['IDJENIS'];
    
    // Find mahasiswa
    if (!isset($mahasiswaMap[$nim])) {
        $beasiswaSkip++;
        continue;
    }
    $mahasiswaId = $mahasiswaMap[$nim];
    
    // Find tahun akademik
    $taKey = $tahun . '-' . $semester;
    if (!isset($taMap[$taKey])) {
        $beasiswaSkip++;
        continue;
    }
    $taId = $taMap[$taKey];
    
    // Find beasiswa
    if (!isset($beasiswaMap[$jenisId])) {
        $beasiswaSkip++;
        continue;
    }
    $beasiswaId = $beasiswaMap[$jenisId];
    
    // Check if already exists
    $exists = DB::table('penerima_beasiswa')
        ->where('mahasiswa_id', $mahasiswaId)
        ->where('beasiswa_id', $beasiswaId)
        ->where('tahun_akademik_id', $taId)
        ->exists();
    
    if (!$exists) {
        DB::table('penerima_beasiswa')->insert([
            'mahasiswa_id' => $mahasiswaId,
            'beasiswa_id' => $beasiswaId,
            'tahun_akademik_id' => $taId,
            'status' => 'Disetujui',
            'tanggal_mulai' => $tahun . '-' . ($semester == 1 ? '09' : '02') . '-01',
            'tanggal_selesai' => $tahun . '-' . ($semester == 1 ? '12' : '07') . '-31',
            'keterangan' => 'Migrasi dari sistem lama',
            'created_at' => $row['DATECREATED'] ?? now(),
            'updated_at' => $row['LASTUPDATE'] ?? now(),
        ]);
        $beasiswaCount++;
    }
}

echo "  Inserted: $beasiswaCount\n";
echo "  Skipped: $beasiswaSkip\n\n";

// ============================================
// 2. MIGRASI PRESENSI/ABSENSI MAHASISWA
// ============================================
echo "=== 2. Migrasi Presensi Mahasiswa ===\n";

// Hapus data absensi lama yang dari seeder
DB::table('absensi')->truncate();
echo "  Truncated existing absensi data\n";

// Status mapping
$statusMap = [
    'H' => 'Hadir',
    'I' => 'Izin',
    'S' => 'Sakit',
    'A' => 'Alpha',
    'T' => 'Alpha', // Tidak hadir
];

$presensiQuery = "SELECT * FROM kelaskuliah_presensimahasiswa ORDER BY TAHUN, SEMESTER, TANGGAL";
$presensiResult = $source->query($presensiQuery);

$presensiCount = 0;
$presensiSkip = 0;
$batch = [];
$batchSize = 500;

while ($row = $presensiResult->fetch_assoc()) {
    $nim = $row['IDMAHASISWA'];
    $kodeMk = $row['IDMAKUL'];
    $tahun = $row['TAHUN'];
    $semester = $row['SEMESTER'];
    $kelas = $row['IDKELAS'];
    $tanggal = $row['TANGGAL'];
    $pertemuan = $row['IDPERTEMUAN'] ?: 1;
    $status = $statusMap[$row['PRESENSI']] ?? 'Alpha';
    
    // Find mahasiswa
    if (!isset($mahasiswaMap[$nim])) {
        $presensiSkip++;
        continue;
    }
    $mahasiswaId = $mahasiswaMap[$nim];
    
    // Find tahun akademik
    $taKey = $tahun . '-' . $semester;
    if (!isset($taMap[$taKey])) {
        $presensiSkip++;
        continue;
    }
    $taId = $taMap[$taKey];
    
    // Find jadwal
    $jadwalKey1 = $kodeMk . '-' . $tahun . '-' . $semester . '-' . $kelas;
    $jadwalKey2 = $kodeMk . '-' . $tahun . '-' . $semester;
    $jadwalId = $jadwalMap[$jadwalKey1] ?? $jadwalMap[$jadwalKey2] ?? null;
    
    if (!$jadwalId) {
        $presensiSkip++;
        continue;
    }
    
    // Find KRS
    $krsKey = $mahasiswaId . '-' . $jadwalId . '-' . $taId;
    if (!isset($krsMap[$krsKey])) {
        $presensiSkip++;
        continue;
    }
    $krsId = $krsMap[$krsKey];
    
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
        $presensiCount += count($batch);
        $batch = [];
        echo "  Progress: $presensiCount inserted...\r";
    }
}

// Insert remaining
if (count($batch) > 0) {
    DB::table('absensi')->insert($batch);
    $presensiCount += count($batch);
}

echo "\n  Inserted: $presensiCount\n";
echo "  Skipped: $presensiSkip\n";

$source->close();

echo "\n=== Migrasi Selesai ===\n";
echo "Penerima Beasiswa: $beasiswaCount\n";
echo "Presensi/Absensi: $presensiCount\n";
