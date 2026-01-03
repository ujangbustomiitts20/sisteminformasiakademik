<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Mahasiswa;
use App\Models\Krs;
use App\Models\TahunAkademik;

$mhs = Mahasiswa::where('nim', '1003230001')->first();
if (!$mhs) {
    echo "Mahasiswa dengan NIM 1003230001 tidak ditemukan!\n";
    exit;
}

echo "Mahasiswa: {$mhs->nim} - {$mhs->nama}\n";
echo "Prodi: " . ($mhs->programStudi->nama ?? 'N/A') . "\n";
echo "Angkatan: {$mhs->angkatan}\n";

// Cek semua KRS mahasiswa ini
$krsList = Krs::with('tahunAkademik')
    ->where('mahasiswa_id', $mhs->id)
    ->get();

echo "\nTotal KRS: " . count($krsList) . "\n";

if (count($krsList) > 0) {
    echo "\nKRS per Tahun Akademik:\n";
    foreach ($krsList->groupBy('tahun_akademik_id') as $taId => $krs) {
        $ta = $krs->first()->tahunAkademik;
        echo "  {$ta->tahun} {$ta->semester}: " . count($krs) . " mata kuliah\n";
    }
}

// Cek tahun akademik aktif
$taAktif = TahunAkademik::getAktif();
echo "\nTahun Akademik Aktif: {$taAktif->tahun} {$taAktif->semester} (ID: {$taAktif->id})\n";

// Cek KRS di tahun aktif
$krsAktif = Krs::where('mahasiswa_id', $mhs->id)
    ->where('tahun_akademik_id', $taAktif->id)
    ->where('status', 'Disetujui')
    ->count();
echo "KRS di tahun aktif: {$krsAktif}\n";

// Cek data KRS di source database
echo "\n--- Cek di Source Database ---\n";
config(['database.connections.source' => [
    'driver' => 'mysql',
    'host' => '192.168.120.121',
    'port' => '3306',
    'database' => 'itts_sikad',
    'username' => 'root',
    'password' => 'bismillaH',
]]);

$sourceKrs = DB::connection('source')->table('krss')
    ->where('IDMAHASISWA', '1003230001')
    ->selectRaw('TAHUN, SEMESTER, count(*) as total')
    ->groupBy('TAHUN', 'SEMESTER')
    ->orderBy('TAHUN', 'desc')
    ->orderBy('SEMESTER', 'desc')
    ->get();

echo "KRS di source:\n";
foreach ($sourceKrs as $k) {
    echo "  {$k->TAHUN}/{$k->SEMESTER}: {$k->total}\n";
}
