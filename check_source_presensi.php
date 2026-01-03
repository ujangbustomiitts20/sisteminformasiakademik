<?php
$source = new mysqli("192.168.120.121", "root", "bismillaH", "itts_sikad");

// Cek presensi untuk kode MK yang masih 0 di mahasiswa 1002230011
$mkList = ["TI136", "TI137", "TI130", "TI132", "TI129"];

echo "=== Presensi di SOURCE untuk mahasiswa 1002230011 ===\n";
foreach ($mkList as $mk) {
    $result = $source->query("
        SELECT COUNT(*) as cnt FROM kelaskuliah_presensimahasiswa 
        WHERE IDMAHASISWA = '1002230011' AND IDMAKUL = '$mk'
    ");
    $row = $result->fetch_assoc();
    echo "$mk: {$row['cnt']} presensi\n";
}

echo "\n=== Sample presensi TI136 untuk TI 2023 ===\n";
$result = $source->query("
    SELECT IDMAHASISWA, IDMAKUL, TAHUN, SEMESTER, TANGGAL, PRESENSI 
    FROM kelaskuliah_presensimahasiswa 
    WHERE IDMAKUL = 'TI136' AND IDMAHASISWA LIKE '1002230%'
    ORDER BY TANGGAL DESC
    LIMIT 15
");
while ($row = $result->fetch_assoc()) {
    echo sprintf("%s | %s | %d-%d | %s | %s\n", 
        $row["IDMAHASISWA"], $row["IDMAKUL"], $row["TAHUN"], $row["SEMESTER"], 
        $row["TANGGAL"], $row["PRESENSI"]);
}

// Cek total presensi per MK untuk TI 2023
echo "\n=== Total presensi per MK untuk semua TI 2023 ===\n";
$result = $source->query("
    SELECT IDMAKUL, COUNT(*) as cnt 
    FROM kelaskuliah_presensimahasiswa 
    WHERE IDMAHASISWA LIKE '1002230%'
    GROUP BY IDMAKUL
    ORDER BY IDMAKUL
");
while ($row = $result->fetch_assoc()) {
    echo sprintf("%s: %d\n", $row["IDMAKUL"], $row["cnt"]);
}

$source->close();
