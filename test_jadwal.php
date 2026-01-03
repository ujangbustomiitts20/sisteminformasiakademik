<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Mahasiswa;
use App\Models\Krs;
use App\Models\TahunAkademik;
use App\Models\User;

// Cek user dengan nim 1001250001
$mhs = Mahasiswa::where('nim', '1001250001')->first();
echo "Mahasiswa: {$mhs->nim} - {$mhs->nama}\n";

// Cek user account
$user = User::where('email', $mhs->email)->first();
if ($user) {
    echo "User: {$user->email} (ID: {$user->id})\n";
    echo "Password untuk login: password123\n";
} else {
    echo "User tidak ditemukan\n";
}

// Cek jadwal
$ta = TahunAkademik::getAktif();
$jadwal = Krs::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'jadwalKuliah.ruangan'])
    ->where('mahasiswa_id', $mhs->id)
    ->where('tahun_akademik_id', $ta->id)
    ->where('status', 'Disetujui')
    ->get();

echo "\nJadwal ({$ta->tahun} {$ta->semester}):\n";
foreach ($jadwal as $j) {
    $r = $j->jadwalKuliah->ruangan;
    echo "  {$j->jadwalKuliah->mataKuliah->kode} - {$j->jadwalKuliah->mataKuliah->nama}\n";
    echo "    {$j->jadwalKuliah->hari} {$j->jadwalKuliah->jam_mulai}-{$j->jadwalKuliah->jam_selesai}";
    echo " | " . ($r ? $r->nama : 'TBA') . "\n";
}
