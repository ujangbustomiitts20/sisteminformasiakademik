<?php
/**
 * Migrate Tugas Akhir & Magang Data from Legacy Database
 * Source: mahasiswa_aktivitas, mahasiswa_aktivitas_peserta, 
 *         mahasiswa_aktivitas_dosenpembimbing, mahasiswa_aktivitas_dosenpenguji
 * Target: tugas_akhir, bimbingan_ta, sidang_ta
 * 
 * JENIS aktivitas:
 * - SKR: Skripsi (25 records)
 * - TAK: Tugas Akhir (65 records)  
 * - SPI: Sertifikat Profesional/Industri (9 records)
 * - MPK: Magang/Praktek Kerja (5 records)
 * - KOM: Kompetisi (1 record)
 */

$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
if ($source->connect_error) {
    die('Connection failed: ' . $source->connect_error);
}
$source->set_charset('utf8mb4');

// Load Laravel
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== MIGRASI DATA TUGAS AKHIR & MAGANG ===\n";
echo "Source: mahasiswa_aktivitas @ 192.168.120.121\n";
echo str_repeat("=", 60) . "\n\n";

// Build maps
$mahasiswaMap = [];
$mahasiswas = DB::table('mahasiswa')->select('id', 'nim')->get();
foreach ($mahasiswas as $mhs) {
    $mahasiswaMap[$mhs->nim] = $mhs->id;
}
echo "Mahasiswa map: " . count($mahasiswaMap) . "\n";

$dosenMap = [];
$dosens = DB::table('dosen')->select('id', 'nidn')->get();
foreach ($dosens as $dosen) {
    if ($dosen->nidn) $dosenMap[$dosen->nidn] = $dosen->id;
}
echo "Dosen map: " . count($dosenMap) . "\n";

$prodiMap = [];
$prodis = DB::table('program_studi')->select('id', 'kode')->get();
foreach ($prodis as $prodi) {
    $prodiMap[$prodi->kode] = $prodi->id;
}
echo "Prodi map: " . count($prodiMap) . "\n";

// Build tahun akademik map
$tahunAkademikMap = [];
$tahunAkademiks = DB::table('tahun_akademik')->get();
foreach ($tahunAkademiks as $ta) {
    $sem = $ta->semester == 'Ganjil' ? 1 : ($ta->semester == 'Genap' ? 2 : 3);
    $key = $ta->tahun . '_' . $sem;
    $tahunAkademikMap[$key] = $ta->id;
}
echo "Tahun akademik map: " . count($tahunAkademikMap) . "\n\n";

// Function to generate nomor TA
function generateNoTA($counter) {
    return "TA/" . date('Y') . "/" . str_pad($counter, 5, '0', STR_PAD_LEFT);
}

// Step 1: Get all aktivitas with peserta and pembimbing
echo "=== STEP 1: MIGRASI TUGAS AKHIR (SKR & TAK) ===\n";

// Only migrate SKR (Skripsi) and TAK (Tugas Akhir)
$query = "SELECT a.*, p.IDMAHASISWA 
    FROM mahasiswa_aktivitas a
    JOIN mahasiswa_aktivitas_peserta p ON 
        a.IDPRODI = p.IDPRODI AND a.ID = p.ID AND a.TAHUN = p.TAHUN AND a.SEMESTER = p.SEMESTER
    WHERE a.JENIS IN ('SKR', 'TAK')
    ORDER BY a.DATECREATED";
$result = $source->query($query);

$taSuccess = 0;
$taSkipped = 0;
$taFailed = 0;
$taCounter = DB::table('tugas_akhir')->count() + 1;

// Map to track aktivitas -> tugas_akhir_id
$aktivitasToTAMap = [];

