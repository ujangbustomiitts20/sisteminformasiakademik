<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;

class WilayahSeeder extends Seeder
{
    /**
     * Base URL untuk API wilayah.id
     */
    protected $baseUrl = 'https://wilayah.id/api';

    /**
     * Run the database seeds.
     * Data diambil dari https://wilayah.id/
     */
    public function run(): void
    {
        $this->command->info('===========================================');
        $this->command->info('Seeding Data Wilayah Indonesia dari wilayah.id');
        $this->command->info('===========================================');

        // Disable foreign key checks untuk mempercepat proses
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        }

        // Truncate tables (use delete for SQLite compatibility)
        $this->command->info('Menghapus data lama...');
        DB::table('kelurahan')->delete();
        DB::table('kecamatan')->delete();
        DB::table('kabupaten')->delete();
        DB::table('provinsi')->delete();

        // Seed provinsi
        $this->seedProvinsi();

        // Re-enable foreign key checks
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        $this->command->info('');
        $this->command->info('===========================================');
        $this->command->info('Seeding Data Wilayah Selesai!');
        $this->command->info('===========================================');
        
        // Tampilkan statistik
        $this->command->info('Statistik Data:');
        $this->command->info('- Provinsi  : ' . Provinsi::count());
        $this->command->info('- Kabupaten : ' . Kabupaten::count());
        $this->command->info('- Kecamatan : ' . Kecamatan::count());
        $this->command->info('- Kelurahan : ' . Kelurahan::count());
    }

    /**
     * Seed data provinsi dan turunannya
     */
    protected function seedProvinsi(): void
    {
        $this->command->info('');
        $this->command->info('Mengambil data provinsi...');

        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/provinces.json");
            
            if (!$response->successful()) {
                $this->command->error('Gagal mengambil data provinsi dari API');
                return;
            }

            $provinces = $response->json()['data'] ?? [];
            $totalProvinsi = count($provinces);
            
            $this->command->info("Ditemukan {$totalProvinsi} provinsi");

            $bar = $this->command->getOutput()->createProgressBar($totalProvinsi);
            $bar->start();

            foreach ($provinces as $province) {
                // Insert provinsi
                $provinsi = Provinsi::create([
                    'kode' => $province['code'],
                    'nama' => $province['name'],
                ]);

                // Seed kabupaten untuk provinsi ini
                $this->seedKabupaten($provinsi, $province['code']);

                $bar->advance();
            }

            $bar->finish();
            $this->command->info('');

        } catch (\Exception $e) {
            $this->command->error('Error: ' . $e->getMessage());
        }
    }

    /**
     * Seed data kabupaten untuk provinsi tertentu
     */
    protected function seedKabupaten(Provinsi $provinsi, string $kodeProvinsi): void
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/regencies/{$kodeProvinsi}.json");
            
            if (!$response->successful()) {
                return;
            }

            $regencies = $response->json()['data'] ?? [];

            foreach ($regencies as $regency) {
                $kabupaten = Kabupaten::create([
                    'provinsi_id' => $provinsi->id,
                    'kode' => $regency['code'],
                    'nama' => $regency['name'],
                ]);

                // Seed kecamatan untuk kabupaten ini
                $this->seedKecamatan($kabupaten, $regency['code']);
            }

        } catch (\Exception $e) {
            // Silent fail, continue to next
        }
    }

    /**
     * Seed data kecamatan untuk kabupaten tertentu
     */
    protected function seedKecamatan(Kabupaten $kabupaten, string $kodeKabupaten): void
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/districts/{$kodeKabupaten}.json");
            
            if (!$response->successful()) {
                return;
            }

            $districts = $response->json()['data'] ?? [];

            foreach ($districts as $district) {
                $kecamatan = Kecamatan::create([
                    'kabupaten_id' => $kabupaten->id,
                    'kode' => $district['code'],
                    'nama' => $district['name'],
                ]);

                // Seed kelurahan untuk kecamatan ini
                $this->seedKelurahan($kecamatan, $district['code']);
            }

        } catch (\Exception $e) {
            // Silent fail, continue to next
        }
    }

    /**
     * Seed data kelurahan untuk kecamatan tertentu
     */
    protected function seedKelurahan(Kecamatan $kecamatan, string $kodeKecamatan): void
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/villages/{$kodeKecamatan}.json");
            
            if (!$response->successful()) {
                return;
            }

            $villages = $response->json()['data'] ?? [];

            $kelurahanData = [];
            foreach ($villages as $village) {
                $kelurahanData[] = [
                    'kecamatan_id' => $kecamatan->id,
                    'kode' => $village['code'],
                    'nama' => $village['name'],
                    'kode_pos' => null, // API tidak menyediakan kode pos
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert untuk performa lebih baik
            if (!empty($kelurahanData)) {
                Kelurahan::insert($kelurahanData);
            }

        } catch (\Exception $e) {
            // Silent fail, continue to next
        }
    }
}
