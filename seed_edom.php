<?php
// Script untuk seed EDOM data

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PeriodeEdom;
use App\Models\PertanyaanEdom;
use App\Models\JadwalKuliah;
use App\Models\Krs;
use App\Models\JawabanEdom;
use App\Models\RekapEdom;
use App\Models\TahunAkademik;

$tahunAkademik = TahunAkademik::where('is_aktif', true)->first();
$periodeEdom = PeriodeEdom::first();
$pertanyaans = PertanyaanEdom::where('is_active', true)->get();

if (!$periodeEdom) {
    echo "No Periode EDOM found\n";
    exit;
}

echo "Using Periode EDOM: {$periodeEdom->nama}\n";
echo "Pertanyaan count: {$pertanyaans->count()}\n";

$jadwals = JadwalKuliah::where('tahun_akademik_id', $tahunAkademik->id)
    ->whereNotNull('dosen_id')
    ->take(10)
    ->get();

echo "Jadwal count: {$jadwals->count()}\n";

$totalJawaban = 0;
$totalRekap = 0;

foreach($jadwals as $jadwal) {
    $krsRecords = Krs::where('jadwal_kuliah_id', $jadwal->id)
        ->where('status', 'Disetujui')
        ->take(15)
        ->get();
    
    echo "Jadwal {$jadwal->id}: {$krsRecords->count()} KRS\n";
    
    $respondents = 0;
    foreach($krsRecords as $k) {
        foreach($pertanyaans as $p) {
            $jawaban = JawabanEdom::firstOrCreate([
                'periode_edom_id' => $periodeEdom->id,
                'mahasiswa_id' => $k->mahasiswa_id,
                'jadwal_kuliah_id' => $jadwal->id,
                'dosen_id' => $jadwal->dosen_id,
                'pertanyaan_edom_id' => $p->id,
            ], ['nilai' => rand(3, 5)]);
            
            if ($jawaban->wasRecentlyCreated) {
                $totalJawaban++;
            }
        }
        $respondents++;
    }
    
    // Create rekap
    $rekap = RekapEdom::firstOrCreate([
        'periode_edom_id' => $periodeEdom->id,
        'dosen_id' => $jadwal->dosen_id,
        'jadwal_kuliah_id' => $jadwal->id,
    ], [
        'rata_rata_pedagogik' => rand(35, 48) / 10,
        'rata_rata_profesional' => rand(35, 48) / 10,
        'rata_rata_kepribadian' => rand(35, 48) / 10,
        'rata_rata_sosial' => rand(35, 48) / 10,
        'rata_rata_total' => rand(35, 48) / 10,
        'jumlah_responden' => $respondents,
    ]);
    
    if ($rekap->wasRecentlyCreated) {
        $totalRekap++;
    }
}

echo "\n===================\n";
echo "Created {$totalJawaban} Jawaban EDOM\n";
echo "Created {$totalRekap} Rekap EDOM\n";
echo "Total Jawaban EDOM: " . JawabanEdom::count() . "\n";
echo "Total Rekap EDOM: " . RekapEdom::count() . "\n";
