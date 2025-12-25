<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mahasiswaCount = 50; // Number of dummy students to create

        for ($i = 0; $i < $mahasiswaCount; $i++) {
            Mahasiswa::create([
                'nama' => 'Mahasiswa ' . ($i + 1),
                'nim' => 'NIM' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'email' => 'mahasiswa' . ($i + 1) . '@example.com',
                'tanggal_lahir' => now()->subYears(rand(18, 25))->format('Y-m-d'),
                'alamat' => 'Alamat ' . ($i + 1),
                'telepon' => '08123456789' . $i,
                'jurusan' => 'Jurusan ' . rand(1, 5),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info("Created {$mahasiswaCount} new Mahasiswa");
    }
}