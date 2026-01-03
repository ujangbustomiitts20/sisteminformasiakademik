<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;

class WilayahSeederFast extends Seeder
{
    /**
     * Base URL untuk API wilayah.id
     */
    protected $baseUrl = 'https://wilayah.id/api';

    /**
     * Run the database seeds dengan batch insert untuk performa lebih baik.
     * Data diambil dari https://wilayah.id/
     * 
     * CATATAN: Proses ini memakan waktu karena ada ~83.000+ kelurahan di Indonesia
     */
    public function run(): void
    {
        $this->command->info('===========================================');
        $this->command->info('Seeding Data Wilayah Indonesia (Fast Mode)');
        $this->command->info('Source: https://wilayah.id/');
        $this->command->info('===========================================');
        $this->command->warn('CATATAN: Proses ini memakan waktu ~15-30 menit');
        $this->command->warn('karena mengambil data dari API external.');
        $this->command->info('');

        $startTime = microtime(true);

        // Disable foreign key checks (support MySQL dan SQLite)
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        }

        // Truncate tables
        $this->command->info('🗑️  Menghapus data lama...');
        DB::table('kelurahan')->delete();
        DB::table('kecamatan')->delete();
        DB::table('kabupaten')->delete();
        DB::table('provinsi')->delete();

        // Step 1: Seed Provinsi
        $provinsiMap = $this->seedProvinsiAll();

        // Step 2: Seed Kabupaten
        $kabupatenMap = $this->seedKabupatenAll($provinsiMap);

        // Step 3: Seed Kecamatan
        $kecamatanMap = $this->seedKecamatanAll($kabupatenMap);

        // Step 4: Seed Kelurahan
        $this->seedKelurahanAll($kecamatanMap);

