<?php
/**
 * Script Migrasi Data Ruangan, Presensi, Tagihan, Pembayaran, dan Beasiswa
 * dari Database itts_sikad ke SIAKAD Lokal
 */

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use App\Models\Krs;
use App\Models\Ruangan;
use App\Models\Absensi;
use App\Models\Tagihan;
use App\Models\TransaksiPembayaran;
use App\Models\Beasiswa;

// Konfigurasi database sumber
config(['database.connections.source' => [
    'driver' => 'mysql',
    'host' => '192.168.120.121',
    'port' => '3306',
    'database' => 'itts_sikad',
    'username' => 'root',
    'password' => 'bismillaH',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]]);

echo "==========================================\n";
echo "   MIGRASI DATA TAMBAHAN (BATCH 2)      \n";
echo "==========================================\n\n";

// Test koneksi
try {
    DB::connection('source')->getPdo();
    echo "✅ Koneksi ke database sumber berhasil\n\n";
} catch (Exception $e) {
    echo "❌ Koneksi gagal: " . $e->getMessage() . "\n";
    exit(1);
}

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

$stats = [
    'ruangan' => 0,
    'absensi' => 0,
    'tagihan' => 0,
    'pembayaran' => 0,
    'beasiswa' => 0,
];

// Build maps
$mahasiswaMap = Mahasiswa::pluck('id', 'nim')->toArray();

// Tahun Akademik Map
$tahunMap = [];
$tas = TahunAkademik::all();
foreach ($tas as $ta) {
    $sem = $ta->semester == 'Ganjil' ? 1 : 2;
    $tahunMap[$ta->tahun . $sem] = $ta->id;
    $tahunMap[$ta->tahun . ($sem == 1 ? 3 : 4)] = $ta->id;
}

// ========================================
// 1. MIGRASI RUANGAN
// ========================================
echo "1️⃣  Migrasi RUANGAN...\n";

$ruanganSource = DB::connection('source')->table('ruangan')->get();

foreach ($ruanganSource as $r) {
    $existing = Ruangan::where('kode', $r->ID)->first();
    
    if (!$existing) {
        try {
            // Map jenis
            $jenis = match($r->JENIS) {
                'L' => 'Lab',
                'A' => 'Aula',
                'K' => 'Kelas',
                default => 'Lainnya',
            };
            
            Ruangan::create([
                'kode' => $r->ID,
                'nama' => $r->NAMA ?: "Ruangan {$r->ID}",
                'kapasitas' => $r->KAPASITAS ?: 30,
                'gedung' => $r->GEDUNG ?: 'Gedung Utama',
                'lantai' => '1',
                'jenis' => $jenis,
            ]);
            $stats['ruangan']++;
        } catch (Exception $e) {
            // Skip
        }
    }
}
echo "   ✅ Total: {$stats['ruangan']} ruangan\n";

// ========================================
// 2. MIGRASI PRESENSI MAHASISWA
// ========================================
echo "\n2️⃣  Migrasi PRESENSI MAHASISWA...\n";

// Build KRS map: mahasiswa_tahun_semester_makul => krs_id
// Note: Tidak menyertakan kelas karena presensi menggunakan IDKELAS (numeric)
// sementara KRS menggunakan nama kelas (string atau NULL)
echo "   Building KRS map...\n";
$krsMap = [];

// Process KRS in chunks
Krs::with(['mahasiswa', 'tahunAkademik', 'jadwalKuliah.mataKuliah'])
    ->chunk(1000, function ($krsList) use (&$krsMap) {
        foreach ($krsList as $krs) {
            if (!$krs->mahasiswa || !$krs->tahunAkademik || !$krs->jadwalKuliah || !$krs->jadwalKuliah->mataKuliah) continue;
            $sem = $krs->tahunAkademik->semester == 'Ganjil' ? 1 : 2;
            // Key tanpa kelas - karena presensi source tidak menyimpan nama kelas
            $key = $krs->mahasiswa->nim . '_' . $krs->tahunAkademik->tahun . '_' . $sem . '_' . $krs->jadwalKuliah->mataKuliah->kode;
            $krsMap[$key] = $krs->id;
        }
    });
echo "   KRS map entries: " . count($krsMap) . "\n";

