<?php
/**
 * Script Migrasi Data Tambahan dari Database itts_sikad ke SIAKAD Lokal
 * 
 * Migrasi:
 * 1. Tahun Akademik
 * 2. Jadwal Kuliah (dari kelaskuliah)
 * 3. KRS (Kartu Rencana Studi) - langsung punya jadwal_kuliah_id
 * 4. Nilai (terhubung ke KRS via krs_id)
 * 
 * Struktur tabel lokal:
 * - krs: mahasiswa_id, tahun_akademik_id, jadwal_kuliah_id, status
 * - nilai: krs_id, tugas, uts, uas, nilai_akhir, huruf, bobot
 * - jadwal_kuliah: tahun_akademik_id, mata_kuliah_id, dosen_id, ruangan_id, kelas, hari, jam_mulai, jam_selesai, kuota
 */

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Models\Krs;
use App\Models\Nilai;
use App\Models\JadwalKuliah;
use App\Models\Ruangan;

// Konfigurasi database sumber
$sourceConfig = [
    'driver' => 'mysql',
    'host' => '192.168.120.121',
    'port' => '3306',
    'database' => 'itts_sikad',
    'username' => 'root',
    'password' => 'bismillaH',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];

config(['database.connections.source' => $sourceConfig]);

echo "===========================================\n";
echo "   MIGRASI DATA TAMBAHAN                  \n";
echo "===========================================\n\n";

// Test koneksi
try {
    $testConn = DB::connection('source')->getPdo();
    echo "✅ Koneksi ke database sumber berhasil\n\n";
} catch (Exception $e) {
    echo "❌ Koneksi gagal: " . $e->getMessage() . "\n";
    exit(1);
}

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

$stats = [
    'tahun_akademik' => 0,
    'jadwal' => 0,
    'krs' => 0,
    'nilai' => 0,
];

// ========================================
// 1. MIGRASI TAHUN AKADEMIK
// ========================================
echo "1️⃣  Migrasi TAHUN AKADEMIK...\n";

// Ambil tahun unik dari kelaskuliah (lebih lengkap)
$tahunSource = DB::connection('source')
    ->table('kelaskuliah')
    ->selectRaw('DISTINCT TAHUN, SEMESTER')
    ->orderBy('TAHUN')
    ->orderBy('SEMESTER')
    ->get();

$tahunMap = [];

// Fungsi helper untuk convert semester
function convertSemester($sem) {
    // 1,3 = Ganjil, 2,4 = Genap
    return in_array($sem, [1, 3]) ? 'Ganjil' : 'Genap';
}

foreach ($tahunSource as $ta) {
    $kode = $ta->TAHUN . $ta->SEMESTER;
    $semester = convertSemester($ta->SEMESTER);
    
    $existing = TahunAkademik::where('tahun', $ta->TAHUN)
        ->where('semester', $semester)
        ->first();
    
    if (!$existing) {
        try {
            $isGanjil = $semester == 'Ganjil';
            $tahunAkademik = TahunAkademik::create([
                'tahun' => $ta->TAHUN,
                'semester' => $semester,
                'tanggal_mulai' => $ta->TAHUN . '-' . ($isGanjil ? '09' : '02') . '-01',
                'tanggal_selesai' => ($isGanjil ? $ta->TAHUN : $ta->TAHUN + 1) . '-' . ($isGanjil ? '01' : '07') . '-28',
                'mulai_krs' => $ta->TAHUN . '-' . ($isGanjil ? '08' : '01') . '-01',
                'selesai_krs' => $ta->TAHUN . '-' . ($isGanjil ? '09' : '02') . '-15',
                'is_aktif' => false,
            ]);
            $tahunMap[$kode] = $tahunAkademik->id;
            $stats['tahun_akademik']++;
            echo "   ✅ {$ta->TAHUN}/{$semester} (dari semester {$ta->SEMESTER})\n";
        } catch (Exception $e) {
            echo "   ⚠️  {$ta->TAHUN}/{$semester}: " . $e->getMessage() . "\n";
            // Even if failed, try to get existing
            $existing = TahunAkademik::where('tahun', $ta->TAHUN)->where('semester', $semester)->first();
            if ($existing) $tahunMap[$kode] = $existing->id;
        }
    } else {
        $tahunMap[$kode] = $existing->id;
        echo "   ⏭️  {$ta->TAHUN}/{$semester} (sudah ada)\n";
    }
}

