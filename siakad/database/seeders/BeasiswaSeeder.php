<?php

namespace Database\Seeders;

use App\Models\Beasiswa;
use Illuminate\Database\Seeder;

class BeasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $beasiswaData = [
            [
                'nama' => 'Beasiswa Prestasi',
                'jumlah' => 2000000,
                'kriteria' => 'IPK minimal 3.5',
            ],
            [
                'nama' => 'Beasiswa Kurang Mampu',
                'jumlah' => 1500000,
                'kriteria' => 'Bukti penghasilan orang tua',
            ],
            [
                'nama' => 'Beasiswa Olahraga',
                'jumlah' => 2500000,
                'kriteria' => 'Juara lomba tingkat nasional',
            ],
            [
                'nama' => 'Beasiswa Seni',
                'jumlah' => 1800000,
                'kriteria' => 'Karya seni yang diakui',
            ],
            [
                'nama' => 'Beasiswa Akademik',
                'jumlah' => 3000000,
                'kriteria' => 'Nilai ujian nasional di atas 90',
            ],
        ];

        foreach ($beasiswaData as $data) {
            Beasiswa::create($data);
        }

        $this->command->info('Beasiswa dummy data created successfully!');
    }
}