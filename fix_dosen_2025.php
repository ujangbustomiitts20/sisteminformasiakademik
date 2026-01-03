<?php
/**
 * Fix dosen pengampu untuk jadwal 2025 Ganjil
 * Update dari sumber kelaskuliah_pengajar
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Source database
$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
if ($source->connect_error) {
    die("Connection failed: " . $source->connect_error);
}
$source->set_charset('utf8');

echo "=== Fix Dosen Pengampu 2025 Ganjil ===\n\n";

// Get TA 2025 Ganjil
$ta = DB::table('tahun_akademik')->where('tahun', 2025)->where('semester', 'Ganjil')->first();
if (!$ta) {
    die("Tahun Akademik 2025 Ganjil tidak ditemukan!\n");
}
echo "TA: {$ta->tahun} {$ta->semester} (ID: {$ta->id})\n";

// Get BAMBANG WIDODO ID (default dosen)
$bambang = DB::table('dosen')->where('nama', 'like', '%BAMBANG WIDODO%')->first();
echo "BAMBANG WIDODO ID: {$bambang->id}\n\n";

// Build dosen map from source - using ID column
$dosenMap = [];
$dosenRes = $source->query("SELECT ID, NAMA FROM dosen");
while ($row = $dosenRes->fetch_assoc()) {
    $localDosen = DB::table('dosen')->whereRaw("UPPER(nama) = UPPER(?)", [$row['NAMA']])->first();
    if ($localDosen) {
        $dosenMap[$row['ID']] = $localDosen->id;
    }
}
echo "Mapped " . count($dosenMap) . " dosen\n\n";

// Get all jadwal with BAMBANG for 2025 Ganjil
$jadwalList = DB::table('jadwal_kuliah as j')
    ->join('mata_kuliah as mk', 'j.mata_kuliah_id', '=', 'mk.id')
    ->where('j.tahun_akademik_id', $ta->id)
    ->where('j.dosen_id', $bambang->id)
    ->select('j.id', 'j.kelas', 'mk.kode', 'mk.nama')
    ->get();

echo "Jadwal dengan BAMBANG WIDODO: " . $jadwalList->count() . "\n";
echo str_repeat('-', 80) . "\n";

$updated = 0;
$notFound = 0;

foreach ($jadwalList as $jadwal) {
    // Query source by kode MK and tahun akademik 2025 Ganjil (TAHUN=2025, SEMESTER=1)
    $mkKode = $source->real_escape_string($jadwal->kode);
    
    $sql = "
        SELECT IDPENGAJAR
        FROM kelaskuliah_pengajar
        WHERE TAHUN = 2025 AND SEMESTER = 1
        AND IDMAKUL = '{$mkKode}'
        AND UTAMA = 1
        LIMIT 1
    ";
    
    $res = $source->query($sql);
    $row = $res ? $res->fetch_assoc() : null;
    
    if ($row && isset($dosenMap[$row['IDPENGAJAR']])) {
        $newDosenId = $dosenMap[$row['IDPENGAJAR']];
        DB::table('jadwal_kuliah')->where('id', $jadwal->id)->update(['dosen_id' => $newDosenId]);
        
        $newDosen = DB::table('dosen')->where('id', $newDosenId)->first();
        echo "[UPDATED] {$jadwal->kode} - Kelas {$jadwal->kelas}: {$newDosen->nama}\n";
        $updated++;
    } else {
        // Try any year - get the most recent pengajar
        $sql2 = "
            SELECT IDPENGAJAR
            FROM kelaskuliah_pengajar
            WHERE IDMAKUL = '{$mkKode}'
            AND UTAMA = 1
            ORDER BY TAHUN DESC, SEMESTER DESC
            LIMIT 1
        ";
        
        $res2 = $source->query($sql2);
        $row2 = $res2 ? $res2->fetch_assoc() : null;
        
        if ($row2 && isset($dosenMap[$row2['IDPENGAJAR']])) {
            $newDosenId = $dosenMap[$row2['IDPENGAJAR']];
            DB::table('jadwal_kuliah')->where('id', $jadwal->id)->update(['dosen_id' => $newDosenId]);
            
            $newDosen = DB::table('dosen')->where('id', $newDosenId)->first();
            echo "[UPDATED-ALT] {$jadwal->kode} - Kelas {$jadwal->kelas}: {$newDosen->nama}\n";
            $updated++;
        } else {
            echo "[NOT FOUND] {$jadwal->kode} - Kelas {$jadwal->kelas} (IDPENGAJAR: " . ($row2['IDPENGAJAR'] ?? 'null') . ")\n";
            $notFound++;
        }
    }
}

$source->close();

echo str_repeat('-', 80) . "\n";
echo "Updated: {$updated}\n";
echo "Not Found: {$notFound}\n";
echo "Done!\n";
