<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simulasi logika controller
$mhs = App\Models\Mahasiswa::where('nim', '1003230001')->first();
$tahunAkademikAktif = App\Models\TahunAkademik::getAktif();

// Get list tahun akademik yang punya KRS
$tahunAkademikList = App\Models\TahunAkademik::whereIn('id', function($query) use ($mhs) {
    $query->select('tahun_akademik_id')
        ->from('krs')
        ->where('mahasiswa_id', $mhs->id)
        ->where('status', 'Disetujui')
        ->distinct();
})->orderBy('tahun', 'desc')->orderBy('semester', 'desc')->get();

// Cek KRS di tahun aktif
$hasKrsAktif = App\Models\Krs::where('mahasiswa_id', $mhs->id)
    ->where('tahun_akademik_id', $tahunAkademikAktif?->id)
    ->where('status', 'Disetujui')
    ->exists();

if ($hasKrsAktif) {
    $tahunAkademik = $tahunAkademikAktif;
} else {
    $tahunAkademik = $tahunAkademikList->first() ?? $tahunAkademikAktif;
}

echo "Mahasiswa: {$mhs->nama} ({$mhs->nim})\n";
echo "Tahun Akademik Aktif: {$tahunAkademikAktif->nama_lengkap}\n";
echo "Has KRS di TA Aktif: " . ($hasKrsAktif ? 'Ya' : 'Tidak') . "\n";
echo "Fallback ke: {$tahunAkademik->nama_lengkap}\n\n";

// Get jadwal
$jadwal = App\Models\Krs::with(['jadwalKuliah.mataKuliah'])
    ->where('mahasiswa_id', $mhs->id)
    ->where('tahun_akademik_id', $tahunAkademik->id)
    ->where('status', 'Disetujui')
    ->get();

echo "Jadwal yang ditampilkan (" . count($jadwal) . " mata kuliah):\n";
foreach($jadwal as $j) {
    echo "  - {$j->jadwalKuliah->hari} {$j->jadwalKuliah->jam_mulai}-{$j->jadwalKuliah->jam_selesai}: {$j->jadwalKuliah->mataKuliah->nama}\n";
}
