<?php
/**
 * Script untuk mengecek dan migrasi KRS yang belum ada
 */

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Mahasiswa;
use App\Models\Krs;
use App\Models\JadwalKuliah;
use App\Models\MataKuliah;
use App\Models\TahunAkademik;

// Konfigurasi database sumber
config(['database.connections.source' => [
    'driver' => 'mysql',
    'host' => '192.168.120.121',
    'port' => '3306',
    'database' => 'itts_sikad',
    'username' => 'root',
    'password' => 'bismillaH',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]]);

echo "==========================================\n";
echo "   CEK KRS YANG BELUM MIGRASI           \n";
echo "==========================================\n\n";

// Get local mahasiswa NIMs
$localNims = Mahasiswa::pluck('nim')->toArray();
echo "Mahasiswa lokal: " . count($localNims) . "\n";

// Count KRS in source
$sourceKrsCount = DB::connection('source')->table('krss')
    ->whereIn('IDMAHASISWA', $localNims)
    ->count();
echo "KRS di source (untuk mahasiswa lokal): {$sourceKrsCount}\n";
echo "KRS di lokal: " . Krs::count() . "\n\n";

// Build local KRS key map
echo "Building local KRS map...\n";
$localKrsKeys = [];
Krs::with(['mahasiswa', 'jadwalKuliah.mataKuliah', 'tahunAkademik'])
    ->chunk(1000, function ($krsList) use (&$localKrsKeys) {
        foreach ($krsList as $krs) {
            if (!$krs->mahasiswa || !$krs->jadwalKuliah || !$krs->jadwalKuliah->mataKuliah || !$krs->tahunAkademik) continue;
            $sem = $krs->tahunAkademik->semester == 'Ganjil' ? 1 : 2;
            $key = $krs->mahasiswa->nim . '_' . $krs->jadwalKuliah->mataKuliah->kode . '_' . $krs->tahunAkademik->tahun . '_' . $sem;
            $localKrsKeys[$key] = true;
        }
    });
echo "Local KRS keys: " . count($localKrsKeys) . "\n\n";

// Get source KRS
$sourceKrs = DB::connection('source')->table('krss')
    ->whereIn('IDMAHASISWA', $localNims)
    ->select('IDMAHASISWA', 'IDMAKUL', 'TAHUN', 'SEMESTER', 'STATUS')
    ->get();

$missing = [];
$missingByTahun = [];
foreach ($sourceKrs as $s) {
    $key = $s->IDMAHASISWA . '_' . $s->IDMAKUL . '_' . $s->TAHUN . '_' . $s->SEMESTER;
    if (!array_key_exists($key, $localKrsKeys)) {
        $missing[] = $s;
        $tahunKey = $s->TAHUN . '/' . $s->SEMESTER;
        $missingByTahun[$tahunKey] = ($missingByTahun[$tahunKey] ?? 0) + 1;
    }
}

echo "KRS lokal yang cocok dengan source: " . (count($sourceKrs) - count($missing)) . "\n";
echo "KRS di source yang TIDAK ada di lokal: " . count($missing) . "\n";

if (!empty($missingByTahun)) {
    echo "\nMissing KRS per tahun:\n";
    arsort($missingByTahun);
    foreach ($missingByTahun as $t => $c) {
        echo "  {$t}: {$c}\n";
    }
}

// Check why missing - is it because jadwal not exists?
if (!empty($missing)) {
    echo "\n==========================================\n";
    echo "   ANALISIS KRS YANG HILANG              \n";
    echo "==========================================\n";
    
    // Get mata kuliah map
    $makulMap = MataKuliah::pluck('id', 'kode')->toArray();
    $mahasiswaMap = Mahasiswa::pluck('id', 'nim')->toArray();
    
    // Tahun Akademik Map
    $tahunMap = [];
    $tas = TahunAkademik::all();
    foreach ($tas as $ta) {
        $sem = $ta->semester == 'Ganjil' ? 1 : 2;
        $tahunMap[$ta->tahun . $sem] = $ta->id;
    }
    
    $noMakul = 0;
    $noJadwal = 0;
    $noTahun = 0;
    $canMigrate = 0;
    
    foreach (array_slice($missing, 0, 100) as $m) { // Sample 100
        $makulId = $makulMap[$m->IDMAKUL] ?? null;
        $tahunId = $tahunMap[$m->TAHUN . $m->SEMESTER] ?? null;
        $mahasiswaId = $mahasiswaMap[$m->IDMAHASISWA] ?? null;
        
        if (!$makulId) {
            $noMakul++;
            continue;
        }
        if (!$tahunId) {
            $noTahun++;
            continue;
        }
        
        // Check jadwal
        $jadwal = JadwalKuliah::where('mata_kuliah_id', $makulId)
            ->where('tahun_akademik_id', $tahunId)
            ->first();
        
        if (!$jadwal) {
            $noJadwal++;
        } else {
            $canMigrate++;
        }
    }
    
    echo "Dari sample 100 KRS yang hilang:\n";
    echo "  - Mata kuliah tidak ada: {$noMakul}\n";
    echo "  - Tahun akademik tidak ada: {$noTahun}\n";
    echo "  - Jadwal tidak ada: {$noJadwal}\n";
    echo "  - Bisa dimigrasi: {$canMigrate}\n";
    
    // Show missing mata kuliah
    if ($noMakul > 0) {
        echo "\nContoh mata kuliah yang tidak ada di lokal:\n";
        $missingMakul = [];
        foreach (array_slice($missing, 0, 50) as $m) {
            if (!isset($makulMap[$m->IDMAKUL])) {
                $missingMakul[$m->IDMAKUL] = true;
            }
        }
        foreach (array_slice(array_keys($missingMakul), 0, 10) as $mk) {
            echo "  - {$mk}\n";
        }
    }
}
