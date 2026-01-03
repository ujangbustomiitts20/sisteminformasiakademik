<?php
/**
 * Cek presensi mahasiswa TI 2023 (1002230xxx) yang belum migrasi
 */

$source = new mysqli("192.168.120.121", "root", "bismillaH", "itts_sikad");
$pdo = new PDO("mysql:host=192.168.200.99;dbname=siakad2025", "development", "Abcde!@#\$%");

// Get all TI 2023 students
$mhsIds = [];
$stmt = $pdo->query("SELECT id, nim FROM mahasiswa WHERE nim LIKE '1002230%'");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $mhsIds[$row['nim']] = $row['id'];
}
echo "Mahasiswa TI 2023: " . count($mhsIds) . "\n\n";

// Get KRS for TI 2023 with kode MK
$krsMap = [];
foreach ($mhsIds as $nim => $mhsId) {
    $stmt = $pdo->prepare("
        SELECT k.id as krs_id, mk.kode 
        FROM krs k
        JOIN jadwal_kuliah j ON k.jadwal_kuliah_id = j.id
        JOIN mata_kuliah mk ON j.mata_kuliah_id = mk.id
        WHERE k.mahasiswa_id = ?
    ");
    $stmt->execute([$mhsId]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $key = $nim . '-' . $row['kode'];
        $krsMap[$key] = $row['krs_id'];
    }
}
echo "Total KRS TI 2023: " . count($krsMap) . "\n\n";

// Get presensi from source for TI 2023
$result = $source->query("
    SELECT IDMAHASISWA, IDMAKUL, COUNT(*) as cnt
    FROM kelaskuliah_presensimahasiswa
    WHERE IDMAHASISWA LIKE '1002230%' AND TANGGAL != '0000-00-00'
    GROUP BY IDMAHASISWA, IDMAKUL
    ORDER BY IDMAHASISWA, IDMAKUL
");

$matched = 0;
$notMatched = [];
while ($row = $result->fetch_assoc()) {
    $key = $row['IDMAHASISWA'] . '-' . $row['IDMAKUL'];
    if (isset($krsMap[$key])) {
        $matched += $row['cnt'];
    } else {
        if (!isset($notMatched[$row['IDMAKUL']])) {
            $notMatched[$row['IDMAKUL']] = ['mhs' => 0, 'presensi' => 0];
        }
        $notMatched[$row['IDMAKUL']]['mhs']++;
        $notMatched[$row['IDMAKUL']]['presensi'] += $row['cnt'];
    }
}

echo "Presensi TI 2023 yang match dengan KRS: $matched\n";
echo "Presensi TI 2023 yang TIDAK match:\n";

arsort($notMatched);
foreach (array_slice($notMatched, 0, 20, true) as $mk => $stats) {
    // Cek apakah MK ada di local
    $stmt = $pdo->prepare("SELECT kode, nama FROM mata_kuliah WHERE kode = ?");
    $stmt->execute([$mk]);
    $mkLocal = $stmt->fetch(PDO::FETCH_ASSOC);
    $mkName = $mkLocal ? $mkLocal['nama'] : '(MK tidak ada)';
    
    echo sprintf("  %s - %s: %d mhs, %d presensi\n", $mk, $mkName, $stats['mhs'], $stats['presensi']);
}

// Total tidak match
$totalNotMatched = array_sum(array_column($notMatched, 'presensi'));
echo "\nTotal presensi TI 2023 tidak match: $totalNotMatched\n";

$source->close();
