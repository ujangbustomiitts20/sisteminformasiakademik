<?php

/**
 * Script Migrasi Data dari Legacy Database (itts_sikad)
 * 
 * Migrasi:
 * 1. Mahasiswa yang belum ada (+163)
 * 2. Penerima Beasiswa (164 records)
 * 3. Cuti Akademik (2 records)
 * 
 * Jalankan dengan: php migrate_all_legacy.php
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== MIGRASI DATA LEGACY ===" . PHP_EOL;
echo "Source: itts_sikad @ 192.168.120.121" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;

// Koneksi ke database source
$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
if ($source->connect_error) {
    die("Koneksi ke source database gagal: " . $source->connect_error . PHP_EOL);
}
$source->set_charset("utf8mb4");

// ============================================================
// 1. MIGRASI MAHASISWA
// ============================================================
echo PHP_EOL . "╔══════════════════════════════════════════════════════════╗" . PHP_EOL;
echo "║  1. MIGRASI MAHASISWA                                    ║" . PHP_EOL;
echo "╚══════════════════════════════════════════════════════════╝" . PHP_EOL;

// Get existing NIMs
$existingNims = DB::table('mahasiswa')->pluck('nim')->toArray();
echo "Existing mahasiswa in local: " . count($existingNims) . PHP_EOL;

// Build prodi map
$prodiMap = DB::table('program_studi')->pluck('id', 'kode')->toArray();
echo "Prodi map: " . implode(', ', array_keys($prodiMap)) . PHP_EOL;

// Build dosen map (NIDN -> id)
$dosenMap = DB::table('dosen')->pluck('id', 'nidn')->toArray();
echo "Dosen count: " . count($dosenMap) . PHP_EOL;

// Get legacy mahasiswa not in local
$result = $source->query("SELECT * FROM mahasiswa ORDER BY ID");
$mhsSuccess = 0;
$mhsSkipped = 0;
$mhsFailed = 0;

while ($row = $result->fetch_assoc()) {
    $nim = trim($row['ID']);
    
    if (in_array($nim, $existingNims)) {
        $mhsSkipped++;
        continue;
    }
    
    // Map prodi (extract from NIM pattern or use IDPRODI)
    $prodiId = null;
    $prodiKode = trim($row['IDPRODI']);
    if (isset($prodiMap[$prodiKode])) {
        $prodiId = $prodiMap[$prodiKode];
    } else {
        // Try to extract from NIM (e.g., 1001 = prodi 1001)
        $nimProdi = substr($nim, 0, 4);
        if (isset($prodiMap[$nimProdi])) {
            $prodiId = $prodiMap[$nimProdi];
        } else {
            // Default to first prodi
            $prodiId = reset($prodiMap);
        }
    }
    
    // Map dosen wali
    $dosenWaliId = null;
    if (!empty($row['IDDOSEN']) && isset($dosenMap[$row['IDDOSEN']])) {
        $dosenWaliId = $dosenMap[$row['IDDOSEN']];
    }
    
    // Map jenis kelamin
    $jenisKelamin = $row['KELAMIN'] == 'L' ? 'L' : ($row['KELAMIN'] == 'P' ? 'P' : null);
    
    // Map status
    $statusMap = [
        'A' => 'Aktif',
        'C' => 'Cuti',
        'N' => 'Non-Aktif',
        'L' => 'Lulus',
        'D' => 'DO',
        'K' => 'Keluar',
    ];
    $status = $statusMap[$row['STATUS']] ?? 'Aktif';
    
    $email = strtolower($nim) . '@student.itts.ac.id';
    
    $data = [
        'nim' => $nim,
        'nama' => $row['NAMA'] ?? 'Mahasiswa ' . $nim,
        'email' => $email, // Add email field
        'program_studi_id' => $prodiId,
        'dosen_wali_id' => $dosenWaliId,
        'jenis_kelamin' => $jenisKelamin,
        'tempat_lahir' => $row['TEMPAT'] ?: null,
        'tanggal_lahir' => ($row['TANGGAL'] && $row['TANGGAL'] != '0000-00-00') ? $row['TANGGAL'] : null,
        'alamat' => $row['ALAMAT'] ?: null,
        'rt' => $row['RT'] ?: null,
        'rw' => $row['RW'] ?: null,
        'kode_pos' => $row['KODEPOS'] ?: null,
        'telepon' => $row['TELEPON'] ?: null,
        'no_hp' => $row['NOWA'] ?: null,
        'nik' => $row['NIK'] ?: null,
        'no_kk' => $row['NOKK'] ?: null,
        'angkatan' => $row['ANGKATAN'] ?: null,
        'asal_sekolah' => $row['ASAL'] ?: null,
        'semester_aktif' => 1,
        'status' => $status,
        'created_at' => now(),
        'updated_at' => now(),
    ];
    
    try {
        // Create user first
        $email = strtolower($nim) . '@student.itts.ac.id';
        $existingUser = DB::table('users')->where('email', $email)->first();
        
        if ($existingUser) {
            $userId = $existingUser->id;
        } else {
            $userId = DB::table('users')->insertGetId([
                'name' => $row['NAMA'] ?? 'Mahasiswa ' . $nim,
                'email' => $email,
                'password' => bcrypt('password123'),
                'role' => 'mahasiswa',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Add user_id to data
        $data['user_id'] = $userId;
        
        DB::table('mahasiswa')->insert($data);
        $mhsSuccess++;
        
        if ($mhsSuccess % 50 == 0) {
            echo "  Processed $mhsSuccess mahasiswa..." . PHP_EOL;
        }
    } catch (\Exception $e) {
        $mhsFailed++;
        echo "  ERROR $nim: " . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL . "Mahasiswa Migration Result:" . PHP_EOL;
echo "  - Success: $mhsSuccess" . PHP_EOL;
echo "  - Skipped (exists): $mhsSkipped" . PHP_EOL;
echo "  - Failed: $mhsFailed" . PHP_EOL;

// Refresh mahasiswa map after insert
$mahasiswaMap = DB::table('mahasiswa')->pluck('id', 'nim')->toArray();
echo "  - Total mahasiswa now: " . count($mahasiswaMap) . PHP_EOL;

// ============================================================
// 2. MIGRASI BEASISWA
// ============================================================
echo PHP_EOL . "╔══════════════════════════════════════════════════════════╗" . PHP_EOL;
echo "║  2. MIGRASI PENERIMA BEASISWA                            ║" . PHP_EOL;
echo "╚══════════════════════════════════════════════════════════╝" . PHP_EOL;

// First, ensure beasiswa types exist
$jenisBeasiswaLegacy = [
    '01' => ['nama' => 'Beasiswa Yayasan', 'jenis' => 'Beasiswa'],
    '02' => ['nama' => 'Beasiswa KIP-K', 'jenis' => 'Beasiswa'],
    '03' => ['nama' => 'Potongan Biaya Kuliah', 'jenis' => 'Potongan'],
];

$beasiswaMap = [];
foreach ($jenisBeasiswaLegacy as $kode => $data) {
    $beasiswa = DB::table('beasiswa')->where('kode', $kode)->first();
    if (!$beasiswa) {
        $beasiswaId = DB::table('beasiswa')->insertGetId([
            'kode' => $kode,
            'nama' => $data['nama'],
            'jenis' => $data['jenis'],
            'tipe_potongan' => 'Persen',
            'nilai_potongan' => 100,
            'sumber_dana' => $data['jenis'] == 'Beasiswa' ? 'Internal' : null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "  Created beasiswa: {$data['nama']} (ID: $beasiswaId)" . PHP_EOL;
        $beasiswaMap[$kode] = $beasiswaId;
    } else {
        $beasiswaMap[$kode] = $beasiswa->id;
    }
}

// Build tahun akademik map (year-semester -> id)
$tahunAkademikMap = [];
$tahunAkademikList = DB::table('tahun_akademik')->get();
foreach ($tahunAkademikList as $ta) {
    $semester = ($ta->semester == 'Ganjil') ? 1 : 2;
    $key = $ta->tahun . '-' . $semester;
    $tahunAkademikMap[$key] = $ta->id;
}
echo "Tahun Akademik map count: " . count($tahunAkademikMap) . PHP_EOL;

// Get legacy beasiswa data
$result = $source->query("SELECT * FROM mahasiswa_beasiswaoperator ORDER BY TAHUN, SEMESTER");
$beasiswaSuccess = 0;
$beasiswaSkipped = 0;
$beasiswaFailed = 0;

while ($row = $result->fetch_assoc()) {
    $nim = trim($row['IDMAHASISWA']);
    
    // Check if mahasiswa exists
    if (!isset($mahasiswaMap[$nim])) {
        $beasiswaSkipped++;
        continue;
    }
    
    $mahasiswaId = $mahasiswaMap[$nim];
    $beasiswaId = $beasiswaMap[$row['IDJENIS']] ?? null;
    
    if (!$beasiswaId) {
        $beasiswaSkipped++;
        continue;
    }
    
    // Find tahun akademik
    $taKey = $row['TAHUN'] . '-' . $row['SEMESTER'];
    $tahunAkademikId = $tahunAkademikMap[$taKey] ?? null;
    
    if (!$tahunAkademikId) {
        // Try to find any matching year
        foreach ($tahunAkademikMap as $key => $id) {
            if (strpos($key, $row['TAHUN']) === 0) {
                $tahunAkademikId = $id;
                break;
            }
        }
    }
    
    // Check if already exists
    $exists = DB::table('penerima_beasiswa')
        ->where('mahasiswa_id', $mahasiswaId)
        ->where('beasiswa_id', $beasiswaId)
        ->where('tahun_akademik_id', $tahunAkademikId)
        ->exists();
    
    if ($exists) {
        $beasiswaSkipped++;
        continue;
    }
    
    $data = [
        'beasiswa_id' => $beasiswaId,
        'mahasiswa_id' => $mahasiswaId,
        'tahun_akademik_id' => $tahunAkademikId,
        'status' => 'Disetujui', // Legacy data is approved
        'tanggal_mulai' => $row['DATECREATED'] ? Carbon::parse($row['DATECREATED'])->format('Y-m-d') : null,
        'keterangan' => 'Migrasi dari legacy database',
        'created_at' => $row['DATECREATED'] ?: now(),
        'updated_at' => $row['LASTUPDATE'] ?: now(),
    ];
    
    try {
        DB::table('penerima_beasiswa')->insert($data);
        $beasiswaSuccess++;
    } catch (\Exception $e) {
        $beasiswaFailed++;
        echo "  ERROR beasiswa $nim: " . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL . "Penerima Beasiswa Migration Result:" . PHP_EOL;
echo "  - Success: $beasiswaSuccess" . PHP_EOL;
echo "  - Skipped: $beasiswaSkipped" . PHP_EOL;
echo "  - Failed: $beasiswaFailed" . PHP_EOL;

// ============================================================
// 3. MIGRASI CUTI AKADEMIK
// ============================================================
echo PHP_EOL . "╔══════════════════════════════════════════════════════════╗" . PHP_EOL;
echo "║  3. MIGRASI CUTI AKADEMIK                                ║" . PHP_EOL;
echo "╚══════════════════════════════════════════════════════════╝" . PHP_EOL;

// Status mapping
$statusCutiMap = [
    'B' => 'Disetujui Dekan', // Berjalan = Disetujui
    'U' => 'Pending',         // Usulan
    'S' => 'Selesai',         // Selesai
    'T' => 'Ditolak',         // Tolak
];

// Alasan mapping
$alasanMap = [
    'Keuangan' => 'Keuangan',
    'Ada kepentingan pribadi' => 'Lainnya',
];

$result = $source->query("SELECT * FROM cuti_usulan");
$cutiSuccess = 0;
$cutiSkipped = 0;
$cutiFailed = 0;

while ($row = $result->fetch_assoc()) {
    $nim = trim($row['IDMAHASISWA']);
    
    // Check if mahasiswa exists
    if (!isset($mahasiswaMap[$nim])) {
        $cutiSkipped++;
        echo "  SKIP: Mahasiswa $nim not found" . PHP_EOL;
        continue;
    }
    
    $mahasiswaId = $mahasiswaMap[$nim];
    
    // Find tahun akademik
    $taKey = $row['TAHUN'] . '-' . $row['SEMESTER'];
    $tahunAkademikId = $tahunAkademikMap[$taKey] ?? null;
    
    if (!$tahunAkademikId) {
        foreach ($tahunAkademikMap as $key => $id) {
            if (strpos($key, $row['TAHUN']) === 0) {
                $tahunAkademikId = $id;
                break;
            }
        }
    }
    
    // Check if already exists
    $exists = DB::table('cuti_akademik')
        ->where('mahasiswa_id', $mahasiswaId)
        ->where('tahun_akademik_id', $tahunAkademikId)
        ->exists();
    
    if ($exists) {
        $cutiSkipped++;
        continue;
    }
    
    $status = $statusCutiMap[$row['STATUS']] ?? 'Pending';
    $alasan = $alasanMap[$row['ALASAN']] ?? 'Lainnya';
    
    // Calculate tanggal_mulai from tahun and semester
    $tahunCuti = $row['TAHUN'] ?: date('Y');
    $semesterCuti = $row['SEMESTER'] ?: 1;
    $tanggalMulai = $semesterCuti == 1 ? "$tahunCuti-09-01" : "$tahunCuti-02-01";
    $tanggalSelesai = $semesterCuti == 1 ? "$tahunCuti-01-31" : "$tahunCuti-07-31";
    if ($semesterCuti == 1) {
        $tanggalSelesai = ($tahunCuti + 1) . "-01-31";
    }
    
    $data = [
        'mahasiswa_id' => $mahasiswaId,
        'tahun_akademik_id' => $tahunAkademikId,
        'alasan' => $alasan,
        'keterangan' => $row['ALASAN'] . ($row['CATATAN'] ? '. ' . $row['CATATAN'] : ''),
        'jumlah_semester' => 1,
        'tanggal_mulai' => $tanggalMulai,
        'tanggal_selesai' => $tanggalSelesai,
        'status' => $status,
        'created_at' => now(),
        'updated_at' => now(),
    ];
    
    try {
        DB::table('cuti_akademik')->insert($data);
        $cutiSuccess++;
        echo "  Migrated cuti for $nim (Status: $status)" . PHP_EOL;
    } catch (\Exception $e) {
        $cutiFailed++;
        echo "  ERROR cuti $nim: " . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL . "Cuti Akademik Migration Result:" . PHP_EOL;
echo "  - Success: $cutiSuccess" . PHP_EOL;
echo "  - Skipped: $cutiSkipped" . PHP_EOL;
echo "  - Failed: $cutiFailed" . PHP_EOL;

// ============================================================
// SUMMARY
// ============================================================
$source->close();

echo PHP_EOL . "╔══════════════════════════════════════════════════════════╗" . PHP_EOL;
echo "║  RINGKASAN MIGRASI                                       ║" . PHP_EOL;
echo "╚══════════════════════════════════════════════════════════╝" . PHP_EOL;
echo PHP_EOL;
echo "1. Mahasiswa      : +$mhsSuccess records" . PHP_EOL;
echo "2. Penerima Beasiswa: +$beasiswaSuccess records" . PHP_EOL;
echo "3. Cuti Akademik  : +$cutiSuccess records" . PHP_EOL;
echo PHP_EOL;

// Final counts
echo "=== DATA AKHIR ===" . PHP_EOL;
echo "Mahasiswa        : " . DB::table('mahasiswa')->count() . PHP_EOL;
echo "Penerima Beasiswa: " . DB::table('penerima_beasiswa')->count() . PHP_EOL;
echo "Cuti Akademik    : " . DB::table('cuti_akademik')->count() . PHP_EOL;
echo "Beasiswa (tipe)  : " . DB::table('beasiswa')->count() . PHP_EOL;

echo PHP_EOL . "=== MIGRASI SELESAI ===" . PHP_EOL;
