<?php
/**
 * Check Yudisium Migration Status
 */

$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
if ($source->connect_error) {
    die('Connection failed: ' . $source->connect_error);
}

// Get all kelulusan_mhs IDs
$result = $source->query("SELECT IDMAHASISWA, STATUSKELUAR FROM kelulusan_mhs");
$legacyIds = [];
while ($row = $result->fetch_assoc()) {
    $legacyIds[$row['IDMAHASISWA']] = $row['STATUSKELUAR'];
}

// Load Laravel for local DB
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Get existing legacy_ids in local yudisium
$existing = DB::table('yudisium')->whereNotNull('legacy_id')->pluck('legacy_id')->toArray();
echo "Legacy records in source: " . count($legacyIds) . "\n";
echo "Already migrated: " . count($existing) . "\n";

// Count how many can be migrated (mahasiswa exists locally)
$canMigrate = 0;
$notMigrated = [];
$noMahasiswa = 0;

foreach ($legacyIds as $nim => $status) {
    if (!in_array($nim, $existing)) {
        $mhs = DB::table('mahasiswa')->where('nim', $nim)->first();
        if ($mhs) {
            $canMigrate++;
            $notMigrated[] = $nim . ' (' . $status . ')';
        } else {
            $noMahasiswa++;
        }
    }
}

echo "Can be migrated (mahasiswa exists): $canMigrate\n";
echo "Cannot migrate (no mahasiswa): $noMahasiswa\n";

echo "\nSample not yet migrated:\n";
foreach (array_slice($notMigrated, 0, 10) as $n) {
    echo "  - $n\n";
}

$source->close();
