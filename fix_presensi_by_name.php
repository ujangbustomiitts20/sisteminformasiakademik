<?php
/**
 * Fix Presensi Migration - Match by MK Name (cross-prodi)
 * 
 * Problem: Presensi dari source menggunakan kode IF (IF130, IF131, IF132)
 * tapi KRS TI 2023 menggunakan kode TI (TI130, TI131, TI132).
 * Nama MK sama, kode berbeda.
 * 
 * Solution: Match presensi ke KRS berdasarkan mahasiswa + nama MK
 */

$source = new mysqli("192.168.120.121", "root", "bismillaH", "itts_sikad");
$target = new PDO("mysql:host=192.168.200.99;dbname=siakad2025", "development", "Abcde!@#\$%");

echo "=== Fix Presensi Migration (Match by MK Name) ===\n\n";

// Build lookup maps
echo "Building lookup maps...\n";

// Mahasiswa lookup: NIM => id
$mahasiswaMap = [];
$stmt = $target->query("SELECT id, nim FROM mahasiswa");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $mahasiswaMap[$row['nim']] = $row['id'];
}
echo "  Mahasiswa: " . count($mahasiswaMap) . "\n";

// MK Name lookup from source: ID => NAMA
$mkNameSource = [];
$result = $source->query("SELECT ID, NAMA FROM makul");
while ($row = $result->fetch_assoc()) {
    $mkNameSource[$row['ID']] = trim($row['NAMA']);
}
echo "  MK Source: " . count($mkNameSource) . "\n";

// MK Name lookup from target: nama => [kode1, kode2, ...]
$mkByName = [];
$stmt = $target->query("SELECT kode, nama FROM mata_kuliah");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $nama = trim($row['nama']);
    if (!isset($mkByName[$nama])) {
        $mkByName[$nama] = [];
    }
    $mkByName[$nama][] = $row['kode'];
}
echo "  MK Target (by name): " . count($mkByName) . "\n";

// KRS lookup: mahasiswa_id => [kode_mk => krs_id]
$krsMap = [];
$stmt = $target->query("
    SELECT k.id as krs_id, k.mahasiswa_id, mk.kode as kode_mk, mk.nama as nama_mk
    FROM krs k
    JOIN jadwal_kuliah j ON k.jadwal_kuliah_id = j.id
    JOIN mata_kuliah mk ON j.mata_kuliah_id = mk.id
");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $mhsId = $row['mahasiswa_id'];
    if (!isset($krsMap[$mhsId])) {
        $krsMap[$mhsId] = ['by_kode' => [], 'by_nama' => []];
    }
    $krsMap[$mhsId]['by_kode'][$row['kode_mk']] = $row['krs_id'];
    $krsMap[$mhsId]['by_nama'][trim($row['nama_mk'])] = $row['krs_id'];
}
echo "  KRS Mahasiswa: " . count($krsMap) . "\n";

// Status mapping
$statusMap = ['H' => 'Hadir', 'I' => 'Izin', 'S' => 'Sakit', 'A' => 'Alpha', 'T' => 'Alpha'];

// Get existing absensi to avoid duplicates
echo "  Loading existing absensi...\n";
$existingAbsensi = [];
$stmt = $target->query("SELECT krs_id, tanggal, pertemuan FROM absensi");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $key = $row['krs_id'] . '-' . $row['tanggal'] . '-' . $row['pertemuan'];
    $existingAbsensi[$key] = true;
}
echo "  Existing absensi: " . count($existingAbsensi) . "\n\n";