// Set tahun akademik aktif (terbaru)
$latestTA = TahunAkademik::orderBy('tahun', 'desc')->orderByRaw("FIELD(semester, 'Ganjil', 'Genap') DESC")->first();
if ($latestTA) {
    TahunAkademik::where('id', '!=', $latestTA->id)->update(['is_aktif' => false]);
    $latestTA->update(['is_aktif' => true]);
    echo "   📌 Tahun Akademik Aktif: {$latestTA->tahun}/{$latestTA->semester}\n";
}

// ========================================
// 2. MIGRASI JADWAL/KELAS KULIAH
// ========================================
echo "\n2️⃣  Migrasi JADWAL KULIAH...\n";

$kelasSource = DB::connection('source')
    ->table('kelaskuliah')
    ->whereNotNull('IDMAKUL')
    ->where('IDMAKUL', '!=', '')
    ->whereRaw("TRIM(IDMAKUL) != ''")
    ->get();

echo "   Total data kelaskuliah: " . count($kelasSource) . "\n";

// Build mapping untuk mahasiswa dan matakuliah
$mahasiswaMap = Mahasiswa::pluck('id', 'nim')->toArray();
$matakuliahMap = MataKuliah::pluck('id', 'kode')->toArray();
$dosenMap = Dosen::pluck('id', 'nidn')->toArray();
$defaultDosenId = Dosen::first()?->id ?? 1;

// Create default ruangan if not exists
$defaultRuangan = Ruangan::firstOrCreate(
    ['kode' => 'DEFAULT'],
    [
        'nama' => 'Ruangan Default',
        'kapasitas' => 40,
        'gedung' => 'Gedung A',
        'lantai' => '1',
        'jenis' => 'Kelas',
    ]
);
$defaultRuanganId = $defaultRuangan->id;

echo "   Default Dosen ID: {$defaultDosenId}\n";
echo "   Default Ruangan ID: {$defaultRuanganId}\n";

$skipCount = 0;
$existCount = 0;

foreach ($kelasSource as $kls) {
    $kode = $kls->TAHUN . $kls->SEMESTER;
    $semester = convertSemester($kls->SEMESTER);
    $tahunAkademikId = $tahunMap[$kode] ?? TahunAkademik::where('tahun', $kls->TAHUN)->where('semester', $semester)->first()?->id;
    $mataKuliahId = $matakuliahMap[$kls->IDMAKUL] ?? null;
    $prodiId = ProgramStudi::where('kode', $kls->IDPRODI)->first()?->id ?? ProgramStudi::first()?->id;
    
    if (!$tahunAkademikId || !$mataKuliahId) {
        $skipCount++;
        continue;
    }
    
    $kelasNama = $kls->IDKELAS ?: 'A';
    
    $existing = JadwalKuliah::where('tahun_akademik_id', $tahunAkademikId)
        ->where('mata_kuliah_id', $mataKuliahId)
        ->where('kelas', $kelasNama)
        ->first();
    
    if (!$existing) {
        try {
            $jadwal = JadwalKuliah::create([
                'tahun_akademik_id' => $tahunAkademikId,
                'mata_kuliah_id' => $mataKuliahId,
                'dosen_id' => $defaultDosenId,
                'ruangan_id' => $defaultRuanganId,
                'kelas' => $kelasNama,
                'kuota' => $kls->KAPASITAS ?: 40,
                'hari' => 'Senin', // Default
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:00',
            ]);
            $stats['jadwal']++;
        } catch (Exception $e) {
            echo "   ⚠️  Error: " . $e->getMessage() . "\n";
        }
    } else {
        $existCount++;
    }
}
echo "   ⚠️  Skip (no tahun/makul): {$skipCount}\n";
echo "   ⏭️  Sudah ada: {$existCount}\n";
echo "   ✅ Total: {$stats['jadwal']} jadwal\n";