while ($row = $result->fetch_assoc()) {
    $nim = $row['IDMAHASISWA'];
    
    // Skip if mahasiswa not found
    if (!isset($mahasiswaMap[$nim])) {
        $taFailed++;
        continue;
    }
    
    $mahasiswaId = $mahasiswaMap[$nim];
    
    // Check if already exists (by mahasiswa and judul)
    $existing = DB::table('tugas_akhir')
        ->where('mahasiswa_id', $mahasiswaId)
        ->where('judul', $row['NAMA'])
        ->first();
    
    if ($existing) {
        $aktivitasToTAMap[$row['IDPRODI'] . '_' . $row['ID'] . '_' . $row['TAHUN'] . '_' . $row['SEMESTER']] = $existing->id;
        $taSkipped++;
        continue;
    }
    
    // Get tahun akademik
    $taKey = $row['TAHUN'] . '_' . $row['SEMESTER'];
    $tahunAkademikId = $tahunAkademikMap[$taKey] ?? null;
    
    // Get pembimbing
    $pembimbingQuery = "SELECT * FROM mahasiswa_aktivitas_dosenpembimbing 
        WHERE IDPRODI = '{$row['IDPRODI']}' AND ID = {$row['ID']} AND TAHUN = {$row['TAHUN']} AND SEMESTER = {$row['SEMESTER']}
        ORDER BY PEMBIMBINGKE";
    $pembimbingResult = $source->query($pembimbingQuery);
    
    $pembimbing1Id = null;
    $pembimbing2Id = null;
    while ($pb = $pembimbingResult->fetch_assoc()) {
        $dosenId = $dosenMap[$pb['IDDOSEN']] ?? null;
        if ($pb['PEMBIMBINGKE'] == 1 && !$pembimbing1Id) {
            $pembimbing1Id = $dosenId;
        } elseif ($pb['PEMBIMBINGKE'] == 2 && !$pembimbing2Id) {
            $pembimbing2Id = $dosenId;
        }
    }
    
    // Determine status based on kelulusan data
    $status = 'penelitian';
    
    // Check if graduated
    $lulusQuery = "SELECT * FROM kelulusan_mhs WHERE IDMAHASISWA = '$nim' AND STATUSKELUAR = 'L'";
    $lulusResult = $source->query($lulusQuery);
    if ($lulusResult->num_rows > 0) {
        $status = 'selesai';
    }
    
    $data = [
        'nomor_ta' => generateNoTA($taCounter),
        'mahasiswa_id' => $mahasiswaId,
        'tahun_akademik_id' => $tahunAkademikId,
        'judul' => mb_substr($row['NAMA'], 0, 191),
        'abstrak' => null,
        'bidang_kajian' => null,
        'latar_belakang' => $row['KET'] ? mb_substr($row['KET'], 0, 65535) : null,
        'pembimbing_1_id' => $pembimbing1Id,
        'pembimbing_2_id' => $pembimbing2Id,
        'status' => $status,
        'tanggal_pengajuan' => ($row['TANGGALMULAI'] && $row['TANGGALMULAI'] != '0000-00-00') 
            ? $row['TANGGALMULAI'] : null,
        'tanggal_lulus' => ($row['TANGGALSELESAI'] && $row['TANGGALSELESAI'] != '0000-00-00') 
            ? $row['TANGGALSELESAI'] : null,
        'created_at' => $row['DATECREATED'] ?: now(),
        'updated_at' => now(),
    ];
    
    try {
        $taId = DB::table('tugas_akhir')->insertGetId($data);
        $aktivitasToTAMap[$row['IDPRODI'] . '_' . $row['ID'] . '_' . $row['TAHUN'] . '_' . $row['SEMESTER']] = $taId;
        $taSuccess++;
        $taCounter++;
        
        $judul = mb_substr($row['NAMA'], 0, 50);
        echo "  + $nim: $judul... (ID: $taId)\n";
    } catch (Exception $e) {
        $taFailed++;
        echo "  ERROR $nim: " . $e->getMessage() . "\n";
    }
}

echo "\nTugas Akhir: $taSuccess added, $taSkipped skipped, $taFailed failed\n\n";

// Step 2: Migrate Sidang TA from penguji data
echo "=== STEP 2: MIGRASI SIDANG TA ===\n";

$sidangSuccess = 0;
$sidangSkipped = 0;
$sidangFailed = 0;

