<?php
/**
 * Investigasi Jadwal Mengajar Dosen Aolia Ikhwanudin
 * dan Mata Kuliah "Logika Digital dan Sistem Digital"
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== INVESTIGASI JADWAL MENGAJAR DOSEN AOLIA IKHWANUDIN ===\n";
echo "=== dan MK 'Logika Digital dan Sistem Digital' ===\n\n";

// 1. Cari di database LOKAL
echo "1. DATA DI DATABASE LOKAL\n";
echo str_repeat("=", 70) . "\n";

// Cari dosen
$dosenLokal = DB::table('dosen')
    ->whereRaw('LOWER(nama) LIKE ?', ['%aolia%'])
    ->orWhereRaw('LOWER(nama) LIKE ?', ['%ikhwanudin%'])
    ->first();

if ($dosenLokal) {
    echo "✓ Dosen ditemukan di lokal:\n";
    echo "  ID: {$dosenLokal->id}\n";
    echo "  NIDN: {$dosenLokal->nidn}\n";
    echo "  Nama: {$dosenLokal->nama}\n";
    echo "  Status: {$dosenLokal->status}\n\n";
} else {
    echo "✗ Dosen TIDAK ditemukan di lokal dengan nama 'Aolia' atau 'Ikhwanudin'\n\n";
}

// Cari mata kuliah
$mkLokal = DB::table('mata_kuliah')
    ->whereRaw('LOWER(nama) LIKE ?', ['%logika digital%'])
    ->orWhereRaw('LOWER(nama) LIKE ?', ['%sistem digital%'])
    ->get();

if ($mkLokal->count() > 0) {
    echo "✓ Mata Kuliah ditemukan di lokal:\n";
    foreach ($mkLokal as $mk) {
        echo "  ID: {$mk->id} | Kode: {$mk->kode} | Nama: {$mk->nama} | SKS: {$mk->sks}\n";
    }
    echo "\n";
} else {
    echo "✗ Mata Kuliah TIDAK ditemukan\n\n";
}

// Cek tahun akademik aktif
$tahunAktif = DB::table('tahun_akademik')->where('is_aktif', true)->first();
echo "Tahun Akademik Aktif: " . ($tahunAktif ? "{$tahunAktif->tahun} {$tahunAktif->semester} (ID: {$tahunAktif->id})" : "TIDAK ADA") . "\n\n";

// Jadwal mengajar di semester aktif
if ($dosenLokal && $tahunAktif) {
    echo "2. JADWAL MENGAJAR DOSEN DI SEMESTER AKTIF\n";
    echo str_repeat("=", 70) . "\n";
    
    $jadwalAktif = DB::table('jadwal_kuliah as j')
        ->join('mata_kuliah as mk', 'j.mata_kuliah_id', '=', 'mk.id')
        ->join('tahun_akademik as ta', 'j.tahun_akademik_id', '=', 'ta.id')
        ->where('j.dosen_id', $dosenLokal->id)
        ->where('j.tahun_akademik_id', $tahunAktif->id)
        ->select('j.*', 'mk.kode', 'mk.nama as mk_nama', 'ta.tahun', 'ta.semester')
        ->get();
    
    if ($jadwalAktif->count() > 0) {
        echo "Ditemukan " . $jadwalAktif->count() . " jadwal di semester aktif:\n";
        foreach ($jadwalAktif as $j) {
            echo "  - [{$j->kode}] {$j->mk_nama} | Kelas: {$j->kelas} | {$j->hari} {$j->jam_mulai}-{$j->jam_selesai}\n";
        }
    } else {
        echo "  Tidak ada jadwal di semester aktif\n";
    }
    echo "\n";
}

// Jadwal MK Logika Digital di semester aktif
if ($mkLokal->count() > 0 && $tahunAktif) {
    echo "3. JADWAL MK 'LOGIKA DIGITAL' DI SEMESTER AKTIF\n";
    echo str_repeat("=", 70) . "\n";
    
    foreach ($mkLokal as $mk) {
        $jadwalMk = DB::table('jadwal_kuliah as j')
            ->leftJoin('dosen as d', 'j.dosen_id', '=', 'd.id')
            ->where('j.mata_kuliah_id', $mk->id)
            ->where('j.tahun_akademik_id', $tahunAktif->id)
            ->select('j.*', 'd.nama as dosen_nama', 'd.nidn')
            ->get();
        
        echo "MK: {$mk->nama} (ID: {$mk->id})\n";
        if ($jadwalMk->count() > 0) {
            foreach ($jadwalMk as $j) {
                $isAolia = ($dosenLokal && $j->dosen_id == $dosenLokal->id) ? " <<<< DOSEN INI" : "";
                echo "  - Kelas {$j->kelas}: Dosen {$j->dosen_nama} (NIDN: {$j->nidn}) ID: {$j->dosen_id}{$isAolia}\n";
            }
        } else {
            echo "  Tidak ada jadwal di semester aktif\n";
        }
    }
    echo "\n";
}

// Cek di SOURCE DATABASE
echo "4. DATA DI DATABASE SOURCE (itts_sikad)\n";
echo str_repeat("=", 70) . "\n";

try {
    $source = @new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
    
    if ($source->connect_error) {
        echo "✗ Tidak dapat terhubung ke DB source: {$source->connect_error}\n";
    } else {
        echo "✓ Terhubung ke DB source\n\n";
        
        // Cari dosen di source
        $dosenResult = $source->query("SELECT * FROM dosen WHERE LOWER(NAMA) LIKE '%aolia%' OR LOWER(NAMA) LIKE '%ikhwanudin%'");
        
        $dosenSourceId = null;
        if ($dosenResult && $dosenResult->num_rows > 0) {
            echo "Dosen di source:\n";
            while ($row = $dosenResult->fetch_assoc()) {
                echo "  ID: {$row['ID']} | NIDN: {$row['NIDN']} | Nama: {$row['NAMA']}\n";
                $dosenSourceId = $row['ID'];
            }
        } else {
            echo "✗ Dosen tidak ditemukan di source\n";
        }
        echo "\n";
        
        // Cari MK di source
        $mkResult = $source->query("SELECT * FROM makul WHERE LOWER(NAMA) LIKE '%logika digital%' OR LOWER(NAMA) LIKE '%sistem digital%'");
        
        $mkSourceIds = [];
        if ($mkResult && $mkResult->num_rows > 0) {
            echo "Mata Kuliah di source:\n";
            while ($row = $mkResult->fetch_assoc()) {
                echo "  ID: {$row['ID']} | Kode: {$row['KODE']} | Nama: {$row['NAMA']}\n";
                $mkSourceIds[] = $row['ID'];
            }
        } else {
            echo "✗ Mata Kuliah tidak ditemukan di source\n";
        }
        echo "\n";
        
        // Cek kelaskuliah_pengajar untuk dosen ini mengajar MK apa
        if ($dosenSourceId) {
            echo "5. HISTORY MENGAJAR DI kelaskuliah_pengajar (source):\n";
            echo str_repeat("=", 70) . "\n";
            
            $pengajarResult = $source->query("
                SELECT kp.*, m.KODE as MK_KODE, m.NAMA as MK_NAMA
                FROM kelaskuliah_pengajar kp
                JOIN makul m ON kp.IDMAKUL = m.ID
                WHERE kp.IDPENGAJAR = '{$dosenSourceId}'
                ORDER BY kp.TAHUN DESC, kp.SEMESTER DESC
            ");
            
            if ($pengajarResult && $pengajarResult->num_rows > 0) {
                echo "History mengajar:\n";
                $lastTahun = '';
                while ($row = $pengajarResult->fetch_assoc()) {
                    $taKey = "{$row['TAHUN']} Sem-{$row['SEMESTER']}";
                    if ($taKey != $lastTahun) {
                        echo "\n  [{$taKey}]\n";
                        $lastTahun = $taKey;
                    }
                    $isLogika = (stripos($row['MK_NAMA'], 'logika') !== false || stripos($row['MK_NAMA'], 'digital') !== false) ? " <<<< MK INI" : "";
                    echo "    - {$row['MK_KODE']} {$row['MK_NAMA']} (Kelas: {$row['IDKELAS']}){$isLogika}\n";
                }
            } else {
                echo "Tidak ada history mengajar\n";
            }
            echo "\n";
        }
        
        // Cek MK Logika Digital diajar siapa
        if (!empty($mkSourceIds)) {
            echo "6. SIAPA YANG MENGAJAR MK 'LOGIKA DIGITAL' (source):\n";
            echo str_repeat("=", 70) . "\n";
            
            foreach ($mkSourceIds as $mkSrcId) {
                $pengajarMkResult = $source->query("
                    SELECT kp.*, d.NIDN, d.NAMA as DOSEN_NAMA
                    FROM kelaskuliah_pengajar kp
                    JOIN dosen d ON kp.IDPENGAJAR = d.ID
                    WHERE kp.IDMAKUL = '{$mkSrcId}'
                    ORDER BY kp.TAHUN DESC, kp.SEMESTER DESC
                ");
                
                if ($pengajarMkResult && $pengajarMkResult->num_rows > 0) {
                    while ($row = $pengajarMkResult->fetch_assoc()) {
                        $isAolia = ($row['IDPENGAJAR'] == $dosenSourceId) ? " <<<< AOLIA" : "";
                        echo "  {$row['TAHUN']}/{$row['SEMESTER']} Kelas {$row['IDKELAS']}: {$row['DOSEN_NAMA']} (NIDN: {$row['NIDN']}){$isAolia}\n";
                    }
                }
            }
        }
        
        $source->close();
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== KESIMPULAN ===\n";
echo str_repeat("=", 70) . "\n";
echo "Jalankan script ini untuk melihat:\n";
echo "1. Apakah dosen dan MK ada di lokal\n";
echo "2. Jadwal mengajar di semester aktif\n";
echo "3. Data history dari source database\n";
echo "4. Apakah ada mismatch mapping dosen\n\n";

echo "Jika dosen mengajar MK ini di source tapi tidak muncul di lokal,\n";
echo "kemungkinan perlu jalankan ulang: php fix_dosen_pengampu.php\n";
