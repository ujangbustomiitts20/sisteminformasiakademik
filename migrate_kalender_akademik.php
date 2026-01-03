<?php
/**
 * Script Migrasi Kalender Akademik
 * Migrasi data dari database itts_sikad ke SIAKAD baru
 * 
 * Data yang dimigrasi:
 * 1. libur_akademik - Hari libur nasional & akademik
 * 2. waktuakademik - Periode perkuliahan semester
 * 3. periodeujian - Periode UTS/UAS
 * 4. waktukrs - Periode pengisian KRS
 * 
 * Usage: php migrate_kalender_akademik.php
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\KalenderAkademik;
use App\Models\TahunAkademik;

echo "===========================================\n";
echo "  MIGRASI KALENDER AKADEMIK\n";
echo "===========================================\n\n";

// Koneksi ke database sumber
$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
$source->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);

if ($source->connect_error) {
    die("Koneksi database sumber gagal: " . $source->connect_error . "\n");
}
echo "✓ Koneksi database sumber berhasil\n\n";

// Build tahun akademik map
$tahunAkademikMap = [];
$tahunAkademiks = TahunAkademik::all();
foreach ($tahunAkademiks as $ta) {
    $key = $ta->tahun . '-' . $ta->semester;
    $tahunAkademikMap[$key] = $ta->id;
}
echo "✓ Tahun Akademik Map: " . count($tahunAkademikMap) . " records\n\n";

// Warna untuk jenis event
$jenisWarna = [
    'libur' => '#dc3545',     // merah
    'akademik' => '#007bff',  // biru
    'ujian' => '#ffc107',     // kuning
    'pendaftaran' => '#28a745', // hijau
    'lainnya' => '#6c757d'    // abu
];

$totalMigrated = 0;
$totalSkipped = 0;

// ===============================================
// 1. MIGRASI LIBUR AKADEMIK
// ===============================================
echo "1. Migrasi Libur Akademik...\n";
echo "-------------------------------------------\n";

$result = $source->query("
    SELECT * FROM libur_akademik 
    WHERE TANGGAL >= '2024-01-01'
    ORDER BY TANGGAL ASC
");

$liburCount = 0;
$liburSkipped = 0;

while ($row = $result->fetch_assoc()) {
    $tanggalMulai = $row['TANGGAL'];
    $tanggalSelesai = ($row['TANGGAL1'] != '0000-00-00' && $row['TANGGAL1'] != null) 
        ? $row['TANGGAL1'] 
        : (($row['TANGGAL2'] != '0000-00-00' && $row['TANGGAL2'] != null) ? $row['TANGGAL2'] : null);
    
    // Tentukan tahun akademik berdasarkan tanggal
    $tahunAkademikId = null;
    $tahun = date('Y', strtotime($tanggalMulai));
    $bulan = date('n', strtotime($tanggalMulai));
    
    // Semester Ganjil: Sep - Feb, Genap: Mar - Agu
    if ($bulan >= 9) {
        $semester = 'Ganjil';
        $tahunAkademik = $tahun;
    } elseif ($bulan >= 3) {
        $semester = 'Genap';
        $tahunAkademik = $tahun - 1;
    } else {
        $semester = 'Ganjil';
        $tahunAkademik = $tahun - 1;
    }
    
    $key = $tahunAkademik . '-' . $semester;
    if (isset($tahunAkademikMap[$key])) {
        $tahunAkademikId = $tahunAkademikMap[$key];
    }
    
    // Cek duplikasi
    $exists = KalenderAkademik::where('judul', $row['NAMA'])
        ->whereDate('tanggal_mulai', $tanggalMulai)
        ->exists();
    
    if ($exists) {
        $liburSkipped++;
        continue;
    }
    
    KalenderAkademik::create([
        'tahun_akademik_id' => $tahunAkademikId,
        'judul' => $row['NAMA'],
        'deskripsi' => 'Hari Libur - ' . ($row['JENISLIBUR'] == 'T' ? 'Tanggal Merah' : 'Hari Libur'),
        'tanggal_mulai' => $tanggalMulai,
        'tanggal_selesai' => $tanggalSelesai,
        'warna' => $jenisWarna['libur'],
        'jenis' => 'libur',
        'is_active' => true
    ]);
    
    $liburCount++;
}

echo "   - Berhasil: $liburCount\n";
echo "   - Skip (duplikat): $liburSkipped\n\n";
$totalMigrated += $liburCount;
$totalSkipped += $liburSkipped;

// ===============================================
// 2. MIGRASI WAKTU AKADEMIK (Periode Perkuliahan)
// ===============================================
echo "2. Migrasi Waktu Akademik (Periode Perkuliahan)...\n";
echo "-------------------------------------------\n";

$result = $source->query("
    SELECT * FROM waktuakademik 
    WHERE TAHUN >= 2024
    ORDER BY TAHUN DESC, SEMESTER DESC
");

$waktuCount = 0;
$waktuSkipped = 0;

while ($row = $result->fetch_assoc()) {
    $tahun = $row['TAHUN'];
    $semesterNum = $row['SEMESTER'];
    
    // Konversi semester number ke nama
    $semester = match((int)$semesterNum) {
        1 => 'Ganjil',
        2 => 'Genap',
        3, 4 => 'Pendek',
        default => 'Ganjil'
    };
    
    $key = $tahun . '-' . $semester;
    $tahunAkademikId = $tahunAkademikMap[$key] ?? null;
    
    $judul = "Periode Perkuliahan Semester " . $semester . " " . $tahun . "/" . ($tahun + 1);
    
    // Cek duplikasi
    $exists = KalenderAkademik::where('judul', $judul)->exists();
    
    if ($exists) {
        $waktuSkipped++;
        continue;
    }
    
    KalenderAkademik::create([
        'tahun_akademik_id' => $tahunAkademikId,
        'judul' => $judul,
        'deskripsi' => 'Periode perkuliahan aktif untuk semester ' . $semester . ' tahun akademik ' . $tahun . '/' . ($tahun + 1),
        'tanggal_mulai' => $row['TANGGALMULAI'],
        'tanggal_selesai' => $row['TANGGALSELESAI'],
        'warna' => $jenisWarna['akademik'],
        'jenis' => 'akademik',
        'is_active' => true
    ]);
    
    $waktuCount++;
    echo "   + $judul ({$row['TANGGALMULAI']} - {$row['TANGGALSELESAI']})\n";
}

echo "   - Berhasil: $waktuCount\n";
echo "   - Skip (duplikat): $waktuSkipped\n\n";
$totalMigrated += $waktuCount;
$totalSkipped += $waktuSkipped;

// ===============================================
// 3. MIGRASI PERIODE UJIAN (UTS/UAS)
// ===============================================
echo "3. Migrasi Periode Ujian (UTS/UAS)...\n";
echo "-------------------------------------------\n";

$result = $source->query("
    SELECT * FROM periodeujian 
    WHERE TAHUN >= 2024
    ORDER BY TAHUN DESC, SEMESTER DESC
");

$ujianCount = 0;
$ujianSkipped = 0;

while ($row = $result->fetch_assoc()) {
    $tahun = $row['TAHUN'];
    $semesterNum = $row['SEMESTER'];
    $jenisUjian = $row['JENIS']; // UTS atau UAS
    
    // Konversi semester number ke nama
    $semester = match((int)$semesterNum) {
        1 => 'Ganjil',
        2 => 'Genap',
        3, 4 => 'Pendek',
        default => 'Ganjil'
    };
    
    $key = $tahun . '-' . $semester;
    $tahunAkademikId = $tahunAkademikMap[$key] ?? null;
    
    $judul = "Periode " . strtoupper($jenisUjian) . " Semester " . $semester . " " . $tahun . "/" . ($tahun + 1);
    
    // Cek duplikasi
    $exists = KalenderAkademik::where('judul', $judul)->exists();
    
    if ($exists) {
        $ujianSkipped++;
        continue;
    }
    
    KalenderAkademik::create([
        'tahun_akademik_id' => $tahunAkademikId,
        'judul' => $judul,
        'deskripsi' => 'Periode ujian ' . strtoupper($jenisUjian) . ' (Ujian ' . ($jenisUjian == 'UTS' ? 'Tengah' : 'Akhir') . ' Semester)',
        'tanggal_mulai' => $row['TANGGALMULAI'],
        'tanggal_selesai' => $row['TANGGALSELESAI'],
        'warna' => $jenisWarna['ujian'],
        'jenis' => 'ujian',
        'is_active' => true
    ]);
    
    $ujianCount++;
    echo "   + $judul ({$row['TANGGALMULAI']} - {$row['TANGGALSELESAI']})\n";
}

echo "   - Berhasil: $ujianCount\n";
echo "   - Skip (duplikat): $ujianSkipped\n\n";
$totalMigrated += $ujianCount;
$totalSkipped += $ujianSkipped;

// ===============================================
// 4. MIGRASI WAKTU KRS (Periode Pengisian KRS)
// ===============================================
echo "4. Migrasi Waktu KRS (Periode Pengisian KRS)...\n";
echo "-------------------------------------------\n";

$result = $source->query("
    SELECT * FROM waktukrs 
    WHERE TAHUN >= 2024
    ORDER BY TAHUN DESC, SEMESTER DESC
");

$krsCount = 0;
$krsSkipped = 0;

while ($row = $result->fetch_assoc()) {
    $tahun = $row['TAHUN'];
    $semesterNum = $row['SEMESTER'];
    
    // Konversi semester number ke nama
    $semester = match((int)$semesterNum) {
        1 => 'Ganjil',
        2 => 'Genap',
        3, 4 => 'Pendek',
        default => 'Ganjil'
    };
    
    $key = $tahun . '-' . $semester;
    $tahunAkademikId = $tahunAkademikMap[$key] ?? null;
    
    $judul = "Pengisian KRS Semester " . $semester . " " . $tahun . "/" . ($tahun + 1);
    
    // Cek duplikasi
    $exists = KalenderAkademik::where('judul', $judul)->exists();
    
    if ($exists) {
        $krsSkipped++;
        continue;
    }
    
    KalenderAkademik::create([
        'tahun_akademik_id' => $tahunAkademikId,
        'judul' => $judul,
        'deskripsi' => 'Periode pengisian Kartu Rencana Studi (KRS) untuk semester ' . $semester,
        'tanggal_mulai' => $row['TANGGALMULAI'],
        'tanggal_selesai' => $row['TANGGALSELESAI'],
        'warna' => $jenisWarna['pendaftaran'],
        'jenis' => 'pendaftaran',
        'is_active' => true
    ]);
    
    $krsCount++;
    echo "   + $judul ({$row['TANGGALMULAI']} - {$row['TANGGALSELESAI']})\n";
}

echo "   - Berhasil: $krsCount\n";
echo "   - Skip (duplikat): $krsSkipped\n\n";
$totalMigrated += $krsCount;
$totalSkipped += $krsSkipped;

// ===============================================
// 5. MIGRASI WAKTU KRS KEDUA (Perubahan KRS)
// ===============================================
echo "5. Migrasi Waktu Perubahan KRS...\n";
echo "-------------------------------------------\n";

$result = $source->query("
    SELECT * FROM waktukrs2 
    WHERE TAHUN >= 2024
    ORDER BY TAHUN DESC, SEMESTER DESC
");

$krs2Count = 0;
$krs2Skipped = 0;

while ($row = $result->fetch_assoc()) {
    $tahun = $row['TAHUN'];
    $semesterNum = $row['SEMESTER'];
    
    // Konversi semester number ke nama
    $semester = match((int)$semesterNum) {
        1 => 'Ganjil',
        2 => 'Genap',
        3, 4 => 'Pendek',
        default => 'Ganjil'
    };
    
    $key = $tahun . '-' . $semester;
    $tahunAkademikId = $tahunAkademikMap[$key] ?? null;
    
    $judul = "Perubahan KRS Semester " . $semester . " " . $tahun . "/" . ($tahun + 1);
    
    // Cek duplikasi
    $exists = KalenderAkademik::where('judul', $judul)->exists();
    
    if ($exists) {
        $krs2Skipped++;
        continue;
    }
    
    KalenderAkademik::create([
        'tahun_akademik_id' => $tahunAkademikId,
        'judul' => $judul,
        'deskripsi' => 'Periode perubahan/revisi KRS untuk semester ' . $semester,
        'tanggal_mulai' => $row['TANGGALMULAI'],
        'tanggal_selesai' => $row['TANGGALSELESAI'],
        'warna' => $jenisWarna['pendaftaran'],
        'jenis' => 'pendaftaran',
        'is_active' => true
    ]);
    
    $krs2Count++;
    echo "   + $judul ({$row['TANGGALMULAI']} - {$row['TANGGALSELESAI']})\n";
}

echo "   - Berhasil: $krs2Count\n";
echo "   - Skip (duplikat): $krs2Skipped\n\n";
$totalMigrated += $krs2Count;
$totalSkipped += $krs2Skipped;

// ===============================================
// SUMMARY
// ===============================================
echo "===========================================\n";
echo "  SUMMARY MIGRASI\n";
echo "===========================================\n";
echo "Total berhasil dimigrasi : $totalMigrated records\n";
echo "Total dilewati (duplikat): $totalSkipped records\n";
echo "===========================================\n";
echo "Migrasi selesai!\n";

$source->close();
