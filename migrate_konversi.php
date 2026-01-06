<?php
/**
 * Migration Script: Konversi Nilai (Transfer Students)
 * Source: itts_sikad @ 192.168.120.121
 * Target: siakad local
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== MIGRASI MAHASISWA KONVERSI & NILAI KONVERSI ===\n\n";

// Connect to source database
$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
if ($source->connect_error) {
    die("Connection failed: " . $source->connect_error);
}
$source->set_charset('utf8mb4');

// Build lookup maps
echo "Building lookup maps...\n";

// Program Studi map (local)
$prodiMap = DB::table('program_studi')->pluck('id', 'kode')->toArray();

// Mahasiswa map (local) by NIM - NOTE: source ID is actually NIM in local
$mahasiswaMap = DB::table('mahasiswa')->pluck('id', 'nim')->toArray();

// Mata Kuliah map (local) by kode
$mataKuliahMap = DB::table('mata_kuliah')->pluck('id', 'kode')->toArray();

// User admin for processing
$adminId = DB::table('users')->where('role', 'admin')->value('id');

// =====================================================
// PART 1: Get mahasiswa konversi from source
// =====================================================
echo "\n=== MAHASISWA KONVERSI ===\n";

$mahasiswaKonversi = $source->query("
    SELECT DISTINCT m.ID, m.NAMA, m.ID as NIM, m.ANGKATAN, m.IDPRODI,
           m.SKPINDAHAN, m.JMLSKSDIAKUIPINDAHAN, m.NIMASALPTPINDAHAN, 
           m.KODEPTPINDAHAN, m.NAMAPRODIPINDAHAN
    FROM mahasiswa m
    WHERE m.KODEPTPINDAHAN IS NOT NULL AND m.KODEPTPINDAHAN != ''
    ORDER BY m.ANGKATAN DESC
");

$pengajuanCreated = 0;
$pengajuanSkipped = 0;
$pengajuanMap = []; // NIM => pengajuan_konversi.id

while ($mhs = $mahasiswaKonversi->fetch_assoc()) {
    $nim = $mhs['NIM'];
    
    // Skip if no NIM or not in local
    if (empty($nim)) {
        echo "  Skip: {$mhs['NAMA']} - No NIM\n";
        $pengajuanSkipped++;
        continue;
    }
    
    // Check if mahasiswa exists in local
    if (!isset($mahasiswaMap[$nim])) {
        echo "  Skip: {$nim} - {$mhs['NAMA']} - Not in local\n";
        $pengajuanSkipped++;
        continue;
    }
    
    $mahasiswaId = $mahasiswaMap[$nim];
    
    // Check if pengajuan_konversi already exists
    $existing = DB::table('pengajuan_konversi')
        ->where('mahasiswa_id', $mahasiswaId)
        ->first();
    
    if ($existing) {
        $pengajuanMap[$nim] = $existing->id;
        echo "  Exists: {$nim} - {$mhs['NAMA']}\n";
        continue;
    }
    
    // Get prodi tujuan
    $prodiTujuanId = null;
    if (!empty($mhs['IDPRODI'])) {
        $prodiTujuanId = $prodiMap[$mhs['IDPRODI']] ?? null;
    }
    
    // Generate nomor pengajuan
    $nomorPengajuan = 'KNV' . date('Ym') . str_pad($pengajuanCreated + 1, 4, '0', STR_PAD_LEFT);
    
    // Create pengajuan_konversi
    $pengajuanId = DB::table('pengajuan_konversi')->insertGetId([
        'nomor_pengajuan' => $nomorPengajuan,
        'nama_calon_mahasiswa' => $mhs['NAMA'],
        'email_calon' => strtolower(str_replace(' ', '.', $mhs['NAMA'])) . '@mail.com',
        'no_hp_calon' => '08' . rand(1000000000, 9999999999),
        'program_studi_tujuan_id' => $prodiTujuanId,
        'mahasiswa_id' => $mahasiswaId,
        'universitas_asal' => $mhs['KODEPTPINDAHAN'] ?: 'Unknown',
        'program_studi_asal' => $mhs['NAMAPRODIPINDAHAN'] ?: 'Unknown',
        'nim_asal' => $mhs['NIMASALPTPINDAHAN'] ?: '-',
        'tahun_masuk_asal' => $mhs['ANGKATAN'] ? ($mhs['ANGKATAN'] - 1) : 2020,
        'status' => 'disetujui',
        'catatan' => "Migrasi dari sistem lama. SKS diakui: {$mhs['JMLSKSDIAKUIPINDAHAN']}",
        'diproses_oleh' => $adminId,
        'tanggal_diproses' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    $pengajuanMap[$nim] = $pengajuanId;
    $pengajuanCreated++;
    echo "  Created: {$nim} - {$mhs['NAMA']} (SKS: {$mhs['JMLSKSDIAKUIPINDAHAN']})\n";
}

echo "\nPengajuan Created: {$pengajuanCreated}\n";
echo "Pengajuan Skipped: {$pengajuanSkipped}\n";

// =====================================================
// PART 2: Migrate nilai konversi
// =====================================================
echo "\n=== NILAI KONVERSI ===\n";

$nilaiKonversi = $source->query("
    SELECT nk.*, a.USERNAME as NIM
    FROM nilaikonversi nk
    LEFT JOIN mahasiswa m ON nk.IDMAHASISWA = m.ID OR nk.IDMAHASISWA = (SELECT USERNAME FROM account WHERE ID = m.ACCOUNT_ID)
    LEFT JOIN account a ON m.ACCOUNT_ID = a.ID
    ORDER BY nk.IDMAHASISWA
");

// Alternative: directly use IDMAHASISWA as NIM if it matches pattern
$nilaiKonversi = $source->query("
    SELECT * FROM nilaikonversi ORDER BY IDMAHASISWA
");

$detailCreated = 0;
$detailSkipped = 0;
$currentMhs = null;
$pengajuanIdForMhs = null;

while ($nilai = $nilaiKonversi->fetch_assoc()) {
    $nimSource = $nilai['IDMAHASISWA']; // This might be NIM directly
    
    // Try to find pengajuan
    if ($currentMhs !== $nimSource) {
        $currentMhs = $nimSource;
        
        // First try direct NIM match
        if (isset($pengajuanMap[$nimSource])) {
            $pengajuanIdForMhs = $pengajuanMap[$nimSource];
        } else {
            // Try to find mahasiswa by NIM pattern
            $mahasiswaId = $mahasiswaMap[$nimSource] ?? null;
            if ($mahasiswaId) {
                // Check if pengajuan exists for this mahasiswa
                $existing = DB::table('pengajuan_konversi')
                    ->where('mahasiswa_id', $mahasiswaId)
                    ->value('id');
                $pengajuanIdForMhs = $existing;
            } else {
                $pengajuanIdForMhs = null;
            }
        }
    }
    
    if (!$pengajuanIdForMhs) {
        $detailSkipped++;
        continue;
    }
    
    // Check if detail already exists
    $exists = DB::table('detail_konversi')
        ->where('pengajuan_konversi_id', $pengajuanIdForMhs)
        ->where('kode_mk_asal', $nilai['IDMAKULASAL'] ?: $nilai['IDMAKUL'])
        ->exists();
    
    if ($exists) {
        continue;
    }
    
    // Find mata kuliah target
    $mataKuliahId = $mataKuliahMap[$nilai['IDMAKUL']] ?? null;
    
    // Convert bobot to grade
    $bobot = floatval($nilai['BOBOT']);
    
    DB::table('detail_konversi')->insert([
        'pengajuan_konversi_id' => $pengajuanIdForMhs,
        'kode_mk_asal' => $nilai['IDMAKULASAL'] ?: '-',
        'nama_mk_asal' => $nilai['NAMAMAKULASAL'] ?: $nilai['NAMAMAKUL'],
        'sks_asal' => intval($nilai['SKSASAL']) ?: intval($nilai['SKS']),
        'nilai_asal' => $nilai['NILAIASAL'] ?: $nilai['NILAI'],
        'bobot_asal' => floatval($nilai['BOBOTASAL']) ?: $bobot,
        'mata_kuliah_id' => $mataKuliahId,
        'nilai_konversi' => $nilai['NILAI'],
        'bobot_konversi' => $bobot,
        'status' => 'disetujui',
        'alasan' => 'Migrasi dari sistem lama',
        'disetujui_oleh' => $adminId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    $detailCreated++;
}

echo "Detail Konversi Created: {$detailCreated}\n";
echo "Detail Konversi Skipped: {$detailSkipped}\n";

// =====================================================
// Summary
// =====================================================
echo "\n=== SUMMARY ===\n";
echo "Pengajuan Konversi: " . DB::table('pengajuan_konversi')->count() . "\n";
echo "Detail Konversi: " . DB::table('detail_konversi')->count() . "\n";

$source->close();
echo "\nDone!\n";
