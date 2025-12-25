<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Ruangan;
use App\Models\MataKuliah;
use App\Models\TahunAkademik;
use App\Models\JadwalKuliah;
use App\Models\Pengumuman;
use Illuminate\Support\Facades\Hash;

class SiakadSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@siakad.ac.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Fakultas
        $fti = Fakultas::create(['kode' => 'FTI', 'nama' => 'Fakultas Teknologi Informasi', 'dekan' => 'Prof. Dr. Ahmad Dahlan']);
        $feb = Fakultas::create(['kode' => 'FEB', 'nama' => 'Fakultas Ekonomi dan Bisnis', 'dekan' => 'Prof. Dr. Siti Ekonomi']);
        
        // Program Studi
        $ti = ProgramStudi::create(['fakultas_id' => $fti->id, 'kode' => 'TI', 'nama' => 'Teknik Informatika', 'jenjang' => 'S1', 'total_sks' => 144]);
        $si = ProgramStudi::create(['fakultas_id' => $fti->id, 'kode' => 'SI', 'nama' => 'Sistem Informasi', 'jenjang' => 'S1', 'total_sks' => 144]);
        $ak = ProgramStudi::create(['fakultas_id' => $feb->id, 'kode' => 'AK', 'nama' => 'Akuntansi', 'jenjang' => 'S1', 'total_sks' => 144]);
        
        // Ruangan
        $ruangan1 = Ruangan::create(['kode' => 'A101', 'nama' => 'Ruang A101', 'kapasitas' => 40, 'gedung' => 'Gedung A', 'lantai' => '1', 'jenis' => 'Kelas']);
        $ruangan2 = Ruangan::create(['kode' => 'A102', 'nama' => 'Ruang A102', 'kapasitas' => 40, 'gedung' => 'Gedung A', 'lantai' => '1', 'jenis' => 'Kelas']);
        $lab1 = Ruangan::create(['kode' => 'LAB1', 'nama' => 'Lab Komputer 1', 'kapasitas' => 30, 'gedung' => 'Gedung B', 'lantai' => '2', 'jenis' => 'Lab']);
        
        // Dosen
        $userDosen1 = User::create([
            'name' => 'Dr. Budi Santoso, M.Kom',
            'email' => 'budi@siakad.ac.id',
            'password' => Hash::make('dosen123'),
            'role' => 'dosen',
        ]);
        $dosen1 = Dosen::create([
            'user_id' => $userDosen1->id,
            'program_studi_id' => $ti->id,
            'nidn' => '0001018001',
            'nama' => 'Dr. Budi Santoso, M.Kom',
            'jenis_kelamin' => 'L',
            'email' => 'budi@siakad.ac.id',
            'jabatan_fungsional' => 'Lektor Kepala',
            'golongan' => 'IV/a',
        ]);

        $userDosen2 = User::create([
            'name' => 'Dr. Siti Rahayu, M.Kom',
            'email' => 'siti@siakad.ac.id',
            'password' => Hash::make('dosen123'),
            'role' => 'dosen',
        ]);
        $dosen2 = Dosen::create([
            'user_id' => $userDosen2->id,
            'program_studi_id' => $ti->id,
            'nidn' => '0002028502',
            'nama' => 'Dr. Siti Rahayu, M.Kom',
            'jenis_kelamin' => 'P',
            'email' => 'siti@siakad.ac.id',
            'jabatan_fungsional' => 'Lektor',
            'golongan' => 'III/d',
        ]);

        // Mahasiswa
        $userMhs1 = User::create([
            'name' => 'Ahmad Fauzan',
            'email' => 'ahmad@student.siakad.ac.id',
            'password' => Hash::make('2024001001'),
            'role' => 'mahasiswa',
        ]);
        $mhs1 = Mahasiswa::create([
            'user_id' => $userMhs1->id,
            'program_studi_id' => $ti->id,
            'dosen_wali_id' => $dosen1->id,
            'nim' => '2024001001',
            'nama' => 'Ahmad Fauzan',
            'jenis_kelamin' => 'L',
            'email' => 'ahmad@student.siakad.ac.id',
            'angkatan' => 2024,
            'semester_aktif' => 1,
        ]);

        $userMhs2 = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi@student.siakad.ac.id',
            'password' => Hash::make('2024001002'),
            'role' => 'mahasiswa',
        ]);
        $mhs2 = Mahasiswa::create([
            'user_id' => $userMhs2->id,
            'program_studi_id' => $ti->id,
            'dosen_wali_id' => $dosen1->id,
            'nim' => '2024001002',
            'nama' => 'Dewi Lestari',
            'jenis_kelamin' => 'P',
            'email' => 'dewi@student.siakad.ac.id',
            'angkatan' => 2024,
            'semester_aktif' => 1,
        ]);

        // Tahun Akademik
        $ta = TahunAkademik::create([
            'tahun' => '2024/2025',
            'semester' => 'Ganjil',
            'tanggal_mulai' => '2024-09-01',
            'tanggal_selesai' => '2025-01-31',
            'mulai_krs' => '2024-08-20',
            'selesai_krs' => '2025-01-15',
            'is_aktif' => true,
        ]);

        // Mata Kuliah
        $mk1 = MataKuliah::create(['program_studi_id' => $ti->id, 'kode' => 'TI101', 'nama' => 'Algoritma dan Pemrograman', 'sks' => 3, 'semester' => 1, 'jenis' => 'Wajib']);
        $mk2 = MataKuliah::create(['program_studi_id' => $ti->id, 'kode' => 'TI102', 'nama' => 'Matematika Diskrit', 'sks' => 3, 'semester' => 1, 'jenis' => 'Wajib']);
        $mk3 = MataKuliah::create(['program_studi_id' => $ti->id, 'kode' => 'TI103', 'nama' => 'Pengantar Teknologi Informasi', 'sks' => 2, 'semester' => 1, 'jenis' => 'Wajib']);
        $mk4 = MataKuliah::create(['program_studi_id' => $ti->id, 'kode' => 'TI104', 'nama' => 'Bahasa Inggris', 'sks' => 2, 'semester' => 1, 'jenis' => 'Wajib']);
        $mk5 = MataKuliah::create(['program_studi_id' => $ti->id, 'kode' => 'TI201', 'nama' => 'Struktur Data', 'sks' => 3, 'semester' => 2, 'jenis' => 'Wajib']);
        $mk6 = MataKuliah::create(['program_studi_id' => $ti->id, 'kode' => 'TI301', 'nama' => 'Basis Data', 'sks' => 3, 'semester' => 3, 'jenis' => 'Wajib']);
        $mk7 = MataKuliah::create(['program_studi_id' => $ti->id, 'kode' => 'TI302', 'nama' => 'Pemrograman Web', 'sks' => 3, 'semester' => 3, 'jenis' => 'Wajib']);

        // Jadwal Kuliah
        JadwalKuliah::create(['tahun_akademik_id' => $ta->id, 'mata_kuliah_id' => $mk1->id, 'dosen_id' => $dosen1->id, 'ruangan_id' => $lab1->id, 'kelas' => 'A', 'hari' => 'Senin', 'jam_mulai' => '08:00', 'jam_selesai' => '10:30', 'kuota' => 30]);
        JadwalKuliah::create(['tahun_akademik_id' => $ta->id, 'mata_kuliah_id' => $mk2->id, 'dosen_id' => $dosen2->id, 'ruangan_id' => $ruangan1->id, 'kelas' => 'A', 'hari' => 'Selasa', 'jam_mulai' => '08:00', 'jam_selesai' => '10:30', 'kuota' => 40]);
        JadwalKuliah::create(['tahun_akademik_id' => $ta->id, 'mata_kuliah_id' => $mk3->id, 'dosen_id' => $dosen1->id, 'ruangan_id' => $ruangan1->id, 'kelas' => 'A', 'hari' => 'Rabu', 'jam_mulai' => '10:00', 'jam_selesai' => '12:00', 'kuota' => 40]);
        JadwalKuliah::create(['tahun_akademik_id' => $ta->id, 'mata_kuliah_id' => $mk4->id, 'dosen_id' => $dosen2->id, 'ruangan_id' => $ruangan2->id, 'kelas' => 'A', 'hari' => 'Kamis', 'jam_mulai' => '13:00', 'jam_selesai' => '15:00', 'kuota' => 40]);

        // Pengumuman
        Pengumuman::create([
            'user_id' => $admin->id,
            'judul' => 'Selamat Datang di SIAKAD',
            'isi' => 'Selamat datang di Sistem Informasi Akademik. Silakan gunakan sistem ini untuk keperluan akademik Anda.',
            'kategori' => 'Umum',
            'tanggal_mulai' => now(),
            'is_aktif' => true,
        ]);

        Pengumuman::create([
            'user_id' => $admin->id,
            'judul' => 'Periode KRS Semester Ganjil 2024/2025',
            'isi' => 'Periode pengisian KRS untuk Semester Ganjil 2024/2025 telah dibuka. Silakan segera mengisi KRS Anda.',
            'kategori' => 'Akademik',
            'tanggal_mulai' => now(),
            'is_aktif' => true,
        ]);

        $this->command->info('Seeder SIAKAD berhasil dijalankan!');
        $this->command->info('');
        $this->command->info('=== AKUN LOGIN ===');
        $this->command->info('Admin    : admin@siakad.ac.id / admin123');
        $this->command->info('Dosen    : budi@siakad.ac.id / dosen123');
        $this->command->info('Mahasiswa: ahmad@student.siakad.ac.id / 2024001001');
    }
}
