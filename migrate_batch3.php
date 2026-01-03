<?php
/**
 * Migrasi Batch 3: Kurikulum, Pembimbing Akademik, Nilai, Kelulusan
 * dari Database itts_sikad ke SIAKAD Lokal
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Koneksi ke source database
$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
if ($source->connect_error) {
    die("Connection failed: " . $source->connect_error);
}
$source->set_charset('utf8mb4');

echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║       MIGRASI BATCH 3: KURIKULUM, WALI, NILAI, KELULUSAN ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

// Build mapping tables
echo "Building mapping tables...\n";

$prodiMap = DB::table('program_studi')->pluck('id', 'kode')->toArray();
$mataKuliahMap = DB::table('mata_kuliah')->pluck('id', 'kode')->toArray();
$mahasiswaMap = DB::table('mahasiswa')->pluck('id', 'nim')->toArray();
$dosenMap = DB::table('dosen')->pluck('id', 'nidn')->toArray();

$tahunAkademikMap = DB::table('tahun_akademik')
    ->select('id', 'tahun', 'semester')
    ->get()
    ->mapWithKeys(function($ta) {
        $sem = match($ta->semester) {
            'Ganjil' => 1,
            'Genap' => 2,
            'Pendek' => 4,
            default => 0
        };
        return [$ta->tahun . $sem => $ta->id];
    })->toArray();

$jadwalMap = DB::table('jadwal_kuliah')
    ->select('id', 'tahun_akademik_id', 'mata_kuliah_id', 'kelas')
    ->get()
    ->mapWithKeys(function($j) {
        return [$j->tahun_akademik_id . '-' . $j->mata_kuliah_id . '-' . $j->kelas => $j->id];
    })->toArray();

echo "Mapping ready.\n\n";

// =====================================================
// 1. KURIKULUM
// =====================================================
echo "=== 1. MIGRASI KURIKULUM ===\n";

$result = $source->query("SELECT * FROM kurikulum ORDER BY ID");
$created = 0;

while ($row = $result->fetch_assoc()) {
    $prodiId = $prodiMap[$row['IDPRODI']] ?? null;
    
    if (!$prodiId) {
        echo "  Skip kurikulum {$row['ID']}: prodi {$row['IDPRODI']} tidak ditemukan\n";
        continue;
    }
    
    $existing = DB::table('kurikulum')
        ->where('kode', $row['ID'])
        ->first();
    
    if (!$existing) {
        DB::table('kurikulum')->insert([
            'kode' => $row['ID'],
            'nama' => $row['NAMA'] ?: "Kurikulum {$row['ID']}",
            'program_studi_id' => $prodiId,
            'tahun_mulai' => $row['TAHUN'] ?? 2020,
            'tahun_selesai' => ($row['TAHUN'] ?? 2020) + 5,
            'is_aktif' => $row['STATUS'] == 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $created++;
    }
}
echo "Kurikulum: $created created\n";

// Migrate kurikulum_makul
$result = $source->query("SELECT * FROM kurikulum_makul");
$mkCreated = 0;

// Get kurikulum map
$kurikulumMap = DB::table('kurikulum')->pluck('id', 'kode')->toArray();

while ($row = $result->fetch_assoc()) {
    $kurikulumId = $kurikulumMap[$row['IDKURIKULUM']] ?? null;
    $mataKuliahId = $mataKuliahMap[$row['IDMAKUL']] ?? null;
    
    if (!$kurikulumId || !$mataKuliahId) continue;
    
    $existing = DB::table('kurikulum_mata_kuliah')
        ->where('kurikulum_id', $kurikulumId)
        ->where('mata_kuliah_id', $mataKuliahId)
        ->first();
    
    if (!$existing) {
        DB::table('kurikulum_mata_kuliah')->insert([
            'kurikulum_id' => $kurikulumId,
            'mata_kuliah_id' => $mataKuliahId,
            'semester_rekomendasi' => $row['SEMESTER'] ?? 1,
            'kategori' => match($row['JENIS'] ?? 'W') {
                'W' => 'Wajib',
                'P' => 'Pilihan',
                default => 'Wajib'
            },
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $mkCreated++;
    }
}
echo "Kurikulum MK: $mkCreated created\n\n";

// =====================================================
// 2. PEMBIMBING AKADEMIK (DOSEN WALI)
// =====================================================
echo "=== 2. MIGRASI PEMBIMBING AKADEMIK ===\n";

$result = $source->query("
    SELECT mw.*, ms.NIMHSMSMHS as NIM, mw.IDDOSEN
    FROM mahasiswa_wali mw
    LEFT JOIN msmhs ms ON mw.IDMAHASISWA = ms.NIMHSMSMHS
    WHERE ms.NIMHSMSMHS IS NOT NULL AND mw.IDDOSEN IS NOT NULL
");

$waliCreated = 0;
$waliUpdated = 0;

while ($row = $result->fetch_assoc()) {
    $mahasiswaId = $mahasiswaMap[$row['NIM']] ?? null;
    $dosenId = $dosenMap[$row['IDDOSEN']] ?? null;
    
    if (!$mahasiswaId || !$dosenId) continue;
    
    // Update mahasiswa dengan dosen_wali_id
    $updated = DB::table('mahasiswa')
        ->where('id', $mahasiswaId)
        ->whereNull('dosen_wali_id')
        ->update(['dosen_wali_id' => $dosenId, 'updated_at' => now()]);
    
    if ($updated) {
        $waliUpdated++;
    }
}
echo "Pembimbing Akademik: $waliUpdated mahasiswa updated\n\n";

// =====================================================
// 3. NILAI (SYNC ULANG)
// =====================================================
echo "=== 3. MIGRASI NILAI ===\n";

// Get existing nilai count
$existingNilai = DB::table('nilai')->count();
echo "Nilai existing: $existingNilai\n";

// Query nilai from source dengan chunking
$totalNilai = $source->query("SELECT COUNT(*) as c FROM nilai")->fetch_assoc()['c'];
echo "Nilai di source: $totalNilai\n";

// Build KRS map: nim-makul-tahun-semester -> krs_id
$krsMap = DB::table('krs as k')
    ->join('mahasiswa as m', 'k.mahasiswa_id', '=', 'm.id')
    ->join('jadwal_kuliah as j', 'k.jadwal_kuliah_id', '=', 'j.id')
    ->join('mata_kuliah as mk', 'j.mata_kuliah_id', '=', 'mk.id')
    ->join('tahun_akademik as ta', 'k.tahun_akademik_id', '=', 'ta.id')
    ->select('k.id', 'm.nim', 'mk.kode', 'ta.tahun', 'ta.semester')
    ->get()
    ->mapWithKeys(function($k) {
        $sem = match($k->semester) {
            'Ganjil' => 1,
            'Genap' => 2,
            'Pendek' => 4,
            default => 0
        };
        return [$k->nim . '-' . $k->kode . '-' . $k->tahun . $sem => $k->id];
    })->toArray();

echo "KRS map built: " . count($krsMap) . " entries\n";

$chunkSize = 5000;
$offset = 0;
$nilaiCreated = 0;
$nilaiSkipped = 0;

while ($offset < $totalNilai) {
    $result = $source->query("
        SELECT n.IDMAHASISWA as NIM, n.IDMAKUL as IDMK, n.TAHUN, n.SEMESTER, 
               n.NILAI, n.KELAS
        FROM nilai n
        WHERE n.IDMAHASISWA IS NOT NULL
        LIMIT $chunkSize OFFSET $offset
    ");
    
    while ($row = $result->fetch_assoc()) {
        $krsKey = $row['NIM'] . '-' . $row['IDMK'] . '-' . $row['TAHUN'] . $row['SEMESTER'];
        $krsId = $krsMap[$krsKey] ?? null;
        
        if (!$krsId) {
            $nilaiSkipped++;
            continue;
        }
        
        // Check if nilai already exists for this KRS
        $exists = DB::table('nilai')
            ->where('krs_id', $krsId)
            ->exists();
        
        if (!$exists) {
            // Parse nilai
            $nilaiAngka = is_numeric($row['NILAI']) ? floatval($row['NILAI']) : null;
            $nilaiHuruf = !is_numeric($row['NILAI']) ? $row['NILAI'] : null;
            
            // If nilai is letter, convert to number
            if ($nilaiHuruf && !$nilaiAngka) {
                $nilaiAngka = match(strtoupper($nilaiHuruf)) {
                    'A' => 90, 'A-' => 82, 'B+' => 77, 'B' => 72, 'B-' => 67,
                    'C+' => 62, 'C' => 57, 'C-' => 52, 'D' => 45, 'E' => 0,
                    default => null
                };
            }
            
            // Calculate bobot
            $bobot = match(strtoupper($nilaiHuruf ?? '')) {
                'A' => 4.0, 'A-' => 3.7, 'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7,
                'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7, 'D' => 1.0, 'E' => 0,
                default => 0
            };
            
            DB::table('nilai')->insert([
                'krs_id' => $krsId,
                'tugas' => null,
                'uts' => null,
                'uas' => null,
                'nilai_akhir' => $nilaiAngka,
                'huruf' => $nilaiHuruf,
                'bobot' => $bobot,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $nilaiCreated++;
        }
    }
    
    $offset += $chunkSize;
    echo "  Processed: $offset / $totalNilai (created: $nilaiCreated, skipped: $nilaiSkipped)\n";
}

echo "Nilai: $nilaiCreated created, $nilaiSkipped skipped\n\n";

// =====================================================
// 4. KELULUSAN / WISUDA
// =====================================================
echo "=== 4. MIGRASI KELULUSAN ===\n";

// Check if kelulusan table exists
$tableExists = DB::select("SHOW TABLES LIKE 'kelulusan'");
if (empty($tableExists)) {
    echo "Creating kelulusan table...\n";
    DB::statement("
        CREATE TABLE IF NOT EXISTS kelulusan (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            mahasiswa_id BIGINT UNSIGNED NOT NULL,
            tanggal_lulus DATE,
            no_ijazah VARCHAR(100),
            tanggal_wisuda DATE,
            ipk DECIMAL(4,2),
            predikat VARCHAR(50),
            judul_tugas_akhir TEXT,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX (mahasiswa_id)
        )
    ");
}

$result = $source->query("
    SELECT k.* 
    FROM kelulusan_mhs k
    WHERE k.IDMAHASISWA IS NOT NULL
");

$kelulusanCreated = 0;

while ($row = $result->fetch_assoc()) {
    $mahasiswaId = $mahasiswaMap[$row['IDMAHASISWA']] ?? null;
    
    if (!$mahasiswaId) continue;
    
    $exists = DB::table('kelulusan')
        ->where('mahasiswa_id', $mahasiswaId)
        ->exists();
    
    if (!$exists) {
        $ipk = floatval($row['IPK'] ?? 0);
        DB::table('kelulusan')->insert([
            'mahasiswa_id' => $mahasiswaId,
            'tanggal_lulus' => $row['TANGGALKELUAR'] ?? null,
            'no_ijazah' => $row['NOIJAZAH'] ?? null,
            'tanggal_wisuda' => null,
            'ipk' => $ipk,
            'predikat' => match(true) {
                $ipk >= 3.51 => 'Cum Laude',
                $ipk >= 3.01 => 'Sangat Memuaskan',
                $ipk >= 2.76 => 'Memuaskan',
                default => 'Cukup'
            },
            'judul_tugas_akhir' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $kelulusanCreated++;
        
        // Update status mahasiswa
        DB::table('mahasiswa')
            ->where('id', $mahasiswaId)
            ->update(['status' => 'Lulus', 'updated_at' => now()]);
    }
}

echo "Kelulusan: $kelulusanCreated created\n\n";

// =====================================================
// SUMMARY
// =====================================================
echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║                    RINGKASAN MIGRASI                     ║\n";
echo "╠══════════════════════════════════════════════════════════╣\n";
printf("║  Kurikulum          : %-6d created                     ║\n", $created);
printf("║  Kurikulum MK       : %-6d created                     ║\n", $mkCreated);
printf("║  Pembimbing Akademik: %-6d updated                     ║\n", $waliUpdated);
printf("║  Nilai              : %-6d created                     ║\n", $nilaiCreated);
printf("║  Kelulusan          : %-6d created                     ║\n", $kelulusanCreated);
echo "╚══════════════════════════════════════════════════════════╝\n";

$source->close();
echo "\nDone!\n";