foreach ($aktivitasToTAMap as $key => $taId) {
    list($idprodi, $id, $tahun, $semester) = explode('_', $key);
    
    // Get penguji
    $pengujiQuery = "SELECT * FROM mahasiswa_aktivitas_dosenpenguji 
        WHERE IDPRODI = '$idprodi' AND ID = $id AND TAHUN = $tahun AND SEMESTER = $semester
        ORDER BY PENGUJIKE";
    $pengujiResult = $source->query($pengujiQuery);
    
    if ($pengujiResult->num_rows == 0) {
        continue; // No penguji = no sidang data
    }
    
    // Check if sidang already exists
    $existingSidang = DB::table('sidang_ta')->where('tugas_akhir_id', $taId)->first();
    if ($existingSidang) {
        $sidangSkipped++;
        continue;
    }
    
    $ketuaPengujiId = null;
    $penguji1Id = null;
    $penguji2Id = null;
    
    while ($pj = $pengujiResult->fetch_assoc()) {
        $dosenId = $dosenMap[$pj['IDDOSEN']] ?? null;
        if ($pj['KATEGORI'] == 'AA' || $pj['PENGUJIKE'] == 1) {
            // Ketua penguji or first penguji
            if (!$ketuaPengujiId) {
                $ketuaPengujiId = $dosenId;
            } elseif (!$penguji1Id) {
                $penguji1Id = $dosenId;
            }
        } elseif ($pj['KATEGORI'] == 'AB' || $pj['PENGUJIKE'] == 2) {
            if (!$penguji2Id) {
                $penguji2Id = $dosenId;
            }
        }
    }
    
    // Get nilai from peserta
    $nilaiQuery = "SELECT * FROM mahasiswa_aktivitas_peserta_nilai 
        WHERE IDPRODI = '$idprodi' AND ID = $id AND TAHUN = $tahun AND SEMESTER = $semester";
    $nilaiResult = $source->query($nilaiQuery);
    
    $nilaiAkhir = null;
    $grade = null;
    if ($nilaiResult && $row = $nilaiResult->fetch_assoc()) {
        $nilaiAkhir = $row['NILAI'] ?? null;
    }
    
    $sidangNo = "SDG/" . date('Y') . "/" . str_pad($sidangSuccess + 1, 5, '0', STR_PAD_LEFT);
    
    $sidangData = [
        'nomor_sidang' => $sidangNo,
        'tugas_akhir_id' => $taId,
        'tanggal' => null, // No exact sidang date in source
        'ketua_penguji_id' => $ketuaPengujiId,
        'penguji_1_id' => $penguji1Id,
        'penguji_2_id' => $penguji2Id,
        'status' => 'selesai',
        'nilai_akhir' => $nilaiAkhir,
        'grade' => $grade,
        'hasil' => 'lulus',
        'created_at' => now(),
        'updated_at' => now(),
    ];
    
    try {
        DB::table('sidang_ta')->insert($sidangData);
        $sidangSuccess++;
    } catch (Exception $e) {
        $sidangFailed++;
        echo "  ERROR sidang for TA $taId: " . $e->getMessage() . "\n";
    }
}

echo "Sidang TA: $sidangSuccess added, $sidangSkipped skipped, $sidangFailed failed\n\n";

// Summary
echo str_repeat("=", 60) . "\n";
echo "HASIL MIGRASI TUGAS AKHIR:\n";
echo "  Tugas Akhir:\n";
echo "    - Added: $taSuccess\n";
echo "    - Skipped: $taSkipped\n";
echo "    - Failed: $taFailed\n";
echo "  Sidang TA:\n";
echo "    - Added: $sidangSuccess\n";
echo "    - Skipped: $sidangSkipped\n";
echo "    - Failed: $sidangFailed\n";
echo "\nTotal tugas_akhir: " . DB::table('tugas_akhir')->count() . "\n";
echo "Total sidang_ta: " . DB::table('sidang_ta')->count() . "\n";

$source->close();
echo "\n=== MIGRASI SELESAI ===\n";
