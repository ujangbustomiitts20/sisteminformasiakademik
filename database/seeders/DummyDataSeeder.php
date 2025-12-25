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
use App\Models\Krs;
use App\Models\Nilai;
use App\Models\Absensi;
use App\Models\Pembayaran;
use App\Models\KalenderAkademik;
use App\Models\ActivityLog;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate all tables
        DB::table('notifications')->truncate();
        DB::table('activity_logs')->truncate();
        DB::table('absensi')->truncate();
        DB::table('nilai')->truncate();
        DB::table('krs')->truncate();
        DB::table('pembayaran')->truncate();
        DB::table('jadwal_kuliah')->truncate();
        DB::table('mata_kuliah')->truncate();
        DB::table('kalender_akademiks')->truncate();
        DB::table('pengumuman')->truncate();
        DB::table('mahasiswa')->truncate();
        DB::table('dosen')->truncate();
        DB::table('ruangan')->truncate();
        DB::table('program_studi')->truncate();
        DB::table('fakultas')->truncate();
        DB::table('tahun_akademik')->truncate();
        DB::table('users')->truncate();
        
        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Creating dummy data...');

        // =====================
        // USERS & ADMIN
        // =====================
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@siakad.ac.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // =====================
        // FAKULTAS (5 Fakultas)
        // =====================
        $fakultas = [
            Fakultas::create(['kode' => 'FTI', 'nama' => 'Fakultas Teknologi Informasi', 'dekan' => 'Prof. Dr. Ahmad Dahlan, M.Kom']),
            Fakultas::create(['kode' => 'FEB', 'nama' => 'Fakultas Ekonomi dan Bisnis', 'dekan' => 'Prof. Dr. Siti Ekonomi, M.M']),
            Fakultas::create(['kode' => 'FH', 'nama' => 'Fakultas Hukum', 'dekan' => 'Prof. Dr. Bambang Hukum, S.H., M.H']),
            Fakultas::create(['kode' => 'FK', 'nama' => 'Fakultas Kedokteran', 'dekan' => 'Prof. Dr. dr. Medika Sehat, Sp.PD']),
            Fakultas::create(['kode' => 'FT', 'nama' => 'Fakultas Teknik', 'dekan' => 'Prof. Dr. Ir. Teguh Konstruksi, M.T']),
        ];

        // =====================
        // PROGRAM STUDI (10 Prodi)
        // =====================
        $prodi = [
            // FTI
            ProgramStudi::create(['fakultas_id' => $fakultas[0]->id, 'kode' => 'TI', 'nama' => 'Teknik Informatika', 'jenjang' => 'S1', 'total_sks' => 144]),
            ProgramStudi::create(['fakultas_id' => $fakultas[0]->id, 'kode' => 'SI', 'nama' => 'Sistem Informasi', 'jenjang' => 'S1', 'total_sks' => 144]),
            ProgramStudi::create(['fakultas_id' => $fakultas[0]->id, 'kode' => 'TK', 'nama' => 'Teknik Komputer', 'jenjang' => 'D3', 'total_sks' => 110]),
            // FEB
            ProgramStudi::create(['fakultas_id' => $fakultas[1]->id, 'kode' => 'AK', 'nama' => 'Akuntansi', 'jenjang' => 'S1', 'total_sks' => 144]),
            ProgramStudi::create(['fakultas_id' => $fakultas[1]->id, 'kode' => 'MN', 'nama' => 'Manajemen', 'jenjang' => 'S1', 'total_sks' => 144]),
            // FH
            ProgramStudi::create(['fakultas_id' => $fakultas[2]->id, 'kode' => 'HK', 'nama' => 'Ilmu Hukum', 'jenjang' => 'S1', 'total_sks' => 144]),
            // FK
            ProgramStudi::create(['fakultas_id' => $fakultas[3]->id, 'kode' => 'KD', 'nama' => 'Pendidikan Dokter', 'jenjang' => 'S1', 'total_sks' => 160]),
            ProgramStudi::create(['fakultas_id' => $fakultas[3]->id, 'kode' => 'KP', 'nama' => 'Keperawatan', 'jenjang' => 'D3', 'total_sks' => 110]),
            // FT
            ProgramStudi::create(['fakultas_id' => $fakultas[4]->id, 'kode' => 'TS', 'nama' => 'Teknik Sipil', 'jenjang' => 'S1', 'total_sks' => 144]),
            ProgramStudi::create(['fakultas_id' => $fakultas[4]->id, 'kode' => 'TE', 'nama' => 'Teknik Elektro', 'jenjang' => 'S1', 'total_sks' => 144]),
        ];

        // =====================
        // RUANGAN (15 Ruangan)
        // =====================
        $ruangan = [];
        $gedung = ['A', 'B', 'C'];
        
        for ($g = 0; $g < 3; $g++) {
            for ($r = 1; $r <= 5; $r++) {
                $jenis = $r <= 3 ? 'Kelas' : ($r == 4 ? 'Lab' : 'Aula');
                $kapasitas = $jenis == 'Kelas' ? 40 : ($jenis == 'Lab' ? 30 : 100);
                $ruangan[] = Ruangan::create([
                    'kode' => $gedung[$g] . '10' . $r,
                    'nama' => 'Ruang ' . $gedung[$g] . '10' . $r,
                    'kapasitas' => $kapasitas,
                    'gedung' => 'Gedung ' . $gedung[$g],
                    'lantai' => (string)ceil($r / 2),
                    'jenis' => $jenis,
                ]);
            }
        }

        // =====================
        // TAHUN AKADEMIK
        // =====================
        $tahunAkademik = [
            TahunAkademik::create([
                'tahun' => '2023/2024',
                'semester' => 'Ganjil',
                'tanggal_mulai' => '2023-09-01',
                'tanggal_selesai' => '2024-01-31',
                'mulai_krs' => '2023-08-15',
                'selesai_krs' => '2023-08-30',
                'is_aktif' => false,
            ]),
            TahunAkademik::create([
                'tahun' => '2023/2024',
                'semester' => 'Genap',
                'tanggal_mulai' => '2024-02-01',
                'tanggal_selesai' => '2024-06-30',
                'mulai_krs' => '2024-01-15',
                'selesai_krs' => '2024-01-30',
                'is_aktif' => false,
            ]),
            TahunAkademik::create([
                'tahun' => '2024/2025',
                'semester' => 'Ganjil',
                'tanggal_mulai' => '2024-09-01',
                'tanggal_selesai' => '2025-01-31',
                'mulai_krs' => '2024-08-15',
                'selesai_krs' => '2024-08-30',
                'is_aktif' => false,
            ]),
            TahunAkademik::create([
                'tahun' => '2024/2025',
                'semester' => 'Genap',
                'tanggal_mulai' => '2025-02-01',
                'tanggal_selesai' => '2025-06-30',
                'mulai_krs' => '2025-01-15',
                'selesai_krs' => '2025-01-30',
                'is_aktif' => true,
            ]),
        ];

        // =====================
        // DOSEN (20 Dosen)
        // =====================
        $namaDosen = [
            ['Dr. Budi Santoso, M.Kom', 'L', 'budi', $prodi[0]->id, 'Lektor Kepala', 'IV/a'],
            ['Dr. Siti Rahayu, M.Kom', 'P', 'siti', $prodi[0]->id, 'Lektor', 'III/d'],
            ['Dr. Agus Wijaya, M.T', 'L', 'agus', $prodi[0]->id, 'Lektor', 'III/d'],
            ['Ir. Dewi Kartika, M.Kom', 'P', 'dewi', $prodi[1]->id, 'Asisten Ahli', 'III/b'],
            ['Dr. Hendra Kusuma, M.Si', 'L', 'hendra', $prodi[1]->id, 'Lektor Kepala', 'IV/a'],
            ['Dr. Rina Marlina, M.M', 'P', 'rina', $prodi[3]->id, 'Lektor', 'III/d'],
            ['Prof. Dr. Joko Susilo, M.M', 'L', 'joko', $prodi[3]->id, 'Guru Besar', 'IV/d'],
            ['Dr. Maya Sari, M.Ak', 'P', 'maya', $prodi[4]->id, 'Lektor', 'III/d'],
            ['Dr. Bambang Prasetyo, S.H., M.H', 'L', 'bambang', $prodi[5]->id, 'Lektor Kepala', 'IV/a'],
            ['Dr. Endang Wahyuni, S.H., M.H', 'P', 'endang', $prodi[5]->id, 'Lektor', 'III/d'],
            ['dr. Andi Pratama, Sp.PD', 'L', 'andi', $prodi[6]->id, 'Lektor Kepala', 'IV/a'],
            ['dr. Lina Setiawati, M.Kes', 'P', 'lina', $prodi[6]->id, 'Lektor', 'III/d'],
            ['Ns. Ratna Dewi, M.Kep', 'P', 'ratna', $prodi[7]->id, 'Asisten Ahli', 'III/b'],
            ['Ir. Teguh Prabowo, M.T', 'L', 'teguh', $prodi[8]->id, 'Lektor', 'III/d'],
            ['Dr. Ir. Surya Dharma, M.T', 'L', 'surya', $prodi[8]->id, 'Lektor Kepala', 'IV/a'],
            ['Ir. Fitri Handayani, M.T', 'P', 'fitri', $prodi[9]->id, 'Asisten Ahli', 'III/b'],
            ['Dr. Ir. Wahyu Hidayat, M.T', 'L', 'wahyu', $prodi[9]->id, 'Lektor', 'III/d'],
            ['Dr. Nurul Aini, M.Kom', 'P', 'nurul', $prodi[0]->id, 'Lektor', 'III/c'],
            ['Dr. Dian Permata, M.Kom', 'P', 'dian', $prodi[1]->id, 'Asisten Ahli', 'III/b'],
            ['Dr. Rudi Hartono, M.T', 'L', 'rudi', $prodi[2]->id, 'Lektor', 'III/d'],
        ];

        $dosen = [];
        foreach ($namaDosen as $i => $d) {
            $userDosen = User::create([
                'name' => $d[0],
                'email' => $d[2] . '@siakad.ac.id',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
            ]);
            
            $dosen[] = Dosen::create([
                'user_id' => $userDosen->id,
                'program_studi_id' => $d[3],
                'nidn' => '000' . str_pad($i + 1, 2, '0', STR_PAD_LEFT) . '0' . rand(18000, 19500),
                'nama' => $d[0],
                'jenis_kelamin' => $d[1],
                'tempat_lahir' => ['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang'][rand(0, 4)],
                'tanggal_lahir' => date('Y-m-d', strtotime('-' . rand(35, 55) . ' years')),
                'alamat' => 'Jl. Contoh No. ' . rand(1, 100) . ', Kota ' . ['Jakarta', 'Bandung', 'Surabaya'][rand(0, 2)],
                'telepon' => '08' . rand(1000000000, 9999999999),
                'email' => $d[2] . '@siakad.ac.id',
                'jabatan_fungsional' => $d[4],
                'golongan' => $d[5],
            ]);
        }

        // =====================
        // MAHASISWA (50 Mahasiswa)
        // =====================
        $namaDepan = ['Ahmad', 'Budi', 'Citra', 'Dewi', 'Eka', 'Fajar', 'Gita', 'Hadi', 'Indah', 'Joko', 'Kartika', 'Lina', 'Maya', 'Nia', 'Oscar', 'Putri', 'Qori', 'Rina', 'Sari', 'Tono', 'Umi', 'Vera', 'Wati', 'Yoga', 'Zahra'];
        $namaBelakang = ['Pratama', 'Kusuma', 'Wijaya', 'Santoso', 'Rahayu', 'Permana', 'Hidayat', 'Saputra', 'Lestari', 'Putra', 'Dewi', 'Sari', 'Wibowo', 'Setiawan', 'Nugraha'];

        $mahasiswa = [];
        for ($i = 1; $i <= 50; $i++) {
            $nama = $namaDepan[array_rand($namaDepan)] . ' ' . $namaBelakang[array_rand($namaBelakang)];
            $gender = rand(0, 1) ? 'L' : 'P';
            $angkatan = rand(0, 3) == 0 ? 2022 : (rand(0, 2) == 0 ? 2023 : 2024);
            $prodiIdx = rand(0, 9);
            $nim = $angkatan . str_pad($prodiIdx + 1, 3, '0', STR_PAD_LEFT) . str_pad($i, 3, '0', STR_PAD_LEFT);
            
            $userMhs = User::create([
                'name' => $nama,
                'email' => strtolower(str_replace(' ', '.', $nama)) . $i . '@student.siakad.ac.id',
                'password' => Hash::make($nim),
                'role' => 'mahasiswa',
            ]);

            $semester = (2025 - $angkatan) * 2;
            if ($semester > 8) $semester = 8;
            if ($semester < 1) $semester = 1;

            $mahasiswa[] = Mahasiswa::create([
                'user_id' => $userMhs->id,
                'program_studi_id' => $prodi[$prodiIdx]->id,
                'dosen_wali_id' => $dosen[array_rand($dosen)]->id,
                'nim' => $nim,
                'nama' => $nama,
                'jenis_kelamin' => $gender,
                'tempat_lahir' => ['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang', 'Medan', 'Makassar'][rand(0, 6)],
                'tanggal_lahir' => date('Y-m-d', strtotime('-' . rand(18, 23) . ' years')),
                'alamat' => 'Jl. Mahasiswa No. ' . rand(1, 200) . ', Kota ' . ['Jakarta', 'Bandung', 'Surabaya'][rand(0, 2)],
                'telepon' => '08' . rand(1000000000, 9999999999),
                'email' => strtolower(str_replace(' ', '.', $nama)) . $i . '@student.siakad.ac.id',
                'angkatan' => $angkatan,
                'semester_aktif' => $semester,
                'status' => 'Aktif',
            ]);
        }

        // =====================
        // MATA KULIAH (30 Mata Kuliah)
        // =====================
        $mataKuliahData = [
            // Teknik Informatika
            [$prodi[0]->id, 'TI101', 'Algoritma dan Pemrograman', 3, 1, 'Wajib'],
            [$prodi[0]->id, 'TI102', 'Matematika Diskrit', 3, 1, 'Wajib'],
            [$prodi[0]->id, 'TI103', 'Pengantar Teknologi Informasi', 2, 1, 'Wajib'],
            [$prodi[0]->id, 'TI104', 'Bahasa Inggris I', 2, 1, 'Wajib'],
            [$prodi[0]->id, 'TI201', 'Struktur Data', 3, 2, 'Wajib'],
            [$prodi[0]->id, 'TI202', 'Pemrograman Berorientasi Objek', 3, 2, 'Wajib'],
            [$prodi[0]->id, 'TI301', 'Basis Data', 3, 3, 'Wajib'],
            [$prodi[0]->id, 'TI302', 'Pemrograman Web', 3, 3, 'Wajib'],
            [$prodi[0]->id, 'TI303', 'Jaringan Komputer', 3, 3, 'Wajib'],
            [$prodi[0]->id, 'TI401', 'Rekayasa Perangkat Lunak', 3, 4, 'Wajib'],
            // Sistem Informasi
            [$prodi[1]->id, 'SI101', 'Pengantar Sistem Informasi', 3, 1, 'Wajib'],
            [$prodi[1]->id, 'SI102', 'Logika Matematika', 2, 1, 'Wajib'],
            [$prodi[1]->id, 'SI201', 'Analisis dan Desain Sistem', 3, 2, 'Wajib'],
            [$prodi[1]->id, 'SI301', 'Manajemen Proyek SI', 3, 3, 'Wajib'],
            // Akuntansi
            [$prodi[3]->id, 'AK101', 'Pengantar Akuntansi I', 3, 1, 'Wajib'],
            [$prodi[3]->id, 'AK102', 'Ekonomi Mikro', 3, 1, 'Wajib'],
            [$prodi[3]->id, 'AK201', 'Akuntansi Biaya', 3, 2, 'Wajib'],
            [$prodi[3]->id, 'AK301', 'Perpajakan', 3, 3, 'Wajib'],
            // Manajemen
            [$prodi[4]->id, 'MN101', 'Pengantar Manajemen', 3, 1, 'Wajib'],
            [$prodi[4]->id, 'MN201', 'Manajemen Pemasaran', 3, 2, 'Wajib'],
            // Hukum
            [$prodi[5]->id, 'HK101', 'Pengantar Ilmu Hukum', 3, 1, 'Wajib'],
            [$prodi[5]->id, 'HK201', 'Hukum Perdata', 3, 2, 'Wajib'],
            // Kedokteran
            [$prodi[6]->id, 'KD101', 'Anatomi Dasar', 4, 1, 'Wajib'],
            [$prodi[6]->id, 'KD201', 'Fisiologi', 4, 2, 'Wajib'],
            // Keperawatan
            [$prodi[7]->id, 'KP101', 'Keperawatan Dasar', 3, 1, 'Wajib'],
            // Teknik Sipil
            [$prodi[8]->id, 'TS101', 'Mekanika Teknik', 3, 1, 'Wajib'],
            [$prodi[8]->id, 'TS201', 'Struktur Beton', 3, 2, 'Wajib'],
            // Teknik Elektro
            [$prodi[9]->id, 'TE101', 'Dasar Rangkaian Listrik', 3, 1, 'Wajib'],
            [$prodi[9]->id, 'TE201', 'Elektronika Analog', 3, 2, 'Wajib'],
            // Mata Kuliah Umum
            [$prodi[0]->id, 'MKU01', 'Pancasila', 2, 1, 'Wajib'],
        ];

        $mataKuliah = [];
        foreach ($mataKuliahData as $mk) {
            $mataKuliah[] = MataKuliah::create([
                'program_studi_id' => $mk[0],
                'kode' => $mk[1],
                'nama' => $mk[2],
                'sks' => $mk[3],
                'semester' => $mk[4],
                'jenis' => $mk[5],
            ]);
        }

        // =====================
        // JADWAL KULIAH (25 Jadwal)
        // =====================
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jamMulai = ['07:30', '08:00', '09:30', '10:00', '13:00', '14:30', '16:00'];
        
        $jadwal = [];
        for ($i = 0; $i < 25; $i++) {
            $mk = $mataKuliah[$i % count($mataKuliah)];
            $jamIdx = rand(0, count($jamMulai) - 1);
            $durasi = $mk->sks * 50; // 50 menit per SKS
            $jamSelesai = date('H:i', strtotime($jamMulai[$jamIdx]) + ($durasi * 60));
            
            $jadwal[] = JadwalKuliah::create([
                'tahun_akademik_id' => $tahunAkademik[3]->id, // Semester aktif
                'mata_kuliah_id' => $mk->id,
                'dosen_id' => $dosen[$i % count($dosen)]->id,
                'ruangan_id' => $ruangan[$i % count($ruangan)]->id,
                'kelas' => ['A', 'B', 'C'][rand(0, 2)],
                'hari' => $hari[$i % 5],
                'jam_mulai' => $jamMulai[$jamIdx],
                'jam_selesai' => $jamSelesai,
                'kuota' => rand(30, 45),
            ]);
        }

        // =====================
        // KRS (100 Data KRS)
        // =====================
        $krsData = [];
        foreach ($mahasiswa as $mhs) {
            // Ambil 4-6 jadwal random untuk setiap mahasiswa
            $jadwalMhs = collect($jadwal)->random(rand(4, 6));
            foreach ($jadwalMhs as $jdwl) {
                $status = ['Disetujui', 'Pending', 'Disetujui'][rand(0, 2)];
                $krsData[] = Krs::create([
                    'mahasiswa_id' => $mhs->id,
                    'jadwal_kuliah_id' => $jdwl->id,
                    'tahun_akademik_id' => $tahunAkademik[3]->id,
                    'tanggal_pengajuan' => now()->subDays(rand(10, 30)),
                    'tanggal_persetujuan' => $status == 'Disetujui' ? now()->subDays(rand(5, 9)) : null,
                    'status' => $status,
                ]);
            }
        }

        // =====================
        // NILAI (untuk KRS yang disetujui dari semester sebelumnya)
        // =====================
        $nilaiHuruf = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E'];
        $nilaiBobot = [4.0, 3.7, 3.3, 3.0, 2.7, 2.3, 2.0, 1.0, 0];
        
        // Buat KRS dan Nilai untuk semester sebelumnya
        foreach ($mahasiswa as $mhs) {
            if ($mhs->angkatan < 2024) {
                // Mahasiswa lama punya nilai semester sebelumnya
                $jadwalLama = collect($jadwal)->random(rand(5, 7));
                foreach ($jadwalLama as $jdwl) {
                    $krsLama = Krs::create([
                        'mahasiswa_id' => $mhs->id,
                        'jadwal_kuliah_id' => $jdwl->id,
                        'tahun_akademik_id' => $tahunAkademik[2]->id, // Semester lalu
                        'tanggal_pengajuan' => now()->subMonths(6),
                        'tanggal_persetujuan' => now()->subMonths(6)->addDays(3),
                        'status' => 'Disetujui',
                    ]);
                    
                    $nilaiIdx = rand(0, count($nilaiHuruf) - 2); // Hindari E
                    $tugas = rand(60, 95);
                    $uts = rand(55, 90);
                    $uas = rand(50, 95);
                    $nilaiAkhir = ($tugas * 0.3) + ($uts * 0.3) + ($uas * 0.4);
                    
                    Nilai::create([
                        'krs_id' => $krsLama->id,
                        'tugas' => $tugas,
                        'uts' => $uts,
                        'uas' => $uas,
                        'nilai_akhir' => round($nilaiAkhir, 2),
                        'huruf' => $nilaiHuruf[$nilaiIdx],
                        'bobot' => $nilaiBobot[$nilaiIdx],
                    ]);
                }
            }
        }

        // =====================
        // ABSENSI (500 Data)
        // =====================
        $pertemuanKe = 16; // 16 pertemuan per semester
        
        foreach ($krsData as $krs) {
            // Buat absensi untuk 8-12 pertemuan
            $jumlahPertemuan = rand(8, 12);
            for ($p = 1; $p <= $jumlahPertemuan; $p++) {
                $statusAbsen = rand(1, 10) <= 8 ? 'Hadir' : (['Izin', 'Sakit', 'Alpha'][rand(0, 2)]);
                Absensi::create([
                    'krs_id' => $krs->id,
                    'tanggal' => now()->subDays(rand(1, 90)),
                    'pertemuan' => $p,
                    'status' => $statusAbsen,
                    'keterangan' => $statusAbsen != 'Hadir' ? 'Keterangan ' . $statusAbsen : null,
                    'materi' => 'Materi Pertemuan ke-' . $p,
                ]);
            }
        }

        // =====================
        // PEMBAYARAN (100 Data)
        // =====================
        // enum: 'SPP', 'Herregistrasi', 'Wisuda', 'Lainnya'
        $jenisPembayaran = ['SPP', 'Herregistrasi', 'Wisuda', 'Lainnya'];
        
        foreach ($mahasiswa as $mhs) {
            // Setiap mahasiswa punya 2-4 pembayaran
            $jumlahPembayaran = rand(2, 4);
            for ($p = 0; $p < $jumlahPembayaran; $p++) {
                $jenis = $jenisPembayaran[rand(0, count($jenisPembayaran) - 1)];
                $jumlah = $jenis == 'SPP' ? rand(3, 6) * 1000000 : 
                          ($jenis == 'Herregistrasi' ? 500000 : 
                          ($jenis == 'Wisuda' ? 2500000 : rand(200, 500) * 1000));
                
                $status = rand(1, 10) <= 7 ? 'Lunas' : 'Belum Lunas';
                
                Pembayaran::create([
                    'mahasiswa_id' => $mhs->id,
                    'tahun_akademik_id' => $tahunAkademik[rand(2, 3)]->id,
                    'jenis' => $jenis,
                    'jumlah' => $jumlah,
                    'tanggal_bayar' => $status == 'Lunas' ? now()->subDays(rand(1, 60)) : null,
                    'metode_bayar' => $status == 'Lunas' ? ['Transfer Bank', 'Virtual Account', 'E-Wallet'][rand(0, 2)] : null,
                    'bukti_bayar' => $status == 'Lunas' ? 'bukti_' . rand(1000, 9999) . '.jpg' : null,
                    'status' => $status,
                    'keterangan' => $jenis . ' Semester ' . $tahunAkademik[rand(2, 3)]->semester,
                ]);
            }
        }

        // =====================
        // PENGUMUMAN (10 Pengumuman)
        // =====================
        // enum: 'Umum','Akademik','Keuangan','Kemahasiswaan'
        $pengumumanData = [
            ['Selamat Datang di SIAKAD 2025', 'Selamat datang di Sistem Informasi Akademik. Silakan gunakan sistem ini untuk keperluan akademik Anda.', 'Umum'],
            ['Periode KRS Semester Genap 2024/2025', 'Periode pengisian KRS untuk Semester Genap 2024/2025 telah dibuka mulai tanggal 15 Januari 2025.', 'Akademik'],
            ['Jadwal UTS Semester Genap', 'Ujian Tengah Semester (UTS) akan dilaksanakan pada tanggal 10-20 Maret 2025.', 'Akademik'],
            ['Pembayaran UKT Semester Genap', 'Batas akhir pembayaran UKT Semester Genap adalah 28 Februari 2025.', 'Keuangan'],
            ['Libur Hari Raya Idul Fitri', 'Libur Hari Raya Idul Fitri 1446 H akan berlangsung dari tanggal 28 Maret - 7 April 2025.', 'Umum'],
            ['Pendaftaran Wisuda Periode Maret 2025', 'Pendaftaran wisuda periode Maret 2025 dibuka mulai 1-15 Februari 2025.', 'Akademik'],
            ['Workshop Data Science', 'Workshop Data Science dengan Python akan diadakan pada 15 Januari 2025 di Auditorium.', 'Kemahasiswaan'],
            ['Beasiswa Prestasi Akademik', 'Pendaftaran beasiswa prestasi akademik dibuka hingga 31 Januari 2025.', 'Kemahasiswaan'],
            ['Perpanjangan Kartu Tanda Mahasiswa', 'Perpanjangan KTM dapat dilakukan di Bagian Akademik mulai 2 Januari 2025.', 'Umum'],
            ['Seminar Nasional Teknologi', 'Seminar Nasional Teknologi Informasi 2025 akan diadakan pada 20 Februari 2025.', 'Kemahasiswaan'],
        ];

        foreach ($pengumumanData as $i => $p) {
            Pengumuman::create([
                'user_id' => $admin->id,
                'judul' => $p[0],
                'isi' => $p[1],
                'kategori' => $p[2],
                'tanggal_mulai' => now()->subDays(rand(0, 30)),
                'tanggal_selesai' => rand(0, 1) ? now()->addDays(rand(30, 90)) : null,
                'is_aktif' => rand(0, 10) > 2,
            ]);
        }

        // =====================
        // KALENDER AKADEMIK (15 Events)
        // =====================
        // enum jenis: 'akademik','libur','ujian','pendaftaran','lainnya'
        $kalenderData = [
            ['Awal Semester Genap 2024/2025', '2025-02-01', '2025-02-01', 'akademik', 'Semester Genap resmi dimulai'],
            ['Perkuliahan Dimulai', '2025-02-03', '2025-02-03', 'akademik', 'Perkuliahan semester genap dimulai'],
            ['Pengisian KRS', '2025-01-15', '2025-01-30', 'pendaftaran', 'Periode pengisian Kartu Rencana Studi'],
            ['Perubahan KRS', '2025-02-03', '2025-02-14', 'pendaftaran', 'Periode perubahan KRS'],
            ['Batas Pembayaran UKT', '2025-02-28', '2025-02-28', 'lainnya', 'Batas akhir pembayaran UKT'],
            ['UTS Semester Genap', '2025-03-10', '2025-03-20', 'ujian', 'Ujian Tengah Semester'],
            ['Libur Idul Fitri', '2025-03-28', '2025-04-07', 'libur', 'Libur Hari Raya Idul Fitri 1446 H'],
            ['UAS Semester Genap', '2025-05-26', '2025-06-06', 'ujian', 'Ujian Akhir Semester'],
            ['Input Nilai Dosen', '2025-06-09', '2025-06-20', 'akademik', 'Periode input nilai oleh dosen'],
            ['Pengumuman Nilai', '2025-06-25', '2025-06-25', 'akademik', 'Pengumuman nilai semester'],
            ['Akhir Semester Genap', '2025-06-30', '2025-06-30', 'akademik', 'Semester Genap berakhir'],
            ['Wisuda Periode Maret', '2025-03-15', '2025-03-15', 'lainnya', 'Wisuda Periode Maret 2025'],
            ['Dies Natalis', '2025-04-15', '2025-04-15', 'lainnya', 'Peringatan Dies Natalis Universitas'],
            ['Pendaftaran Mahasiswa Baru', '2025-04-01', '2025-07-31', 'pendaftaran', 'Pendaftaran PMB Tahun Ajaran 2025/2026'],
            ['Libur Semester', '2025-07-01', '2025-08-31', 'libur', 'Libur antar semester'],
        ];

        foreach ($kalenderData as $k) {
            KalenderAkademik::create([
                'tahun_akademik_id' => $tahunAkademik[3]->id,
                'judul' => $k[0],
                'tanggal_mulai' => $k[1],
                'tanggal_selesai' => $k[2],
                'jenis' => $k[3],
                'deskripsi' => $k[4],
                'warna' => ['akademik' => '#007bff', 'ujian' => '#dc3545', 'libur' => '#28a745', 'lainnya' => '#ffc107', 'pendaftaran' => '#17a2b8'][$k[3]],
                'is_active' => true,
            ]);
        }

        // =====================
        // ACTIVITY LOG (50 Logs)
        // =====================
        $actions = [
            ['login', 'User logged in'],
            ['logout', 'User logged out'],
            ['krs_submit', 'KRS submitted for approval'],
            ['krs_approved', 'KRS approved by advisor'],
            ['nilai_input', 'Grades entered for course'],
            ['pembayaran', 'Payment received'],
            ['profile_update', 'Profile updated'],
            ['password_change', 'Password changed'],
            ['pengumuman_create', 'Announcement created'],
            ['backup_create', 'Database backup created'],
        ];

        $allUsers = User::all();
        for ($i = 0; $i < 50; $i++) {
            $action = $actions[rand(0, count($actions) - 1)];
            $user = $allUsers->random();
            
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => $action[0],
                'description' => $action[1] . ' by ' . $user->name,
                'ip_address' => '192.168.1.' . rand(1, 255),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
            ]);
        }

        // =====================
        // NOTIFICATIONS (30 Notifications)
        // =====================
        $notifTypes = [
            ['krs_approved', 'KRS Disetujui', 'KRS Anda untuk semester ini telah disetujui oleh dosen wali.'],
            ['nilai_published', 'Nilai Dipublikasikan', 'Nilai mata kuliah telah dipublikasikan. Silakan cek di menu Nilai.'],
            ['pembayaran_reminder', 'Pengingat Pembayaran', 'Jangan lupa untuk melakukan pembayaran UKT sebelum batas waktu.'],
            ['jadwal_change', 'Perubahan Jadwal', 'Terdapat perubahan jadwal kuliah. Silakan cek jadwal terbaru.'],
            ['pengumuman_new', 'Pengumuman Baru', 'Ada pengumuman baru dari admin. Silakan cek halaman pengumuman.'],
        ];

        foreach ($mahasiswa as $mhs) {
            // Setiap mahasiswa dapat 1-3 notifikasi
            $jumlahNotif = rand(1, 3);
            for ($n = 0; $n < $jumlahNotif; $n++) {
                $notif = $notifTypes[rand(0, count($notifTypes) - 1)];
                $isRead = rand(0, 1);
                Notification::create([
                    'user_id' => $mhs->user_id,
                    'type' => $notif[0],
                    'title' => $notif[1],
                    'message' => $notif[2],
                    'link' => null,
                    'is_read' => $isRead,
                    'read_at' => $isRead ? now()->subDays(rand(1, 10)) : null,
                ]);
            }
        }

        // Info selesai
        $this->command->newLine();
        $this->command->info('=============================================');
        $this->command->info('✅ DUMMY DATA BERHASIL DIBUAT!');
        $this->command->info('=============================================');
        $this->command->newLine();
        $this->command->table(
            ['Modul', 'Jumlah Data'],
            [
                ['Users', User::count()],
                ['Fakultas', Fakultas::count()],
                ['Program Studi', ProgramStudi::count()],
                ['Dosen', Dosen::count()],
                ['Mahasiswa', Mahasiswa::count()],
                ['Ruangan', Ruangan::count()],
                ['Tahun Akademik', TahunAkademik::count()],
                ['Mata Kuliah', MataKuliah::count()],
                ['Jadwal Kuliah', JadwalKuliah::count()],
                ['KRS', Krs::count()],
                ['Nilai', Nilai::count()],
                ['Absensi', Absensi::count()],
                ['Pembayaran', Pembayaran::count()],
                ['Pengumuman', Pengumuman::count()],
                ['Kalender Akademik', KalenderAkademik::count()],
                ['Activity Log', ActivityLog::count()],
                ['Notifications', Notification::count()],
            ]
        );
        $this->command->newLine();
        $this->command->info('=== AKUN LOGIN ===');
        $this->command->info('Admin     : admin@siakad.ac.id / admin123');
        $this->command->info('Dosen     : budi@siakad.ac.id / dosen123');
        $this->command->info('           (semua dosen: [nama]@siakad.ac.id / dosen123)');
        $this->command->info('Mahasiswa : [email] / [nim sebagai password]');
        $this->command->newLine();
    }
}
