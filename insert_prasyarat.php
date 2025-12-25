<?php

// Script untuk insert data dummy prasyarat mata kuliah
// Jalankan: php insert_prasyarat.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\MataKuliah;

echo "=== Insert Data Dummy Prasyarat Mata Kuliah ===\n\n";

// Cek apakah tabel ada
if (!DB::getSchemaBuilder()->hasTable('prasyarat_mata_kuliah')) {
    echo "Tabel prasyarat_mata_kuliah belum ada!\n";
    echo "Jalankan: php artisan migrate\n";
    exit(1);
}

// Ambil semua mata kuliah
$mataKuliah = MataKuliah::orderBy('semester')->orderBy('kode')->get();

echo "Daftar Mata Kuliah:\n";
echo str_repeat('-', 60) . "\n";
foreach ($mataKuliah as $mk) {
    echo sprintf("ID: %d | %s | %s | Semester %d\n", $mk->id, $mk->kode, $mk->nama, $mk->semester);
}
echo str_repeat('-', 60) . "\n\n";

// Clear existing prasyarat
DB::table('prasyarat_mata_kuliah')->truncate();
echo "Cleared existing prasyarat data.\n\n";

// Buat mapping berdasarkan kode
$mkByKode = $mataKuliah->keyBy('kode');

// Definisi prasyarat (sesuaikan dengan kode MK yang ada)
$prasyaratList = [];

// Cari pola mata kuliah untuk membuat prasyarat yang masuk akal
$semester1 = $mataKuliah->where('semester', 1);
$semester2 = $mataKuliah->where('semester', 2);
$semester3 = $mataKuliah->where('semester', 3);
$semester4 = $mataKuliah->where('semester', 4);
$semester5 = $mataKuliah->where('semester', 5);
$semester6 = $mataKuliah->where('semester', 6);

echo "Membuat prasyarat berdasarkan semester...\n\n";

// MK Semester 2 membutuhkan MK Semester 1
foreach ($semester2 as $mk2) {
    $prasyaratMK = $semester1->random();
    if ($prasyaratMK) {
        $prasyaratList[] = [
            'mata_kuliah_id' => $mk2->id,
            'mata_kuliah_prasyarat_id' => $prasyaratMK->id,
            'jenis_prasyarat' => 'wajib',
            'nilai_minimal' => 'D',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        echo "- {$mk2->kode} ({$mk2->nama}) <- {$prasyaratMK->kode} ({$prasyaratMK->nama}) [WAJIB, min D]\n";
    }
}

// MK Semester 3 membutuhkan MK Semester 2
foreach ($semester3 as $mk3) {
    $prasyaratMK = $semester2->random();
    if ($prasyaratMK) {
        $prasyaratList[] = [
            'mata_kuliah_id' => $mk3->id,
            'mata_kuliah_prasyarat_id' => $prasyaratMK->id,
            'jenis_prasyarat' => 'wajib',
            'nilai_minimal' => 'D',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        echo "- {$mk3->kode} ({$mk3->nama}) <- {$prasyaratMK->kode} ({$prasyaratMK->nama}) [WAJIB, min D]\n";
    }
}

// MK Semester 4 membutuhkan MK Semester 3 (beberapa dengan nilai minimal C)
foreach ($semester4 as $index => $mk4) {
    $prasyaratMK = $semester3->random();
    if ($prasyaratMK) {
        $prasyaratList[] = [
            'mata_kuliah_id' => $mk4->id,
            'mata_kuliah_prasyarat_id' => $prasyaratMK->id,
            'jenis_prasyarat' => $index % 2 == 0 ? 'wajib' : 'pilihan',
            'nilai_minimal' => $index % 2 == 0 ? 'C' : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $jenis = $index % 2 == 0 ? 'WAJIB, min C' : 'PILIHAN';
        echo "- {$mk4->kode} ({$mk4->nama}) <- {$prasyaratMK->kode} ({$prasyaratMK->nama}) [{$jenis}]\n";
    }
}

// MK Semester 5 membutuhkan MK Semester 4
foreach ($semester5 as $mk5) {
    $prasyaratMK = $semester4->random();
    if ($prasyaratMK) {
        $prasyaratList[] = [
            'mata_kuliah_id' => $mk5->id,
            'mata_kuliah_prasyarat_id' => $prasyaratMK->id,
            'jenis_prasyarat' => 'wajib',
            'nilai_minimal' => 'D',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        echo "- {$mk5->kode} ({$mk5->nama}) <- {$prasyaratMK->kode} ({$prasyaratMK->nama}) [WAJIB, min D]\n";
    }
}

// MK Semester 6 membutuhkan MK Semester 5
foreach ($semester6 as $index => $mk6) {
    $prasyaratMK = $semester5->random();
    if ($prasyaratMK) {
        $prasyaratList[] = [
            'mata_kuliah_id' => $mk6->id,
            'mata_kuliah_prasyarat_id' => $prasyaratMK->id,
            'jenis_prasyarat' => 'wajib',
            'nilai_minimal' => 'C',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        echo "- {$mk6->kode} ({$mk6->nama}) <- {$prasyaratMK->kode} ({$prasyaratMK->nama}) [WAJIB, min C]\n";
    }
    
    // Beberapa MK semester 6 butuh 2 prasyarat
    if ($index % 2 == 0 && $semester4->count() > 0) {
        $prasyaratMK2 = $semester4->random();
        if ($prasyaratMK2 && $prasyaratMK2->id != ($prasyaratMK->id ?? 0)) {
            $prasyaratList[] = [
                'mata_kuliah_id' => $mk6->id,
                'mata_kuliah_prasyarat_id' => $prasyaratMK2->id,
                'jenis_prasyarat' => 'pilihan',
                'nilai_minimal' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            echo "- {$mk6->kode} ({$mk6->nama}) <- {$prasyaratMK2->kode} ({$prasyaratMK2->nama}) [PILIHAN]\n";
        }
    }
}

// Insert ke database
if (count($prasyaratList) > 0) {
    DB::table('prasyarat_mata_kuliah')->insert($prasyaratList);
    echo "\n" . str_repeat('=', 60) . "\n";
    echo "✅ Berhasil insert " . count($prasyaratList) . " data prasyarat!\n";
    echo str_repeat('=', 60) . "\n";
} else {
    echo "\n⚠️ Tidak ada data prasyarat yang dibuat. Pastikan ada mata kuliah di database.\n";
}

echo "\nSelesai!\n";
