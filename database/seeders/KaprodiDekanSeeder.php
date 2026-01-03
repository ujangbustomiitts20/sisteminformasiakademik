<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KaprodiDekanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create fakultas
        $fakultas = Fakultas::first();
        if (!$fakultas) {
            $fakultas = Fakultas::create([
                'kode' => 'FTI',
                'nama' => 'Fakultas Teknologi Informasi',
                'dekan' => 'Dr. Ahmad Dekan, M.T.',
            ]);
        }

        // Get or create program studi
        $programStudi = ProgramStudi::first();
        if (!$programStudi) {
            $programStudi = ProgramStudi::create([
                'fakultas_id' => $fakultas->id,
                'kode' => 'TI',
                'nama' => 'Teknik Informatika',
                'jenjang' => 'S1',
                'kaprodi' => 'Dr. Budi Kaprodi, M.Kom.',
                'total_sks' => 144,
            ]);
        }

        // Create Kaprodi User & Dosen
        $kaprodiUser = User::where('email', 'kaprodi@siakad.ac.id')->first();
        if (!$kaprodiUser) {
            $kaprodiUser = User::create([
                'name' => 'Ketua Program Studi',
                'email' => 'kaprodi@siakad.ac.id',
                'password' => Hash::make('password'),
                'role' => 'kaprodi',
            ]);

            Dosen::create([
                'user_id' => $kaprodiUser->id,
                'program_studi_id' => $programStudi->id,
                'nidn' => '0012345678',
                'nama' => 'Dr. Budi Kaprodi, M.Kom.',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1975-05-10',
                'alamat' => 'Jl. Pendidikan No. 10',
                'telepon' => '081234567890',
                'email' => 'kaprodi@siakad.ac.id',
                'jabatan_fungsional' => 'Lektor Kepala',
                'golongan' => 'IV/a',
                'status' => 'Aktif',
            ]);

            $this->command->info('Kaprodi user created: kaprodi@siakad.ac.id / password');
        } else {
            // Update role if user exists
            $kaprodiUser->update(['role' => 'kaprodi']);
            $this->command->info('Kaprodi user already exists, role updated.');
        }

        // Create Dekan User & Dosen
        $dekanUser = User::where('email', 'dekan@siakad.ac.id')->first();
        if (!$dekanUser) {
            $dekanUser = User::create([
                'name' => 'Dekan Fakultas',
                'email' => 'dekan@siakad.ac.id',
                'password' => Hash::make('password'),
                'role' => 'dekan',
            ]);

            Dosen::create([
                'user_id' => $dekanUser->id,
                'program_studi_id' => $programStudi->id,
                'nidn' => '0087654321',
                'nama' => 'Dr. Ahmad Dekan, M.T.',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1970-08-15',
                'alamat' => 'Jl. Akademik No. 1',
                'telepon' => '081234567891',
                'email' => 'dekan@siakad.ac.id',
                'jabatan_fungsional' => 'Guru Besar',
                'golongan' => 'IV/c',
                'status' => 'Aktif',
            ]);

            $this->command->info('Dekan user created: dekan@siakad.ac.id / password');
        } else {
            // Update role if user exists
            $dekanUser->update(['role' => 'dekan']);
            $this->command->info('Dekan user already exists, role updated.');
        }

        // Update program studi kaprodi field
        $programStudi->update(['kaprodi' => 'Dr. Budi Kaprodi, M.Kom.']);

        // Update fakultas dekan field
        $fakultas->update(['dekan' => 'Dr. Ahmad Dekan, M.T.']);

        $this->command->info('');
        $this->command->info('======================================');
        $this->command->info('KAPRODI & DEKAN USERS CREATED');
        $this->command->info('======================================');
        $this->command->info('Kaprodi: kaprodi@siakad.ac.id / password');
        $this->command->info('Dekan  : dekan@siakad.ac.id / password');
        $this->command->info('======================================');
    }
}
