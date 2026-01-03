<?php
/**
 * Check dan migrate presensi untuk TA 2026 Ganjil
 */

$source = new mysqli("192.168.120.121", "root", "bismillaH", "itts_sikad");
$pdo = new PDO("mysql:host=192.168.200.99;dbname=siakad2025", "development", "Abcde!@#\$%");

// Cek presensi dengan TAHUN=2026 di source
$result = $source->query("
    SELECT COUNT(*) as cnt FROM kelaskuliah_presensimahasiswa WHERE TAHUN = 2026
");
$cnt = $result->fetch_assoc()['cnt'];
echo "=== Presensi TAHUN=2026 di source: $cnt ===\n\n";

// Cek KRS TA 2026 Ganjil di local
$ta2026 = $pdo->query("SELECT * FROM tahun_akademik WHERE tahun = 2026 AND semester = 'Ganjil'")->fetch(PDO::FETCH_ASSOC);
if (!$ta2026) {
    echo "TA 2026 Ganjil tidak ditemukan!\n";
    exit;
}
echo "TA 2026 Ganjil ID: {$ta2026['id']}\n";

$stmt = $pdo->query("SELECT COUNT(*) FROM krs WHERE tahun_akademik_id = {$ta2026['id']}");
$krsCount = $stmt->fetchColumn();
echo "Total KRS TA 2026 Ganjil: $krsCount\n\n";

// Sample KRS TA 2026 Ganjil
echo "=== Sample KRS TA 2026 Ganjil ===\n";
$stmt = $pdo->query("
    SELECT m.nim, mk.kode, mk.nama, k.id as krs_id
    FROM krs k
    JOIN mahasiswa m ON k.mahasiswa_id = m.id
    JOIN jadwal_kuliah j ON k.jadwal_kuliah_id = j.id
    JOIN mata_kuliah mk ON j.mata_kuliah_id = mk.id
    WHERE k.tahun_akademik_id = {$ta2026['id']}
    ORDER BY m.nim, mk.kode
    LIMIT 20
");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Count absensi
    $absCount = $pdo->query("SELECT COUNT(*) FROM absensi WHERE krs_id = {$row['krs_id']}")->fetchColumn();
    echo sprintf("%s | %s - %s | Absensi: %d\n", $row['nim'], $row['kode'], $row['nama'], $absCount);
}

// Cek sample presensi 2026 di source
echo "\n=== Sample presensi TAHUN=2026 di source ===\n";
$result = $source->query("
    SELECT IDMAHASISWA, IDMAKUL, TAHUN, SEMESTER, TANGGAL, PRESENSI
    FROM kelaskuliah_presensimahasiswa
    WHERE TAHUN = 2026
    ORDER BY TANGGAL DESC
    LIMIT 15
");
while ($row = $result->fetch_assoc()) {
    echo sprintf("%s | %s | %d-%d | %s | %s\n", 
        $row["IDMAHASISWA"], $row["IDMAKUL"], $row["TAHUN"], $row["SEMESTER"], 
        $row["TANGGAL"], $row["PRESENSI"]);
}

$source->close();
