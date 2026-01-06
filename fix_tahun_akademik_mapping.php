<?php
/**
 * Fix Tahun Akademik Mapping
 * 
 * Source database menggunakan konvensi berbeda:
 * - Source 2026/1 = Semester Ganjil tahun ajaran 2025/2026 (Sep-Des 2025)
 * - Source 2025/2 = Semester Genap tahun ajaran 2024/2025 (Feb-Jul 2025)
 * - Source 2025/1 = Semester Ganjil tahun ajaran 2024/2025 (Sep-Des 2024)
 * 
 * Local database menggunakan:
 * - 2025 Ganjil = Semester Ganjil tahun 2025 (Sep-Des 2025) <- AKTIF
 * - 2024 Genap = Semester Genap tahun 2024/2025
 * - 2024 Ganjil = Semester Ganjil tahun 2024 (Sep-Des 2024)
 * 
 * Jadi mapping yang benar:
 * Source 2026/1 => Local 2025 Ganjil (ID: 2)
 * Source 2025/2 => Local 2024 Genap (ID: 11) - BUKAN 2025 Genap!
 * Source 2025/1 => Local 2024 Ganjil (ID: 10) - BUKAN 2025 Ganjil!
 * Source 2025/4 => Local 2024 Pendek atau 2025 Pendek
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIX TAHUN AKADEMIK MAPPING ===\n\n";

// Mapping corrections needed
// Current wrong mapping -> Correct mapping
$corrections = [
    // 2026 Ganjil (ID:4) seharusnya 2025 Ganjil (ID:2) - karena source 2026/1
    // Tapi 2025 Ganjil sudah ada data dari source 2025/1 yang seharusnya 2024 Ganjil
    
    // Jadi kita perlu shift:
    // Step 1: Pindahkan data 2025 Ganjil (dari source 2025/1) -> 2024 Ganjil
    // Step 2: Pindahkan data 2026 Ganjil (dari source 2026/1) -> 2025 Ganjil
    // Step 3: Pindahkan data 2025 Genap (dari source 2025/2) -> 2024 Genap
];

// First, let's see what dates the KRS were created to determine source
echo "1. Analyzing KRS data by creation patterns...\n";

// Check KRS in 2026 Ganjil - should be current semester
$krs2026 = DB::table('krs')
    ->where('tahun_akademik_id', 4)
    ->count();
echo "   KRS di 2026 Ganjil (ID:4): {$krs2026}\n";

// Check KRS in 2025 Ganjil - might be mixed
$krs2025g = DB::table('krs')
    ->where('tahun_akademik_id', 2)
    ->count();
echo "   KRS di 2025 Ganjil (ID:2): {$krs2025g}\n";

// Check KRS in 2025 Genap
$krs2025n = DB::table('krs')
    ->where('tahun_akademik_id', 3)
    ->count();
echo "   KRS di 2025 Genap (ID:3): {$krs2025n}\n";

// Check KRS in 2024 Ganjil
$krs2024g = DB::table('krs')
    ->where('tahun_akademik_id', 10)
    ->count();
echo "   KRS di 2024 Ganjil (ID:10): {$krs2024g}\n";

// Check KRS in 2024 Genap  
$krs2024n = DB::table('krs')
    ->where('tahun_akademik_id', 11)
    ->count();
echo "   KRS di 2024 Genap (ID:11): {$krs2024n}\n";

echo "\n2. Fixing mappings...\n";

// Karena data sudah ada dan tercampur, kita perlu hati-hati.
// Yang paling aman adalah menggunakan mapping berdasarkan tanggal presensi di source

// STEP 1: Update KRS dan Jadwal dari 2026 Ganjil -> 2025 Ganjil
// Ini adalah data semester aktif saat ini (source 2026/1 = local 2025 Ganjil)

echo "\n   STEP 1: Moving 2026 Ganjil (ID:4) -> 2025 Ganjil (ID:2)\n";

// Pertama, hapus duplikat jika ada (KRS yang sama di kedua semester)
$duplicates = DB::select("
    SELECT k1.id 
    FROM krs k1
    JOIN krs k2 ON k1.mahasiswa_id = k2.mahasiswa_id 
        AND k1.jadwal_kuliah_id = k2.jadwal_kuliah_id
    WHERE k1.tahun_akademik_id = 4 
        AND k2.tahun_akademik_id = 2
");
$dupIds = array_map(fn($d) => $d->id, $duplicates);
if (count($dupIds) > 0) {
    DB::table('krs')->whereIn('id', $dupIds)->delete();
    echo "   Deleted " . count($dupIds) . " duplicate KRS\n";
}

// Update jadwal_kuliah tahun_akademik_id dari 4 -> 2
// Tapi hanya yang belum ada di 2025 Ganjil
$jadwalToMove = DB::table('jadwal_kuliah as j1')
    ->where('j1.tahun_akademik_id', 4)
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))
            ->from('jadwal_kuliah as j2')
            ->whereColumn('j2.mata_kuliah_id', 'j1.mata_kuliah_id')
            ->whereColumn('j2.kelas', 'j1.kelas')
            ->where('j2.tahun_akademik_id', 2);
    })
    ->pluck('id');

if ($jadwalToMove->count() > 0) {
    DB::table('jadwal_kuliah')
        ->whereIn('id', $jadwalToMove)
        ->update(['tahun_akademik_id' => 2]);
    echo "   Moved " . $jadwalToMove->count() . " jadwal to 2025 Ganjil\n";
}

// Update KRS tahun_akademik_id dari 4 -> 2
$krsUpdated = DB::table('krs')
    ->where('tahun_akademik_id', 4)
    ->update(['tahun_akademik_id' => 2]);
echo "   Moved {$krsUpdated} KRS to 2025 Ganjil\n";

// STEP 2: Hapus jadwal di 2026 Ganjil yang sudah tidak punya KRS
$orphanJadwal = DB::table('jadwal_kuliah as j')
    ->where('j.tahun_akademik_id', 4)
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))
            ->from('krs')
            ->whereColumn('krs.jadwal_kuliah_id', 'j.id');
    })
    ->delete();
echo "   Deleted {$orphanJadwal} orphan jadwal from 2026 Ganjil\n";

// STEP 3: Fix mapping untuk semester lain jika perlu
// 2025 Genap (ID:3) seharusnya dari source 2025/2 = 2024 Genap di local
// Tapi ini mungkin sudah benar atau perlu cek lebih lanjut

echo "\n3. Verifikasi hasil...\n";

// Recount
$krs2026_new = DB::table('krs')->where('tahun_akademik_id', 4)->count();
$krs2025g_new = DB::table('krs')->where('tahun_akademik_id', 2)->count();
$jadwal2026_new = DB::table('jadwal_kuliah')->where('tahun_akademik_id', 4)->count();
$jadwal2025g_new = DB::table('jadwal_kuliah')->where('tahun_akademik_id', 2)->count();

echo "   KRS di 2026 Ganjil: {$krs2026} -> {$krs2026_new}\n";
echo "   KRS di 2025 Ganjil: {$krs2025g} -> {$krs2025g_new}\n";
echo "   Jadwal di 2026 Ganjil: sebelum -> {$jadwal2026_new}\n";
echo "   Jadwal di 2025 Ganjil: sebelum -> {$jadwal2025g_new}\n";

// Verifikasi mahasiswa contoh
$mhs = DB::table('mahasiswa')->where('nim', '1003230001')->first();
if ($mhs) {
    echo "\n4. Verifikasi KRS mahasiswa {$mhs->nim}...\n";
    $krsNow = DB::table('krs as k')
        ->join('jadwal_kuliah as j', 'k.jadwal_kuliah_id', '=', 'j.id')
        ->join('mata_kuliah as mk', 'j.mata_kuliah_id', '=', 'mk.id')
        ->join('tahun_akademik as ta', 'k.tahun_akademik_id', '=', 'ta.id')
        ->where('k.mahasiswa_id', $mhs->id)
        ->where('k.tahun_akademik_id', 2) // 2025 Ganjil
        ->select('mk.kode', 'mk.nama', 'ta.tahun', 'ta.semester')
        ->get();
    
    echo "   KRS di 2025 Ganjil (semester aktif):\n";
    foreach ($krsNow as $k) {
        echo "     - {$k->kode} | {$k->nama}\n";
    }
    echo "   Total: " . $krsNow->count() . " MK\n";
}

echo "\nDone!\n";
