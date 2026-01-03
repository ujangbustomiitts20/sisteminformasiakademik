<?php
/**
 * Migrasi KRS dan Nilai dari transkrip_detil
 * Untuk mahasiswa yang sudah punya transkrip lengkap (semester 1-8)
 * tapi belum ada KRS/nilai di sistem lokal
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
if ($source->connect_error) {
    die("Koneksi gagal: " . $source->connect_error);
}
$source->set_charset('utf8mb4');

echo "=== MIGRASI KRS & NILAI DARI TRANSKRIP_DETIL ===\n\n";

// 1. Build mahasiswa map
echo "1. Building mahasiswa map...\n";
$mhsMap = []; // nim => [id, angkatan, prodi_id]
$mhs = DB::table('mahasiswa')->select('id', 'nim', 'angkatan', 'program_studi_id')->get();
foreach ($mhs as $m) {
    $mhsMap[$m->nim] = ['id' => $m->id, 'angkatan' => $m->angkatan, 'prodi_id' => $m->program_studi_id];
}
echo "   - " . count($mhsMap) . " mahasiswa\n";

// 2. Build MK map by nama (transkrip_detil pakai NAMA bukan kode)
echo "2. Building MK map...\n";
$mkByNama = []; // nama => [id, kode, sks]
$mkByKode = []; // kode => [id, nama, sks]
$mks = DB::table('mata_kuliah')->select('id', 'kode', 'nama', 'sks', 'program_studi_id')->get();
foreach ($mks as $mk) {
    $namaNorm = strtolower(trim($mk->nama));
    $mkByNama[$namaNorm] = ['id' => $mk->id, 'kode' => $mk->kode, 'sks' => $mk->sks, 'prodi_id' => $mk->program_studi_id];
    $mkByKode[$mk->kode] = ['id' => $mk->id, 'nama' => $mk->nama, 'sks' => $mk->sks, 'prodi_id' => $mk->program_studi_id];
}
echo "   - " . count($mkByNama) . " MK by nama\n";

// 3. Build tahun akademik map
echo "3. Building tahun akademik map...\n";
$taMap = []; // "2021-1" => id
$taList = DB::table('tahun_akademik')->get();
foreach ($taList as $ta) {
    $semNum = match($ta->semester) {
        'Ganjil' => 1,
        'Genap' => 2,
        'Pendek' => 4,
        default => 0
    };
    $taMap["{$ta->tahun}{$semNum}"] = $ta->id;
}
echo "   - " . count($taMap) . " tahun akademik\n";

// 4. Build jadwal kuliah map
echo "4. Building jadwal map...\n";
$jadwalMap = []; // "mk_id-ta_id" => id
$jadwals = DB::table('jadwal_kuliah')->select('id', 'mata_kuliah_id', 'tahun_akademik_id')->get();
foreach ($jadwals as $j) {
    $key = "{$j->mata_kuliah_id}-{$j->tahun_akademik_id}";
    $jadwalMap[$key] = $j->id;
}
echo "   - " . count($jadwalMap) . " jadwal\n";

// 5. Get existing KRS
echo "5. Getting existing KRS...\n";
$existingKrs = [];
$krs = DB::table('krs as k')
    ->join('jadwal_kuliah as j', 'k.jadwal_kuliah_id', '=', 'j.id')
    ->select('k.id', 'k.mahasiswa_id', 'j.mata_kuliah_id', 'k.tahun_akademik_id')
    ->get();
foreach ($krs as $k) {
    $key = "{$k->mahasiswa_id}-{$k->mata_kuliah_id}-{$k->tahun_akademik_id}";
    $existingKrs[$key] = $k->id;
}
echo "   - " . count($existingKrs) . " existing KRS\n";

// 6. Query transkrip_detil dari source
echo "\n6. Fetching transkrip_detil...\n";
$result = $source->query("
    SELECT td.IDMAHASISWA, td.IDMAKUL, td.NAMA, td.SKS, td.SEMESTERMK, td.SIMBOL, td.BOBOT, td.NILAI
    FROM transkrip_detil td
    WHERE td.STATUSDELETE = 0 OR td.STATUSDELETE IS NULL
    ORDER BY td.IDMAHASISWA, td.SEMESTERMK
");
$total = $result->num_rows;
echo "   - Found $total transkrip records\n";

// 7. Process and create KRS + nilai
echo "\n7. Creating KRS and nilai...\n";
$krsCreated = 0;
$jadwalCreated = 0;
$nilaiCreated = 0;
$skipped = 0;
$noMhs = 0;
$noMk = 0;
$noTa = 0;
$progress = 0;

// Bobot mapping
$bobotMap = ['A' => 4.00, 'A-' => 3.75, 'B+' => 3.50, 'B' => 3.00, 'B-' => 2.75, 'C+' => 2.50, 'C' => 2.00, 'D' => 1.00, 'E' => 0.00];

while ($row = $result->fetch_assoc()) {
    $progress++;
    
    // Get mahasiswa
    $nim = $row['IDMAHASISWA'];
    if (!isset($mhsMap[$nim])) {
        $noMhs++;
        continue;
    }
    $mhsData = $mhsMap[$nim];
    $mhsId = $mhsData['id'];
    $angkatan = $mhsData['angkatan'];
    $prodiId = $mhsData['prodi_id'];
    
    // Calculate tahun akademik from semester MK and angkatan
    // Semester 1 = angkatan Ganjil, Semester 2 = angkatan Genap, etc.
    $semMk = (int)$row['SEMESTERMK'];
    $tahun = $angkatan + floor(($semMk - 1) / 2);
    $semNum = ($semMk % 2 == 1) ? 1 : 2; // Ganjil = 1, Genap = 2
    
    $taKey = "{$tahun}{$semNum}";
    if (!isset($taMap[$taKey])) {
        // Create tahun akademik if not exists
        $semester = $semNum == 1 ? 'Ganjil' : 'Genap';
        $existing = DB::table('tahun_akademik')
            ->where('tahun', $tahun)
            ->where('semester', $semester)
            ->first();
        
        if (!$existing) {
            $taId = DB::table('tahun_akademik')->insertGetId([
                'tahun' => $tahun,
                'semester' => $semester,
                'is_aktif' => false,
                'tanggal_mulai' => "$tahun-" . ($semNum == 1 ? '08' : '02') . "-01",
                'tanggal_selesai' => ($semNum == 1 ? $tahun : $tahun + 1) . "-" . ($semNum == 1 ? '01' : '07') . "-31",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $taMap[$taKey] = $taId;
            echo "   Created tahun akademik: $tahun $semester (ID: $taId)\n";
        } else {
            $taMap[$taKey] = $existing->id;
        }
    }
    $taId = $taMap[$taKey];
    
    // Get MK - try by kode first, then by nama
    $mkId = null;
    $mkKode = $row['IDMAKUL'];
    $mkNama = strtolower(trim($row['NAMA']));
    
    if (isset($mkByKode[$mkKode])) {
        $mkId = $mkByKode[$mkKode]['id'];
    } elseif (isset($mkByNama[$mkNama])) {
        $mkId = $mkByNama[$mkNama]['id'];
    }
    
    if (!$mkId) {
        $noMk++;
        continue;
    }
    
    // Check if jadwal exists, create if not
    $jadwalKey = "{$mkId}-{$taId}";
    if (!isset($jadwalMap[$jadwalKey])) {
        // Create jadwal
        $jadwalId = DB::table('jadwal_kuliah')->insertGetId([
            'mata_kuliah_id' => $mkId,
            'tahun_akademik_id' => $taId,
            'dosen_id' => 1, // Default dosen
            'ruangan_id' => 1,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'kuota' => 40,
            'kelas' => 'A',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $jadwalMap[$jadwalKey] = $jadwalId;
        $jadwalCreated++;
    }
    $jadwalId = $jadwalMap[$jadwalKey];
    
    // Check if KRS exists
    $krsKey = "{$mhsId}-{$mkId}-{$taId}";
    if (!isset($existingKrs[$krsKey])) {
        // Create KRS
        $krsId = DB::table('krs')->insertGetId([
            'mahasiswa_id' => $mhsId,
            'jadwal_kuliah_id' => $jadwalId,
            'tahun_akademik_id' => $taId,
            'status' => 'disetujui',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $existingKrs[$krsKey] = $krsId;
        $krsCreated++;
    } else {
        $krsId = $existingKrs[$krsKey];
    }
    
    // Check if nilai exists
    $existingNilai = DB::table('nilai')->where('krs_id', $krsId)->first();
    if (!$existingNilai) {
        // Create nilai
        $huruf = $row['SIMBOL'] ?: '';
        $bobot = $bobotMap[$huruf] ?? ($row['BOBOT'] ?: 0);
        $nilaiAkhir = $row['NILAI'] ?: ($bobot * 25); // Estimate if not provided
        
        DB::table('nilai')->insert([
            'krs_id' => $krsId,
            'tugas' => null,
            'kehadiran' => null,
            'uts' => null,
            'uas' => null,
            'nilai_akhir' => $nilaiAkhir,
            'huruf' => $huruf,
            'bobot' => $bobot,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $nilaiCreated++;
    } else {
        $skipped++;
    }
    
    if ($progress % 500 == 0) {
        echo "   Progress: $progress/$total (KRS: $krsCreated, Nilai: $nilaiCreated)\n";
    }
}

echo "\n=== HASIL MIGRASI ===\n";
echo "Tahun Akademik created: " . (count($taMap) - count($taList)) . "\n";
echo "Jadwal created: $jadwalCreated\n";
echo "KRS created: $krsCreated\n";
echo "Nilai created: $nilaiCreated\n";
echo "Skipped (already exists): $skipped\n";
echo "Skipped (no mahasiswa): $noMhs\n";
echo "Skipped (no MK): $noMk\n";

$source->close();
echo "\nDone!\n";