        // Re-enable foreign key checks
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);

        $this->command->info('');
        $this->command->info('===========================================');
        $this->command->info('✅ Seeding Data Wilayah Selesai!');
        $this->command->info("⏱️  Waktu: {$duration} detik");
        $this->command->info('===========================================');
        
        // Tampilkan statistik
        $this->command->info('📊 Statistik Data:');
        $this->command->info('   - Provinsi  : ' . DB::table('provinsi')->count());
        $this->command->info('   - Kabupaten : ' . DB::table('kabupaten')->count());
        $this->command->info('   - Kecamatan : ' . DB::table('kecamatan')->count());
        $this->command->info('   - Kelurahan : ' . DB::table('kelurahan')->count());
    }

    /**
     * Seed semua provinsi dan return mapping kode -> id
     */
    protected function seedProvinsiAll(): array
    {
        $this->command->info('');
        $this->command->info('📍 [1/4] Mengambil data PROVINSI...');

        $provinsiMap = [];

        try {
            $response = Http::timeout(60)->get("{$this->baseUrl}/provinces.json");
            
            if (!$response->successful()) {
                $this->command->error('❌ Gagal mengambil data provinsi dari API');
                return $provinsiMap;
            }

            $provinces = $response->json()['data'] ?? [];
            $this->command->info("   Ditemukan " . count($provinces) . " provinsi");

            $bar = $this->command->getOutput()->createProgressBar(count($provinces));
            $bar->start();

            foreach ($provinces as $province) {
                $id = DB::table('provinsi')->insertGetId([
                    'kode' => $province['code'],
                    'nama' => $province['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $provinsiMap[$province['code']] = $id;
                $bar->advance();
            }

            $bar->finish();
            $this->command->info(' ✅');

        } catch (\Exception $e) {
            $this->command->error('Error: ' . $e->getMessage());
        }

        return $provinsiMap;
    }

    /**
     * Seed semua kabupaten dan return mapping kode -> id
     */
    protected function seedKabupatenAll(array $provinsiMap): array
    {
        $this->command->info('');
        $this->command->info('📍 [2/4] Mengambil data KABUPATEN/KOTA...');

        $kabupatenMap = [];
        $totalKabupaten = 0;

        $bar = $this->command->getOutput()->createProgressBar(count($provinsiMap));
        $bar->start();

        foreach ($provinsiMap as $kodeProvinsi => $provinsiId) {
            try {
                $response = Http::timeout(30)->get("{$this->baseUrl}/regencies/{$kodeProvinsi}.json");
                
                if ($response->successful()) {
                    $regencies = $response->json()['data'] ?? [];

                    foreach ($regencies as $regency) {
                        $id = DB::table('kabupaten')->insertGetId([
                            'provinsi_id' => $provinsiId,
                            'kode' => $regency['code'],
                            'nama' => $regency['name'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        $kabupatenMap[$regency['code']] = $id;
                        $totalKabupaten++;
                    }
                }
            } catch (\Exception $e) {
                // Continue to next province
            }

            $bar->advance();
            usleep(100000); // 100ms delay to prevent rate limiting
        }

        $bar->finish();
        $this->command->info(" ✅ ({$totalKabupaten} kabupaten)");

        return $kabupatenMap;
    }

    /**
     * Seed semua kecamatan dan return mapping kode -> id
     */
    protected function seedKecamatanAll(array $kabupatenMap): array
    {
        $this->command->info('');
        $this->command->info('📍 [3/4] Mengambil data KECAMATAN...');

        $kecamatanMap = [];
        $totalKecamatan = 0;

        $bar = $this->command->getOutput()->createProgressBar(count($kabupatenMap));
        $bar->start();

        foreach ($kabupatenMap as $kodeKabupaten => $kabupatenId) {
            try {
                $response = Http::timeout(30)->get("{$this->baseUrl}/districts/{$kodeKabupaten}.json");
                
                if ($response->successful()) {
                    $districts = $response->json()['data'] ?? [];

                    foreach ($districts as $district) {
                        $id = DB::table('kecamatan')->insertGetId([
                            'kabupaten_id' => $kabupatenId,
                            'kode' => $district['code'],
                            'nama' => $district['name'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        $kecamatanMap[$district['code']] = $id;
                        $totalKecamatan++;
                    }
                }
            } catch (\Exception $e) {
                // Continue to next
            }

            $bar->advance();
            usleep(50000); // 50ms delay
        }

        $bar->finish();
        $this->command->info(" ✅ ({$totalKecamatan} kecamatan)");

        return $kecamatanMap;
    }

    /**
     * Seed semua kelurahan dengan batch insert
     */
    protected function seedKelurahanAll(array $kecamatanMap): void
    {
        $this->command->info('');
        $this->command->info('📍 [4/4] Mengambil data KELURAHAN/DESA...');
        $this->command->warn('   (Tahap ini paling lama karena ada ~83.000+ kelurahan)');

        $totalKelurahan = 0;
        $batchData = [];
        $batchSize = 500;

        $bar = $this->command->getOutput()->createProgressBar(count($kecamatanMap));
        $bar->start();

        foreach ($kecamatanMap as $kodeKecamatan => $kecamatanId) {
            try {
                $response = Http::timeout(30)->get("{$this->baseUrl}/villages/{$kodeKecamatan}.json");
                
                if ($response->successful()) {
                    $villages = $response->json()['data'] ?? [];

                    foreach ($villages as $village) {
                        $batchData[] = [
                            'kecamatan_id' => $kecamatanId,
                            'kode' => $village['code'],
                            'nama' => $village['name'],
                            'kode_pos' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        $totalKelurahan++;

                        // Insert when batch is full
                        if (count($batchData) >= $batchSize) {
                            DB::table('kelurahan')->insert($batchData);
                            $batchData = [];
                        }
                    }
                }
            } catch (\Exception $e) {
                // Continue to next
            }

            $bar->advance();
            usleep(30000); // 30ms delay
        }

        // Insert remaining data
        if (!empty($batchData)) {
            DB::table('kelurahan')->insert($batchData);
        }

        $bar->finish();
        $this->command->info(" ✅ ({$totalKelurahan} kelurahan)");
    }
}
