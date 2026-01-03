<?php
/**
 * Fix Dosen Pengampu Jadwal Kuliah
 * Update dosen_id di jadwal_kuliah berdasarkan data kelaskuliah_pengajar di source
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIX DOSEN PENGAMPU JADWAL KULIAH ===\n\n";

// Koneksi ke source database
$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
$source->set_charset('utf8mb4');

if ($source->connect_error) {
    die("Connection failed: " . $source->connect_error);
}

// Load mapping dosen lokal (by NIDN)
$dosenLokal = DB::table('dosen')
    ->whereNotNull('nidn')
    ->pluck('id', 'nidn')
    ->toArray();

echo "Dosen lokal dengan NIDN: " . count($dosenLokal) . "\n";

// Load mapping dosen lokal (by nama) untuk fallback
$dosenByNama = DB::table('dosen')
    ->pluck('id', 'nama')
    ->toArray();

// Load mapping mata kuliah lokal (by kode)
$mkLokal = DB::table('mata_kuliah')
    ->pluck('id', 'kode')
    ->toArray();

echo "Mata kuliah lokal: " . count($mkLokal) . "\n";

// Load mapping tahun akademik lokal
$taLokal = [];
$taList = DB::table('tahun_akademik')->get();
foreach ($taList as $ta) {
    $semester = $ta->semester == 'Ganjil' ? 1 : ($ta->semester == 'Genap' ? 2 : 3);
    $key = $ta->tahun . '_' . $semester;
    $taLokal[$key] = $ta->id;
}

// Ambil data dosen pengampu dari source
$result = $source->query("
    SELECT kp.TAHUN, kp.SEMESTER, kp.IDMAKUL, kp.IDKELAS, kp.IDPENGAJAR, kp.UTAMA,
           m.KODEMAKUL, d.NIDN, d.NAMA as NAMA_DOSEN
    FROM kelaskuliah_pengajar kp
    JOIN makul m ON kp.IDMAKUL = m.ID
    JOIN dosen d ON kp.IDPENGAJAR = d.ID
    WHERE kp.TAHUN >= 2020
    ORDER BY kp.TAHUN, kp.SEMESTER, m.KODEMAKUL, kp.IDKELAS
");

$updated = 0;
$notFound = 0;
$dosenNotFound = 0;

$processed = [];

while ($row = $result->fetch_assoc()) {
    $kode = $row['KODEMAKUL'];
    $tahun = $row['TAHUN'];
    $semester = $row['SEMESTER'];
    $kelas = $row['IDKELAS'];
    $nidn = trim($row['NIDN']);
    $namaDosen = $row['NAMA_DOSEN'];
    
    // Skip jika bukan pengajar utama
    if ($row['UTAMA'] != 1) {
        continue;
    }
    
    // Cari mata kuliah
    $mkId = $mkLokal[$kode] ?? null;
    if (!$mkId) {
        continue; // Skip jika MK tidak ada di lokal
    }
    
    // Cari tahun akademik
    $taKey = $tahun . '_' . $semester;
    $taId = $taLokal[$taKey] ?? null;
    if (!$taId) {
        continue; // Skip jika TA tidak ada
    }
    
    // Cari dosen
    $dosenId = null;
    if ($nidn && isset($dosenLokal[$nidn])) {
        $dosenId = $dosenLokal[$nidn];
    } elseif (isset($dosenByNama[$namaDosen])) {
        $dosenId = $dosenByNama[$namaDosen];
    }
    
    if (!$dosenId) {
        $dosenNotFound++;
        continue;
    }
    
    // Update jadwal_kuliah
    $affected = DB::table('jadwal_kuliah')
        ->where('mata_kuliah_id', $mkId)
        ->where('tahun_akademik_id', $taId)
        ->where(function($q) use ($kelas) {
            $q->where('kelas', $kelas)
              ->orWhereNull('kelas')
              ->orWhere('kelas', '');
        })
        ->update(['dosen_id' => $dosenId]);
    
    if ($affected > 0) {
        $updated += $affected;
        $key = "$kode|$tahun|$semester";
        if (!isset($processed[$key])) {
            echo "✓ $kode ($tahun/$semester) -> $namaDosen\n";
            $processed[$key] = true;
        }
    }
}

echo "\n=== SUMMARY ===\n";
echo "Jadwal updated: $updated\n";
echo "Dosen not found: $dosenNotFound\n";

// Verifikasi
echo "\n=== VERIFIKASI SAMPLE ===\n";
$sample = DB::table('jadwal_kuliah as j')
    ->join('mata_kuliah as mk', 'j.mata_kuliah_id', '=', 'mk.id')
    ->join('tahun_akademik as ta', 'j.tahun_akademik_id', '=', 'ta.id')
    ->leftJoin('dosen as d', 'j.dosen_id', '=', 'd.id')
    ->where('ta.tahun', 2025)
    ->where('ta.semester', 'Ganjil')
    ->whereIn('mk.kode', ['IF115', 'IF116', 'IF117', 'IF118', 'IF132', 'SI120', 'TI119'])
    ->select('mk.kode', 'mk.nama', 'j.kelas', 'd.nama as dosen_nama')
    ->orderBy('mk.kode')
    ->get();

foreach ($sample as $s) {
    echo "$s->kode | Kelas $s->kelas | Dosen: " . ($s->dosen_nama ?? '-') . "\n";
}

$source->close();
