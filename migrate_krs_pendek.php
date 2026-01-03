<?php
/**
 * Script untuk migrasi KRS semester pendek yang belum termigrasi
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
echo "   MIGRASI KRS SEMESTER PENDEK          \n";
echo "==========================================\n\n";

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// Get maps
$mahasiswaMap = Mahasiswa::pluck('id', 'nim')->toArray();
$makulMap = MataKuliah::pluck('id', 'kode')->toArray();

// Tahun Akademik Map - include semester 4 (Pendek)
$tahunMap = [];
$tas = TahunAkademik::all();
foreach ($tas as $ta) {
    $semCode = match($ta->semester) {
        'Ganjil' => 1,
        'Genap' => 2,
        'Pendek' => 4,
        default => 1,
    };
    $tahunMap[$ta->tahun . $semCode] = $ta->id;
}

echo "Tahun Akademik Map:\n";
foreach ($tahunMap as $k => $v) {
    echo "  {$k} => {$v}\n";
}

// Get local mahasiswa NIMs
$localNims = array_keys($mahasiswaMap);

// Get missing KRS (semester 4)
$missingKrs = DB::connection('source')->table('krss')
    ->whereIn('IDMAHASISWA', $localNims)
    ->where('SEMESTER', 4)
    ->get();

echo "\nKRS semester pendek di source: " . count($missingKrs) . "\n";

// Build existing KRS keys
$existingKeys = [];
Krs::with(['mahasiswa', 'jadwalKuliah.mataKuliah', 'tahunAkademik'])
    ->whereHas('tahunAkademik', function($q) {
        $q->where('semester', 'Pendek');
    })
    ->chunk(1000, function ($krsList) use (&$existingKeys) {
        foreach ($krsList as $krs) {
            if (!$krs->mahasiswa || !$krs->jadwalKuliah || !$krs->jadwalKuliah->mataKuliah) continue;
            $key = $krs->mahasiswa->nim . '_' . $krs->jadwalKuliah->mataKuliah->kode . '_' . $krs->tahunAkademik->tahun . '_4';
            $existingKeys[$key] = true;
        }
    });

$stats = [
    'krs' => 0,
    'jadwal' => 0,
    'skipped_no_makul' => 0,
    'skipped_no_tahun' => 0,
    'skipped_exists' => 0,
];

// First, ensure jadwal exists for semester pendek
echo "\nMemastikan jadwal kuliah semester pendek ada...\n";

$uniqueJadwal = [];
foreach ($missingKrs as $krs) {
    $key = $krs->IDMAKUL . '_' . $krs->TAHUN . '_' . $krs->SEMESTER;
    if (!isset($uniqueJadwal[$key])) {
        $uniqueJadwal[$key] = $krs;
    }
}

foreach ($uniqueJadwal as $krs) {
    $makulId = $makulMap[$krs->IDMAKUL] ?? null;
    $tahunId = $tahunMap[$krs->TAHUN . $krs->SEMESTER] ?? null;
    
    if (!$makulId || !$tahunId) continue;
    
    // Check if jadwal exists
    $jadwal = JadwalKuliah::where('mata_kuliah_id', $makulId)
        ->where('tahun_akademik_id', $tahunId)
        ->first();
    
    if (!$jadwal) {
        // Get dosen from source kelaskuliah_pengajar
        $pengajar = DB::connection('source')->table('kelaskuliah_pengajar')
            ->where('IDMAKUL', $krs->IDMAKUL)
            ->where('TAHUN', $krs->TAHUN)
            ->where('SEMESTER', $krs->SEMESTER)
            ->first();
        
        $dosenId = null;
        if ($pengajar && isset($pengajar->IDPENGAJAR)) {
            $dosen = \App\Models\Dosen::where('nidn', $pengajar->IDPENGAJAR)->first();
            if ($dosen) $dosenId = $dosen->id;
        }
        
        // Fallback to first dosen if not found
        if (!$dosenId) {
            $dosenId = \App\Models\Dosen::first()->id ?? 1;
        }
        
        JadwalKuliah::create([
            'mata_kuliah_id' => $makulId,
            'tahun_akademik_id' => $tahunId,
            'dosen_id' => $dosenId,
            'kelas' => $krs->KELAS ?: 'A',
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'ruangan_id' => \App\Models\Ruangan::first()->id ?? null,
            'kuota' => 40,
        ]);
        $stats['jadwal']++;
    }
}
echo "  Jadwal baru: {$stats['jadwal']}\n";

// Now migrate KRS
echo "\nMigrasi KRS semester pendek...\n";

foreach ($missingKrs as $krs) {
    $makulId = $makulMap[$krs->IDMAKUL] ?? null;
    $tahunId = $tahunMap[$krs->TAHUN . $krs->SEMESTER] ?? null;
    $mahasiswaId = $mahasiswaMap[$krs->IDMAHASISWA] ?? null;
    
    if (!$makulId) {
        $stats['skipped_no_makul']++;
        continue;
    }
    if (!$tahunId) {
        $stats['skipped_no_tahun']++;
        continue;
    }
    
    $key = $krs->IDMAHASISWA . '_' . $krs->IDMAKUL . '_' . $krs->TAHUN . '_' . $krs->SEMESTER;
    if (isset($existingKeys[$key])) {
        $stats['skipped_exists']++;
        continue;
    }
    
    // Get jadwal
    $jadwal = JadwalKuliah::where('mata_kuliah_id', $makulId)
        ->where('tahun_akademik_id', $tahunId)
        ->first();
    
    if (!$jadwal) {
        continue;
    }
    
    // Check existing KRS
    $existing = Krs::where('mahasiswa_id', $mahasiswaId)
        ->where('tahun_akademik_id', $tahunId)
        ->where('jadwal_kuliah_id', $jadwal->id)
        ->first();
    
    if ($existing) {
        $stats['skipped_exists']++;
        continue;
    }
    
    try {
        Krs::create([
            'mahasiswa_id' => $mahasiswaId,
            'tahun_akademik_id' => $tahunId,
            'jadwal_kuliah_id' => $jadwal->id,
            'status' => 'Disetujui',
            'tanggal_pengajuan' => now(),
            'tanggal_persetujuan' => now(),
        ]);
        $stats['krs']++;
        $existingKeys[$key] = true;
    } catch (Exception $e) {
        // Skip duplicates
    }
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "\n==========================================\n";
echo "   HASIL MIGRASI                        \n";
echo "==========================================\n";
echo "   KRS baru           : {$stats['krs']}\n";
echo "   Jadwal baru        : {$stats['jadwal']}\n";
echo "   Skip (no makul)    : {$stats['skipped_no_makul']}\n";
echo "   Skip (no tahun)    : {$stats['skipped_no_tahun']}\n";
echo "   Skip (exists)      : {$stats['skipped_exists']}\n";
echo "==========================================\n";

echo "\nVerifikasi:\n";
echo "   Total KRS         : " . Krs::count() . "\n";
echo "   Total Jadwal      : " . JadwalKuliah::count() . "\n";