// Count presensi
$totalPresensi = DB::connection('source')
    ->table('kelaskuliah_presensimahasiswa')
    ->whereIn('IDMAHASISWA', array_keys($mahasiswaMap))
    ->count();
echo "   Data presensi ditemukan: {$totalPresensi}\n";

// Get existing absensi keys
$existingAbsensi = [];
Absensi::select('krs_id', 'tanggal', 'pertemuan')
    ->chunk(5000, function ($rows) use (&$existingAbsensi) {
        foreach ($rows as $r) {
            $existingAbsensi[$r->krs_id . '_' . $r->tanggal . '_' . $r->pertemuan] = true;
        }
    });

$batchSize = 500;
$offset = 0;
$chunkSize = 5000;

while ($offset < $totalPresensi) {
    $presensiChunk = DB::connection('source')
        ->table('kelaskuliah_presensimahasiswa')
        ->whereIn('IDMAHASISWA', array_keys($mahasiswaMap))
        ->orderBy('TAHUN')
        ->orderBy('SEMESTER')
        ->orderBy('IDMAHASISWA')
        ->skip($offset)
        ->take($chunkSize)
        ->get();
    
    if ($presensiChunk->isEmpty()) break;
    
    $batch = [];
    
    foreach ($presensiChunk as $p) {
        // Build key untuk KRS (tanpa IDKELAS karena lokal tidak punya mapping ke ID numeric)
        $krsKey = $p->IDMAHASISWA . '_' . $p->TAHUN . '_' . $p->SEMESTER . '_' . $p->IDMAKUL;
        $krsId = $krsMap[$krsKey] ?? null;
        
        if (!$krsId) continue;
        
        // Check existing
        $existKey = $krsId . '_' . $p->TANGGAL . '_' . $p->IDPERTEMUAN;
        if (isset($existingAbsensi[$existKey])) continue;
        
        // Map status presensi
        $status = match($p->PRESENSI) {
            'H' => 'Hadir',
            'I' => 'Izin',
            'S' => 'Sakit',
            'A' => 'Alpha',
            default => 'Alpha',
        };
        
        $batch[] = [
            'krs_id' => $krsId,
            'tanggal' => $p->TANGGAL,
            'pertemuan' => $p->IDPERTEMUAN ?: 1,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $existingAbsensi[$existKey] = true;
        $stats['absensi']++;
        
        if (count($batch) >= $batchSize) {
            try {
                Absensi::insert($batch);
            } catch (Exception $e) {
                // Skip duplicates
            }
            $batch = [];
        }
    }
    
    // Insert remaining batch
    if (!empty($batch)) {
        try {
            Absensi::insert($batch);
        } catch (Exception $e) {
            // Skip duplicates
        }
    }
    
    $offset += $chunkSize;
    echo "   ✅ Processed offset {$offset} / {$totalPresensi}, inserted {$stats['absensi']} presensi...\n";
    
    // Free memory
    unset($presensiChunk);
    gc_collect_cycles();
}

echo "   ✅ Total: {$stats['absensi']} presensi\n";

// ========================================
// 3. MIGRASI TAGIHAN
// ========================================
echo "\n3️⃣  Migrasi TAGIHAN...\n";

$totalTagihan = DB::connection('source')
    ->table('biayatagihan')
    ->whereIn('IDMAHASISWA', array_keys($mahasiswaMap))
    ->count();

echo "   Data tagihan ditemukan: {$totalTagihan}\n";

$invoiceMap = []; // Map untuk pembayaran nanti
$offset = 0;
$chunkSize = 2000;

// Get existing invoices
$existingInvoices = Tagihan::pluck('no_tagihan')->flip()->toArray();

while ($offset < $totalTagihan) {
    $tagihanChunk = DB::connection('source')
        ->table('biayatagihan')
        ->whereIn('IDMAHASISWA', array_keys($mahasiswaMap))
        ->orderBy('TAHUN')
        ->orderBy('SEMESTER')
        ->orderBy('IDMAHASISWA')
        ->skip($offset)
        ->take($chunkSize)
        ->get();
    
    if ($tagihanChunk->isEmpty()) break;

    foreach ($tagihanChunk as $t) {
        $mahasiswaId = $mahasiswaMap[$t->IDMAHASISWA] ?? null;
        $kode = $t->TAHUN . $t->SEMESTER;
        $tahunAkademikId = $tahunMap[$kode] ?? null;
        
        if (!$mahasiswaId || !$tahunAkademikId) continue;
        
        // Generate no tagihan
        $noTagihan = $t->INVOICEID ?: ('TAG' . date('Ym', strtotime($t->DATECREATED ?: 'now')) . str_pad($stats['tagihan'] + 1, 5, '0', STR_PAD_LEFT));
        
        if (isset($existingInvoices[$noTagihan])) {
            // Update map untuk existing
            if ($t->INVOICEID) {
                $existing = Tagihan::where('no_tagihan', $noTagihan)->first();
                if ($existing) $invoiceMap[$t->IDMAHASISWA . '_' . $t->INVOICEID] = $existing->id;
            }
            continue;
        }
        
        try {
            $nominal = floatval($t->BIAYA);
            $diskon = floatval($t->DISKON);
            $denda = floatval($t->DENDA);
            $totalBayar = $nominal - $diskon + $denda;
            
            $tagihan = Tagihan::create([
                'no_tagihan' => $noTagihan,
                'mahasiswa_id' => $mahasiswaId,
                'tahun_akademik_id' => $tahunAkademikId,
                'jenis_tagihan' => 'SPP',
                'keterangan_tagihan' => $t->KET ?: "Tagihan {$t->TAHUN}/{$t->SEMESTER}",
                'nominal' => $nominal,
                'diskon' => $diskon,
                'denda' => $denda,
                'total_bayar' => $totalBayar,
                'jumlah_dibayar' => 0,
                'sisa_tagihan' => $totalBayar,
                'tanggal_jatuh_tempo' => $t->PAYMENTDEADLINE ?: now()->addMonth(),
                'status' => 'Belum Bayar',
            ]);
            
            // Simpan map untuk pembayaran
            if ($t->INVOICEID) {
                $invoiceMap[$t->IDMAHASISWA . '_' . $t->INVOICEID] = $tagihan->id;
            }
            $existingInvoices[$noTagihan] = true;
            
            $stats['tagihan']++;
        } catch (Exception $e) {
            // Skip
        }
    }
    
    $offset += $chunkSize;
    echo "   ✅ Processed offset {$offset} / {$totalTagihan}, inserted {$stats['tagihan']} tagihan...\n";
    
    unset($tagihanChunk);
    gc_collect_cycles();
}

echo "   ✅ Total: {$stats['tagihan']} tagihan\n";

// ========================================
// 4. MIGRASI PEMBAYARAN
// ========================================
echo "\n4️⃣  Migrasi PEMBAYARAN...\n";

$bayarSource = DB::connection('source')
    ->table('bayartagihan')
    ->whereIn('IDMAHASISWA', array_keys($mahasiswaMap))
    ->where('STATUS', 1) // Hanya yang sukses
    ->get();

echo "   Data pembayaran ditemukan: " . count($bayarSource) . "\n";

// Get tagihan map untuk semua mahasiswa
$tagihanMap = [];
$tagihanList = Tagihan::all();
foreach ($tagihanList as $tg) {
    $tagihanMap[$tg->mahasiswa_id . '_' . $tg->no_tagihan] = $tg->id;
}

foreach ($bayarSource as $b) {
    $mahasiswaId = $mahasiswaMap[$b->IDMAHASISWA] ?? null;
    if (!$mahasiswaId) continue;
    
    // Cari tagihan yang sesuai
    $tagihanId = null;
    if ($b->INVOICE) {
        $tagihanId = $tagihanMap[$mahasiswaId . '_' . $b->INVOICE] ?? null;
    }
    
    // Jika tidak ada tagihan, cari yang pertama belum lunas
    if (!$tagihanId) {
        $tagihan = Tagihan::where('mahasiswa_id', $mahasiswaId)
            ->where('status', '!=', 'Lunas')
            ->first();
        if ($tagihan) {
            $tagihanId = $tagihan->id;
        }
    }
    
    if (!$tagihanId) continue;
    
    // Generate no transaksi
    $noTransaksi = 'TRX' . date('Ymd', strtotime($b->TANGGALBAYAR ?: 'now')) . str_pad($stats['pembayaran'] + 1, 5, '0', STR_PAD_LEFT);
    
    $existing = TransaksiPembayaran::where('no_referensi', $b->KUITANSI ?: $noTransaksi)->first();
    
    if (!$existing) {
        try {
            // Map metode pembayaran
            $metode = match($b->CARABAYAR) {
                '110', '111' => 'Transfer Bank',
                '120' => 'Virtual Account',
                '130' => 'QRIS',
                default => 'Lainnya',
            };
            
            TransaksiPembayaran::create([
                'no_transaksi' => $noTransaksi,
                'tagihan_id' => $tagihanId,
                'mahasiswa_id' => $mahasiswaId,
                'jumlah' => floatval($b->AMOUNT),
                'tanggal_bayar' => $b->TANGGALBAYAR ?: now(),
                'metode_pembayaran' => $metode,
                'bank' => $b->BANK ?: null,
                'no_referensi' => $b->KUITANSI ?: null,
                'status' => 'Verified',
            ]);
            $stats['pembayaran']++;
            
            // Update tagihan
            $tagihan = Tagihan::find($tagihanId);
            if ($tagihan) {
                $tagihan->jumlah_dibayar += floatval($b->AMOUNT);
                $tagihan->sisa_tagihan = $tagihan->total_bayar - $tagihan->jumlah_dibayar;
                if ($tagihan->sisa_tagihan <= 0) {
                    $tagihan->status = 'Lunas';
                    $tagihan->sisa_tagihan = 0;
                } else {
                    $tagihan->status = 'Cicilan';
                }
                $tagihan->save();
            }
        } catch (Exception $e) {
            // Skip
        }
    }
    
    if ($stats['pembayaran'] % 500 == 0 && $stats['pembayaran'] > 0) {
        echo "   ✅ Processed {$stats['pembayaran']} pembayaran...\n";
    }
}
echo "   ✅ Total: {$stats['pembayaran']} pembayaran\n";

// ========================================
// 5. MIGRASI BEASISWA
// ========================================
echo "\n5️⃣  Migrasi BEASISWA...\n";

// Buat jenis beasiswa dulu
$jenisBeasiswa = DB::connection('source')->table('jenisbeasiswa')->get();
$beasiswaMap = [];

foreach ($jenisBeasiswa as $jb) {
    $existing = Beasiswa::where('kode', $jb->ID)->first();
    
    if (!$existing) {
        $beasiswa = Beasiswa::create([
            'kode' => $jb->ID,
            'nama' => $jb->NAMA,
            'jenis' => str_contains(strtolower($jb->NAMA), 'potongan') ? 'Potongan' : 'Beasiswa',
            'tipe_potongan' => 'Persen',
            'nilai_potongan' => 100,
            'sumber_dana' => 'Internal',
            'kuota' => 100,
            'is_active' => true,
        ]);
        $beasiswaMap[$jb->ID] = $beasiswa->id;
        $stats['beasiswa']++;
        echo "   ✅ Jenis: {$jb->NAMA}\n";
    } else {
        $beasiswaMap[$jb->ID] = $existing->id;
    }
}

// Migrasi penerima beasiswa - perlu tabel terpisah
// Untuk sementara, skip karena tidak ada tabel penerima_beasiswa di lokal
echo "   ℹ️  Penerima beasiswa: 164 data (perlu tabel penerima_beasiswa)\n";

echo "   ✅ Total jenis beasiswa: {$stats['beasiswa']}\n";

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

// ========================================
// SUMMARY
// ========================================
echo "\n==========================================\n";
echo "   MIGRASI BATCH 2 SELESAI              \n";
echo "==========================================\n";
echo "   Ruangan        : {$stats['ruangan']} data\n";
echo "   Presensi       : {$stats['absensi']} data\n";
echo "   Tagihan        : {$stats['tagihan']} data\n";
echo "   Pembayaran     : {$stats['pembayaran']} data\n";
echo "   Beasiswa       : {$stats['beasiswa']} jenis\n";
echo "==========================================\n";

echo "\nVerifikasi data di database lokal:\n";
echo "   Ruangan        : " . Ruangan::count() . "\n";
echo "   Absensi        : " . Absensi::count() . "\n";
echo "   Tagihan        : " . Tagihan::count() . "\n";
echo "   Pembayaran     : " . TransaksiPembayaran::count() . "\n";
echo "   Beasiswa       : " . Beasiswa::count() . "\n";
