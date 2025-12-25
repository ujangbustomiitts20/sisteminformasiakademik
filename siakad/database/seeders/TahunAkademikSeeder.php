<?php

namespace Database\Seeders;

use App\Models\TahunAkademik;
use Illuminate\Database\Seeder;

class TahunAkademikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahunAkademikData = [
            ['nama' => '2023/2024', 'tanggal_mulai' => '2023-08-01', 'tanggal_selesai' => '2024-07-31'],
            ['nama' => '2024/2025', 'tanggal_mulai' => '2024-08-01', 'tanggal_selesai' => '2025-07-31'],
            ['nama' => '2025/2026', 'tanggal_mulai' => '2025-08-01', 'tanggal_selesai' => '2026-07-31'],
            ['nama' => '2026/2027', 'tanggal_mulai' => '2026-08-01', 'tanggal_selesai' => '2027-07-31'],
            ['nama' => '2027/2028', 'tanggal_mulai' => '2027-08-01', 'tanggal_selesai' => '2028-07-31'],
        ];

        foreach ($tahunAkademikData as $data) {
            TahunAkademik::create($data);
        }
    }
}