<?php

/**
 * Script untuk testing semua route Tugas Akhir
 * Jalankan dengan: php test_tugas_akhir_routes.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\TugasAkhir;
use App\Models\SeminarProposal;
use App\Models\SidangTA;
use App\Models\BimbinganTA;

echo "=== TESTING TUGAS AKHIR MODULE ===\n\n";

$errors = [];
$success = [];

// Get test users
$admin = User::where('role', 'admin')->first();
$dosenUser = User::where('role', 'dosen')->whereHas('dosen')->first();
$mahasiswaUser = User::where('role', 'mahasiswa')->whereHas('mahasiswa')->first();

if (!$admin || !$dosenUser || !$mahasiswaUser) {
    die("ERROR: Missing test users!\n");
}

// Test data
$ta = TugasAkhir::first();
$taWithPembimbing = TugasAkhir::whereNotNull('pembimbing_1_id')->first();
$seminar = SeminarProposal::first();
$sidang = SidangTA::first();
$bimbingan = BimbinganTA::first();

echo "--- ADMIN TESTS ---\n";

// Test admin routes
try {
    Auth::login($admin);
    
    // 1. Index
    $request = app('request');
    $controller = new \App\Http\Controllers\TugasAkhirController();
    $response = $controller->index($request);
    echo "✓ Admin: tugas-akhir.index\n";
    $success[] = 'admin.tugas-akhir.index';
} catch (Exception $e) {
    echo "✗ Admin: tugas-akhir.index - " . $e->getMessage() . "\n";
    $errors[] = 'admin.tugas-akhir.index: ' . $e->getMessage();
}

try {
    // 2. Show
    if ($ta) {
        $response = $controller->show($ta);
        echo "✓ Admin: tugas-akhir.show\n";
        $success[] = 'admin.tugas-akhir.show';
    }
} catch (Exception $e) {
    echo "✗ Admin: tugas-akhir.show - " . $e->getMessage() . "\n";
    $errors[] = 'admin.tugas-akhir.show: ' . $e->getMessage();
}

try {
    // 3. Seminar Index
    $response = $controller->seminarIndex($request);
    echo "✓ Admin: tugas-akhir.seminar.index\n";
    $success[] = 'admin.tugas-akhir.seminar.index';
} catch (Exception $e) {
    echo "✗ Admin: tugas-akhir.seminar.index - " . $e->getMessage() . "\n";
    $errors[] = 'admin.tugas-akhir.seminar.index: ' . $e->getMessage();
}

try {
    // 4. Sidang Index
    $response = $controller->sidangIndex($request);
    echo "✓ Admin: tugas-akhir.sidang.index\n";
    $success[] = 'admin.tugas-akhir.sidang.index';
} catch (Exception $e) {
    echo "✗ Admin: tugas-akhir.sidang.index - " . $e->getMessage() . "\n";
    $errors[] = 'admin.tugas-akhir.sidang.index: ' . $e->getMessage();
}

echo "\n--- DOSEN TESTS ---\n";

try {
    Auth::login($dosenUser);
    
    $dosenController = new \App\Http\Controllers\TugasAkhirDosenController();
    
    // 1. Index
    $response = $dosenController->index();
    echo "✓ Dosen: tugas-akhir.index\n";
    $success[] = 'dosen.tugas-akhir.index';
} catch (Exception $e) {
    echo "✗ Dosen: tugas-akhir.index - " . $e->getMessage() . "\n";
    $errors[] = 'dosen.tugas-akhir.index: ' . $e->getMessage();
}

try {
    // 2. Jadwal Bimbingan
    $response = $dosenController->jadwalBimbingan();
    echo "✓ Dosen: tugas-akhir.jadwal-bimbingan\n";
    $success[] = 'dosen.tugas-akhir.jadwal-bimbingan';
} catch (Exception $e) {
    echo "✗ Dosen: tugas-akhir.jadwal-bimbingan - " . $e->getMessage() . "\n";
    $errors[] = 'dosen.tugas-akhir.jadwal-bimbingan: ' . $e->getMessage();
}

try {
    // 3. Riwayat Bimbingan
    $response = $dosenController->riwayatBimbingan();
    echo "✓ Dosen: tugas-akhir.riwayat-bimbingan\n";
    $success[] = 'dosen.tugas-akhir.riwayat-bimbingan';
} catch (Exception $e) {
    echo "✗ Dosen: tugas-akhir.riwayat-bimbingan - " . $e->getMessage() . "\n";
    $errors[] = 'dosen.tugas-akhir.riwayat-bimbingan: ' . $e->getMessage();
}

try {
    // 4. Seminar Penguji
    $response = $dosenController->seminarPenguji();
    echo "✓ Dosen: tugas-akhir.seminar-penguji\n";
    $success[] = 'dosen.tugas-akhir.seminar-penguji';
} catch (Exception $e) {
    echo "✗ Dosen: tugas-akhir.seminar-penguji - " . $e->getMessage() . "\n";
    $errors[] = 'dosen.tugas-akhir.seminar-penguji: ' . $e->getMessage();
}

try {
    // 5. Sidang Penguji
    $response = $dosenController->sidangPenguji();
    echo "✓ Dosen: tugas-akhir.sidang-penguji\n";
    $success[] = 'dosen.tugas-akhir.sidang-penguji';
} catch (Exception $e) {
    echo "✗ Dosen: tugas-akhir.sidang-penguji - " . $e->getMessage() . "\n";
    $errors[] = 'dosen.tugas-akhir.sidang-penguji: ' . $e->getMessage();
}

echo "\n--- MAHASISWA TESTS ---\n";

try {
    Auth::login($mahasiswaUser);
    
    $mhsController = new \App\Http\Controllers\TugasAkhirMahasiswaController();
    
    // 1. Index
    $response = $mhsController->index();
    echo "✓ Mahasiswa: tugas-akhir.index\n";
    $success[] = 'mahasiswa.tugas-akhir.index';
} catch (Exception $e) {
    echo "✗ Mahasiswa: tugas-akhir.index - " . $e->getMessage() . "\n";
    $errors[] = 'mahasiswa.tugas-akhir.index: ' . $e->getMessage();
}

try {
    // 2. Create
    $response = $mhsController->create();
    echo "✓ Mahasiswa: tugas-akhir.create\n";
    $success[] = 'mahasiswa.tugas-akhir.create';
} catch (Exception $e) {
    // Redirect is expected if already has TA
    if (strpos($e->getMessage(), 'Redirect') !== false || strpos(get_class($e), 'Redirect') !== false) {
        echo "✓ Mahasiswa: tugas-akhir.create (redirect - already has TA)\n";
        $success[] = 'mahasiswa.tugas-akhir.create';
    } else {
        echo "✗ Mahasiswa: tugas-akhir.create - " . $e->getMessage() . "\n";
        $errors[] = 'mahasiswa.tugas-akhir.create: ' . $e->getMessage();
    }
}

echo "\n=== RESULTS ===\n";
echo "Success: " . count($success) . "\n";
echo "Errors: " . count($errors) . "\n";

if (count($errors) > 0) {
    echo "\nErrors Detail:\n";
    foreach ($errors as $err) {
        echo "  - $err\n";
    }
}

echo "\n=== TEST COMPLETE ===\n";
