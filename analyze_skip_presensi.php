<?php
/**
 * Analisis presensi yang skip karena KRS not found
 */

$source = new mysqli("192.168.120.121", "root", "bismillaH", "itts_sikad");
$pdo = new PDO("mysql:host=192.168.200.99;dbname=siakad2025", "development", "Abcde!@#\$%");

// Build KRS lookup
$krsMap = [];
$stmt = $pdo->query("
    SELECT k.id as krs_id, k.mahasiswa_id, m.nim, mk.kode as kode_mk
    FROM krs k
    JOIN mahasiswa m ON k.mahasiswa_id = m.id
    JOIN jadwal_kuliah j ON k.jadwal_kuliah_id = j.id
    JOIN mata_kuliah mk ON j.mata_kuliah_id = mk.id
");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $key = $row['nim'] . '-' . $row['kode_mk'];
    $krsMap[$key] = $row['krs_id'];
}
echo "Total KRS combinations: " . count($krsMap) . "\n\n";

// Sample presensi yang KRS not found
echo "=== Sample presensi TAHUN=2026 yang KRS tidak ditemukan ===\n";
$result = $source->query("
    SELECT IDMAHASISWA, IDMAKUL, TAHUN, SEMESTER, TANGGAL, COUNT(*) as cnt
    FROM kelaskuliah_presensimahasiswa
    WHERE TAHUN = 2026 AND TANGGAL != '0000-00-00'
    GROUP BY IDMAHASISWA, IDMAKUL, TAHUN, SEMESTER
    ORDER BY cnt DESC
    LIMIT 20
");

$notFound = [];
while ($row = $result->fetch_assoc()) {
    $key = $row['IDMAHASISWA'] . '-' . $row['IDMAKUL'];
    if (!isset($krsMap[$key])) {
        $notFound[] = $row;
    }
}

echo "Sample yang tidak punya KRS:\n";
foreach (array_slice($notFound, 0, 15) as $row) {
    echo sprintf("  %s | %s | %d-%d | %d presensi\n", 
        $row['IDMAHASISWA'], $row['IDMAKUL'], $row['TAHUN'], $row['SEMESTER'], $row['cnt']);
}

// Cek apakah mahasiswa ada
echo "\n=== Cek mahasiswa yang tidak ada di local ===\n";
$result = $source->query("
    SELECT DISTINCT IDMAHASISWA 
    FROM kelaskuliah_presensimahasiswa
    WHERE TAHUN = 2026 AND TANGGAL != '0000-00-00'
    LIMIT 100
");

$mhsNotFound = [];
while ($row = $result->fetch_assoc()) {
    $stmt = $pdo->prepare("SELECT id FROM mahasiswa WHERE nim = ?");
    $stmt->execute([$row['IDMAHASISWA']]);
    if (!$stmt->fetch()) {
        $mhsNotFound[] = $row['IDMAHASISWA'];
    }
}
echo "Mahasiswa tidak ditemukan (sample): " . count($mhsNotFound) . "\n";
if ($mhsNotFound) {
    echo "  Sample: " . implode(", ", array_slice($mhsNotFound, 0, 10)) . "\n";
}

// Cek kode MK yang tidak ada
echo "\n=== Kode MK di source yang tidak ada KRS-nya ===\n";
$result = $source->query("
    SELECT IDMAKUL, COUNT(DISTINCT IDMAHASISWA) as mhs_count, COUNT(*) as presensi_count
    FROM kelaskuliah_presensimahasiswa
    WHERE TAHUN = 2026 AND TANGGAL != '0000-00-00'
    GROUP BY IDMAKUL
    ORDER BY presensi_count DESC
");

$mkStats = [];
while ($row = $result->fetch_assoc()) {
    // Cek apakah ada KRS dengan kode MK ini
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM krs k
        JOIN jadwal_kuliah j ON k.jadwal_kuliah_id = j.id
        JOIN mata_kuliah mk ON j.mata_kuliah_id = mk.id
        WHERE mk.kode = ?
    ");
    $stmt->execute([$row['IDMAKUL']]);
    $krsCount = $stmt->fetchColumn();
    
    if ($krsCount == 0) {
        $mkStats[] = [
            'kode' => $row['IDMAKUL'],
            'mhs' => $row['mhs_count'],
            'presensi' => $row['presensi_count']
        ];
    }
}

echo "MK yang tidak ada KRS-nya:\n";
foreach (array_slice($mkStats, 0, 15) as $mk) {
    echo sprintf("  %s: %d mhs, %d presensi\n", $mk['kode'], $mk['mhs'], $mk['presensi']);
}

$source->close();
