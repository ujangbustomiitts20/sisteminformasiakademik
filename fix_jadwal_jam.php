<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\JadwalKuliah;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Fix jam yang formatnya salah
$updated = 0;
JadwalKuliah::chunk(500, function ($jadwals) use (&$updated) {
    foreach ($jadwals as $j) {
        $jamMulai = $j->jam_mulai;
        $jamSelesai = $j->jam_selesai;
        
        // Cek jika format datetime lengkap (lebih dari 8 karakter)
        if (strlen($jamMulai) > 8) {
            $newMulai = Carbon::parse($jamMulai)->format('H:i:s');
            $newSelesai = Carbon::parse($jamSelesai)->format('H:i:s');
            
            DB::table('jadwal_kuliah')
                ->where('id', $j->id)
                ->update([
                    'jam_mulai' => $newMulai,
                    'jam_selesai' => $newSelesai,
                ]);
            $updated++;
        }
    }
});

echo "Jadwal yang diperbaiki: {$updated}\n";

// Verifikasi
$sample = JadwalKuliah::first();
echo "Sample setelah fix: {$sample->jam_mulai} - {$sample->jam_selesai}\n";
