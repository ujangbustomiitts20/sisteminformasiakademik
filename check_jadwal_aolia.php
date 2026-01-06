<?php
/**
 * Script untuk memeriksa jadwal mengajar dosen Aolia Ikhwanudin
 * dan mata kuliah "Logika Digital dan Sistem Digital"
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\JadwalKuliah;
use App\Models\TahunAkademik;

echo "=== INVESTIGASI JADWAL MENGAJAR DOSEN AOLIA IKHWANUDIN ===\n\n";

// 1. Cari dosen dengan nama mengandung "aolia" atau "ikhwanudin"
echo "1. MENCARI DATA DOSEN\n";
echo str_repeat("-", 60) . "\n";

$dosen = Dosen::where('nama', 'like', '%aolia%')
    ->orWhere('nama', 'like', '%ikhwanudin%')
    ->first();

if (!$dosen) {
    // Coba cari dengan case insensitive
    $dosen = Dosen::whereRaw('LOWER(nama) LIKE ?', ['%aolia%'])
        ->orWhereRaw('LOWER(nama) LIKE ?', ['%ikhwanudin%'])
        ->first();
}

if ($dosen) {
    echo "Dosen ditemukan:\n";
    echo "  ID: {$dosen->id}\n";
    echo "  NIDN: {$dosen->nidn}\n";
    echo "  Nama: {$dosen->nama}\n";
    echo "  Status: {$dosen->status}\n";
    echo "  Program Studi ID: {$dosen->program_studi_id}\n";
    if ($dosen->programStudi) {
        echo "  Program Studi: {$dosen->programStudi->nama}\n";
    }
} else {
    echo "Dosen dengan nama 'Aolia Ikhwanudin' TIDAK DITEMUKAN!\n";
    
    // Tampilkan dosen yang mirip
    echo "\nDaftar dosen yang mungkin mirip:\n";
    $similar = Dosen::whereRaw('LOWER(nama) LIKE ?', ['%lia%'])
        ->orWhereRaw('LOWER(nama) LIKE ?', ['%din%'])
        ->limit(10)
        ->get(['id', 'nidn', 'nama', 'status']);
    
    foreach ($similar as $d) {
        echo "  - [{$d->id}] {$d->nidn} - {$d->nama} ({$d->status})\n";
    }
}

echo "\n";

// 2. Cari mata kuliah "Logika Digital dan Sistem Digital"
echo "2. MENCARI MATA KULIAH\n";
echo str_repeat("-", 60) . "\n";

$mataKuliah = MataKuliah::where('nama', 'like', '%Logika Digital%')
    ->orWhere('nama', 'like', '%Sistem Digital%')
    ->get();

if ($mataKuliah->count() > 0) {
    echo "Mata kuliah ditemukan:\n";
    foreach ($mataKuliah as $mk) {
        echo "  ID: {$mk->id}\n";
        echo "  Kode: {$mk->kode}\n";
        echo "  Nama: {$mk->nama}\n";
        echo "  SKS: {$mk->sks}\n";
        if ($mk->programStudi) {
            echo "  Program Studi: {$mk->programStudi->nama}\n";
        }
        echo "\n";
    }
} else {
    echo "Mata kuliah dengan nama 'Logika Digital' atau 'Sistem Digital' TIDAK DITEMUKAN!\n";
}

echo "\n";

// 3. Cek tahun akademik aktif
echo "3. TAHUN AKADEMIK AKTIF\n";
echo str_repeat("-", 60) . "\n";

$tahunAktif = TahunAkademik::getAktif();
if ($tahunAktif) {
    echo "Tahun Akademik Aktif: {$tahunAktif->tahun} {$tahunAktif->semester}\n";
    echo "ID: {$tahunAktif->id}\n";
    echo "Periode: {$tahunAktif->tanggal_mulai} - {$tahunAktif->tanggal_selesai}\n";
} else {
    echo "Tidak ada tahun akademik aktif!\n";
}

echo "\n";

// 4. Cek jadwal mengajar dosen tersebut
echo "4. JADWAL MENGAJAR DOSEN (SEMUA SEMESTER)\n";
echo str_repeat("-", 60) . "\n";

if ($dosen) {
    $jadwalSemua = JadwalKuliah::with(['mataKuliah', 'tahunAkademik', 'ruangan'])
        ->where('dosen_id', $dosen->id)
        ->orderBy('tahun_akademik_id', 'desc')
        ->get();
    
    if ($jadwalSemua->count() > 0) {
        echo "Total jadwal: {$jadwalSemua->count()}\n\n";
        
        foreach ($jadwalSemua as $j) {
            $ta = $j->tahunAkademik;
            $mk = $j->mataKuliah;
            $taInfo = $ta ? "{$ta->tahun} {$ta->semester}" : "N/A";
            $mkInfo = $mk ? "{$mk->kode} - {$mk->nama}" : "N/A";
            $isAktif = ($ta && $tahunAktif && $ta->id == $tahunAktif->id) ? " [AKTIF]" : "";
            
            echo "  - Tahun Akademik: {$taInfo}{$isAktif}\n";
            echo "    Mata Kuliah: {$mkInfo}\n";
            echo "    Kelas: {$j->kelas} | Hari: {$j->hari} | Jam: {$j->jam_mulai} - {$j->jam_selesai}\n";
            echo "\n";
        }
    } else {
        echo "Tidak ada jadwal mengajar untuk dosen ini!\n";
    }
}

echo "\n";

// 5. Cek jadwal mata kuliah Logika Digital di semester aktif
echo "5. JADWAL MATA KULIAH 'LOGIKA DIGITAL' DI SEMESTER AKTIF\n";
echo str_repeat("-", 60) . "\n";

if ($mataKuliah->count() > 0 && $tahunAktif) {
    foreach ($mataKuliah as $mk) {
        $jadwalMk = JadwalKuliah::with(['dosen', 'ruangan'])
            ->where('mata_kuliah_id', $mk->id)
            ->where('tahun_akademik_id', $tahunAktif->id)
            ->get();
        
        echo "Mata Kuliah: {$mk->nama} (ID: {$mk->id})\n";
        
        if ($jadwalMk->count() > 0) {
            foreach ($jadwalMk as $j) {
                $dosenInfo = $j->dosen ? $j->dosen->nama : "N/A";
                echo "  - Dosen: {$dosenInfo}\n";
                echo "    Kelas: {$j->kelas} | Hari: {$j->hari} | Jam: {$j->jam_mulai} - {$j->jam_selesai}\n";
            }
        } else {
            echo "  Tidak ada jadwal di semester aktif!\n";
        }
        echo "\n";
    }
}

echo "\n";

// 6. Cek di source database jika ada koneksi
echo "6. VERIFIKASI SUMBER DATA (LEGACY DB)\n";
echo str_repeat("-", 60) . "\n";

try {
    $source = @new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
    
    if ($source->connect_error) {
        echo "Tidak dapat terhubung ke database legacy: {$source->connect_error}\n";
    } else {
        echo "Terhubung ke database legacy.\n\n";
        
        // Cari dosen di source
        $dosenQuery = "SELECT * FROM dosen WHERE LOWER(DSNNAMA) LIKE '%aolia%' OR LOWER(DSNNAMA) LIKE '%ikhwanudin%'";
        $dosenResult = $source->query($dosenQuery);
        
        if ($dosenResult && $dosenResult->num_rows > 0) {
            echo "Dosen di DB Legacy:\n";
            while ($row = $dosenResult->fetch_assoc()) {
                echo "  DSNID: {$row['DSNID']}, NIDN: {$row['NIDN']}, Nama: {$row['DSNNAMA']}\n";
            }
        }
        
        // Cari mata kuliah
        echo "\nMata Kuliah 'Logika Digital' di DB Legacy:\n";
        $mkQuery = "SELECT * FROM makul WHERE LOWER(MAKULNAMA) LIKE '%logika digital%' OR LOWER(MAKULNAMA) LIKE '%sistem digital%'";
        $mkResult = $source->query($mkQuery);
        
        if ($mkResult && $mkResult->num_rows > 0) {
            while ($row = $mkResult->fetch_assoc()) {
                echo "  MAKULID: {$row['MAKULID']}, Kode: {$row['MAKULKODE']}, Nama: {$row['MAKULNAMA']}\n";
            }
        }
        
        $source->close();
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== SELESAI ===\n";
