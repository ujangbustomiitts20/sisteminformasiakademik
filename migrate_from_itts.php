<?php
/**
 * Script Migrasi Data dari Database itts_sikad ke SIAKAD Lokal
 * 
 * Urutan migrasi:
 * 1. Fakultas
 * 2. Program Studi (Prodi)
 * 3. Dosen
 * 4. Mahasiswa
 * 5. Mata Kuliah
 */

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;

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

// Tambahkan koneksi sumber
config(['database.connections.source' => $sourceConfig]);

echo "===========================================\n";
echo "   MIGRASI DATA itts_sikad → SIAKAD       \n";
echo "===========================================\n\n";

// Test koneksi
try {
    $testConn = DB::connection('source')->getPdo();
    echo "✅ Koneksi ke database sumber berhasil\n\n";
} catch (Exception $e) {
    echo "❌ Koneksi gagal: " . $e->getMessage() . "\n";
    exit(1);
}

// Disable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

$stats = [
    'fakultas' => 0,
    'prodi' => 0,
    'dosen' => 0,
    'mahasiswa' => 0,
    'matakuliah' => 0,
];

// ========================================
// 1. MIGRASI FAKULTAS
// ========================================
echo "1️⃣  Migrasi FAKULTAS...\n";

$fakultasSource = DB::connection('source')->table('fakultas')->get();
$fakultasMap = []; // Untuk mapping ID lama -> ID baru

foreach ($fakultasSource as $fak) {
    $existing = Fakultas::where('kode', $fak->ID)->first();
    
    if (!$existing) {
        $fakultas = Fakultas::create([
            'kode' => $fak->ID,
            'nama' => $fak->NAMA,
            'dekan' => $fak->NAMAPIMPINAN,
        ]);
        $fakultasMap[$fak->ID] = $fakultas->id;
        $stats['fakultas']++;
        echo "   ✅ {$fak->NAMA}\n";
    } else {
        $fakultasMap[$fak->ID] = $existing->id;
        echo "   ⏭️  {$fak->NAMA} (sudah ada)\n";
    }
}

// ========================================
// 2. MIGRASI PROGRAM STUDI
// ========================================
echo "\n2️⃣  Migrasi PROGRAM STUDI...\n";

$prodiSource = DB::connection('source')->table('prodi')->get();
$prodiMap = []; // Untuk mapping ID lama -> ID baru

foreach ($prodiSource as $prd) {
    $existing = ProgramStudi::where('kode', $prd->ID)->first();
    
    // Cari fakultas_id dari mapping (gunakan KODEFAKULTAS atau IDDEPARTEMEN)
    $fakultasKey = $prd->KODEFAKULTAS ?? $prd->IDDEPARTEMEN ?? null;
    $fakultasId = null;
    
    if ($fakultasKey && isset($fakultasMap[$fakultasKey])) {
        $fakultasId = $fakultasMap[$fakultasKey];
    } else {
        // Coba cari di mapping dengan berbagai format
        foreach ($fakultasMap as $key => $id) {
            if (stripos($key, $fakultasKey ?? '') !== false || stripos($fakultasKey ?? '', $key) !== false) {
                $fakultasId = $id;
                break;
            }
        }
    }
    
    if (!$fakultasId) {
        $fakultasId = Fakultas::first()?->id;
    }
    
    if (!$existing && $fakultasId) {
        // Parse jenjang dari nama atau ID
        $jenjang = 'S1';
        if (stripos($prd->NAMA, 'Magister') !== false || stripos($prd->NAMA, 'S2') !== false) {
            $jenjang = 'S2';
        } elseif (stripos($prd->NAMA, 'Doktor') !== false || stripos($prd->NAMA, 'S3') !== false) {
            $jenjang = 'S3';
        } elseif (stripos($prd->NAMA, 'D3') !== false || stripos($prd->NAMA, 'Diploma') !== false) {
            $jenjang = 'D3';
        }
        
        $prodi = ProgramStudi::create([
            'fakultas_id' => $fakultasId,
            'kode' => $prd->ID,
            'nama' => $prd->NAMA,
            'jenjang' => $jenjang,
        ]);
        $prodiMap[$prd->ID] = $prodi->id;
        $stats['prodi']++;
        echo "   ✅ {$prd->NAMA}\n";
    } else {
        $prodiMap[$prd->ID] = $existing->id ?? null;
        echo "   ⏭️  {$prd->NAMA} (sudah ada)\n";
    }
}

// ========================================
// 3. MIGRASI DOSEN
// ========================================
echo "\n3️⃣  Migrasi DOSEN...\n";

$dosenSource = DB::connection('source')->table('dosen')->get();

