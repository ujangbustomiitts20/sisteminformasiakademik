<?php
/**
 * Script untuk mengecek dan migrasi jadwal kuliah yang belum ada
 */

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\JadwalKuliah;
use App\Models\Krs;
use App\Models\TahunAkademik;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\Ruangan;
use App\Models\Mahasiswa;

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
echo "   CEK JADWAL KULIAH                    \n";
echo "==========================================\n\n";

echo "Jadwal Kuliah lokal: " . JadwalKuliah::count() . "\n";
echo "KRS lokal: " . Krs::count() . "\n";

// Cek jadwal per tahun akademik
echo "\nJadwal per Tahun Akademik (lokal):\n";
$jadwalPerTa = JadwalKuliah::selectRaw('tahun_akademik_id, count(*) as total')
    ->groupBy('tahun_akademik_id')
    ->with('tahunAkademik')
    ->get();
foreach ($jadwalPerTa as $j) {
    echo "  {$j->tahunAkademik->tahun} {$j->tahunAkademik->semester}: {$j->total}\n";
}

// Cek jadwal di source (kelaskuliah)
echo "\nKelaskuliah di source:\n";
$sourceJadwal = DB::connection('source')->table('kelaskuliah')
    ->selectRaw('TAHUN, SEMESTER, count(*) as total')
    ->groupBy('TAHUN', 'SEMESTER')
    ->orderBy('TAHUN', 'desc')
    ->orderBy('SEMESTER', 'desc')
    ->get();
foreach ($sourceJadwal as $s) {
    echo "  {$s->TAHUN}/{$s->SEMESTER}: {$s->total}\n";
}

// Get local mahasiswa NIMs
$localNims = Mahasiswa::pluck('nim')->toArray();

// Count unique jadwal from KRS source for local mahasiswa
echo "\nAnalisis jadwal dari KRS source:\n";
$krsJadwal = DB::connection('source')->table('krss')
    ->whereIn('IDMAHASISWA', $localNims)
    ->selectRaw('DISTINCT IDMAKUL, TAHUN, SEMESTER, KELAS')
    ->get();
echo "Unique jadwal dari KRS mahasiswa lokal: " . count($krsJadwal) . "\n";

// Build tahun akademik map
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

// Build mata kuliah map
$makulMap = MataKuliah::pluck('id', 'kode')->toArray();

// Build existing jadwal keys
$existingJadwal = [];
JadwalKuliah::with(['mataKuliah', 'tahunAkademik'])
    ->chunk(1000, function ($jadwals) use (&$existingJadwal) {
        foreach ($jadwals as $j) {
            $sem = match($j->tahunAkademik->semester) {
                'Ganjil' => 1,
                'Genap' => 2,
                'Pendek' => 4,
                default => 1,
            };
            $key = $j->mataKuliah->kode . '_' . $j->tahunAkademik->tahun . '_' . $sem . '_' . $j->kelas;
            $existingJadwal[$key] = true;
        }
    });

// Find missing jadwal
$missingJadwal = [];
$missingByReason = [
    'no_makul' => 0,
    'no_tahun' => 0,
    'exists' => 0,
    'can_migrate' => 0,
];

foreach ($krsJadwal as $krs) {
    $makulId = $makulMap[$krs->IDMAKUL] ?? null;
    $tahunId = $tahunMap[$krs->TAHUN . $krs->SEMESTER] ?? null;
    
    if (!$makulId) {
        $missingByReason['no_makul']++;
        continue;
    }
    if (!$tahunId) {
        $missingByReason['no_tahun']++;
        continue;
    }
    
    $kelas = $krs->KELAS ?: 'A';
    if ($kelas === 'NULL') $kelas = 'A';
    
    $key = $krs->IDMAKUL . '_' . $krs->TAHUN . '_' . $krs->SEMESTER . '_' . $kelas;
    if (isset($existingJadwal[$key])) {
        $missingByReason['exists']++;
        continue;
    }
    
    $missingJadwal[$key] = $krs;
    $missingByReason['can_migrate']++;
}