// ========================================
// 3. MIGRASI KRS (dengan jadwal_kuliah_id)
// ========================================
echo "\n3️⃣  Migrasi KRS...\n";

// Build jadwal map: tahun_semester_matakuliah_kelas => jadwal_id
$jadwalMap = [];
$jadwals = JadwalKuliah::with('tahunAkademik')->get();
foreach ($jadwals as $jd) {
    $sem = $jd->tahunAkademik->semester == 'Ganjil' ? 1 : 2;
    $key = $jd->tahunAkademik->tahun . '_' . $sem . '_' . $jd->mata_kuliah_id . '_' . $jd->kelas;
    $jadwalMap[$key] = $jd->id;
}

$krssSource = DB::connection('source')->table('krss')->get();

$krsMap = []; // Untuk mapping ke nilai nanti

foreach ($krssSource as $krss) {
    $mahasiswaId = $mahasiswaMap[$krss->IDMAHASISWA] ?? null;
    $mataKuliahId = $matakuliahMap[$krss->IDMAKUL] ?? null;
    $kode = $krss->TAHUN . $krss->SEMESTER;
    $semester = convertSemester($krss->SEMESTER);
    $tahunAkademikId = $tahunMap[$kode] ?? TahunAkademik::where('tahun', $krss->TAHUN)->where('semester', $semester)->first()?->id;
    
    if (!$mahasiswaId || !$tahunAkademikId) continue;
    
    // Find or create jadwal
    $kelas = $krss->KELAS ?: 'A';
    $jadwalKey = $krss->TAHUN . '_' . $krss->SEMESTER . '_' . $mataKuliahId . '_' . $kelas;
    $jadwalId = $jadwalMap[$jadwalKey] ?? null;
    
    // Create jadwal if not exists (for mata kuliah that have KRS but no jadwal)
    if (!$jadwalId && $mataKuliahId) {
        try {
            $jadwal = JadwalKuliah::create([
                'tahun_akademik_id' => $tahunAkademikId,
                'mata_kuliah_id' => $mataKuliahId,
                'dosen_id' => $defaultDosenId,
                'ruangan_id' => $defaultRuanganId,
                'kelas' => $kelas,
                'kuota' => 40,
                'hari' => 'Senin',
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:00',
            ]);
            $jadwalId = $jadwal->id;
            $jadwalMap[$jadwalKey] = $jadwalId;
            $stats['jadwal']++;
        } catch (Exception $e) {
            continue;
        }
    }
    
    if (!$jadwalId) continue;
    
    $existing = Krs::where('mahasiswa_id', $mahasiswaId)
        ->where('tahun_akademik_id', $tahunAkademikId)
        ->where('jadwal_kuliah_id', $jadwalId)
        ->first();
    
    if (!$existing) {
        try {
            $krs = Krs::create([
                'mahasiswa_id' => $mahasiswaId,
                'tahun_akademik_id' => $tahunAkademikId,
                'jadwal_kuliah_id' => $jadwalId,
                'status' => 'Disetujui',
                'tanggal_pengajuan' => now(),
                'tanggal_persetujuan' => now(),
            ]);
            // Map untuk nilai nanti: mahasiswa_makul_tahun_semester => krs_id
            $nilaiKey = $krss->IDMAHASISWA . '_' . $krss->IDMAKUL . '_' . $krss->TAHUN . '_' . $krss->SEMESTER;
            $krsMap[$nilaiKey] = $krs->id;
            $stats['krs']++;
        } catch (Exception $e) {
            // Skip duplicates
        }
    } else {
        $nilaiKey = $krss->IDMAHASISWA . '_' . $krss->IDMAKUL . '_' . $krss->TAHUN . '_' . $krss->SEMESTER;
        $krsMap[$nilaiKey] = $existing->id;
    }
}
echo "   ✅ Total: {$stats['krs']} KRS baru\n";
echo "   ✅ Jadwal auto-created: {$stats['jadwal']}\n";

