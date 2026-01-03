<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\View;
use App\Models\KontenPmb;
use App\Models\KontakPmb;
use App\Models\SliderPmb;
use App\Models\PeriodePmb;
use App\Models\GelombangPmb;
use App\Models\JalurSeleksi;
use App\Models\ProgramStudi;
use App\Models\KeunggulanPmb;
use App\Models\FasilitasPmb;
use App\Models\TestimoniPmb;
use App\Models\FaqPmb;
use App\Models\BeritaPmb;
use App\Models\GaleriPmb;

$konten = KontenPmb::getAllFlat();
View::share('konten', $konten);

$views = [
    'portal-pmb.index' => [
        'sliders' => SliderPmb::active()->get(),
        'keunggulan' => KeunggulanPmb::active()->take(6)->get(),
        'programStudi' => ProgramStudi::with('fakultas')->get(),
        'testimoni' => TestimoniPmb::active()->take(6)->get(),
        'beritaTerbaru' => BeritaPmb::published()->latest('published_at')->take(3)->get(),
        'fasilitas' => FasilitasPmb::active()->take(6)->get(),
        'kontak' => KontakPmb::getKontak(),
        'sosialMedia' => KontakPmb::getSosialMedia(),
        'periodePmb' => PeriodePmb::getActive(),
        'gelombangAktif' => null,
        'jalurSeleksi' => JalurSeleksi::active()->get(),
        'faqPopuler' => FaqPmb::active()->take(5)->get(),
        'statistik' => [
            'total_prodi' => 10,
            'total_mahasiswa' => 5000,
            'total_dosen' => 200,
            'tahun_berdiri' => '1990',
        ],
    ],
    'portal-pmb.jalur-seleksi' => [
        'jalurSeleksi' => JalurSeleksi::active()->get(),
        'periodePmb' => PeriodePmb::getActive(),
        'kontak' => KontakPmb::getKontak(),
        'sosialMedia' => KontakPmb::getSosialMedia(),
    ],
    'portal-pmb.program-studi' => [
        'programStudi' => ProgramStudi::with('fakultas')->get()->groupBy('fakultas.nama'),
        'kontak' => KontakPmb::getKontak(),
        'sosialMedia' => KontakPmb::getSosialMedia(),
    ],
    'portal-pmb.faq' => [
        'faq' => FaqPmb::active()->get(),
        'categories' => FaqPmb::active()->pluck('kategori')->unique()->filter(),
        'kontak' => KontakPmb::getKontak(),
        'sosialMedia' => KontakPmb::getSosialMedia(),
    ],
    'portal-pmb.berita' => [
        'berita' => BeritaPmb::published()->paginate(9),
        'beritaPopuler' => BeritaPmb::published()->featured()->take(5)->get(),
        'kategori' => ['berita', 'pengumuman', 'info'],
        'kontak' => KontakPmb::getKontak(),
        'sosialMedia' => KontakPmb::getSosialMedia(),
    ],
    'portal-pmb.galeri' => [
        'galeri' => GaleriPmb::active()->paginate(12),
        'kategori' => GaleriPmb::distinct()->pluck('kategori')->filter(),
        'kontak' => KontakPmb::getKontak(),
        'sosialMedia' => KontakPmb::getSosialMedia(),
    ],
    'portal-pmb.fasilitas' => [
        'fasilitas' => FasilitasPmb::active()->get(),
        'kontak' => KontakPmb::getKontak(),
        'sosialMedia' => KontakPmb::getSosialMedia(),
    ],
    'portal-pmb.kontak' => [
        'kontak' => KontakPmb::getKontak(),
        'sosialMedia' => KontakPmb::getSosialMedia(),
    ],
    'portal-pmb.alur-pendaftaran' => [
        'kontak' => KontakPmb::getKontak(),
        'sosialMedia' => KontakPmb::getSosialMedia(),
    ],
    'portal-pmb.pendaftaran-tutup' => [
        'periodePmb' => PeriodePmb::getActive(),
        'gelombangBerikutnya' => null,
        'kontak' => KontakPmb::getKontak(),
        'sosialMedia' => KontakPmb::getSosialMedia(),
    ],
];

echo "===========================================\n";
echo "   TESTING VIEW RENDERING                 \n";
echo "===========================================\n\n";

$errors = [];
$success = 0;

foreach ($views as $viewName => $data) {
    try {
        $rendered = View::make($viewName, $data)->render();
        
        if (strlen($rendered) > 0) {
            echo "✅ {$viewName}: OK (" . number_format(strlen($rendered)) . " bytes)\n";
            $success++;
        } else {
            echo "⚠️  {$viewName}: Empty output\n";
        }
    } catch (Exception $e) {
        echo "❌ {$viewName}: ERROR\n";
        echo "   " . $e->getMessage() . "\n";
        $errors[] = $viewName . ": " . $e->getMessage();
    }
}

echo "\n===========================================\n";
echo "   RESULTS: {$success}/" . count($views) . " views OK\n";

if (!empty($errors)) {
    echo "\n   ERRORS:\n";
    foreach ($errors as $error) {
        echo "   - {$error}\n";
    }
}
echo "===========================================\n";

// Save to file
file_put_contents(__DIR__ . '/view_test_results.txt', 
    "Success: {$success}/" . count($views) . "\n" .
    "Errors: " . count($errors) . "\n" .
    implode("\n", $errors)
);
