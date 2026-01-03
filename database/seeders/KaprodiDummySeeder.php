<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\ProgramStudi;
use App\Models\Fakultas;
use App\Models\User;
use App\Models\Krs;
use App\Models\Nilai;
use App\Models\TugasAkhir;
use App\Models\CutiAkademik;
use App\Models\BimbinganAkademik;
use App\Models\JadwalKuliah;
use App\Models\TahunAkademik;
use App\Models\MataKuliah;
use App\Models\Kurikulum;
use App\Models\Ruangan;
use App\Models\Absensi;
use App\Models\PeriodeWisuda;
use App\Models\PendaftaranWisuda;
use App\Models\Yudisium;
use App\Models\PeriodeEdom;
use App\Models\PertanyaanEdom;
use App\Models\JawabanEdom;
use App\Models\RekapEdom;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class KaprodiDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Kaprodi Dummy Data...');

        // Check if required data exists
        $fakultas = Fakultas::first();
        if (!$fakultas) {
            $fakultas = Fakultas::create([
                'kode' => 'FTI',
                'nama' => 'Fakultas Teknologi Informasi',
            ]);
            $this->command->info('Created Fakultas: ' . $fakultas->nama);
        }

        // Create or get Program Studi
        $prodi = ProgramStudi::first();
        if (!$prodi) {
            $prodi = ProgramStudi::create([
                'kode' => 'TI',
                'nama' => 'Teknik Informatika',
                'jenjang' => 'S1',
                'fakultas_id' => $fakultas->id,
                'akreditasi' => 'A',
            ]);
            $this->command->info('Created Program Studi: ' . $prodi->nama);
        }

        // Create Tahun Akademik if not exists
        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();
        if (!$tahunAkademik) {
            $tahunAkademik = TahunAkademik::create([
                'tahun' => 2024,
                'semester' => 'Ganjil',
                'is_aktif' => true,
                'tanggal_mulai' => '2024-09-01',
                'tanggal_selesai' => '2025-02-28',
            ]);
            $this->command->info('Created Tahun Akademik: ' . $tahunAkademik->tahun . ' ' . $tahunAkademik->semester);
        }

        // Create Kurikulum if not exists
        $kurikulum = Kurikulum::where('program_studi_id', $prodi->id)->first();
        if (!$kurikulum) {
            $kurikulum = Kurikulum::create([
                'kode' => 'KUR-2020',
                'nama' => 'Kurikulum 2020',
                'program_studi_id' => $prodi->id,
                'tahun_mulai' => 2020,
                'tahun_selesai' => 2024,
                'is_aktif' => true,
            ]);
            $this->command->info('Created Kurikulum: ' . $kurikulum->nama);
        }

        // Create Ruangan if not exists
        $ruangan = Ruangan::first();
        if (!$ruangan) {
            $ruangans = [
                ['kode' => 'LAB-01', 'nama' => 'Lab Komputer 1', 'kapasitas' => 40, 'gedung' => 'Gedung A'],
                ['kode' => 'LAB-02', 'nama' => 'Lab Komputer 2', 'kapasitas' => 40, 'gedung' => 'Gedung A'],
                ['kode' => 'R-101', 'nama' => 'Ruang Kelas 101', 'kapasitas' => 50, 'gedung' => 'Gedung B'],
                ['kode' => 'R-102', 'nama' => 'Ruang Kelas 102', 'kapasitas' => 50, 'gedung' => 'Gedung B'],
                ['kode' => 'R-201', 'nama' => 'Ruang Kelas 201', 'kapasitas' => 45, 'gedung' => 'Gedung B'],
            ];
            foreach ($ruangans as $r) {
                Ruangan::create($r);
            }
            $ruangan = Ruangan::first();
            $this->command->info('Created Ruangan');
        }

        // Create Kaprodi User and Dosen
        $kaprodiUser = User::where('email', 'kaprodi@siakad.test')->first();
        if (!$kaprodiUser) {
            $kaprodiUser = User::create([
                'name' => 'Dr. Ahmad Fauzi, M.Kom',
                'email' => 'kaprodi@siakad.test',
                'password' => Hash::make('password'),
                'role' => 'kaprodi',
            ]);
            $this->command->info('Created Kaprodi User: ' . $kaprodiUser->email);
        }

        $kaprodiDosen = Dosen::where('user_id', $kaprodiUser->id)->first();
        if (!$kaprodiDosen) {
            $kaprodiDosen = Dosen::create([
                'user_id' => $kaprodiUser->id,
                'nidn' => '0115017501',
                'nama' => 'Dr. Ahmad Fauzi, M.Kom',
                'email' => 'kaprodi@siakad.test',
                'program_studi_id' => $prodi->id,
                'jenis_kelamin' => 'L',
                'status' => 'aktif',
                'jabatan_fungsional' => 'Lektor Kepala',
            ]);
            $this->command->info('Created Kaprodi Dosen');
        }

        // Create Additional Dosen
        $dosenData = [
            ['nama' => 'Dr. Budi Santoso, M.T.', 'nidn' => '0101018001', 'jk' => 'L'],
            ['nama' => 'Ir. Citra Dewi, M.Sc.', 'nidn' => '0515028201', 'jk' => 'P'],
            ['nama' => 'Drs. Eko Prasetyo, M.Kom.', 'nidn' => '0320037801', 'jk' => 'L'],
            ['nama' => 'Dr. Fitri Anggraini, M.T.', 'nidn' => '0610058501', 'jk' => 'P'],
            ['nama' => 'Ir. Gunawan Wibowo, M.Eng.', 'nidn' => '0825018101', 'jk' => 'L'],
        ];

        $dosens = [$kaprodiDosen];
        foreach ($dosenData as $d) {
            $existingDosen = Dosen::where('nidn', $d['nidn'])->first();
            if (!$existingDosen) {
                $user = User::create([
                    'name' => $d['nama'],
                    'email' => strtolower(str_replace([' ', '.', ','], '', explode(',', $d['nama'])[0])) . '@siakad.test',
                    'password' => Hash::make('password'),
                    'role' => 'dosen',
                ]);

                $existingDosen = Dosen::create([
                    'user_id' => $user->id,
                    'nidn' => $d['nidn'],
                    'nama' => $d['nama'],
                    'email' => $user->email,
                    'program_studi_id' => $prodi->id,
                    'jenis_kelamin' => $d['jk'],
                    'status' => 'aktif',
                ]);
            }
            $dosens[] = $existingDosen;
        }
        $this->command->info('Created ' . count($dosens) . ' Dosen');

        // Create Mata Kuliah
        $mataKuliahData = [
            ['kode' => 'TI101', 'nama' => 'Algoritma dan Pemrograman', 'sks' => 4, 'semester' => 1],
            ['kode' => 'TI102', 'nama' => 'Matematika Diskrit', 'sks' => 3, 'semester' => 1],
            ['kode' => 'TI103', 'nama' => 'Pengantar Teknologi Informasi', 'sks' => 2, 'semester' => 1],
            ['kode' => 'TI201', 'nama' => 'Struktur Data', 'sks' => 4, 'semester' => 2],
            ['kode' => 'TI202', 'nama' => 'Basis Data', 'sks' => 4, 'semester' => 2],
            ['kode' => 'TI203', 'nama' => 'Sistem Operasi', 'sks' => 3, 'semester' => 2],
            ['kode' => 'TI301', 'nama' => 'Pemrograman Web', 'sks' => 4, 'semester' => 3],
            ['kode' => 'TI302', 'nama' => 'Jaringan Komputer', 'sks' => 3, 'semester' => 3],
            ['kode' => 'TI303', 'nama' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'semester' => 3],
            ['kode' => 'TI401', 'nama' => 'Pemrograman Mobile', 'sks' => 4, 'semester' => 4],
            ['kode' => 'TI402', 'nama' => 'Kecerdasan Buatan', 'sks' => 3, 'semester' => 4],
            ['kode' => 'TI403', 'nama' => 'Keamanan Sistem Informasi', 'sks' => 3, 'semester' => 4],
            ['kode' => 'TI501', 'nama' => 'Machine Learning', 'sks' => 3, 'semester' => 5],
            ['kode' => 'TI502', 'nama' => 'Cloud Computing', 'sks' => 3, 'semester' => 5],
            ['kode' => 'TI601', 'nama' => 'Proyek Perangkat Lunak', 'sks' => 4, 'semester' => 6],
            ['kode' => 'TI701', 'nama' => 'Kerja Praktek', 'sks' => 2, 'semester' => 7],
            ['kode' => 'TI801', 'nama' => 'Tugas Akhir', 'sks' => 6, 'semester' => 8],
        ];

        $mataKuliahs = [];
        foreach ($mataKuliahData as $mk) {
            $existingMk = MataKuliah::where('kode', $mk['kode'])->where('program_studi_id', $prodi->id)->first();
            if (!$existingMk) {
                $existingMk = MataKuliah::create([
                    'kode' => $mk['kode'],
                    'nama' => $mk['nama'],
                    'sks' => $mk['sks'],
                    'semester' => $mk['semester'],
                    'program_studi_id' => $prodi->id,
                    'jenis' => 'wajib',
                ]);

                // Attach to kurikulum
                try {
                    $kurikulum->mataKuliah()->attach($existingMk->id, [
                        'semester_rekomendasi' => $mk['semester'],
                        'kategori' => 'wajib',
                    ]);
                } catch (\Exception $e) {
                    // Already attached
                }
            }
            $mataKuliahs[] = $existingMk;
        }
        $this->command->info('Created ' . count($mataKuliahs) . ' Mata Kuliah');

        // Create Jadwal Kuliah
        $ruanganAll = Ruangan::all();
        $haris = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jams = ['08:00', '10:00', '13:00', '15:00'];

        foreach ($mataKuliahs as $index => $mk) {
            $existingJadwal = JadwalKuliah::where('mata_kuliah_id', $mk->id)
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->first();
            
            if (!$existingJadwal) {
                JadwalKuliah::create([
                    'mata_kuliah_id' => $mk->id,
                    'dosen_id' => $dosens[$index % count($dosens)]->id,
                    'ruangan_id' => $ruanganAll[$index % count($ruanganAll)]->id,
                    'tahun_akademik_id' => $tahunAkademik->id,
                    'kelas' => 'A',
                    'hari' => $haris[$index % count($haris)],
                    'jam_mulai' => $jams[$index % count($jams)],
                    'jam_selesai' => date('H:i', strtotime($jams[$index % count($jams)]) + 100 * 60),
                    'kuota' => 40,
                ]);
            }
        }
        $this->command->info('Created Jadwal Kuliah');

        // Create Mahasiswa
        $mahasiswaData = [];
        $angkatans = [2021, 2022, 2023, 2024];
        $statuses = ['Aktif', 'Aktif', 'Aktif', 'Aktif', 'Aktif', 'Aktif', 'Cuti'];
        $namaDepan = ['Andi', 'Budi', 'Citra', 'Dian', 'Eka', 'Fajar', 'Gita', 'Hadi', 'Indah', 'Joko', 'Kartika', 'Lina', 'Mahmud', 'Nina', 'Oscar', 'Putri', 'Qori', 'Rini', 'Santi', 'Tono'];
        $namaBelakang = ['Pratama', 'Wijaya', 'Kusuma', 'Sari', 'Putra', 'Dewi', 'Santoso', 'Lestari', 'Hidayat', 'Wulandari'];

        $counter = Mahasiswa::count();
        $baseCounter = $counter;
        foreach ($angkatans as $angkatan) {
            for ($i = 0; $i < 15; $i++) {
                $counter++;
                $nim = $angkatan . str_pad($counter, 4, '0', STR_PAD_LEFT);
                
                $existingMhs = Mahasiswa::where('nim', $nim)->first();
                if (!$existingMhs) {
                    $nama = $namaDepan[array_rand($namaDepan)] . ' ' . $namaBelakang[array_rand($namaBelakang)];
                    $jk = rand(0, 1) ? 'L' : 'P';
                    $status = $statuses[array_rand($statuses)];
                    
                    // Check if email already exists
                    $email = strtolower($nim) . '@student.siakad.test';
                    $existingUser = User::where('email', $email)->first();
                    
                    if ($existingUser) {
                        // Use existing user
                        $user = $existingUser;
                    } else {
                        // Create user
                        $user = User::create([
                            'name' => $nama,
                            'email' => $email,
                            'password' => Hash::make('password'),
                            'role' => 'mahasiswa',
                        ]);
                    }

                    $mhs = Mahasiswa::create([
                        'user_id' => $user->id,
                        'nim' => $nim,
                        'nama' => $nama,
                        'email' => $user->email,
                        'program_studi_id' => $prodi->id,
                        'angkatan' => $angkatan,
                        'jenis_kelamin' => $jk,
                        'status' => $status,
                        'dosen_wali_id' => $dosens[array_rand($dosens)]->id,
                    ]);

                    $mahasiswaData[] = $mhs;
                }
            }
        }
        $this->command->info('Created ' . count($mahasiswaData) . ' Mahasiswa');

        // Get all mahasiswa
        $allMahasiswa = Mahasiswa::where('program_studi_id', $prodi->id)->get();
        $jadwalKuliahs = JadwalKuliah::where('tahun_akademik_id', $tahunAkademik->id)->get();

        // Create KRS for active students
        $krsStatuses = ['Disetujui', 'Disetujui', 'Disetujui', 'Pending', 'Ditolak'];
        foreach ($allMahasiswa->where('status', 'Aktif') as $mhs) {
            // Ambil 5-7 mata kuliah random
            $selectedJadwals = $jadwalKuliahs->random(min(rand(5, 7), $jadwalKuliahs->count()));
            
            foreach ($selectedJadwals as $jadwal) {
                $existingKrs = Krs::where('mahasiswa_id', $mhs->id)
                    ->where('jadwal_kuliah_id', $jadwal->id)
                    ->where('tahun_akademik_id', $tahunAkademik->id)
                    ->first();

                if (!$existingKrs) {
                    $krs = Krs::create([
                        'mahasiswa_id' => $mhs->id,
                        'jadwal_kuliah_id' => $jadwal->id,
                        'tahun_akademik_id' => $tahunAkademik->id,
                        'status' => $krsStatuses[array_rand($krsStatuses)],
                    ]);

                    // Create Nilai for approved KRS
                    if ($krs->status == 'Disetujui') {
                        $nilaiAngka = rand(50, 100);
                        $nilaiHuruf = $this->getNilaiHuruf($nilaiAngka);
                        
                        Nilai::create([
                            'krs_id' => $krs->id,
                            'mahasiswa_id' => $mhs->id,
                            'mata_kuliah_id' => $jadwal->mata_kuliah_id,
                            'nilai_angka' => $nilaiAngka,
                            'nilai_huruf' => $nilaiHuruf,
                            'nilai_tugas' => rand(60, 100),
                            'nilai_uts' => rand(50, 100),
                            'nilai_uas' => rand(50, 100),
                        ]);
                    }
                }
            }
        }
        $this->command->info('Created KRS and Nilai');

        // Create Tugas Akhir
        $taStatuses = ['draft', 'diajukan', 'judul_disetujui', 'bimbingan', 'seminar', 'sidang', 'selesai'];
        $judulTA = [
            'Sistem Informasi Manajemen Perpustakaan Berbasis Web',
            'Aplikasi Mobile E-Commerce dengan Flutter',
            'Implementasi Machine Learning untuk Prediksi Penjualan',
            'Sistem Deteksi Objek menggunakan YOLO',
            'Analisis Sentimen Media Sosial dengan NLP',
            'Aplikasi IoT untuk Smart Home',
            'Sistem Rekomendasi Film dengan Collaborative Filtering',
            'Chatbot Customer Service dengan AI',
            'Blockchain untuk Sistem Voting Online',
            'Aplikasi AR untuk Pembelajaran Interaktif',
        ];

        $seniorMhs = $allMahasiswa->whereIn('angkatan', [2021, 2022])->where('status', 'Aktif')->take(15);
        foreach ($seniorMhs as $index => $mhs) {
            $existingTA = TugasAkhir::where('mahasiswa_id', $mhs->id)->first();
            if (!$existingTA) {
                TugasAkhir::create([
                    'mahasiswa_id' => $mhs->id,
                    'judul' => $judulTA[$index % count($judulTA)] . ' - ' . $mhs->nama,
                    'pembimbing_1_id' => $dosens[array_rand($dosens)]->id,
                    'pembimbing_2_id' => $dosens[array_rand($dosens)]->id,
                    'status' => $taStatuses[array_rand($taStatuses)],
                    'tanggal_pengajuan' => now()->subDays(rand(1, 180)),
                ]);
            }
        }
        $this->command->info('Created Tugas Akhir');

        // Create Cuti Akademik
        $cutiStatuses = ['Pending', 'Disetujui Kaprodi', 'Disetujui', 'Ditolak'];
        $cutiAlasan = [
            'Alasan kesehatan',
            'Masalah keuangan',
            'Urusan keluarga',
            'Bekerja sementara',
        ];
        
        $cutiMhs = $allMahasiswa->where('status', 'Cuti');
        foreach ($cutiMhs as $mhs) {
            $existingCuti = CutiAkademik::where('mahasiswa_id', $mhs->id)->first();
            if (!$existingCuti) {
                CutiAkademik::create([
                    'mahasiswa_id' => $mhs->id,
                    'tahun_akademik_id' => $tahunAkademik->id,
                    'alasan' => $cutiAlasan[array_rand($cutiAlasan)],
                    'status' => $cutiStatuses[array_rand($cutiStatuses)],
                    'tanggal_pengajuan' => now()->subDays(rand(1, 60)),
                ]);
            }
        }
        $this->command->info('Created Cuti Akademik');

        // Create Bimbingan Akademik
        $bimbinganTopics = [
            'Konsultasi pemilihan mata kuliah',
            'Konsultasi karir dan magang',
            'Konsultasi akademik - IPK rendah',
            'Konsultasi persiapan tugas akhir',
            'Konsultasi rencana studi',
            'Konsultasi masalah perkuliahan',
        ];
        $bimbinganStatuses = ['pending', 'dijadwalkan', 'selesai', 'dibatalkan'];

        foreach ($allMahasiswa->where('status', 'Aktif')->take(30) as $mhs) {
            if ($mhs->dosen_wali_id) {
                $existingBimbingan = BimbinganAkademik::where('mahasiswa_id', $mhs->id)->first();
                if (!$existingBimbingan) {
                    BimbinganAkademik::create([
                        'mahasiswa_id' => $mhs->id,
                        'dosen_id' => $mhs->dosen_wali_id,
                        'topik' => $bimbinganTopics[array_rand($bimbinganTopics)],
                        'catatan' => 'Catatan bimbingan untuk ' . $mhs->nama,
                        'tanggal' => now()->subDays(rand(1, 30)),
                        'status' => $bimbinganStatuses[array_rand($bimbinganStatuses)],
                    ]);
                }
            }
        }
        $this->command->info('Created Bimbingan Akademik');

        // Create Absensi (using krs_id as per database schema)
        foreach ($jadwalKuliahs->take(10) as $jadwal) {
            $krsList = Krs::where('jadwal_kuliah_id', $jadwal->id)
                ->where('status', 'Disetujui')
                ->get();

            for ($pertemuan = 1; $pertemuan <= rand(8, 14); $pertemuan++) {
                foreach ($krsList as $krs) {
                    $existingAbsensi = Absensi::where('krs_id', $krs->id)
                        ->where('pertemuan', $pertemuan)
                        ->first();

                    if (!$existingAbsensi) {
                        $statusAbsensi = ['Hadir', 'Hadir', 'Hadir', 'Hadir', 'Hadir', 'Hadir', 'Izin', 'Sakit', 'Alpha'];
                        Absensi::create([
                            'krs_id' => $krs->id,
                            'pertemuan' => $pertemuan,
                            'tanggal' => now()->subWeeks(14 - $pertemuan),
                            'status' => $statusAbsensi[array_rand($statusAbsensi)],
                            'materi' => 'Materi pertemuan ke-' . $pertemuan,
                        ]);
                    }
                }
            }
        }
        $this->command->info('Created Absensi');

        // ==================================
        // WISUDA & YUDISIUM DATA
        // ==================================
        
        // Create Periode Wisuda
        $periodeWisuda = PeriodeWisuda::where('tahun_akademik_id', $tahunAkademik->id)->first();
        if (!$periodeWisuda) {
            $periodeWisuda = PeriodeWisuda::create([
                'tahun_akademik_id' => $tahunAkademik->id,
                'nama' => 'Wisuda Periode I Tahun 2024',
                'tanggal_wisuda' => now()->addMonths(2),
                'tanggal_buka_pendaftaran' => now()->subDays(30),
                'tanggal_tutup_pendaftaran' => now()->addDays(30),
                'tanggal_yudisium' => now()->addMonths(1),
                'lokasi' => 'Gedung Auditorium Kampus',
                'kuota' => 500,
                'biaya_wisuda' => 1500000,
                'persyaratan' => 'Bebas tanggungan perpustakaan, bebas keuangan, foto formal',
                'status' => PeriodeWisuda::STATUS_DIBUKA,
            ]);
            $this->command->info('Created Periode Wisuda');
        }

        // Get senior students (2021) for wisuda
        $mahasiswaWisuda = Mahasiswa::where('program_studi_id', $prodi->id)
            ->where('angkatan', 2021)
            ->where('status', 'Aktif')
            ->take(15)
            ->get();

        $pendaftaranCount = 0;
        $yudisiumCount = 0;
        $statusList = [
            PendaftaranWisuda::STATUS_PENDING,
            PendaftaranWisuda::STATUS_VERIFIKASI,
            PendaftaranWisuda::STATUS_LOLOS_YUDISIUM,
            PendaftaranWisuda::STATUS_LULUS,
        ];

        foreach ($mahasiswaWisuda as $index => $mhs) {
            // Check existing
            $existingPendaftaran = PendaftaranWisuda::where('mahasiswa_id', $mhs->id)
                ->where('periode_wisuda_id', $periodeWisuda->id)
                ->first();
            
            if (!$existingPendaftaran) {
                $ipk = rand(280, 385) / 100; // 2.80 - 3.85
                $totalSks = rand(140, 148);
                $status = $statusList[$index % count($statusList)];
                
                $pendaftaran = PendaftaranWisuda::create([
                    'mahasiswa_id' => $mhs->id,
                    'periode_wisuda_id' => $periodeWisuda->id,
                    'tanggal_daftar' => now()->subDays(rand(1, 25)),
                    'ipk' => $ipk,
                    'total_sks' => $totalSks,
                    'judul_skripsi' => 'Implementasi Sistem Informasi ' . ['Akademik', 'Keuangan', 'Perpustakaan', 'Kepegawaian', 'E-Commerce'][rand(0, 4)] . ' Berbasis Web',
                    'tanggal_lulus_sidang' => now()->subDays(rand(30, 60)),
                    'status' => $status,
                ]);
                $pendaftaranCount++;

                // Create Yudisium for approved registrations
                if (in_array($status, [PendaftaranWisuda::STATUS_LOLOS_YUDISIUM, PendaftaranWisuda::STATUS_LULUS])) {
                    $masaStudi = (now()->year - 2021) * 12 + rand(0, 11); // 36-48 bulan
                    $predikat = Yudisium::hitungPredikat($ipk, $masaStudi, $totalSks);
                    
                    Yudisium::create([
                        'pendaftaran_wisuda_id' => $pendaftaran->id,
                        'mahasiswa_id' => $mhs->id,
                        'tanggal_yudisium' => $periodeWisuda->tanggal_yudisium,
                        'ipk_akhir' => $ipk,
                        'total_sks_lulus' => $totalSks,
                        'predikat' => $predikat,
                        'tanggal_masuk' => $mhs->created_at ?? now()->subYears(4),
                        'tanggal_lulus' => now()->subDays(rand(10, 30)),
                        'masa_studi_bulan' => $masaStudi,
                        'no_ijazah' => $status === PendaftaranWisuda::STATUS_LULUS ? 'IJZ/' . date('Y') . '/' . str_pad($index + 1, 5, '0', STR_PAD_LEFT) : null,
                        'no_transkrip' => $status === PendaftaranWisuda::STATUS_LULUS ? 'TRS/' . date('Y') . '/' . str_pad($index + 1, 5, '0', STR_PAD_LEFT) : null,
                        'status' => $status === PendaftaranWisuda::STATUS_LULUS ? Yudisium::STATUS_DISETUJUI : Yudisium::STATUS_PENDING,
                    ]);
                    $yudisiumCount++;
                }
            }
        }
        $this->command->info("Created {$pendaftaranCount} Pendaftaran Wisuda, {$yudisiumCount} Yudisium");

        // ==================================
        // EDOM DATA
        // ==================================
        
        // Create Periode EDOM
        $periodeEdom = PeriodeEdom::where('tahun_akademik_id', $tahunAkademik->id)->first();
        if (!$periodeEdom) {
            $periodeEdom = PeriodeEdom::create([
                'nama' => 'EDOM Semester Ganjil 2024/2025',
                'tahun_akademik_id' => $tahunAkademik->id,
                'tanggal_mulai' => now()->subDays(14),
                'tanggal_selesai' => now()->addDays(14),
                'status' => 'aktif',
                'deskripsi' => 'Evaluasi Dosen Oleh Mahasiswa untuk semester Ganjil 2024/2025',
            ]);
            $this->command->info('Created Periode EDOM');
        }

        // Create Pertanyaan EDOM
        $pertanyaanEdom = PertanyaanEdom::count();
        if ($pertanyaanEdom == 0) {
            $pertanyaans = [
                // Pedagogik
                ['kode' => 'PED01', 'pertanyaan' => 'Dosen menjelaskan materi dengan jelas dan sistematis', 'kategori' => 'kompetensi_pedagogik', 'urutan' => 1],
                ['kode' => 'PED02', 'pertanyaan' => 'Dosen memberikan contoh yang relevan dengan materi', 'kategori' => 'kompetensi_pedagogik', 'urutan' => 2],
                ['kode' => 'PED03', 'pertanyaan' => 'Dosen menggunakan metode pembelajaran yang efektif', 'kategori' => 'kompetensi_pedagogik', 'urutan' => 3],
                
                // Profesional
                ['kode' => 'PRO01', 'pertanyaan' => 'Dosen menguasai materi yang diajarkan', 'kategori' => 'kompetensi_profesional', 'urutan' => 4],
                ['kode' => 'PRO02', 'pertanyaan' => 'Dosen memberikan referensi yang up-to-date', 'kategori' => 'kompetensi_profesional', 'urutan' => 5],
                ['kode' => 'PRO03', 'pertanyaan' => 'Dosen mampu menjawab pertanyaan mahasiswa dengan baik', 'kategori' => 'kompetensi_profesional', 'urutan' => 6],
                
                // Kepribadian
                ['kode' => 'KEP01', 'pertanyaan' => 'Dosen bersikap sopan dan ramah', 'kategori' => 'kompetensi_kepribadian', 'urutan' => 7],
                ['kode' => 'KEP02', 'pertanyaan' => 'Dosen disiplin dalam mengajar', 'kategori' => 'kompetensi_kepribadian', 'urutan' => 8],
                ['kode' => 'KEP03', 'pertanyaan' => 'Dosen bersikap adil kepada semua mahasiswa', 'kategori' => 'kompetensi_kepribadian', 'urutan' => 9],
                
                // Sosial
                ['kode' => 'SOS01', 'pertanyaan' => 'Dosen mudah dihubungi untuk konsultasi', 'kategori' => 'kompetensi_sosial', 'urutan' => 10],
                ['kode' => 'SOS02', 'pertanyaan' => 'Dosen memberikan motivasi kepada mahasiswa', 'kategori' => 'kompetensi_sosial', 'urutan' => 11],
                ['kode' => 'SOS03', 'pertanyaan' => 'Dosen berkomunikasi dengan baik', 'kategori' => 'kompetensi_sosial', 'urutan' => 12],
            ];

            foreach ($pertanyaans as $p) {
                PertanyaanEdom::create([
                    'kode' => $p['kode'],
                    'pertanyaan' => $p['pertanyaan'],
                    'kategori' => $p['kategori'],
                    'urutan' => $p['urutan'],
                    'is_active' => true,
                ]);
            }
            $this->command->info('Created Pertanyaan EDOM');
        }

        // Create Jawaban EDOM & Rekap
        $pertanyaans = PertanyaanEdom::where('is_active', true)->get();
        $jawabanCount = 0;
        $rekapCount = 0;

        // Get jadwal from prodi
        $jadwalProdi = JadwalKuliah::whereHas('mataKuliah', function($q) use ($prodi) {
                $q->where('program_studi_id', $prodi->id);
            })
            ->where('tahun_akademik_id', $tahunAkademik->id)
            ->with(['dosen', 'mataKuliah'])
            ->take(10)
            ->get();

        foreach ($jadwalProdi as $jadwal) {
            if (!$jadwal->dosen_id) continue;
            
            // Get mahasiswa who enrolled in this jadwal
            $mahasiswaKrs = Krs::where('jadwal_kuliah_id', $jadwal->id)
                ->where('status', 'Disetujui')
                ->with('mahasiswa')
                ->take(20)
                ->get();

            $jawabanPerJadwal = 0;
            foreach ($mahasiswaKrs as $krs) {
                // Check if already filled
                $existingJawaban = JawabanEdom::where('periode_edom_id', $periodeEdom->id)
                    ->where('mahasiswa_id', $krs->mahasiswa_id)
                    ->where('jadwal_kuliah_id', $jadwal->id)
                    ->exists();
                
                if (!$existingJawaban && rand(1, 100) <= 70) { // 70% response rate
                    foreach ($pertanyaans as $pertanyaan) {
                        JawabanEdom::create([
                            'periode_edom_id' => $periodeEdom->id,
                            'mahasiswa_id' => $krs->mahasiswa_id,
                            'jadwal_kuliah_id' => $jadwal->id,
                            'dosen_id' => $jadwal->dosen_id,
                            'pertanyaan_edom_id' => $pertanyaan->id,
                            'nilai' => rand(3, 5), // Random score 3-5
                        ]);
                        $jawabanCount++;
                    }
                    $jawabanPerJadwal++;
                }
            }

            // Create Rekap EDOM per jadwal
            if ($jawabanPerJadwal > 0) {
                $existingRekap = RekapEdom::where('periode_edom_id', $periodeEdom->id)
                    ->where('dosen_id', $jadwal->dosen_id)
                    ->where('jadwal_kuliah_id', $jadwal->id)
                    ->first();
                
                if (!$existingRekap) {
                    // Calculate averages
                    $jawaban = JawabanEdom::where('periode_edom_id', $periodeEdom->id)
                        ->where('dosen_id', $jadwal->dosen_id)
                        ->where('jadwal_kuliah_id', $jadwal->id)
                        ->join('pertanyaan_edom', 'jawaban_edom.pertanyaan_edom_id', '=', 'pertanyaan_edom.id')
                        ->selectRaw('pertanyaan_edom.kategori, AVG(jawaban_edom.nilai) as avg_nilai')
                        ->groupBy('pertanyaan_edom.kategori')
                        ->pluck('avg_nilai', 'kategori');

                    $pedagogik = $jawaban['kompetensi_pedagogik'] ?? rand(350, 450) / 100;
                    $profesional = $jawaban['kompetensi_profesional'] ?? rand(350, 450) / 100;
                    $kepribadian = $jawaban['kompetensi_kepribadian'] ?? rand(350, 450) / 100;
                    $sosial = $jawaban['kompetensi_sosial'] ?? rand(350, 450) / 100;
                    $total = ($pedagogik + $profesional + $kepribadian + $sosial) / 4;

                    RekapEdom::create([
                        'periode_edom_id' => $periodeEdom->id,
                        'dosen_id' => $jadwal->dosen_id,
                        'jadwal_kuliah_id' => $jadwal->id,
                        'rata_rata_pedagogik' => $pedagogik,
                        'rata_rata_profesional' => $profesional,
                        'rata_rata_kepribadian' => $kepribadian,
                        'rata_rata_sosial' => $sosial,
                        'rata_rata_total' => $total,
                        'jumlah_responden' => $jawabanPerJadwal,
                    ]);
                    $rekapCount++;
                }
            }
        }
        $this->command->info("Created {$jawabanCount} Jawaban EDOM, {$rekapCount} Rekap EDOM");

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('Kaprodi Dummy Data seeding completed!');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('Login sebagai Kaprodi:');
        $this->command->info('Email: kaprodi@siakad.test');
        $this->command->info('Password: password');
        $this->command->info('');
    }

    /**
     * Convert nilai angka to nilai huruf
     */
    private function getNilaiHuruf($nilai): string
    {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 80) return 'A-';
        if ($nilai >= 75) return 'B+';
        if ($nilai >= 70) return 'B';
        if ($nilai >= 65) return 'B-';
        if ($nilai >= 60) return 'C+';
        if ($nilai >= 55) return 'C';
        if ($nilai >= 50) return 'D';
        return 'E';
    }
}
