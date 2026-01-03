<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PengajuanKonversi;
use App\Models\DetailKonversi;

echo "=== Data Konversi Nilai ===\n\n";

echo "Total Pengajuan: " . PengajuanKonversi::count() . "\n";
echo "Total Detail: " . DetailKonversi::count() . "\n\n";

echo "=== Per Status ===\n";
$statuses = PengajuanKonversi::selectRaw('status, COUNT(*) as total')
    ->groupBy('status')
    ->get();

foreach ($statuses as $s) {
    echo "- {$s->status}: {$s->total}\n";
}

echo "\n=== Sample Data BARU (dengan nama_calon_mahasiswa) ===\n";
$samples = PengajuanKonversi::with('detailKonversi', 'programStudiTujuan')
    ->whereNotNull('nama_calon_mahasiswa')
    ->take(10)
    ->get();

foreach ($samples as $p) {
    echo "\n{$p->nomor_pengajuan} - {$p->nama_calon_mahasiswa}\n";
    echo "  Status: {$p->status}\n";
    echo "  Prodi Tujuan: " . ($p->programStudiTujuan->nama ?? 'N/A') . "\n";
    echo "  Universitas Asal: {$p->universitas_asal}\n";
    echo "  Jumlah MK: " . $p->detailKonversi->count() . "\n";
}

echo "\n=== DONE ===\n";
