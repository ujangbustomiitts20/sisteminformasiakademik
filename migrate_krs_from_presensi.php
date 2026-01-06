<?php
/**
 * Migrasi KRS dari kelaskuliah_presensimahasiswa
 * 
 * Tabel krss tidak lengkap - tidak mencakup kelas gabungan dan semester terbaru.
 * Data KRS yang paling lengkap ada di kelaskuliah_presensimahasiswa.
 * 
 * Script ini akan:
 * 1. Mengambil data KRS dari kelaskuliah_presensimahasiswa
 * 2. Mapping ke jadwal_kuliah atau membuat jadwal baru jika belum ada
 * 3. Insert KRS yang belum ada
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Krs;
use App\Models\JadwalKuliah;
use App\Models\MataKuliah;
use App\Models\TahunAkademik;
use App\Models\Mahasiswa;
use App\Models\Dosen;

$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
if ($source->connect_error) {
    die("Koneksi gagal: " . $source->connect_error);
}
$source->set_charset('utf8mb4');

echo "=== MIGRASI KRS DARI PRESENSI (KELAS GABUNGAN) ===\n\n";

// 1. Build mapping mahasiswa (nim => local_id)
echo "1. Building mahasiswa map...\n";
$mhsMap = [];
$mhs = DB::table('mahasiswa')->select('id', 'nim')->get();
foreach ($mhs as $m) {
    $mhsMap[$m->nim] = $m->id;
}
echo "   - " . count($mhsMap) . " mahasiswa\n";

// 2. Build mapping MK by kode
echo "2. Building MK map...\n";
$mkMap = []; // kode => local_id
$mkNamaMap = []; // lowercase nama => local_id
$mks = DB::table('mata_kuliah')->select('id', 'kode', 'nama', 'program_studi_id')->get();
foreach ($mks as $mk) {
    $mkMap[$mk->kode] = $mk->id;
    // Simpan juga berdasarkan nama (lowercase, tanpa spasi berlebih)
    $namaNormalized = strtolower(preg_replace('/\s+/', ' ', trim($mk->nama)));
    if (!isset($mkNamaMap[$namaNormalized])) {
        $mkNamaMap[$namaNormalized] = [];
    }
    $mkNamaMap[$namaNormalized][] = ['id' => $mk->id, 'kode' => $mk->kode, 'prodi_id' => $mk->program_studi_id];
}
echo "   - " . count($mkMap) . " MK by kode\n";

// 3. Build source MK map (ID => nama, kode)
echo "3. Building source MK map...\n";
$sourceMk = [];
$result = $source->query("SELECT ID, NAMA, KODEMAKUL, IDPRODI FROM makul");
while ($row = $result->fetch_assoc()) {
    $sourceMk[$row['ID']] = [
        'nama' => trim($row['NAMA']),
        'kode' => trim($row['KODEMAKUL']),
        'prodi' => $row['IDPRODI']
    ];
}
echo "   - " . count($sourceMk) . " source MK\n";

// 4. Build tahun akademik map
echo "4. Building tahun akademik map...\n";
$taMap = []; // "2025-1" => ta_id
$tas = DB::table('tahun_akademik')->get();
foreach ($tas as $ta) {
    $semNum = match($ta->semester) {
        'Ganjil' => 1,
        'Genap' => 2,
        'Pendek' => 4,
        default => 0
    };
    $key = "{$ta->tahun}{$semNum}";
    $taMap[$key] = $ta->id;
}
echo "   - " . count($taMap) . " tahun akademik\n";

// 5. Build prodi map (source prodi => local prodi)
echo "5. Building prodi map...\n";
$prodiMap = [
    '55202' => 1, // Informatika
    '57201' => 2, // Sistem Informasi  
    '59201' => 3, // Teknik Industri (atau sesuaikan)
];
// Get actual prodi IDs
$prodis = DB::table('program_studi')->select('id', 'kode')->get();
foreach ($prodis as $p) {
    // Map source kode to local id
    foreach (['55202', '57201', '59201'] as $sourceKode) {
        if (strpos($p->kode, substr($sourceKode, 0, 3)) !== false) {
            $prodiMap[$sourceKode] = $p->id;
        }
    }
}
echo "   Prodi mapping: " . json_encode($prodiMap) . "\n";

// 6. Get existing KRS untuk cek duplikat
echo "6. Getting existing KRS...\n";
$existingKrs = [];
$krs = DB::table('krs as k')
    ->join('jadwal_kuliah as j', 'k.jadwal_kuliah_id', '=', 'j.id')
    ->select('k.mahasiswa_id', 'j.mata_kuliah_id', 'k.tahun_akademik_id')
    ->get();
foreach ($krs as $k) {
    $key = "{$k->mahasiswa_id}-{$k->mata_kuliah_id}-{$k->tahun_akademik_id}";
    $existingKrs[$key] = true;
}
echo "   - " . count($existingKrs) . " existing KRS\n";

// 7. Get jadwal kuliah map
echo "7. Building jadwal kuliah map...\n";
$jadwalMap = []; // "mk_id-ta_id-kelas" => jadwal_id
$jadwals = DB::table('jadwal_kuliah')->select('id', 'mata_kuliah_id', 'tahun_akademik_id', 'kelas')->get();
foreach ($jadwals as $j) {
    $key = "{$j->mata_kuliah_id}-{$j->tahun_akademik_id}-{$j->kelas}";
    $jadwalMap[$key] = $j->id;
    // Also map without kelas
    $keyNoKelas = "{$j->mata_kuliah_id}-{$j->tahun_akademik_id}";
    if (!isset($jadwalMap[$keyNoKelas])) {
        $jadwalMap[$keyNoKelas] = $j->id;
    }
}
echo "   - " . count($jadwalMap) . " jadwal\n";

// 8. Get source kelaskuliah for dosen info
echo "8. Building kelaskuliah dosen map...\n";
$kelasDosenMap = []; // "tahun-semester-idmakul-kelas" => dosen_id
$result = $source->query("
    SELECT kp.TAHUN, kp.SEMESTER, kp.IDMAKUL, kp.IDKELAS, kp.IDPENGAJAR as IDDOSEN
    FROM kelaskuliah_pengajar kp
");
while ($row = $result->fetch_assoc()) {
    $key = "{$row['TAHUN']}-{$row['SEMESTER']}-{$row['IDMAKUL']}-{$row['IDKELAS']}";
    $kelasDosenMap[$key] = $row['IDDOSEN'];
}
echo "   - " . count($kelasDosenMap) . " kelas dengan dosen\n";

// 9. Build dosen map
echo "9. Building dosen map...\n";
$dosenMap = [];
$dosens = DB::table('dosen')->select('id', 'nidn')->get();
foreach ($dosens as $d) {
    if ($d->nidn) $dosenMap[$d->nidn] = $d->id;
}
echo "   - " . count($dosenMap) . " dosen\n";

// 10. Query unique KRS dari kelaskuliah_presensimahasiswa
echo "\n10. Querying KRS dari kelaskuliah_presensimahasiswa...\n";
$result = $source->query("
    SELECT DISTINCT 
        kpm.IDMAHASISWA, 
        kpm.IDMAKUL, 
        kpm.TAHUN, 
        kpm.SEMESTER, 
        kpm.IDKELAS
    FROM kelaskuliah_presensimahasiswa kpm
    ORDER BY kpm.TAHUN DESC, kpm.SEMESTER
");

$sourceKrs = [];
while ($row = $result->fetch_assoc()) {
    $sourceKrs[] = $row;
}
echo "   - " . count($sourceKrs) . " unique KRS entries from presensi\n";

// 11. Process dan insert KRS
echo "\n11. Processing KRS...\n";
$inserted = 0;
$skipped = 0;
$noMhs = 0;
$noMk = 0;
$noTa = 0;
$createdJadwal = 0;

$batchInsert = [];

foreach ($sourceKrs as $idx => $row) {
    // Find mahasiswa
    if (!isset($mhsMap[$row['IDMAHASISWA']])) {
        $noMhs++;
        continue;
    }
    $mhsId = $mhsMap[$row['IDMAHASISWA']];
    
    // Find tahun akademik
    $taKey = "{$row['TAHUN']}{$row['SEMESTER']}";
    if (!isset($taMap[$taKey])) {
        $noTa++;
        continue;
    }
    $taId = $taMap[$taKey];
    
    // Find mata kuliah
    $mkId = null;
    $sourceMkData = $sourceMk[$row['IDMAKUL']] ?? null;
    
    if ($sourceMkData) {
        // Try by kode first
        $kode = $sourceMkData['kode'];
        if (isset($mkMap[$kode])) {
            $mkId = $mkMap[$kode];
        } else {
            // Try by normalized name
            $namaNormalized = strtolower(preg_replace('/\s+/', ' ', trim($sourceMkData['nama'])));
            if (isset($mkNamaMap[$namaNormalized])) {
                // If multiple, prefer same prodi
                $candidates = $mkNamaMap[$namaNormalized];
                $localProdiId = $prodiMap[$sourceMkData['prodi']] ?? null;
                
                foreach ($candidates as $c) {
                    if ($c['prodi_id'] == $localProdiId) {
                        $mkId = $c['id'];
                        break;
                    }
                }
                // If not found, use first match
                if (!$mkId && count($candidates) > 0) {
                    $mkId = $candidates[0]['id'];
                }
            }
        }
    }
    
    if (!$mkId) {
        $noMk++;
        continue;
    }
    
    // Check if KRS already exists
    $krsKey = "{$mhsId}-{$mkId}-{$taId}";
    if (isset($existingKrs[$krsKey])) {
        $skipped++;
        continue;
    }
    
    // Find or create jadwal
    $kelas = $row['IDKELAS'] ?? 'A';
    $jadwalKey = "{$mkId}-{$taId}-{$kelas}";
    $jadwalKeyNoKelas = "{$mkId}-{$taId}";
    
    $jadwalId = $jadwalMap[$jadwalKey] ?? $jadwalMap[$jadwalKeyNoKelas] ?? null;
    
    if (!$jadwalId) {
        // Create jadwal kuliah
        $dosenKey = "{$row['TAHUN']}-{$row['SEMESTER']}-{$row['IDMAKUL']}-{$kelas}";
        $sourceDosenId = $kelasDosenMap[$dosenKey] ?? null;
        $dosenId = $sourceDosenId ? ($dosenMap[$sourceDosenId] ?? null) : null;
        
        // Get default dosen if not found
        if (!$dosenId) {
            $dosenId = Dosen::first()?->id;
        }
        
        try {
            $jadwal = JadwalKuliah::create([
                'mata_kuliah_id' => $mkId,
                'tahun_akademik_id' => $taId,
                'dosen_id' => $dosenId,
                'kelas' => $kelas,
                'hari' => 'Senin',
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:00',
                'ruangan_id' => 1,
                'kapasitas' => 40,
            ]);
            $jadwalId = $jadwal->id;
            $jadwalMap[$jadwalKey] = $jadwalId;
            $createdJadwal++;
        } catch (\Exception $e) {
            // Skip if can't create
            continue;
        }
    }
    
    // Prepare KRS insert
    $batchInsert[] = [
        'mahasiswa_id' => $mhsId,
        'jadwal_kuliah_id' => $jadwalId,
        'tahun_akademik_id' => $taId,
        'status' => 'disetujui',
        'created_at' => now(),
        'updated_at' => now(),
    ];
    
    // Mark as existing to prevent duplicate in this batch
    $existingKrs[$krsKey] = true;
    $inserted++;
    
    // Batch insert every 500
    if (count($batchInsert) >= 500) {
        DB::table('krs')->insert($batchInsert);
        echo "   Inserted batch: {$inserted} total\n";
        $batchInsert = [];
    }
    
    // Progress
    if (($idx + 1) % 1000 === 0) {
        echo "   Processed " . ($idx + 1) . " / " . count($sourceKrs) . "\n";
    }
}

// Insert remaining
if (count($batchInsert) > 0) {
    DB::table('krs')->insert($batchInsert);
}

echo "\n=== HASIL ===\n";
echo "Inserted: {$inserted}\n";
echo "Skipped (existing): {$skipped}\n";
echo "No mahasiswa: {$noMhs}\n";
echo "No mata kuliah: {$noMk}\n";
echo "No tahun akademik: {$noTa}\n";
echo "Created jadwal: {$createdJadwal}\n";

$source->close();
echo "\nDone!\n";