foreach ($dosenSource as $dsn) {
    $nidn = $dsn->NIDN ?: $dsn->ID;
    $existing = Dosen::where('nidn', $nidn)->first();
    if (!$existing && $dsn->EMAIL) {
        $existing = Dosen::where('email', $dsn->EMAIL)->first();
    }
    
    if (!$existing) {
        try {
            // Buat user untuk dosen
            $email = $dsn->EMAIL ?: strtolower(str_replace(' ', '', $dsn->NAMA)) . '@itts.ac.id';
        
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $dsn->NAMA,
                'password' => Hash::make('password123'),
                'role' => 'dosen',
            ]
        );
        
        // Tentukan program studi
        $prodiId = null;
        if (isset($dsn->KODEPRODI) && isset($prodiMap[$dsn->KODEPRODI])) {
            $prodiId = $prodiMap[$dsn->KODEPRODI];
        } elseif (isset($dsn->IDKONSENTRASI) && isset($prodiMap[$dsn->IDKONSENTRASI])) {
            $prodiId = $prodiMap[$dsn->IDKONSENTRASI];
        } elseif (isset($dsn->IDDEPARTEMEN) && isset($prodiMap[$dsn->IDDEPARTEMEN])) {
            $prodiId = $prodiMap[$dsn->IDDEPARTEMEN];
        } else {
            $prodiId = ProgramStudi::first()?->id;
        }
        
        // Mapping jenis kelamin
        $jenisKelamin = match($dsn->KELAMIN ?? null) {
            'L', 'l', '1' => 'L',
            'P', 'p', '2' => 'P',
            default => 'L',
        };
        
        // Mapping status
        $status = match($dsn->STATUS ?? null) {
            'A', 'a', '1' => 'Aktif',
            'N', 'n', '0' => 'Tidak Aktif',
            default => 'Aktif',
        };
        
        $dosen = Dosen::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodiId,
            'nidn' => $nidn,
            'nama' => $dsn->NAMA,
            'jenis_kelamin' => $jenisKelamin,
            'tempat_lahir' => $dsn->TEMPATLAHIR,
            'tanggal_lahir' => $dsn->TGLLAHIR,
            'alamat' => $dsn->ALAMAT,
            'telepon' => $dsn->TELEPON,
            'no_hp' => $dsn->HP,
            'email' => $email,
            'jabatan_fungsional' => $dsn->JABATAN,
            'golongan' => $dsn->GOLONGAN,
            'status' => $status,
            'gelar_depan' => $dsn->GELARDEPAN,
            'no_npwp' => $dsn->NPWP,
            'no_rekening' => $dsn->REKENING,
            'atas_nama_rekening' => $dsn->NAMAREKENING,
            'nama_bank' => $dsn->BANK,
        ]);
        
        $stats['dosen']++;
        echo "   ✅ {$dsn->NAMA}\n";
        } catch (Exception $e) {
            echo "   ⚠️  {$dsn->NAMA}: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   ⏭️  {$dsn->NAMA} (sudah ada)\n";
    }
}

// ========================================
// 4. MIGRASI MAHASISWA
// ========================================
echo "\n4️⃣  Migrasi MAHASISWA...\n";

$mahasiswaSource = DB::connection('source')->table('mahasiswa')->get();

