<?php
$source = new mysqli("192.168.120.121", "root", "bismillaH", "itts_sikad");

// Total presensi valid
$result = $source->query("SELECT COUNT(*) as cnt FROM kelaskuliah_presensimahasiswa WHERE TANGGAL != '0000-00-00'");
echo "Total presensi valid di source: " . $result->fetch_assoc()["cnt"] . "\n";

// Per TAHUN
$result = $source->query("
    SELECT TAHUN, COUNT(*) as cnt 
    FROM kelaskuliah_presensimahasiswa 
    WHERE TANGGAL != '0000-00-00'
    GROUP BY TAHUN 
    ORDER BY TAHUN DESC
");
echo "\nPer TAHUN:\n";
while ($row = $result->fetch_assoc()) {
    echo "  {$row['TAHUN']}: {$row['cnt']}\n";
}

$source->close();
