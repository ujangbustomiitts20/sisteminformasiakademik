<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Dosen;
use App\Models\User;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use Illuminate\Support\Facades\Hash;

// Create Program Studi first if not exists
$fakultas = Fakultas::first();
if (!$fakultas) {
    $fakultas = Fakultas::create([
        'kode' => 'FT',
        'nama' => 'Fakultas Teknik',
        'status' => 'aktif'
    ]);
    echo "Created Fakultas: {$fakultas->nama}\n\n";
}

$prodi = ProgramStudi::first();
if (!$prodi) {
    $prodi = ProgramStudi::create([
        'fakultas_id' => $fakultas->id,
        'kode' => 'TI',
        'nama' => 'Teknik Informatika',
        'jenjang' => 'S1',
        'akreditasi' => 'A',
        'status' => 'aktif'
    ]);
    echo "Created Program Studi: {$prodi->nama}\n\n";
}

$dosenData = [
    ['nidn' => '0001018001', 'nama' => 'Dr. Ahmad Sudrajat, M.Kom.', 'jenis_kelamin' => 'L', 'jabatan_fungsional' => 'Lektor Kepala'],
    ['nidn' => '0002028002', 'nama' => 'Dr. Siti Nurhaliza, M.T.', 'jenis_kelamin' => 'P', 'jabatan_fungsional' => 'Lektor'],
    ['nidn' => '0003038003', 'nama' => 'Prof. Dr. Budi Santoso, M.Sc.', 'jenis_kelamin' => 'L', 'jabatan_fungsional' => 'Guru Besar'],
    ['nidn' => '0004048004', 'nama' => 'Dr. Dewi Kartika, S.T., M.T.', 'jenis_kelamin' => 'P', 'jabatan_fungsional' => 'Lektor'],
    ['nidn' => '0005058005', 'nama' => 'Ir. Hadi Wijaya, M.Eng.', 'jenis_kelamin' => 'L', 'jabatan_fungsional' => 'Asisten Ahli'],
    ['nidn' => '0006068006', 'nama' => 'Dr. Rina Marlina, M.Pd.', 'jenis_kelamin' => 'P', 'jabatan_fungsional' => 'Lektor Kepala'],
    ['nidn' => '0007078007', 'nama' => 'Drs. Agus Pratama, M.M.', 'jenis_kelamin' => 'L', 'jabatan_fungsional' => 'Lektor'],
    ['nidn' => '0008088008', 'nama' => 'Dr. Endang Sulistyowati, M.Si.', 'jenis_kelamin' => 'P', 'jabatan_fungsional' => 'Lektor'],
    ['nidn' => '0009098009', 'nama' => 'Dr. Fajar Hermawan, S.Kom., M.Kom.', 'jenis_kelamin' => 'L', 'jabatan_fungsional' => 'Asisten Ahli'],
    ['nidn' => '0010108010', 'nama' => 'Ir. Gita Anggraini, M.T.', 'jenis_kelamin' => 'P', 'jabatan_fungsional' => 'Lektor'],
];

echo "Menambahkan data Dosen...\n\n";

foreach ($dosenData as $data) {
    // Generate email
    $email = strtolower(str_replace([' ', '.', ','], '', explode(',', $data['nama'])[0])) . '@kampus.ac.id';
    
    // Check if dosen already exists
    $existingDosen = Dosen::where('nidn', $data['nidn'])->first();
    if ($existingDosen) {
        echo "  ⏭️  {$data['nama']} (sudah ada)\n";
        continue;
    }
    
    // Create user for dosen
    $user = User::firstOrCreate(
        ['email' => $email],
        [
            'name' => $data['nama'],
            'password' => Hash::make('password'),
            'role' => 'dosen',
        ]
    );
    
    $dosen = Dosen::create([
        'user_id' => $user->id,
        'program_studi_id' => $prodi->id,
        'nidn' => $data['nidn'],
        'nama' => $data['nama'],
        'jenis_kelamin' => $data['jenis_kelamin'],
        'jabatan_fungsional' => $data['jabatan_fungsional'],
        'status' => 'Aktif',
        'email' => $email,
        'no_hp' => '08' . rand(1000000000, 9999999999),
        'alamat' => 'Jl. Contoh No. ' . rand(1, 100),
    ]);
    
    echo "  ✅ {$dosen->nama}\n";
}

echo "\n===========================================\n";
echo "Total Dosen sekarang: " . Dosen::count() . "\n";
echo "Dosen Aktif: " . Dosen::where('status', 'Aktif')->count() . "\n";
echo "===========================================\n";
