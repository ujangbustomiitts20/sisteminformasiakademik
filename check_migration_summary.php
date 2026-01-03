<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Ruangan;
use App\Models\Absensi;
use App\Models\Tagihan;
use App\Models\TransaksiPembayaran;
use App\Models\Beasiswa;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\JadwalKuliah;
use App\Models\Krs;
use App\Models\Nilai;
use App\Models\User;

echo "===========================================" . PHP_EOL;
echo "   RINGKASAN SEMUA DATA MIGRASI           " . PHP_EOL;
echo "===========================================" . PHP_EOL;

echo PHP_EOL . "--- DATA AKADEMIK ---" . PHP_EOL;
echo "   Mahasiswa         : " . Mahasiswa::count() . PHP_EOL;
echo "   Dosen             : " . Dosen::count() . PHP_EOL;
echo "   Mata Kuliah       : " . MataKuliah::count() . PHP_EOL;
echo "   Jadwal Kuliah     : " . JadwalKuliah::count() . PHP_EOL;
echo "   KRS               : " . Krs::count() . PHP_EOL;
echo "   Nilai             : " . Nilai::count() . PHP_EOL;
echo "   Presensi/Absensi  : " . Absensi::count() . PHP_EOL;
echo "   Ruangan           : " . Ruangan::count() . PHP_EOL;

echo PHP_EOL . "--- DATA KEUANGAN ---" . PHP_EOL;
echo "   Tagihan           : " . Tagihan::count() . PHP_EOL;
echo "   Pembayaran        : " . TransaksiPembayaran::count() . PHP_EOL;
echo "   Beasiswa          : " . Beasiswa::count() . " jenis" . PHP_EOL;

echo PHP_EOL . "--- USER ACCOUNTS ---" . PHP_EOL;
echo "   Total Users       : " . User::count() . PHP_EOL;
$roles = User::selectRaw('role, count(*) as total')->groupBy('role')->get();
foreach ($roles as $r) {
    echo "   - {$r->role}: {$r->total}" . PHP_EOL;
}

echo PHP_EOL . "--- STATUS TAGIHAN ---" . PHP_EOL;
$statuses = Tagihan::selectRaw('status, count(*) as total')->groupBy('status')->get();
foreach ($statuses as $s) {
    echo "   {$s->status}: {$s->total}" . PHP_EOL;
}

echo PHP_EOL . "--- METODE PEMBAYARAN ---" . PHP_EOL;
$methods = TransaksiPembayaran::selectRaw('metode_pembayaran, count(*) as total')->groupBy('metode_pembayaran')->get();
foreach ($methods as $m) {
    echo "   {$m->metode_pembayaran}: {$m->total}" . PHP_EOL;
}

echo PHP_EOL . "--- STATUS ABSENSI ---" . PHP_EOL;
$absStatuses = Absensi::selectRaw('status, count(*) as total')->groupBy('status')->get();
foreach ($absStatuses as $a) {
    echo "   {$a->status}: {$a->total}" . PHP_EOL;
}

echo PHP_EOL . "===========================================" . PHP_EOL;
