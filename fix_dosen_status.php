<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check dosen statuses
$dosens = App\Models\Dosen::take(5)->get(['id', 'nama', 'status']);
echo "Sample Dosen:\n";
foreach ($dosens as $d) {
    echo "  - {$d->nama}: {$d->status}\n";
}

// Update all dosen to Aktif
$updated = App\Models\Dosen::whereNull('status')->orWhere('status', '!=', 'Aktif')->update(['status' => 'Aktif']);
echo "\nUpdated {$updated} dosen to Aktif\n";

// Verify
$aktif = App\Models\Dosen::where('status', 'Aktif')->count();
echo "Dosen Aktif sekarang: {$aktif}\n";
