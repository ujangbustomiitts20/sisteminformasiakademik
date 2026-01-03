<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\KontenPmb;

echo "===========================================\n";
echo "   TESTING getAllFlat() METHOD            \n";
echo "===========================================\n\n";

$konten = KontenPmb::getAllFlat();

echo "Konten keys available:\n";
print_r(array_keys($konten));

echo "\n\nChecking critical keys for views:\n";
$criticalKeys = [
    'nama_universitas',  // used in layout
    'meta_description',  // used in layout
    'meta_keywords',     // used in layout
    'hero_title',        // used in index
    'hero_subtitle',     // used in index
    'alur_step_1',       // used in alur-pendaftaran
    'alur_step_2',
    'alur_step_3',
    'alur_step_4',
    'alur_step_5',
    'alur_step_6',
    'alur_step_7',
];

foreach ($criticalKeys as $key) {
    $status = isset($konten[$key]) ? '✅' : '❌';
    $value = isset($konten[$key]) ? substr($konten[$key], 0, 50) . '...' : 'NOT FOUND';
    echo "  {$status} {$key}: {$value}\n";
}
