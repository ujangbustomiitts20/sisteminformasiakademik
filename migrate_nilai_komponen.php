<?php
/**
 * Migrasi Nilai dari database lama (itts_sikad)
 * Source memiliki nilai per komponen (UTS, UAS, Tugas, Kehadiran) dalam baris terpisah
 * Local menggabungkan semua komponen dalam satu baris
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Koneksi ke database sumber
$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
if ($source->connect_error) {
    die("Koneksi gagal: " . $source->connect_error);
}
$source->set_charset('utf8mb4');

echo "=== MIGRASI NILAI DARI DATABASE LAMA ===\n\n";

// 1. Build mapping: NIM -> mahasiswa_id lokal
echo "1. Building mahasiswa map...\n";
$mahasiswaMap = [];
$mahasiswas = DB::table('mahasiswa')->select('id', 'nim')->get();
foreach ($mahasiswas as $mhs) {
    $mahasiswaMap[$mhs->nim] = $mhs->id;
}
echo "   - " . count($mahasiswaMap) . " mahasiswa\n";

// 2. Build mapping: kode MK -> mata_kuliah_id lokal
echo "2. Building mata kuliah map...\n";
$mkMap = [];
$mks = DB::table('mata_kuliah')->select('id', 'kode', 'nama')->get();
foreach ($mks as $mk) {
    $mkMap[$mk->kode] = ['id' => $mk->id, 'nama' => $mk->nama];
}
echo "   - " . count($mkMap) . " mata kuliah\n";

// 3. Build mapping: KRS -> krs_id (key: mahasiswa_id-mk_id-tahun-semester)
echo "3. Building KRS map...\n";
$krsMap = [];
$krsList = DB::table('krs as k')
    ->join('jadwal_kuliah as j', 'k.jadwal_kuliah_id', '=', 'j.id')
    ->join('tahun_akademik as ta', 'k.tahun_akademik_id', '=', 'ta.id')
    ->select('k.id as krs_id', 'k.mahasiswa_id', 'j.mata_kuliah_id', 'ta.tahun', 'ta.semester')
    ->get();

foreach ($krsList as $krs) {
    $semNum = match($krs->semester) {
        'Ganjil' => 1,
        'Genap' => 2,
        'Pendek' => 4,
        default => 0
    };
    $key = "{$krs->mahasiswa_id}-{$krs->mata_kuliah_id}-{$krs->tahun}{$semNum}";
    $krsMap[$key] = $krs->krs_id;
}
echo "   - " . count($krsMap) . " KRS entries\n";

// 4. Mapping source MK kode ke local (source pakai IF/SI, bisa berbeda)
// Build from makul table di source untuk mapping IDMAKUL -> nama
echo "4. Building source makul nama map...\n";
$sourceMkNamaMap = [];
$result = $source->query("SELECT ID, NAMA FROM makul");
while ($row = $result->fetch_assoc()) {
    $sourceMkNamaMap[$row['ID']] = trim($row['NAMA']);
}
echo "   - " . count($sourceMkNamaMap) . " source MK\n";

// Local MK nama -> kode map (untuk fallback matching by nama)
$localMkByNama = [];
foreach ($mks as $mk) {
    $namaNormalized = strtolower(trim($mk->nama));
    if (!isset($localMkByNama[$namaNormalized])) {
        $localMkByNama[$namaNormalized] = $mk->kode;
    }
}

// 5. Ambil nilai dari source dan group per mahasiswa-makul-tahun-semester
echo "5. Fetching nilai from source (grouped)...\n";

// Query nilai dengan group per mahasiswa-makul-tahun-semester
$query = "
    SELECT 
        n.IDMAHASISWA as nim,
        n.IDMAKUL as kode_mk,
        n.TAHUN as tahun,
        n.SEMESTER as semester,
        MAX(CASE WHEN k.NAMA LIKE '%UTS%' THEN n.NILAI END) as uts,
        MAX(CASE WHEN k.NAMA LIKE '%UAS%' THEN n.NILAI END) as uas,
        MAX(CASE WHEN k.NAMA LIKE '%TUGAS%' OR k.NAMA LIKE '%Tugas%' THEN n.NILAI END) as tugas,
        MAX(CASE WHEN k.NAMA LIKE '%KEHADIRAN%' OR k.NAMA LIKE '%Presensi%' THEN n.NILAI END) as kehadiran
    FROM nilai n
    LEFT JOIN komponen k ON n.IDKOMPONEN = k.IDKOMPONEN 
        AND n.IDMAKUL = k.IDMAKUL 
        AND n.TAHUN = k.TAHUN 
        AND n.SEMESTER = k.SEMESTER
    GROUP BY n.IDMAHASISWA, n.IDMAKUL, n.TAHUN, n.SEMESTER
";

$result = $source->query($query);
$totalRows = $result->num_rows;
echo "   - Found $totalRows grouped nilai records\n\n";

// 6. Migrate nilai
echo "6. Migrating nilai...\n";

$created = 0;
$updated = 0;
$skipped = 0;
$errors = [];
$progress = 0;

while ($row = $result->fetch_assoc()) {
    $progress++;
    
    // Get mahasiswa_id
    $mhsId = $mahasiswaMap[$row['nim']] ?? null;
    if (!$mhsId) {
        $skipped++;
        continue;
    }
    
    // Get MK kode - coba langsung dulu
    $mkKode = $row['kode_mk'];
    $mkId = null;
    
    if (isset($mkMap[$mkKode])) {
        $mkId = $mkMap[$mkKode]['id'];
    } else {
        // Coba cari by nama
        $mkNama = $sourceMkNamaMap[$mkKode] ?? null;
        if ($mkNama) {
            $namaNormalized = strtolower(trim($mkNama));
            if (isset($localMkByNama[$namaNormalized])) {
                $kodeLocal = $localMkByNama[$namaNormalized];
                $mkId = $mkMap[$kodeLocal]['id'] ?? null;
            }
        }
    }
    
    if (!$mkId) {
        $skipped++;
        if (!isset($errors[$mkKode])) {
            $errors[$mkKode] = $sourceMkNamaMap[$mkKode] ?? 'unknown';
        }
        continue;
    }
    
    // Build KRS key
    $key = "{$mhsId}-{$mkId}-{$row['tahun']}{$row['semester']}";
    $krsId = $krsMap[$key] ?? null;
    
    if (!$krsId) {
        $skipped++;
        continue;
    }
    
    // Prepare nilai data
    $nilaiData = [
        'tugas' => $row['tugas'] !== null ? (float)$row['tugas'] : null,
        'kehadiran' => $row['kehadiran'] !== null ? (float)$row['kehadiran'] : null,
        'uts' => $row['uts'] !== null ? (float)$row['uts'] : null,
        'uas' => $row['uas'] !== null ? (float)$row['uas'] : null,
    ];
    
    // Hitung nilai akhir jika UTS dan UAS ada
    $nilaiAkhir = null;
    $huruf = null;
    $bobot = null;
    
    if ($nilaiData['uts'] !== null && $nilaiData['uas'] !== null) {
        if ($nilaiData['kehadiran'] !== null) {
            // Dengan kehadiran: 20% Kehadiran + 20% Tugas + 25% UTS + 35% UAS
            $nilaiAkhir = 
                ($nilaiData['kehadiran'] * 0.20) + 
                (($nilaiData['tugas'] ?? 0) * 0.20) + 
                ($nilaiData['uts'] * 0.25) + 
                ($nilaiData['uas'] * 0.35);
        } else {
            // Tanpa kehadiran: 30% Tugas + 30% UTS + 40% UAS
            $nilaiAkhir = 
                (($nilaiData['tugas'] ?? 0) * 0.30) + 
                ($nilaiData['uts'] * 0.30) + 
                ($nilaiData['uas'] * 0.40);
        }
        
        // Konversi ke huruf
        if ($nilaiAkhir >= 85) $huruf = 'A';
        elseif ($nilaiAkhir >= 80) $huruf = 'A-';
        elseif ($nilaiAkhir >= 75) $huruf = 'B+';
        elseif ($nilaiAkhir >= 70) $huruf = 'B';
        elseif ($nilaiAkhir >= 65) $huruf = 'B-';
        elseif ($nilaiAkhir >= 60) $huruf = 'C+';
        elseif ($nilaiAkhir >= 55) $huruf = 'C';
        elseif ($nilaiAkhir >= 50) $huruf = 'D';
        else $huruf = 'E';
        
        // Konversi ke bobot
        $bobotMap = ['A' => 4.00, 'A-' => 3.75, 'B+' => 3.50, 'B' => 3.00, 'B-' => 2.75, 'C+' => 2.50, 'C' => 2.00, 'D' => 1.00, 'E' => 0.00];
        $bobot = $bobotMap[$huruf] ?? 0;
    }
    
    $nilaiData['nilai_akhir'] = $nilaiAkhir;
    $nilaiData['huruf'] = $huruf;
    $nilaiData['bobot'] = $bobot;
    
    // Insert or update
    $existing = DB::table('nilai')->where('krs_id', $krsId)->first();
    
    if ($existing) {
        // Update jika ada nilai baru
        $shouldUpdate = false;
        foreach (['tugas', 'kehadiran', 'uts', 'uas'] as $field) {
            if ($existing->$field === null && $nilaiData[$field] !== null) {
                $shouldUpdate = true;
                break;
            }
        }
        
        if ($shouldUpdate) {
            DB::table('nilai')->where('id', $existing->id)->update($nilaiData);
            $updated++;
        } else {
            $skipped++;
        }
    } else {
        DB::table('nilai')->insert(array_merge(['krs_id' => $krsId], $nilaiData));
        $created++;
    }
    
    // Progress
    if ($progress % 500 == 0) {
        echo "   Progress: $progress/$totalRows (created: $created, updated: $updated, skipped: $skipped)\n";
    }
}

echo "\n=== HASIL MIGRASI ===\n";
echo "Created: $created\n";
echo "Updated: $updated\n";
echo "Skipped: $skipped\n";

if (!empty($errors)) {
    echo "\nMK tidak ditemukan (sample 10):\n";
    $i = 0;
    foreach ($errors as $kode => $nama) {
        echo "  - $kode: $nama\n";
        if (++$i >= 10) break;
    }
}

$source->close();
echo "\nDone!\n";