echo "\nAnalisis jadwal yang hilang:\n";
echo "  Sudah ada di lokal   : {$missingByReason['exists']}\n";
echo "  Makul tidak ada      : {$missingByReason['no_makul']}\n";
echo "  Tahun tidak ada      : {$missingByReason['no_tahun']}\n";
echo "  Bisa dimigrasi       : {$missingByReason['can_migrate']}\n";

if ($missingByReason['can_migrate'] > 0) {
    echo "\n==========================================\n";
    echo "   MIGRASI JADWAL KULIAH                \n";
    echo "==========================================\n\n";
    
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
    $stats = ['jadwal' => 0, 'error' => 0];
    $defaultDosen = Dosen::first();
    $defaultRuangan = Ruangan::first();
    
    foreach ($missingJadwal as $key => $krs) {
        $makulId = $makulMap[$krs->IDMAKUL];
        $tahunId = $tahunMap[$krs->TAHUN . $krs->SEMESTER];
        $kelas = $krs->KELAS ?: 'A';
        if ($kelas === 'NULL') $kelas = 'A';
        
        // Get pengajar from source
        $pengajar = DB::connection('source')->table('kelaskuliah_pengajar')
            ->where('IDMAKUL', $krs->IDMAKUL)
            ->where('TAHUN', $krs->TAHUN)
            ->where('SEMESTER', $krs->SEMESTER)
            ->first();
        
        $dosenId = $defaultDosen->id ?? 1;
        if ($pengajar && isset($pengajar->IDPENGAJAR)) {
            $dosen = Dosen::where('nidn', $pengajar->IDPENGAJAR)->first();
            if ($dosen) $dosenId = $dosen->id;
        }
        
        // Get jadwal detail from kelaskuliah_jadwal if exists
        $jadwalDetail = DB::connection('source')->table('kelaskuliah_jadwal')
            ->where('IDMAKUL', $krs->IDMAKUL)
            ->where('TAHUN', $krs->TAHUN)
            ->where('SEMESTER', $krs->SEMESTER)
            ->first();
        
        $hari = 'Senin';
        $jamMulai = '08:00';
        $jamSelesai = '10:00';
        $ruanganId = $defaultRuangan->id ?? null;
        
        if ($jadwalDetail) {
            // Map hari
            $hariMap = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
            if (isset($jadwalDetail->HARI)) {
                $hari = $hariMap[$jadwalDetail->HARI] ?? 'Senin';
            }
            if (isset($jadwalDetail->JAMMULAI)) {
                $jamMulai = substr($jadwalDetail->JAMMULAI, 0, 5);
            }
            if (isset($jadwalDetail->JAMSELESAI)) {
                $jamSelesai = substr($jadwalDetail->JAMSELESAI, 0, 5);
            }
            if (isset($jadwalDetail->IDRUANG)) {
                $ruangan = Ruangan::where('kode', $jadwalDetail->IDRUANG)->first();
                if ($ruangan) $ruanganId = $ruangan->id;
            }
        }
        
        // Check if jadwal already exists (double check)
        $existing = JadwalKuliah::where('mata_kuliah_id', $makulId)
            ->where('tahun_akademik_id', $tahunId)
            ->where('kelas', $kelas)
            ->first();
        
        if ($existing) continue;
        
        try {
            JadwalKuliah::create([
                'mata_kuliah_id' => $makulId,
                'tahun_akademik_id' => $tahunId,
                'dosen_id' => $dosenId,
                'kelas' => $kelas,
                'hari' => $hari,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'ruangan_id' => $ruanganId,
                'kuota' => 40,
            ]);
            $stats['jadwal']++;
        } catch (Exception $e) {
            $stats['error']++;
        }
    }
    
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    
    echo "Jadwal baru ditambahkan: {$stats['jadwal']}\n";
    echo "Error: {$stats['error']}\n";
}

echo "\n==========================================\n";
echo "   HASIL AKHIR                          \n";
echo "==========================================\n";
echo "Total Jadwal Kuliah: " . JadwalKuliah::count() . "\n";
