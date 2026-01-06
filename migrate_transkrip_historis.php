<?php
/**
 * Migrasi Data Transkrip Historis dari Database Source
 * 
 * Source table: transkrip_detil
 * - Data nilai dari semua semester sebelumnya
 * - Field SEMESTERMK menunjukkan semester ke berapa
 * 
 * Target: nilai table (via KRS)
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Krs;
use App\Models\Nilai;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\TahunAkademik;
use App\Models\JadwalKuliah;

// Connect to source database
$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
$source->set_charset('utf8mb4');

if ($source->connect_error) {
    die("Connection failed: " . $source->connect_error);
}

echo "=== MIGRASI DATA TRANSKRIP HISTORIS ===\n\n";

// Build mahasiswa mapping (nim -> [id, angkatan])
$mahasiswaData = Mahasiswa::all()->keyBy('nim');
echo "Total Mahasiswa di local: " . $mahasiswaData->count() . "\n";

// Build mata kuliah mapping (kode -> id)
$mkMap = MataKuliah::pluck('id', 'kode')->toArray();
echo "Total Mata Kuliah: " . count($mkMap) . "\n";

// Get all tahun akademik
$taList = TahunAkademik::orderBy('tahun')->orderByRaw("FIELD(semester, 'Ganjil', 'Genap', 'Pendek')")->get();
echo "\nTahun Akademik tersedia:\n";
foreach ($taList as $ta) {
    echo "  ID:{$ta->id} = {$ta->tahun} {$ta->semester}\n";
}

// Function to determine tahun akademik from angkatan and semester number
function getTahunAkademikId($angkatan, $semesterMk, $taList) {
    // Semester 1 = Ganjil tahun angkatan
    // Semester 2 = Genap tahun angkatan  
    // Semester 3 = Ganjil tahun angkatan+1
    // Semester 4 = Genap tahun angkatan+1
    // etc.
    
    $tahun = $angkatan + floor(($semesterMk - 1) / 2);
    $semester = ($semesterMk % 2 == 1) ? 'Ganjil' : 'Genap';
    
    $ta = $taList->first(function($t) use ($tahun, $semester) {
        return $t->tahun == $tahun && $t->semester == $semester;
    });
    
    return $ta ? $ta->id : null;
}

// Get transkrip_detil data
echo "\nFetching transkrip_detil from source...\n";
$query = "
    SELECT td.IDMAHASISWA, td.IDMAKUL, td.NAMA, td.SKS, td.SEMESTERMK, 
           td.BOBOT, td.NILAI as NILAI_ANGKA, td.SIMBOL as NILAI_HURUF,
           m.ANGKATAN
    FROM transkrip_detil td
    LEFT JOIN mahasiswa m ON td.IDMAHASISWA = m.ID
    WHERE (td.STATUSDELETE = 0 OR td.STATUSDELETE IS NULL)
    ORDER BY td.IDMAHASISWA, td.SEMESTERMK
";

$result = $source->query($query);
$totalRecords = $result->num_rows;
echo "Found {$totalRecords} transkrip records\n\n";

$inserted = 0;
$skipped = 0;
$noMahasiswa = 0;
$noMataKuliah = 0;
$noTahunAkademik = 0;
$alreadyExists = 0;
$krsCreated = 0;
$jadwalCreated = 0;
$processed = 0;

while ($row = $result->fetch_assoc()) {
    $processed++;
    
    if ($processed % 1000 == 0) {
        echo "Progress: {$processed}/{$totalRecords}\n";
    }
    
    $nim = $row['IDMAHASISWA'];
    $kodeMk = $row['IDMAKUL'];
    $semesterMk = (int)$row['SEMESTERMK'];
    $angkatan = (int)$row['ANGKATAN'];
    $nilaiAngka = $row['NILAI_ANGKA'] ?? 0;
    $nilaiHuruf = $row['NILAI_HURUF'] ?? 'E';
    $bobot = $row['BOBOT'] ?? 0;
    
    // Get mahasiswa from local
    $mhs = $mahasiswaData->get($nim);
    if (!$mhs) {
        $noMahasiswa++;
        continue;
    }
    
    // Use local angkatan if source is empty
    if (!$angkatan) {
        $angkatan = $mhs->angkatan;
    }
    
    // Get mata kuliah ID
    $mkId = $mkMap[$kodeMk] ?? null;
    if (!$mkId) {
        $noMataKuliah++;
        continue;
    }
    
    // Determine tahun akademik from angkatan and semester
    $tahunAkademikId = getTahunAkademikId($angkatan, $semesterMk, $taList);
    if (!$tahunAkademikId) {
        $noTahunAkademik++;
        continue;
    }
    
    // Check if KRS already exists
    $krs = Krs::where('mahasiswa_id', $mhs->id)
        ->where('tahun_akademik_id', $tahunAkademikId)
        ->whereHas('jadwalKuliah', function($q) use ($mkId) {
            $q->where('mata_kuliah_id', $mkId);
        })
        ->first();
    
    if (!$krs) {
        // Find or create jadwal
        $jadwal = JadwalKuliah::where('mata_kuliah_id', $mkId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->first();
        
        if (!$jadwal) {
            // Create jadwal for this mata kuliah and tahun akademik
            $ruanganId = \App\Models\Ruangan::first()?->id ?? 1;
            $dosenId = \App\Models\Dosen::first()?->id ?? 1;
            $jadwal = JadwalKuliah::create([
                'mata_kuliah_id' => $mkId,
                'tahun_akademik_id' => $tahunAkademikId,
                'dosen_id' => $dosenId, // Default dosen for historical data
                'kelas' => '01',
                'hari' => 'Senin',
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:00',
                'ruangan_id' => $ruanganId,
                'kuota' => 40,
            ]);
            $jadwalCreated++;
        }
        
        // Create KRS
        $krs = Krs::create([
            'mahasiswa_id' => $mhs->id,
            'tahun_akademik_id' => $tahunAkademikId,
            'jadwal_kuliah_id' => $jadwal->id,
            'status' => 'disetujui',
            'tanggal_pengajuan' => now(),
            'tanggal_persetujuan' => now(),
        ]);
        $krsCreated++;
    }
    
    // Check if nilai already exists for this KRS
    if (Nilai::where('krs_id', $krs->id)->exists()) {
        $alreadyExists++;
        continue;
    }
    
    // Create nilai
    Nilai::create([
        'krs_id' => $krs->id,
        'tugas' => null,
        'kehadiran' => null,
        'uts' => null,
        'uas' => null,
        'nilai_akhir' => $nilaiAngka,
        'huruf' => $nilaiHuruf,
        'bobot' => $bobot,
    ]);
    
    $inserted++;
}

echo "\n=== HASIL MIGRASI ===\n";
echo "Processed: {$processed}\n";
echo "Inserted nilai: {$inserted}\n";
echo "KRS created: {$krsCreated}\n";
echo "Jadwal created: {$jadwalCreated}\n";
echo "Already exists: {$alreadyExists}\n";
echo "Skipped (no mahasiswa): {$noMahasiswa}\n";
echo "Skipped (no mata kuliah): {$noMataKuliah}\n";
echo "Skipped (no tahun akademik): {$noTahunAkademik}\n";

// Verifikasi untuk mahasiswa 1003230001
echo "\n=== VERIFIKASI MHS 1003230001 ===\n";
$mhs = Mahasiswa::where('nim', '1003230001')->first();
if ($mhs) {
    $krsList = Krs::with(['nilai', 'jadwalKuliah.mataKuliah', 'tahunAkademik'])
        ->where('mahasiswa_id', $mhs->id)
        ->whereHas('nilai')
        ->orderBy('tahun_akademik_id')
        ->get();
    
    $grouped = $krsList->groupBy(function($krs) {
        return $krs->tahunAkademik->tahun . ' ' . $krs->tahunAkademik->semester;
    });
    
    foreach ($grouped as $sem => $items) {
        echo "\n{$sem}: " . $items->count() . " MK\n";
        foreach ($items->take(5) as $krs) {
            $mk = $krs->jadwalKuliah->mataKuliah ?? null;
            $nilai = $krs->nilai;
            if ($mk && $nilai) {
                echo "  {$mk->kode} | {$mk->nama} | {$nilai->huruf} ({$nilai->bobot})\n";
            }
        }
        if ($items->count() > 5) {
            echo "  ... dan " . ($items->count() - 5) . " MK lainnya\n";
        }
    }
    
    // Calculate IPK
    $totalSks = 0;
    $totalBobot = 0;
    foreach ($krsList as $krs) {
        $sks = $krs->jadwalKuliah->mataKuliah->sks ?? 0;
        $bobot = $krs->nilai->bobot ?? 0;
        $totalSks += $sks;
        $totalBobot += ($sks * $bobot);
    }
    $ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;
    echo "\nTotal MK: " . $krsList->count() . ", Total SKS: {$totalSks}, IPK: {$ipk}\n";
}

$source->close();
echo "\n=== SELESAI ===\n";
