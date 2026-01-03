<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Models\MataKuliah;
use App\Models\JadwalKuliah;
use App\Models\Krs;
use App\Models\KrsDetail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DashboardDummySeeder extends Seeder
{
    /**
     * Run the database seeds untuk dashboard kaprodi/dekan
     */
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            // Pastikan ada fakultas dan prodi
            $fakultas = Fakultas::firstOrCreate(
                ['kode' => 'FT'],
                [
                    'nama' => 'Fakultas Teknik',
                    'alamat' => 'Jl. Universitas No. 1',
                    'telepon' => '021-1234567',
                    'email' => 'ft@universitas.ac.id',
                ]
            );

            $prodi = ProgramStudi::firstOrCreate(
                ['kode' => 'TI'],
                [
                    'nama' => 'Teknik Informatika',
                    'fakultas_id' => $fakultas->id,
                    'jenjang' => 'S1',
                    'akreditasi' => 'A',
                    'kapasitas' => 120,
                ]
            );

            // Buat tahun akademik aktif
            $tahunAkademik = TahunAkademik::firstOrCreate(
                ['tahun' => '2024', 'semester' => 'Ganjil'],
                [
                    'tanggal_mulai' => '2024-09-01',
                    'tanggal_selesai' => '2025-01-31',
                    'is_aktif' => true,
                ]
            );

            // Buat beberapa dosen
            for ($i = 1; $i <= 3; $i++) {
                $userDosen = User::firstOrCreate(
                    ['email' => "dosen{$i}@universitas.ac.id"],
                    [
                        'name' => "Dosen {$i}",
                        'password' => Hash::make('password'),
                        'role' => 'dosen',
                        'email_verified_at' => now(),
                    ]
                );

                Dosen::firstOrCreate(
                    ['nidn' => "000300800{$i}"],
                    [
                        'user_id' => $userDosen->id,
                        'nama' => "Dosen {$i}, M.Kom",
                        'program_studi_id' => $prodi->id,
                        'jabatan_fungsional' => 'Lektor',
                        'email' => "dosen{$i}@universitas.ac.id",
                        'telepon' => "08123456789{$i}",
                        'status' => 'Aktif',
                    ]
                );
            }

            // Buat mata kuliah
            $mataKuliahData = [
                ['kode' => 'TI101', 'nama' => 'Algoritma dan Pemrograman', 'sks' => 3],
                ['kode' => 'TI102', 'nama' => 'Basis Data', 'sks' => 3],
                ['kode' => 'TI103', 'nama' => 'Struktur Data', 'sks' => 3],
            ];

            foreach ($mataKuliahData as $mk) {
                MataKuliah::firstOrCreate(
                    ['kode' => $mk['kode']],
                    [
                        'nama' => $mk['nama'],
                        'sks_teori' => $mk['sks'],
                        'sks_praktik' => 0,
                        'semester' => 1,
                        'program_studi_id' => $prodi->id,
                        'status' => 'Aktif',
                    ]
                );
            }

            // Buat beberapa mahasiswa
            $dosenWali = Dosen::first();
            for ($i = 1; $i <= 5; $i++) {
                $userMhs = User::firstOrCreate(
                    ['email' => "mahasiswa{$i}@universitas.ac.id"],
                    [
                        'name' => "Mahasiswa {$i}",
                        'password' => Hash::make('password'),
                        'role' => 'mahasiswa',
                        'email_verified_at' => now(),
                    ]
                );

                Mahasiswa::firstOrCreate(
                    ['nim' => "2024001{$i}"],
                    [
                        'user_id' => $userMhs->id,
                        'nama' => "Mahasiswa {$i}",
                        'program_studi_id' => $prodi->id,
                        'angkatan' => 2024,
                        'semester_aktif' => 1,
                        'status' => 'Aktif',
                        'email' => "mahasiswa{$i}@universitas.ac.id",
                        'dosen_wali_id' => $dosenWali?->id,
                    ]
                );
            }

            DB::commit();

            $this->command->info('Dashboard Dummy Data seeded successfully!');
            $this->command->info('Created: Fakultas, Prodi, Tahun Akademik, Dosen (3), Mahasiswa (5)');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
