<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$pegawaiAktif = App\Models\Pegawai::where('status', 'Aktif')->get();
$dosenAktif = App\Models\Dosen::where('status', 'Aktif')->get();

echo "Pegawai Aktif: " . $pegawaiAktif->count() . "\n";
echo "Dosen Aktif: " . $dosenAktif->count() . "\n";

// Show all statuses
echo "\nPegawai by status:\n";
$byStatus = App\Models\Pegawai::selectRaw('status, count(*) as cnt')->groupBy('status')->get();
foreach ($byStatus as $s) {
    echo "  - {$s->status}: {$s->cnt}\n";
}

echo "\nDosen by status:\n";
$byStatus = App\Models\Dosen::selectRaw('status, count(*) as cnt')->groupBy('status')->get();
foreach ($byStatus as $s) {
    echo "  - {$s->status}: {$s->cnt}\n";
}
