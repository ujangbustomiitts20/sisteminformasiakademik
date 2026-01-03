<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Verifikasi data di database lokal:\n";
echo "   Fakultas    : " . DB::table('fakultas')->count() . "\n";
echo "   Prodi       : " . DB::table('program_studi')->count() . "\n";
echo "   Dosen       : " . DB::table('dosen')->count() . "\n";
echo "   Mahasiswa   : " . DB::table('mahasiswa')->count() . "\n";
echo "   Mata Kuliah : " . DB::table('mata_kuliah')->count() . "\n";
echo "   Users       : " . DB::table('users')->count() . "\n";