// Fetch all presensi from source
echo "Fetching presensi from source...\n";
$result = $source->query("
    SELECT IDMAHASISWA, IDMAKUL, TAHUN, SEMESTER, TANGGAL, PRESENSI, IDPERTEMUAN, IDKELAS
    FROM kelaskuliah_presensimahasiswa
    WHERE TANGGAL != '0000-00-00' AND TANGGAL IS NOT NULL
    ORDER BY IDMAHASISWA, TANGGAL
");
$total = $result->num_rows;
echo "  Total presensi: $total\n\n";

// Process
$inserted = 0;
$skipped = ['mhs_not_found' => 0, 'krs_not_found' => 0, 'duplicate' => 0, 'mk_name_not_found' => 0];
$matchedByName = 0;
$processed = 0;
$batchSize = 1000;
$batchData = [];

// Prepare insert statement
$insertSql = "INSERT INTO absensi (krs_id, pertemuan, tanggal, status, created_at, updated_at) VALUES ";

while ($row = $result->fetch_assoc()) {
    $processed++;
    
    // Find mahasiswa
    $nim = $row['IDMAHASISWA'];
    if (!isset($mahasiswaMap[$nim])) {
        $skipped['mhs_not_found']++;
        continue;
    }
    $mahasiswaId = $mahasiswaMap[$nim];
    
    // Skip if no KRS at all
    if (!isset($krsMap[$mahasiswaId])) {
        $skipped['krs_not_found']++;
        continue;
    }
    
    // Find KRS - first try by kode_mk
    $kodeMk = $row['IDMAKUL'];
    $krsId = null;
    
    if (isset($krsMap[$mahasiswaId]['by_kode'][$kodeMk])) {
        $krsId = $krsMap[$mahasiswaId]['by_kode'][$kodeMk];
    } else {
        // Try by nama MK
        $namaMk = $mkNameSource[$kodeMk] ?? null;
        if ($namaMk && isset($krsMap[$mahasiswaId]['by_nama'][$namaMk])) {
            $krsId = $krsMap[$mahasiswaId]['by_nama'][$namaMk];
            $matchedByName++;
        }
    }
    
    if (!$krsId) {
        $skipped['krs_not_found']++;
        continue;
    }
    
    // Check duplicate
    $tanggal = $row['TANGGAL'];
    $pertemuan = $row['IDPERTEMUAN'] ?? 1;
    $dupKey = $krsId . '-' . $tanggal . '-' . $pertemuan;
    
    if (isset($existingAbsensi[$dupKey])) {
        $skipped['duplicate']++;
        continue;
    }
    $existingAbsensi[$dupKey] = true; // Mark as processed
    
    // Map status
    $status = $statusMap[$row['PRESENSI']] ?? 'Alpha';
    $now = date('Y-m-d H:i:s');
    
    $batchData[] = sprintf(
        "(%d, %d, '%s', '%s', '%s', '%s')",
        $krsId,
        $pertemuan,
        $tanggal,
        $status,
        $now,
        $now
    );
    
    // Execute batch
    if (count($batchData) >= $batchSize) {
        $target->exec($insertSql . implode(',', $batchData));
        $inserted += count($batchData);
        $batchData = [];
        echo "\r  Processed: $processed / $total (Inserted: $inserted, Matched by name: $matchedByName)    ";
    }
}

// Insert remaining
if (!empty($batchData)) {
    $target->exec($insertSql . implode(',', $batchData));
    $inserted += count($batchData);
}

echo "\n\n=== Hasil ===\n";
echo "Inserted: $inserted\n";
echo "Matched by MK name: $matchedByName\n";
echo "Skipped: " . array_sum($skipped) . "\n";
echo "  - Mahasiswa not found: {$skipped['mhs_not_found']}\n";
echo "  - KRS not found: {$skipped['krs_not_found']}\n";
echo "  - Duplicate: {$skipped['duplicate']}\n";

// Verify sample - TI 2023
echo "\n=== Verifikasi Sample TI 2023 ===\n";
$stmt = $target->query("
    SELECT m.nim, mk.kode, ta.tahun, ta.semester, COUNT(a.id) as total_absensi
    FROM absensi a
    JOIN krs k ON a.krs_id = k.id
    JOIN mahasiswa m ON k.mahasiswa_id = m.id
    JOIN jadwal_kuliah j ON k.jadwal_kuliah_id = j.id
    JOIN mata_kuliah mk ON j.mata_kuliah_id = mk.id
    JOIN tahun_akademik ta ON k.tahun_akademik_id = ta.id
    WHERE m.nim LIKE '1002230%' AND mk.kode LIKE 'TI13%'
    GROUP BY m.nim, mk.kode, ta.tahun, ta.semester
    ORDER BY m.nim, mk.kode
    LIMIT 20
");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo sprintf("  %s | %s | %d %s | %d absensi\n", 
        $row['nim'], $row['kode'], $row['tahun'], $row['semester'], $row['total_absensi']);
}

$source->close();
