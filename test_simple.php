<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\KontenPmb;

$konten = KontenPmb::getAllFlat();

file_put_contents(__DIR__ . '/konten_output.txt', json_encode($konten, JSON_PRETTY_PRINT));

echo "Data saved to konten_output.txt\n";
echo "Count: " . count($konten) . "\n";
echo "Has nama_universitas: " . (isset($konten['nama_universitas']) ? 'YES' : 'NO') . "\n";
