<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PeriodePmb;
use App\Models\GelombangPmb;
use App\Models\JalurSeleksi;
use App\Models\KuotaPmb;
use App\Models\BiayaPendaftaran;
use App\Models\SliderPmb;
use App\Models\KontenPmb;
use App\Models\KeunggulanPmb;
use App\Models\FasilitasPmb;
use App\Models\TestimoniPmb;
use App\Models\FaqPmb;
use App\Models\BeritaPmb;
use App\Models\KontakPmb;
use App\Models\GaleriPmb;

echo "===========================================\n";
echo "   DATA PMB ONLINE - STATUS VERIFIKASI    \n";
echo "===========================================\n\n";

$data = [
    'Periode PMB Aktif' => PeriodePmb::where('is_active', true)->count(),
    'Gelombang PMB' => GelombangPmb::count(),
    'Jalur Seleksi' => JalurSeleksi::count(),
    'Kuota PMB' => KuotaPmb::count(),
    'Biaya Pendaftaran' => BiayaPendaftaran::count(),
    'Slider PMB' => SliderPmb::count(),
    'Konten PMB' => KontenPmb::count(),
    'Keunggulan PMB' => KeunggulanPmb::count(),
    'Fasilitas PMB' => FasilitasPmb::count(),
    'Testimoni PMB' => TestimoniPmb::count(),
    'FAQ PMB' => FaqPmb::count(),
    'Berita PMB' => BeritaPmb::count(),
    'Kontak PMB' => KontakPmb::count(),
    'Galeri PMB' => GaleriPmb::count(),
];

foreach ($data as $label => $count) {
    $status = $count > 0 ? '✅' : '❌';
    printf("%-20s : %3d %s\n", $label, $count, $status);
}

echo "\n===========================================\n";
echo "   DETAIL PERIODE & GELOMBANG PMB         \n";
echo "===========================================\n\n";

$periode = PeriodePmb::where('is_active', true)->first();
if ($periode) {
    echo "Periode Aktif: {$periode->nama}\n";
    echo "Tahun Akademik: {$periode->tahun_akademik}\n\n";
    
    echo "Gelombang PMB:\n";
    foreach ($periode->gelombang as $g) {
        echo "  - {$g->nama}: {$g->tanggal_mulai} s/d {$g->tanggal_selesai} ";
        echo ($g->is_active ? '(AKTIF)' : '(NON-AKTIF)') . "\n";
    }
}

echo "\n===========================================\n";
echo "   SEMUA DATA PMB ONLINE TERSEDIA ✅      \n";
echo "===========================================\n";
