<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KonfigurasiCetak;

class KonfigurasiCetakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = KonfigurasiCetak::getDefaults();

        foreach ($defaults as $config) {
            KonfigurasiCetak::updateOrCreate(
                ['kode' => $config['kode']],
                array_merge($config, ['is_active' => true])
            );
        }

        $this->command->info('Konfigurasi cetak berhasil di-seed!');
    }
}
