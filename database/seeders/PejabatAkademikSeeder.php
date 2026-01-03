<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PejabatAkademikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            // Buat Fakultas jika belum ada
            $fakultas = Fakultas::firstOrCreate(
                ['kode' => 'FT'],
                [
                    'nama' => 'Fakultas Teknik',
                    'alamat' => 'Jl. Universitas No. 1',
                    'telepon' => '021-1234567',
                    'email' => 'ft@universitas.ac.id',
                ]
            );

            // Buat Program Studi jika belum ada
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

            // Buat User dan Dosen untuk Kaprodi
            $userKaprodi = User::firstOrCreate(
                ['email' => 'kaprodi@universitas.ac.id'],
                [
                    'name' => 'Dr. Ahmad Kaprodi, M.Kom',
                    'password' => Hash::make('password'),
                    'role' => 'kaprodi',
                    'email_verified_at' => now(),
                ]
            );

            // Update role jika sudah ada tapi bukan kaprodi
            if ($userKaprodi->role !== 'kaprodi') {
                $userKaprodi->update(['role' => 'kaprodi']);
            }

            $dosenKaprodi = Dosen::firstOrCreate(
                ['nidn' => '0001018001'],
                [
                    'user_id' => $userKaprodi->id,
                    'nama' => 'Dr. Ahmad Kaprodi, M.Kom',
                    'program_studi_id' => $prodi->id,
                    'jabatan_fungsional' => 'Lektor Kepala',
                    'email' => 'kaprodi@universitas.ac.id',
                    'telepon' => '081234567890',
                    'alamat' => 'Jl. Dosen No. 1',
                    'status' => 'Aktif',
                ]
            );

            // Update dosen dengan user_id jika belum
            if (!$dosenKaprodi->user_id) {
                $dosenKaprodi->update(['user_id' => $userKaprodi->id]);
            }

            // Buat User dan Dosen untuk Dekan
            $userDekan = User::firstOrCreate(
                ['email' => 'dekan@universitas.ac.id'],
                [
                    'name' => 'Prof. Dr. Budi Dekan, M.T',
                    'password' => Hash::make('password'),
                    'role' => 'dekan',
                    'email_verified_at' => now(),
                ]
            );

            // Update role jika sudah ada tapi bukan dekan
            if ($userDekan->role !== 'dekan') {
                $userDekan->update(['role' => 'dekan']);
            }

            $dosenDekan = Dosen::firstOrCreate(
                ['nidn' => '0002027502'],
                [
                    'user_id' => $userDekan->id,
                    'nama' => 'Prof. Dr. Budi Dekan, M.T',
                    'program_studi_id' => $prodi->id,
                    'jabatan_fungsional' => 'Guru Besar',
                    'email' => 'dekan@universitas.ac.id',
                    'telepon' => '081234567891',
                    'alamat' => 'Jl. Dekan No. 1',
                    'status' => 'Aktif',
                ]
            );

            // Update dosen dengan user_id jika belum
            if (!$dosenDekan->user_id) {
                $dosenDekan->update(['user_id' => $userDekan->id]);
            }

            DB::commit();

            $this->command->info('Pejabat Akademik seeded successfully!');
            $this->command->info('Kaprodi: kaprodi@universitas.ac.id / password');
            $this->command->info('Dekan: dekan@universitas.ac.id / password');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
