<?php

namespace Database\Seeders;

use App\Models\Tarif;
use Illuminate\Database\Seeder;

class TarifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tarifs = [
            ['nama' => 'SPP', 'nominal' => 5000000],
            ['nama' => 'Uang Gedung', 'nominal' => 3000000],
            ['nama' => 'Uang Praktikum', 'nominal' => 2000000],
            ['nama' => 'Uang Buku', 'nominal' => 1000000],
            ['nama' => 'Uang Kegiatan', 'nominal' => 1500000],
        ];

        foreach ($tarifs as $tarif) {
            Tarif::create($tarif);
        }

        $this->command->info('Tarif data has been seeded successfully!');
    }
}