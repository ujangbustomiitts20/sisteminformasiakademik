<?php
/**
 * Fix encoding issues in tugas_akhir data
 * Converts mojibake (double-encoded UTF-8) to proper UTF-8
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIX ENCODING TUGAS AKHIR ===\n";

// Map of common mojibake patterns to correct characters
$fixMap = [
    'Ã¯' => 'ï',
    'Ã©' => 'é',
    'Ã¨' => 'è',
    'Ã ' => 'à',
    'Ã¢' => 'â',
    'Ãª' => 'ê',
    'Ã®' => 'î',
    'Ã´' => 'ô',
    'Ã»' => 'û',
    'Ã§' => 'ç',
    'Ã¼' => 'ü',
    'Ã¶' => 'ö',
    'Ã¤' => 'ä',
    'â€œ' => '"',
    'â€' => '"',
    'â€"' => '–',
    'â€"' => '—',
    'â€™' => "'",
    'â€˜' => "'",
    'â€¦' => '…',
];

function fixString($str, $fixMap) {
    if (!$str) return $str;
    foreach ($fixMap as $bad => $good) {
        $str = str_replace($bad, $good, $str);
    }
    return $str;
}

$updated = 0;
$tugasAkhirs = DB::table('tugas_akhir')->get();

foreach ($tugasAkhirs as $ta) {
    $changes = [];
    
    $fixedJudul = fixString($ta->judul, $fixMap);
    if ($fixedJudul != $ta->judul) {
        $changes['judul'] = $fixedJudul;
    }
    
    $fixedLB = fixString($ta->latar_belakang, $fixMap);
    if ($fixedLB != $ta->latar_belakang) {
        $changes['latar_belakang'] = $fixedLB;
    }
    
    if (!empty($changes)) {
        DB::table('tugas_akhir')->where('id', $ta->id)->update($changes);
        $updated++;
        echo "  Fixed ID {$ta->id}: " . substr($fixedJudul ?? $ta->judul, 0, 50) . "...\n";
    }
}

echo "\nTotal fixed: $updated records\n";
echo "=== DONE ===\n";
