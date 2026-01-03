<?php
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

echo "=== FIX JADWAL DATA (TANPA KELAS FILTER) ===\n\n";

// Mapping hari dari ID ke nama
$hariMap = [
    1 => 'Senin',
    2 => 'Selasa', 
    3 => 'Rabu',
    4 => 'Kamis',
    5 => 'Jumat',
    6 => 'Sabtu',
    7 => 'Minggu'
];

// Get mapping tahun akademik
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

$mataKuliahMap = DB::table('mata_kuliah')
    ->pluck('id', 'kode')
    ->toArray();

$ruanganMap = DB::table('ruangan')
    ->pluck('id', 'kode')
    ->toArray();

// Get jadwal dari kelaskuliah_jadwal - group by IDMAKUL, TAHUN, SEMESTER (ambil 1 kelas saja)
$query = "
    SELECT 
        TAHUN, SEMESTER, IDMAKUL, MIN(IDKELAS) as IDKELAS, 
        MIN(IDHARI) as IDHARI, MIN(JAMMULAI) as JAMMULAI, 
        MIN(JAMSELESAI) as JAMSELESAI, MIN(IDRUANGAN) as IDRUANGAN
    FROM kelaskuliah_jadwal
    GROUP BY TAHUN, SEMESTER, IDMAKUL
";

$result = $source->query($query);
$total = $result->num_rows;
echo "Total jadwal unik (per MK/TA) di source: $total\n\n";

$updated = 0;
$notFound = 0;

while ($row = $result->fetch_assoc()) {
    $tahun = $row['TAHUN'];
    $sem = $row['SEMESTER'];
    $taKey = $tahun . $sem;
    
    $tahunAkademikId = $tahunAkademikMap[$taKey] ?? null;
    $mataKuliahId = $mataKuliahMap[$row['IDMAKUL']] ?? null;
    $ruanganId = $ruanganMap[$row['IDRUANGAN']] ?? null;
    $hari = $hariMap[$row['IDHARI']] ?? 'Senin';
    
    if (!$tahunAkademikId || !$mataKuliahId) {
        $notFound++;
        continue;
    }
    
    // Format jam
    $jamMulai = substr($row['JAMMULAI'], 0, 5) . ':00';
    $jamSelesai = substr($row['JAMSELESAI'], 0, 5) . ':00';
    
    // Update SEMUA jadwal dengan MK dan TA yang sama (termasuk yang kelas NULL)
    $count = DB::table('jadwal_kuliah')
        ->where('tahun_akademik_id', $tahunAkademikId)
        ->where('mata_kuliah_id', $mataKuliahId)
        ->update([
            'hari' => $hari,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'ruangan_id' => $ruanganId ?: DB::raw('ruangan_id'),
            'updated_at' => now()
        ]);
    
    $updated += $count;
}

echo "Hasil:\n";
echo "  - Updated: $updated jadwal\n";
echo "  - Not found: $notFound\n";

// Verifikasi untuk mahasiswa 1003230001
echo "\n=== VERIFIKASI MAHASISWA 1003230001 ===\n";
$mhs = App\Models\Mahasiswa::where('nim', '1003230001')->first();
$krs = App\Models\Krs::with('jadwalKuliah.mataKuliah')
    ->where('mahasiswa_id', $mhs->id)
    ->where('status', 'Disetujui')
    ->get()
    ->sortBy(function($k) {
        $hariOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6];
        return ($hariOrder[$k->jadwalKuliah->hari] ?? 7) . $k->jadwalKuliah->jam_mulai;
    });

echo "\nJadwal {$mhs->nama}:\n";
foreach($krs as $k) {
    $j = $k->jadwalKuliah;
    $jamMulai = \Carbon\Carbon::parse($j->jam_mulai)->format('H:i');
    $jamSelesai = \Carbon\Carbon::parse($j->jam_selesai)->format('H:i');
    echo "  {$j->hari} $jamMulai-$jamSelesai: {$j->mataKuliah->nama}\n";
}

$source->close();
echo "\nDone!\n";
