<?php
$pdo = new PDO("mysql:host=192.168.200.99;dbname=siakad2025", "development", "Abcde!@#\$%");
$mhs = $pdo->query("SELECT * FROM mahasiswa WHERE nim = '1002230011'")->fetch();
$ta = $pdo->query("SELECT * FROM tahun_akademik WHERE tahun = 2024 AND semester = 'Ganjil'")->fetch();

if (!$ta) {
    echo "TA 2024 Ganjil tidak ditemukan!\n";
    exit;
}

echo "Mahasiswa: {$mhs['nama']} ({$mhs['nim']})\n";
echo "TA: {$ta['tahun']} {$ta['semester']} (ID: {$ta['id']})\n\n";

$stmt = $pdo->prepare("
    SELECT k.id, mk.kode, mk.nama 
    FROM krs k
    JOIN jadwal_kuliah j ON k.jadwal_kuliah_id = j.id
    JOIN mata_kuliah mk ON j.mata_kuliah_id = mk.id
    WHERE k.mahasiswa_id = ? AND k.tahun_akademik_id = ?
");
$stmt->execute([$mhs["id"], $ta["id"]]);

echo "KRS TA 2024 Ganjil:\n";
while ($k = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $absCount = $pdo->query("SELECT COUNT(*) FROM absensi WHERE krs_id = {$k['id']}")->fetchColumn();
    echo sprintf("  KRS %d: %s - %s (Absensi: %d)\n", $k["id"], $k["kode"], $k["nama"], $absCount);
}
