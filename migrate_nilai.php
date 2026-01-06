<?php
/**
 * Migrasi Data Nilai dari Database Source
 * 
 * Source table: nilai
 * - IDKOMPONEN: 1 (Tugas), 2 (Kehadiran), UTS, UAS
 * - Data di-pivot per mahasiswa-matakuliah-semester
 * 
 * Target: nilai table (krs_id, tugas, kehadiran, uts, uas, nilai_akhir, huruf, bobot)
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Krs;
use App\Models\Nilai;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\TahunAkademik;

// Connect to source database
$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
$source->set_charset('utf8mb4');

if ($source->connect_error) {
    die("Connection failed: " . $source->connect_error);
}

echo "=== MIGRASI DATA NILAI ===\n\n";

// Build tahun akademik mapping (same as KRS fix)
// Source uses year when semester ENDS, Local uses year when semester STARTS
$tahunAkademikMap = [];
$taList = TahunAkademik::all();
foreach ($taList as $ta) {
    echo "Local TA: {$ta->id} = {$ta->tahun} {$ta->semester}\n";
}
echo "\n";

// Manual mapping based on our analysis:
// Source 2025/1 (Sep-Des 2024) => Local 2024 Ganjil
// Source 2025/2 (Feb-Jul 2025) => Local 2024 Genap  
// Source 2026/1 (Sep-Des 2025) => Local 2025 Ganjil (AKTIF)
// Source 2025/4 (Pendek) => Local 2025 Pendek

// Build mapping
foreach ($taList as $ta) {
    if ($ta->semester == 'Ganjil') {
        // Source year = Local year + 1, semester = 1
        $srcYear = $ta->tahun + 1;
        $srcSem = 1;
        $key = "{$srcYear}-{$srcSem}";
        $tahunAkademikMap[$key] = $ta->id;
        echo "Mapping: Source {$srcYear}/{$srcSem} => Local {$ta->tahun} Ganjil (ID:{$ta->id})\n";
    } elseif ($ta->semester == 'Genap') {
        // Source year = Local year + 1, semester = 2
        $srcYear = $ta->tahun + 1;
        $srcSem = 2;
        $key = "{$srcYear}-{$srcSem}";
        $tahunAkademikMap[$key] = $ta->id;
        echo "Mapping: Source {$srcYear}/{$srcSem} => Local {$ta->tahun} Genap (ID:{$ta->id})\n";
    } elseif ($ta->semester == 'Pendek') {
        // Source year = Local year, semester = 4
        $srcYear = $ta->tahun;
        $srcSem = 4;
        $key = "{$srcYear}-{$srcSem}";
        $tahunAkademikMap[$key] = $ta->id;
        echo "Mapping: Source {$srcYear}/{$srcSem} => Local {$ta->tahun} Pendek (ID:{$ta->id})\n";
    }
}
echo "\n";

// Build mahasiswa mapping (nim -> id)
$mahasiswaMap = Mahasiswa::pluck('id', 'nim')->toArray();
echo "Total Mahasiswa: " . count($mahasiswaMap) . "\n";

// Build mata kuliah mapping (kode -> id)
$mkMap = MataKuliah::pluck('id', 'kode')->toArray();
echo "Total Mata Kuliah: " . count($mkMap) . "\n\n";

// Get all unique mahasiswa-matakuliah-semester combinations with nilai
echo "Fetching nilai data from source...\n";
$query = "
    SELECT 
        IDMAHASISWA, IDMAKUL, TAHUN, SEMESTER, KELAS,
        MAX(CASE WHEN IDKOMPONEN = '1' THEN NILAI END) as tugas,
        MAX(CASE WHEN IDKOMPONEN = '2' THEN NILAI END) as kehadiran,
        MAX(CASE WHEN IDKOMPONEN = 'UTS' THEN NILAI END) as uts,
        MAX(CASE WHEN IDKOMPONEN = 'UAS' THEN NILAI END) as uas
    FROM nilai
    GROUP BY IDMAHASISWA, IDMAKUL, TAHUN, SEMESTER, KELAS
    ORDER BY IDMAHASISWA, TAHUN, SEMESTER
";

$result = $source->query($query);
$totalNilai = $result->num_rows;
echo "Found {$totalNilai} nilai records to process\n\n";

// Clear existing nilai
$clearedCount = Nilai::count();
Nilai::truncate();
echo "Cleared existing nilai: {$clearedCount}\n\n";

$inserted = 0;
$skipped = 0;
$noKrs = 0;
$krsCreated = 0;
$processed = 0;

while ($row = $result->fetch_assoc()) {
    $processed++;
    
    if ($processed % 1000 == 0) {
        echo "Progress: {$processed}/{$totalNilai}\n";
    }
    
    $nim = $row['IDMAHASISWA'];
    $kodeMk = $row['IDMAKUL'];
    $srcTahun = $row['TAHUN'];
    $srcSemester = $row['SEMESTER'];
    $kelas = $row['KELAS'];
    
    // Get local IDs
    $mahasiswaId = $mahasiswaMap[$nim] ?? null;
    $mkId = $mkMap[$kodeMk] ?? null;
    $taKey = "{$srcTahun}-{$srcSemester}";
    $tahunAkademikId = $tahunAkademikMap[$taKey] ?? null;
    
    if (!$mahasiswaId || !$mkId || !$tahunAkademikId) {
        $skipped++;
        continue;
    }
    
    // Find matching KRS
    $krs = Krs::where('mahasiswa_id', $mahasiswaId)
        ->where('tahun_akademik_id', $tahunAkademikId)
        ->whereHas('jadwalKuliah', function($q) use ($mkId) {
            $q->where('mata_kuliah_id', $mkId);
        })
        ->first();
    
    if (!$krs) {
        // Try to create KRS if jadwal exists
        $jadwal = \App\Models\JadwalKuliah::where('mata_kuliah_id', $mkId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->first();
        
        if ($jadwal) {
            $krs = Krs::create([
                'mahasiswa_id' => $mahasiswaId,
                'tahun_akademik_id' => $tahunAkademikId,
                'jadwal_kuliah_id' => $jadwal->id,
                'status' => 'disetujui',
                'tanggal_pengajuan' => now(),
                'tanggal_persetujuan' => now(),
            ]);
            $krsCreated++;
        } else {
            $noKrs++;
            continue;
        }
    }
    
    // Check if nilai already exists
    if (Nilai::where('krs_id', $krs->id)->exists()) {
        $skipped++;
        continue;
    }
    
    // Calculate nilai akhir
    // Bobot: Tugas 20%, Kehadiran 20%, UTS 25%, UAS 35%
    $tugas = $row['tugas'] ?? 0;
    $kehadiran = $row['kehadiran'] ?? 0;
    $uts = $row['uts'] ?? 0;
    $uas = $row['uas'] ?? 0;
    
    $nilaiAkhir = ($tugas * 0.20) + ($kehadiran * 0.20) + ($uts * 0.25) + ($uas * 0.35);
    $nilaiAkhir = round($nilaiAkhir, 2);
    
    // Convert to huruf
    $huruf = konversiHuruf($nilaiAkhir);
    $bobot = konversiBobot($huruf);
    
    Nilai::create([
        'krs_id' => $krs->id,
        'tugas' => $tugas,
        'kehadiran' => $kehadiran,
        'uts' => $uts,
        'uas' => $uas,
        'nilai_akhir' => $nilaiAkhir,
        'huruf' => $huruf,
        'bobot' => $bobot,
    ]);
    
    $inserted++;
}

echo "\n=== HASIL MIGRASI ===\n";
echo "Processed: {$processed}\n";
echo "Inserted: {$inserted}\n";
echo "KRS Created: {$krsCreated}\n";
echo "Skipped (no mahasiswa/mk/tahun_akademik): {$skipped}\n";
echo "Skipped (no jadwal): {$noKrs}\n";

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
        echo "\n{$sem}: " . $items->count() . " MK dengan nilai\n";
        foreach ($items as $krs) {
            $mk = $krs->jadwalKuliah->mataKuliah ?? null;
            $nilai = $krs->nilai;
            if ($mk && $nilai) {
                echo "  {$mk->kode} | {$mk->nama} | Nilai: {$nilai->nilai_akhir} ({$nilai->huruf})\n";
            }
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
    echo "\nTotal SKS Lulus: {$totalSks}, IPK: {$ipk}\n";
}

$source->close();
echo "\n=== SELESAI ===\n";

// Helper functions
function konversiHuruf($nilai) {
    if ($nilai >= 85) return 'A';
    if ($nilai >= 80) return 'A-';
    if ($nilai >= 75) return 'B+';
    if ($nilai >= 70) return 'B';
    if ($nilai >= 65) return 'B-';
    if ($nilai >= 60) return 'C+';
    if ($nilai >= 55) return 'C';
    if ($nilai >= 50) return 'D';
    return 'E';
}

function konversiBobot($huruf) {
    $bobotMap = [
        'A' => 4.00,
        'A-' => 3.75,
        'B+' => 3.50,
        'B' => 3.00,
        'B-' => 2.75,
        'C+' => 2.50,
        'C' => 2.00,
        'D' => 1.00,
        'E' => 0.00,
    ];
    return $bobotMap[$huruf] ?? 0;
}