foreach ($mahasiswaSource as $mhs) {
    $existing = Mahasiswa::where('nim', $mhs->ID)->first();
    
    if (!$existing) {
        try {
            // Buat user untuk mahasiswa
            $email = $mhs->EMAIL ?? strtolower($mhs->ID) . '@student.itts.ac.id';
        
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $mhs->NAMA ?? 'Mahasiswa ' . $mhs->ID,
                'password' => Hash::make('password123'),
                'role' => 'mahasiswa',
            ]
        );
        
        // Tentukan program studi - coba berbagai key
        $prodiId = null;
        $prodiKeys = [$mhs->IDPRODI ?? null, $mhs->KODEPRODI ?? null];
        foreach ($prodiKeys as $key) {
            if ($key && isset($prodiMap[$key])) {
                $prodiId = $prodiMap[$key];
                break;
            }
        }
        // Jika tidak ditemukan, cari berdasarkan kode prodi di database
        if (!$prodiId && ($mhs->IDPRODI || $mhs->KODEPRODI)) {
            $prodi = ProgramStudi::where('kode', $mhs->IDPRODI)
                ->orWhere('kode', $mhs->KODEPRODI)
                ->first();
            $prodiId = $prodi?->id;
        }
        if (!$prodiId) {
            $prodiId = ProgramStudi::first()?->id;
        }
        
        // Mapping jenis kelamin dari tabel mahasiswa langsung
        $jenisKelamin = match($mhs->KELAMIN ?? null) {
            'L', 'l', '1', 'M' => 'L',
            'P', 'p', '2', 'F' => 'P',
            default => 'L',
        };
        
        // Mapping status
        $status = match($mhs->STATUS ?? null) {
            'A', 'a', '1' => 'Aktif',
            'C' => 'Cuti',
            'L' => 'Lulus',
            'K' => 'Keluar',
            'N', 'n', '0' => 'Tidak Aktif',
            default => 'Aktif',
        };
        
        // Extract angkatan dari NIM (biasanya 2 digit tahun di awal)
        $angkatan = null;
        if (preg_match('/^(\d{2})/', $mhs->ID, $matches)) {
            $year = (int) $matches[1];
            $angkatan = $year > 50 ? 1900 + $year : 2000 + $year;
        }
        
        $mahasiswa = Mahasiswa::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodiId,
            'nim' => $mhs->ID,
            'nama' => $mhs->NAMA ?? 'Mahasiswa ' . $mhs->ID,
            'jenis_kelamin' => $jenisKelamin,
            'tempat_lahir' => $mhs->TEMPAT ?? null,
            'tanggal_lahir' => ($mhs->TANGGAL && $mhs->TANGGAL != '0000-00-00') ? $mhs->TANGGAL : null,
            'alamat' => $mhs->ALAMAT ?? null,
            'telepon' => $mhs->TELEPON ?? null,
            'no_hp' => $mhs->HP ?? $mhs->NOWA ?? null,
            'email' => $email,
            'nik' => $mhs->NIK ?? null,
            'no_kk' => $mhs->NOKK ?? null,
            'agama' => $mhs->AGAMA ?? null,
            'angkatan' => $mhs->ANGKATAN ?? $angkatan,
            'status' => $status,
            'nama_ayah' => $mhs->NAMAAYAH ?? null,
            'nik_ayah' => $mhs->NIKAYAH ?? null,
            'nama_ibu' => $mhs->NAMAIBU ?? null,
            'nik_ibu' => $mhs->NIKIBU ?? null,
            'no_hp_ortu' => $mhs->NOAYAH ?? $mhs->NOIBU ?? null,
            'nama_wali' => $mhs->NAMAWALI ?? null,
            'asal_sekolah' => $mhs->ASAL ?? null,
        ]);
        
        $stats['mahasiswa']++;
        
        if ($stats['mahasiswa'] % 100 == 0) {
            echo "   ✅ Processed {$stats['mahasiswa']} mahasiswa...\n";
        }
        } catch (Exception $e) {
            // Skip error, continue to next
        }
    }
}
echo "   ✅ Total: {$stats['mahasiswa']} mahasiswa\n";

// ========================================
// 5. MIGRASI MATA KULIAH
// ========================================
echo "\n5️⃣  Migrasi MATA KULIAH...\n";

$makulSource = DB::connection('source')->table('makul')->get();

foreach ($makulSource as $mk) {
    $existing = MataKuliah::where('kode', $mk->ID)->first();
    
    if (!$existing) {
        // Tentukan program studi
        $prodiId = null;
        if (isset($mk->IDPRODI) && isset($prodiMap[$mk->IDPRODI])) {
            $prodiId = $prodiMap[$mk->IDPRODI];
        } else {
            $prodiId = ProgramStudi::first()?->id;
        }
        
        // Mapping jenis: W/Wajib -> Wajib, P/Pilihan -> Pilihan
        $jenis = 'Wajib';
        if (isset($mk->JENIS)) {
            $jenis = in_array(strtoupper($mk->JENIS), ['P', 'PILIHAN']) ? 'Pilihan' : 'Wajib';
        }
        
        $mataKuliah = MataKuliah::create([
            'program_studi_id' => $prodiId,
            'kode' => $mk->ID,
            'nama' => $mk->NAMA,
            'sks' => $mk->SKS ?? 3,
            'semester' => $mk->SEMESTER ?? 1,
            'jenis' => $jenis,
            'jumlah_pertemuan' => 16,
        ]);
        
        $stats['matakuliah']++;
        echo "   ✅ {$mk->NAMA}\n";
    } else {
        echo "   ⏭️  {$mk->NAMA} (sudah ada)\n";
    }
}

// Re-enable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

// ========================================
// SUMMARY
// ========================================
echo "\n===========================================\n";
echo "   MIGRASI SELESAI                        \n";
echo "===========================================\n";
echo "   Fakultas    : {$stats['fakultas']} data\n";
echo "   Prodi       : {$stats['prodi']} data\n";
echo "   Dosen       : {$stats['dosen']} data\n";
echo "   Mahasiswa   : {$stats['mahasiswa']} data\n";
echo "   Mata Kuliah : {$stats['matakuliah']} data\n";
echo "===========================================\n";

// Verifikasi
echo "\nVerifikasi data di database lokal:\n";
echo "   Fakultas    : " . Fakultas::count() . "\n";
echo "   Prodi       : " . ProgramStudi::count() . "\n";
echo "   Dosen       : " . Dosen::count() . "\n";
echo "   Mahasiswa   : " . Mahasiswa::count() . "\n";
echo "   Mata Kuliah : " . MataKuliah::count() . "\n";
echo "   Users       : " . User::count() . "\n";
