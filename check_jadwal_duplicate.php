<?php
/**
 * Script untuk memeriksa jadwal kuliah yang duplikat atau salah tahun akademik
 * Jalankan: php check_jadwal_duplicate.php
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\JadwalKuliah;
use App\Models\TahunAkademik;
use App\Models\Dosen;

echo "=== INVESTIGASI JADWAL KULIAH ===\n\n";

// 1. Tampilkan semua tahun akademik
echo "1. DAFTAR TAHUN AKADEMIK\n";
echo str_repeat('-', 80) . "\n";
$tahunAkademiks = TahunAkademik::orderBy('tahun', 'desc')
    ->orderByRaw("FIELD(semester, 'Ganjil', 'Genap', 'Pendek')")
    ->get();

foreach ($tahunAkademiks as $ta) {
    $jadwalCount = JadwalKuliah::where('tahun_akademik_id', $ta->id)->count();
    $status = $ta->is_aktif ? ' [AKTIF]' : '';
    printf("ID: %3d | %s %s%s | Jadwal: %d\n", 
        $ta->id, 
        $ta->tahun, 
        $ta->semester,
        $status,
        $jadwalCount
    );
}

echo "\n";

// 2. Tahun akademik aktif
$tahunAktif = TahunAkademik::getAktif();
echo "2. TAHUN AKADEMIK AKTIF\n";
echo str_repeat('-', 80) . "\n";
if ($tahunAktif) {
    printf("ID: %d | %s %s\n", $tahunAktif->id, $tahunAktif->tahun, $tahunAktif->semester);
    printf("Periode: %s s/d %s\n", 
        $tahunAktif->tanggal_mulai?->format('d/m/Y') ?? '-',
        $tahunAktif->tanggal_selesai?->format('d/m/Y') ?? '-'
    );
} else {
    echo "TIDAK ADA TAHUN AKADEMIK AKTIF!\n";
}

echo "\n";

// 3. Cari dosen dengan jadwal di banyak semester
echo "3. DOSEN DENGAN JADWAL DI SEMESTER AKTIF\n";
echo str_repeat('-', 80) . "\n";

if ($tahunAktif) {
    $dosensWithJadwal = JadwalKuliah::where('tahun_akademik_id', $tahunAktif->id)
        ->with(['dosen', 'mataKuliah', 'tahunAkademik'])
        ->get()
        ->groupBy('dosen_id');

    foreach ($dosensWithJadwal as $dosenId => $jadwals) {
        $dosen = $jadwals->first()->dosen;
        echo "\nDosen: " . ($dosen->nama ?? 'Unknown') . " (ID: {$dosenId})\n";
        echo "Jadwal di " . $tahunAktif->tahun . " " . $tahunAktif->semester . ":\n";
        
        foreach ($jadwals as $j) {
            printf("  - %s (%s) Kelas %s | %s %s-%s\n",
                $j->mataKuliah->nama ?? '-',
                $j->mataKuliah->kode ?? '-',
                $j->kelas,
                $j->hari,
                substr($j->jam_mulai, 0, 5),
                substr($j->jam_selesai, 0, 5)
            );
        }
    }
}

echo "\n";

// 4. Cek duplikat jadwal (matkul + kelas + tahun yang sama muncul 2x dengan tahun_akademik berbeda)
echo "4. KEMUNGKINAN JADWAL DUPLIKAT (mata kuliah + kelas sama di beberapa semester)\n";
echo str_repeat('-', 80) . "\n";

$potentialDuplicates = JadwalKuliah::with(['mataKuliah', 'tahunAkademik', 'dosen'])
    ->get()
    ->groupBy(function($j) {
        return $j->mata_kuliah_id . '_' . $j->kelas . '_' . $j->dosen_id;
    })
    ->filter(function($group) {
        return $group->count() > 1;
    });

foreach ($potentialDuplicates as $key => $jadwals) {
    $first = $jadwals->first();
    echo "\nMata Kuliah: " . ($first->mataKuliah->nama ?? '-') . " (" . ($first->mataKuliah->kode ?? '-') . ") Kelas " . $first->kelas . "\n";
    echo "Dosen: " . ($first->dosen->nama ?? '-') . "\n";
    echo "Muncul di:\n";
    
    foreach ($jadwals as $j) {
        printf("  - Tahun Akademik ID %d: %s %s | %s %s-%s\n",
            $j->tahun_akademik_id,
            $j->tahunAkademik->tahun ?? '-',
            $j->tahunAkademik->semester ?? '-',
            $j->hari,
            substr($j->jam_mulai, 0, 5),
            substr($j->jam_selesai, 0, 5)
        );
    }
}

if ($potentialDuplicates->isEmpty()) {
    echo "Tidak ada duplikat ditemukan.\n";
}

echo "\n";

// 5. Cari jadwal di semester aktif yang mata kuliahnya sudah ada di semester sebelumnya
echo "5. JADWAL DI SEMESTER AKTIF YANG MUNGKIN SALAH (harusnya semester lama)\n";
echo str_repeat('-', 80) . "\n";

if ($tahunAktif) {
    // Ambil tahun akademik sebelumnya
    $prevTahun = TahunAkademik::where('id', '<', $tahunAktif->id)
        ->orderBy('id', 'desc')
        ->first();
    
    if ($prevTahun) {
        echo "Membandingkan: {$tahunAktif->tahun} {$tahunAktif->semester} (aktif) vs {$prevTahun->tahun} {$prevTahun->semester} (sebelumnya)\n\n";
        
        $jadwalAktif = JadwalKuliah::where('tahun_akademik_id', $tahunAktif->id)
            ->with(['mataKuliah', 'dosen'])
            ->get();
        
        $jadwalPrev = JadwalKuliah::where('tahun_akademik_id', $prevTahun->id)
            ->with(['mataKuliah', 'dosen'])
            ->get();
        
        // Cari yang sama
        foreach ($jadwalAktif as $ja) {
            $match = $jadwalPrev->first(function($jp) use ($ja) {
                return $jp->mata_kuliah_id == $ja->mata_kuliah_id 
                    && $jp->kelas == $ja->kelas
                    && $jp->dosen_id == $ja->dosen_id;
            });
            
            if ($match) {
                printf("DUPLIKAT: %s (%s) Kelas %s - Dosen: %s\n",
                    $ja->mataKuliah->nama ?? '-',
                    $ja->mataKuliah->kode ?? '-',
                    $ja->kelas,
                    $ja->dosen->nama ?? '-'
                );
                printf("  -> Ada di %s %s (ID: %d) DAN %s %s (ID: %d)\n",
                    $prevTahun->tahun, $prevTahun->semester, $match->id,
                    $tahunAktif->tahun, $tahunAktif->semester, $ja->id
                );
            }
        }
    }
}

echo "\n=== SELESAI ===\n";
