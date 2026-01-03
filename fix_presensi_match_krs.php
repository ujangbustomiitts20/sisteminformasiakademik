<?php
/**
 * Fix Presensi Migration - Match by KRS (mahasiswa + kode_mk)
 * 
 * Problem: Presensi dari source menggunakan TAHUN=2025 untuk semua semester,
 * tapi kode MK-nya adalah semester lama (IF115, IF116 dll untuk semester 3).
 * 
 * Solution: Match presensi ke KRS berdasarkan mahasiswa + kode_mk saja,
 * tanpa memfilter tahun akademik.
 */

$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
$target = new PDO("mysql:host=192.168.200.99;dbname=siakad2025", "development", "Abcde!@#\$%");

// Build lookup maps
echo "Building lookup maps...\n";

// Mahasiswa lookup: NIM => id
$mahasiswaMap = [];
$stmt = $target->query("SELECT id, nim FROM mahasiswa");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $mahasiswaMap[$row['nim']] = $row['id'];
}
echo "  Mahasiswa: " . count($mahasiswaMap) . "\n";

// KRS lookup: mahasiswa_id-kode_mk => [krs records with tahun_akademik info]
// Get ALL KRS with their mata kuliah code
$krsMap = [];
$stmt = $target->query("
    SELECT k.id as krs_id, k.mahasiswa_id, k.tahun_akademik_id, mk.kode as kode_mk,
           ta.tahun, ta.semester
    FROM krs k
    JOIN jadwal_kuliah j ON k.jadwal_kuliah_id = j.id
    JOIN mata_kuliah mk ON j.mata_kuliah_id = mk.id
    JOIN tahun_akademik ta ON k.tahun_akademik_id = ta.id
    ORDER BY ta.tahun DESC, FIELD(ta.semester, 'Ganjil', 'Genap', 'Pendek')
");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $key = $row['mahasiswa_id'] . '-' . $row['kode_mk'];
    if (!isset($krsMap[$key])) {
        $krsMap[$key] = [];
    }
    $krsMap[$key][] = $row;
}
echo "  KRS combinations: " . count($krsMap) . "\n";

// Status mapping
$statusMap = ['H' => 'Hadir', 'I' => 'Izin', 'S' => 'Sakit', 'A' => 'Alpha', 'T' => 'Alpha'];

// Delete existing absensi first to avoid duplicates
echo "\nClearing existing absensi...\n";
$target->exec("DELETE FROM absensi");
$target->exec("ALTER TABLE absensi AUTO_INCREMENT = 1");

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
$skipped = ['mhs_not_found' => 0, 'krs_not_found' => 0, 'invalid_date' => 0];
$processed = 0;
$batchSize = 1000;
$batchData = [];

// Prepare insert statement
$insertSql = "INSERT INTO absensi (krs_id, pertemuan, tanggal, status, created_at, updated_at) VALUES ";

while ($row = $result->fetch_assoc()) {
    $processed++;
    
    // Validate date
    $tanggal = $row['TANGGAL'];
    if (empty($tanggal) || $tanggal == '0000-00-00' || !strtotime($tanggal)) {
        $skipped['invalid_date']++;
        continue;
    }
    
    // Find mahasiswa
    $nim = $row['IDMAHASISWA'];
    if (!isset($mahasiswaMap[$nim])) {
        $skipped['mhs_not_found']++;
        continue;
    }
    $mahasiswaId = $mahasiswaMap[$nim];
    
    // Find KRS by mahasiswa + kode_mk (pick any matching KRS)
    $kodeMk = $row['IDMAKUL'];
    $key = $mahasiswaId . '-' . $kodeMk;
    
    if (!isset($krsMap[$key]) || empty($krsMap[$key])) {
        $skipped['krs_not_found']++;
        continue;
    }
    
    // Pick the most recent KRS for this mhs-mk combination
    $krs = $krsMap[$key][0];
    $krsId = $krs['krs_id'];
    
    // Map status
    $status = $statusMap[$row['PRESENSI']] ?? 'Alpha';
    $pertemuan = $row['IDPERTEMUAN'] ?? 1;
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
        echo "\r  Processed: $processed / $total (Inserted: $inserted)    ";
    }
}

// Insert remaining
if (!empty($batchData)) {
    $target->exec($insertSql . implode(',', $batchData));
    $inserted += count($batchData);
}

echo "\n\n=== Hasil ===\n";
echo "Inserted: $inserted\n";
echo "Skipped: " . array_sum($skipped) . "\n";
echo "  - Mahasiswa not found: {$skipped['mhs_not_found']}\n";
echo "  - KRS not found: {$skipped['krs_not_found']}\n";
echo "  - Invalid date: {$skipped['invalid_date']}\n";

// Verify sample
echo "\n=== Verifikasi Sample ===\n";
$stmt = $target->query("
    SELECT m.nim, mk.kode, ta.tahun, ta.semester, COUNT(a.id) as total_absensi
    FROM absensi a
    JOIN krs k ON a.krs_id = k.id
    JOIN mahasiswa m ON k.mahasiswa_id = m.id
    JOIN jadwal_kuliah j ON k.jadwal_kuliah_id = j.id
    JOIN mata_kuliah mk ON j.mata_kuliah_id = mk.id
    JOIN tahun_akademik ta ON k.tahun_akademik_id = ta.id
    WHERE m.nim = '1002230011'
    GROUP BY m.nim, mk.kode, ta.tahun, ta.semester
    ORDER BY ta.tahun, FIELD(ta.semester, 'Ganjil', 'Genap', 'Pendek')
");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo sprintf("  %s | %s | %d %s | %d absensi\n", 
        $row['nim'], $row['kode'], $row['tahun'], $row['semester'], $row['total_absensi']);
}

$source->close();