// ========================================
// 4. MIGRASI NILAI (terhubung ke krs_id)
// ========================================
echo "\n4️⃣  Migrasi NILAI...\n";

$nilaiSource = DB::connection('source')->table('nilai')->get();

// Mapping simbol nilai
function getNilaiHuruf($nilai) {
    if ($nilai >= 85) return 'A';
    if ($nilai >= 80) return 'A-';
    if ($nilai >= 75) return 'B+';
    if ($nilai >= 70) return 'B';
    if ($nilai >= 65) return 'B-';
    if ($nilai >= 60) return 'C+';
    if ($nilai >= 55) return 'C';
    if ($nilai >= 50) return 'C-';
    if ($nilai >= 45) return 'D';
    return 'E';
}

function getBobot($huruf) {
    return match($huruf) {
        'A' => 4.0,
        'A-' => 3.75,
        'B+' => 3.5,
        'B' => 3.0,
        'B-' => 2.75,
        'C+' => 2.5,
        'C' => 2.0,
        'C-' => 1.75,
        'D' => 1.0,
        default => 0.0,
    };
}

foreach ($nilaiSource as $nls) {
    // Build key untuk mencari KRS
    $nilaiKey = $nls->IDMAHASISWA . '_' . $nls->IDMAKUL . '_' . $nls->TAHUN . '_' . $nls->SEMESTER;
    $krsId = $krsMap[$nilaiKey] ?? null;
    
    if (!$krsId) continue;
    
    $existing = Nilai::where('krs_id', $krsId)->first();
    
    if (!$existing) {
        try {
            $nilaiAngka = floatval($nls->NILAI);
            $huruf = getNilaiHuruf($nilaiAngka);
            $bobot = getBobot($huruf);
            
            Nilai::create([
                'krs_id' => $krsId,
                'tugas' => $nilaiAngka * 0.3, // Estimasi distribusi
                'uts' => $nilaiAngka * 0.35,
                'uas' => $nilaiAngka * 0.35,
                'nilai_akhir' => $nilaiAngka,
                'huruf' => $huruf,
                'bobot' => $bobot,
            ]);
            $stats['nilai']++;
        } catch (Exception $e) {
            // Skip errors
        }
    }
    
    if ($stats['nilai'] % 1000 == 0 && $stats['nilai'] > 0) {
        echo "   ✅ Processed {$stats['nilai']} nilai...\n";
    }
}
echo "   ✅ Total: {$stats['nilai']} nilai\n";

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

// ========================================
// SUMMARY
// ========================================
echo "\n===========================================\n";
echo "   MIGRASI TAMBAHAN SELESAI               \n";
echo "===========================================\n";
echo "   Tahun Akademik : {$stats['tahun_akademik']} data\n";
echo "   Jadwal Kuliah  : {$stats['jadwal']} data\n";
echo "   KRS            : {$stats['krs']} data\n";
echo "   Nilai          : {$stats['nilai']} data\n";
echo "===========================================\n";

// Verifikasi
echo "\nVerifikasi data di database lokal:\n";
echo "   Tahun Akademik : " . TahunAkademik::count() . "\n";
echo "   Jadwal Kuliah  : " . JadwalKuliah::count() . "\n";
echo "   KRS            : " . Krs::count() . "\n";
echo "   Nilai          : " . Nilai::count() . "\n";
