<?php
/**
 * Update nilai yang kosong dari transkrip_detil
 * Fix nilai yang sudah ada tapi huruf/bobot-nya kosong
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

echo "=== UPDATE NILAI DARI TRANSKRIP_DETIL ===\n\n";

// 1. Build mahasiswa map
echo "1. Building mahasiswa map...\n";
$mhsMap = []; // nim => id
$mhs = DB::table('mahasiswa')->select('id', 'nim')->get();
foreach ($mhs as $m) {
    $mhsMap[$m->nim] = $m->id;
}
echo "   - " . count($mhsMap) . " mahasiswa\n";

// 2. Build MK map by nama
echo "2. Building MK map...\n";
$mkByNama = []; // nama => id
$mkByKode = []; // kode => id
$mks = DB::table('mata_kuliah')->select('id', 'kode', 'nama')->get();
foreach ($mks as $mk) {
    $namaNorm = strtolower(trim($mk->nama));
    $mkByNama[$namaNorm] = $mk->id;
    $mkByKode[$mk->kode] = $mk->id;
}
echo "   - " . count($mkByNama) . " MK\n";

// 3. Get nilai yang perlu diupdate (bobot null atau 0, atau huruf kosong)
echo "3. Getting nilai to update...\n";
$nilaiToUpdate = DB::table('nilai as n')
    ->join('krs as k', 'n.krs_id', '=', 'k.id')
    ->join('mahasiswa as m', 'k.mahasiswa_id', '=', 'm.id')
    ->join('jadwal_kuliah as j', 'k.jadwal_kuliah_id', '=', 'j.id')
    ->join('mata_kuliah as mk', 'j.mata_kuliah_id', '=', 'mk.id')
    ->where(function($q) {
        $q->whereNull('n.bobot')
          ->orWhere('n.bobot', 0)
          ->orWhereNull('n.huruf')
          ->orWhere('n.huruf', '');
    })
    ->select('n.id as nilai_id', 'm.nim', 'mk.nama as mk_nama', 'mk.kode as mk_kode')
    ->get();

echo "   - " . count($nilaiToUpdate) . " nilai to update\n";

// 4. Build lookup dari transkrip_detil
echo "\n4. Fetching transkrip_detil from source...\n";
$result = $source->query("
    SELECT IDMAHASISWA, IDMAKUL, NAMA, SIMBOL, BOBOT, NILAI
    FROM transkrip_detil
    WHERE (STATUSDELETE = 0 OR STATUSDELETE IS NULL)
    AND SIMBOL IS NOT NULL AND SIMBOL != ''
");

$transkripMap = []; // "nim-mkNama" => [simbol, bobot, nilai]
while ($row = $result->fetch_assoc()) {
    $nim = $row['IDMAHASISWA'];
    $mkNama = strtolower(trim($row['NAMA']));
    $key = "$nim-$mkNama";
    
    $transkripMap[$key] = [
        'simbol' => $row['SIMBOL'],
        'bobot' => (float)$row['BOBOT'],
        'nilai' => (float)$row['NILAI'],
    ];
    
    // Also by kode
    $mkKode = strtolower($row['IDMAKUL']);
    $keyKode = "$nim-$mkKode";
    $transkripMap[$keyKode] = [
        'simbol' => $row['SIMBOL'],
        'bobot' => (float)$row['BOBOT'],
        'nilai' => (float)$row['NILAI'],
    ];
}
echo "   - " . count($transkripMap) . " transkrip entries loaded\n";

// 5. Update nilai
echo "\n5. Updating nilai...\n";
$updated = 0;
$notFound = 0;

foreach ($nilaiToUpdate as $nilai) {
    $nim = $nilai->nim;
    $mkNama = strtolower(trim($nilai->mk_nama));
    $mkKode = strtolower($nilai->mk_kode);
    
    // Try by nama first, then by kode
    $key = "$nim-$mkNama";
    $keyKode = "$nim-$mkKode";
    
    $transkrip = $transkripMap[$key] ?? $transkripMap[$keyKode] ?? null;
    
    if ($transkrip) {
        DB::table('nilai')
            ->where('id', $nilai->nilai_id)
            ->update([
                'huruf' => $transkrip['simbol'],
                'bobot' => $transkrip['bobot'],
                'nilai_akhir' => $transkrip['nilai'],
                'updated_at' => now(),
            ]);
        $updated++;
    } else {
        $notFound++;
    }
    
    if (($updated + $notFound) % 500 == 0) {
        echo "   Progress: " . ($updated + $notFound) . "/" . count($nilaiToUpdate) . " (updated: $updated)\n";
    }
}

echo "\n=== HASIL ===\n";
echo "Updated: $updated\n";
echo "Not found in transkrip: $notFound\n";

$source->close();
echo "\nDone!\n";
