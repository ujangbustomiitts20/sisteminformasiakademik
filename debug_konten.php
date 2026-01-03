<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\KontenPmb;

echo "getAllGrouped() structure:\n";
$grouped = KontenPmb::getAllGrouped();
print_r($grouped->toArray());

echo "\n\nAccessing konten['general']:\n";
$general = $grouped['general'] ?? collect();
print_r($general->pluck('value', 'key')->toArray());
