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

echo "=== FIX JADWAL DATA FROM kelaskuliah_jadwal ===\n\n";

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

// Get semua jadwal dari kelaskuliah_jadwal
$query = "
    SELECT 
        kj.TAHUN, kj.SEMESTER, kj.IDMAKUL, kj.IDKELAS, kj.IDHARI, 
        kj.JAMMULAI, kj.JAMSELESAI, kj.IDRUANGAN
    FROM kelaskuliah_jadwal kj
    ORDER BY kj.TAHUN DESC, kj.SEMESTER DESC
";

$result = $source->query($query);
$total = $result->num_rows;
echo "Total jadwal di source: $total\n\n";

// Get mapping data lokal
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

$dosenMap = DB::table('dosen')
    ->pluck('id', 'nidn')
    ->toArray();

$ruanganMap = DB::table('ruangan')
    ->pluck('id', 'kode')
    ->toArray();

$prodiMap = DB::table('program_studi')
    ->pluck('id', 'kode')
    ->toArray();

// Get prodi from mata kuliah
$mkProdiMap = [];
$mkProdiResult = $source->query("SELECT ID, IDPRODI FROM makul");
while ($row = $mkProdiResult->fetch_assoc()) {
    $mkProdiMap[$row['ID']] = $row['IDPRODI'];
}

$updated = 0;
$created = 0;
$skipped = 0;

while ($row = $result->fetch_assoc()) {
    $tahun = $row['TAHUN'];
    $sem = $row['SEMESTER'];
    $taKey = $tahun . $sem;
    
    $tahunAkademikId = $tahunAkademikMap[$taKey] ?? null;
    $mataKuliahId = $mataKuliahMap[$row['IDMAKUL']] ?? null;
    $ruanganId = $ruanganMap[$row['IDRUANGAN']] ?? null;
    $hari = $hariMap[$row['IDHARI']] ?? 'Senin';
    
    // Get prodi from mata kuliah
    $prodiKode = $mkProdiMap[$row['IDMAKUL']] ?? null;
    $prodiId = $prodiMap[$prodiKode] ?? null;
    
    if (!$tahunAkademikId || !$mataKuliahId) {
        $skipped++;
        continue;
    }
    
    // Format jam (remove seconds if present)
    $jamMulai = substr($row['JAMMULAI'], 0, 5) . ':00';
    $jamSelesai = substr($row['JAMSELESAI'], 0, 5) . ':00';
    
    // Cek apakah sudah ada jadwal dengan kombinasi ini
    $existing = DB::table('jadwal_kuliah')
        ->where('tahun_akademik_id', $tahunAkademikId)
        ->where('mata_kuliah_id', $mataKuliahId)
        ->where('kelas', $row['IDKELAS'])
        ->first();
    
    if ($existing) {
        // Update jadwal yang sudah ada
        $updateData = [
            'hari' => $hari,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'updated_at' => now()
        ];
        if ($ruanganId) {
            $updateData['ruangan_id'] = $ruanganId;
        }
        
        DB::table('jadwal_kuliah')
            ->where('id', $existing->id)
            ->update($updateData);
        $updated++;
    } else {
        // Skip create baru karena tidak ada dosen_id 
        // Fokus hanya update yang sudah ada
        $skipped++;
    }
}

echo "Hasil:\n";
echo "  - Updated: $updated\n";
echo "  - Created: $created\n";
echo "  - Skipped: $skipped\n";

// Verifikasi
echo "\n=== VERIFIKASI ===\n";
$sample = DB::table('jadwal_kuliah as j')
    ->join('mata_kuliah as m', 'j.mata_kuliah_id', '=', 'm.id')
    ->join('tahun_akademik as ta', 'j.tahun_akademik_id', '=', 'ta.id')
    ->select('j.hari', 'j.jam_mulai', 'j.jam_selesai', 'm.nama as mk', 'ta.nama_lengkap as ta')
    ->where('ta.tahun', 2025)
    ->where('ta.semester', 'Ganjil')
    ->orderBy('j.hari')
    ->orderBy('j.jam_mulai')
    ->limit(10)
    ->get();

echo "\nSample jadwal 2025 Ganjil:\n";
foreach ($sample as $j) {
    echo "  {$j->hari} {$j->jam_mulai}-{$j->jam_selesai}: {$j->mk}\n";
}

$source->close();
echo "\nDone!\n";
