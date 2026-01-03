<?php
$pdo = new PDO("mysql:host=192.168.200.99;dbname=siakad2025", "development", "Abcde!@#\$%");

echo "=== Cek Prerequisites untuk Layanan Mahasiswa ===\n\n";

// 1. Mahasiswa dengan dosen wali
$stmt = $pdo->query("SELECT COUNT(*) FROM mahasiswa WHERE dosen_wali_id IS NOT NULL");
$mhsWithWali = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM mahasiswa");
$totalMhs = $stmt->fetchColumn();
echo "1. Mahasiswa dengan Dosen Wali: $mhsWithWali / $totalMhs\n";

// 2. Tahun Akademik aktif
$stmt = $pdo->query("SELECT * FROM tahun_akademik WHERE is_aktif = 1");
$taAktif = $stmt->fetch(PDO::FETCH_ASSOC);
if ($taAktif) {
    echo "2. Tahun Akademik Aktif: {$taAktif['tahun']} {$taAktif['semester']}\n";
} else {
    echo "2. Tahun Akademik Aktif: TIDAK ADA!\n";
}

// 3. User mahasiswa dengan relasi (via user_id di mahasiswa)
$stmt = $pdo->query("SELECT COUNT(*) FROM mahasiswa WHERE user_id IS NOT NULL");
$mhsWithUser = $stmt->fetchColumn();
echo "3. Mahasiswa dengan User Account: $mhsWithUser\n";

// 4. User dosen (via user_id di dosen)
$stmt = $pdo->query("SELECT COUNT(*) FROM dosen WHERE user_id IS NOT NULL");
$dosenWithUser = $stmt->fetchColumn();
echo "4. Dosen dengan User Account: $dosenWithUser\n";

// 5. User by role
$stmt = $pdo->query("SELECT role, COUNT(*) as cnt FROM users GROUP BY role");
echo "5. Users by Role:\n";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "   - {$row['role']}: {$row['cnt']}\n";
}

// Sample mahasiswa untuk test
echo "\n=== Sample Mahasiswa untuk Testing ===\n";
$stmt = $pdo->query("
    SELECT m.nim, m.nama, d.nama as dosen_wali, u.email
    FROM mahasiswa m
    LEFT JOIN dosen d ON m.dosen_wali_id = d.id
    LEFT JOIN users u ON m.user_id = u.id
    WHERE m.user_id IS NOT NULL
    LIMIT 5
");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $wali = $row['dosen_wali'] ?? "TIDAK ADA";
    echo "  {$row['nim']} - {$row['nama']} | Wali: $wali | Login: {$row['email']}\n";
}

// Sample dosen untuk test
echo "\n=== Sample Dosen untuk Testing ===\n";
$stmt = $pdo->query("
    SELECT d.nidn, d.nama, u.email, u.role
    FROM dosen d
    LEFT JOIN users u ON d.user_id = u.id
    WHERE d.user_id IS NOT NULL
    LIMIT 5
");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "  {$row['nidn']} - {$row['nama']} | Login: {$row['email']} | Role: {$row['role']}\n";
}

// Check admin users
echo "\n=== User Admin ===\n";
$stmt = $pdo->query("SELECT email, role FROM users WHERE role = 'admin' LIMIT 3");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "  {$row['email']} | Role: {$row['role']}\n";
}
