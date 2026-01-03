<?php

namespace Database\Seeders;

use App\Models\JadwalKuliah;
use App\Models\JadwalPengganti;
use App\Models\Mahasiswa;
use App\Models\PendaftaranWisuda;
use App\Models\PeriodeWisuda;
use App\Models\Ruangan;
use App\Models\TahunAkademik;
use App\Models\User;
use App\Models\Yudisium;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AdminAkademikDummySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Admin Akademik dummy data...');

        // Check dependencies
        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();
        $jadwalKuliah = JadwalKuliah::first();
        $ruangan = Ruangan::first();
        $mahasiswas = Mahasiswa::take(20)->get();
        $admin = User::where('role', 'admin')->first();

        if (!$tahunAkademik) {
            $this->command->warn('Tahun Akademik tidak tersedia. Skip seeding.');
            return;
        }

        // 1. Seed Jadwal Pengganti
        $this->seedJadwalPengganti($jadwalKuliah, $ruangan, $admin);

        // 2. Seed Periode Wisuda & Pendaftaran
        $this->seedWisuda($tahunAkademik, $mahasiswas, $admin);

        // 3. Seed Yudisium
        $this->seedYudisium($mahasiswas, $admin);

        $this->command->info('Admin Akademik dummy data seeded successfully!');
    }

    private function seedJadwalPengganti($jadwalKuliah, $ruangan, $admin): void
    {
        if (!$jadwalKuliah || !$ruangan) {
            $this->command->warn('Jadwal Kuliah atau Ruangan tidak tersedia. Skip Jadwal Pengganti.');
            return;
        }

        $this->command->info('Creating Jadwal Pengganti...');

        $jadwalKuliahList = JadwalKuliah::with(['mataKuliah', 'dosen'])->take(10)->get();
        $ruanganList = Ruangan::all();
        $alasanList = array_keys(JadwalPengganti::ALASAN_LIST);
        $statusList = ['Pending', 'Disetujui', 'Ditolak', 'Selesai'];

        $counter = JadwalPengganti::count();

        foreach ($jadwalKuliahList as $index => $jadwal) {
            $counter++;
            $tanggalAsli = Carbon::now()->subDays(rand(1, 30));
            $tanggalPengganti = Carbon::now()->addDays(rand(1, 30));
            $status = $statusList[array_rand($statusList)];

            $data = [
                'jadwal_kuliah_id' => $jadwal->id,
                'tanggal_asli' => $tanggalAsli,
                'tanggal_pengganti' => $tanggalPengganti,
                'jam_mulai' => $jadwal->jam_mulai ?? '08:00',
                'jam_selesai' => $jadwal->jam_selesai ?? '10:00',
                'ruangan_id' => $ruanganList->random()->id,
                'alasan' => $alasanList[array_rand($alasanList)],
                'keterangan' => 'Keterangan jadwal pengganti #' . $counter,
                'status' => $status,
                'diajukan_oleh' => $admin?->id,
            ];

            if (in_array($status, ['Disetujui', 'Ditolak', 'Selesai'])) {
                $data['disetujui_oleh'] = $admin?->id;
                $data['tanggal_persetujuan'] = Carbon::now()->subDays(rand(1, 5));
            }

            JadwalPengganti::create($data);
        }

        $this->command->info('Created ' . $counter . ' Jadwal Pengganti records.');
    }

    private function seedWisuda($tahunAkademik, $mahasiswas, $admin): void
    {
        $this->command->info('Creating Periode Wisuda & Pendaftaran...');

        // Create Periode Wisuda
        $periodeWisudaData = [
            [
                'tahun_akademik_id' => $tahunAkademik->id,
                'nama' => 'Wisuda Periode I Tahun ' . date('Y'),
                'tanggal_wisuda' => Carbon::now()->addMonths(2),
                'tanggal_buka_pendaftaran' => Carbon::now()->subDays(30),
                'tanggal_tutup_pendaftaran' => Carbon::now()->addDays(30),
                'tanggal_yudisium' => Carbon::now()->addMonths(1),
                'lokasi' => 'Gedung Auditorium Utama',
                'kuota' => 500,
                'biaya_wisuda' => 1500000,
                'persyaratan' => 'Bebas administrasi keuangan, Bebas perpustakaan, Lulus sidang skripsi',
                'status' => 'Dibuka',
            ],
            [
                'tahun_akademik_id' => $tahunAkademik->id,
                'nama' => 'Wisuda Periode II Tahun ' . date('Y'),
                'tanggal_wisuda' => Carbon::now()->addMonths(6),
                'tanggal_buka_pendaftaran' => Carbon::now()->addMonths(3),
                'tanggal_tutup_pendaftaran' => Carbon::now()->addMonths(5),
                'tanggal_yudisium' => Carbon::now()->addMonths(5)->addDays(15),
                'lokasi' => 'Gedung Auditorium Utama',
                'kuota' => 500,
                'biaya_wisuda' => 1500000,
                'persyaratan' => 'Bebas administrasi keuangan, Bebas perpustakaan, Lulus sidang skripsi',
                'status' => 'Draft',
            ],
            [
                'tahun_akademik_id' => $tahunAkademik->id,
                'nama' => 'Wisuda Periode III Tahun ' . (date('Y') - 1),
                'tanggal_wisuda' => Carbon::now()->subMonths(3),
                'tanggal_buka_pendaftaran' => Carbon::now()->subMonths(6),
                'tanggal_tutup_pendaftaran' => Carbon::now()->subMonths(4),
                'tanggal_yudisium' => Carbon::now()->subMonths(4)->addDays(15),
                'lokasi' => 'Gedung Auditorium Utama',
                'kuota' => 450,
                'biaya_wisuda' => 1500000,
                'persyaratan' => 'Bebas administrasi keuangan, Bebas perpustakaan, Lulus sidang skripsi',
                'status' => 'Selesai',
            ],
        ];

        $periodeWisuda = [];
        foreach ($periodeWisudaData as $data) {
            $periodeWisuda[] = PeriodeWisuda::create($data);
        }

        $this->command->info('Created ' . count($periodeWisuda) . ' Periode Wisuda records.');

        // Create Pendaftaran Wisuda
        if ($mahasiswas->isEmpty()) {
            $this->command->warn('Mahasiswa tidak tersedia. Skip Pendaftaran Wisuda.');
            return;
        }

        $statusPendaftaran = ['Pending', 'Verifikasi Berkas', 'Lolos Yudisium', 'Ditolak', 'Lulus', 'Batal'];
        $judulSkripsiList = [
            'Analisis Pengaruh Media Sosial Terhadap Perilaku Konsumen di Era Digital',
            'Implementasi Machine Learning untuk Prediksi Harga Saham',
            'Pengembangan Aplikasi Mobile Learning Berbasis Android',
            'Sistem Informasi Manajemen Perpustakaan Digital',
            'Analisis Sentimen Twitter Menggunakan Deep Learning',
            'Rancang Bangun E-Commerce dengan Metode Agile',
            'Penerapan Internet of Things pada Smart Home',
            'Optimasi Algoritma Genetika untuk Penjadwalan',
            'Sistem Pendukung Keputusan Pemilihan Karyawan Terbaik',
            'Implementasi Blockchain untuk Keamanan Data Akademik',
        ];

        $pendaftaranCount = 0;
        $mahasiswaUsed = [];

        foreach ($periodeWisuda as $periode) {
            // 5-8 pendaftar per periode
            $jumlahPendaftar = rand(5, min(8, $mahasiswas->count()));
            $availableMahasiswa = $mahasiswas->whereNotIn('id', $mahasiswaUsed)->take($jumlahPendaftar);

            foreach ($availableMahasiswa as $mhs) {
                $pendaftaranCount++;
                $mahasiswaUsed[] = $mhs->id;
                $status = $statusPendaftaran[array_rand($statusPendaftaran)];

                $data = [
                    'mahasiswa_id' => $mhs->id,
                    'periode_wisuda_id' => $periode->id,
                    'tanggal_daftar' => Carbon::now()->subDays(rand(5, 20)),
                    'ipk' => rand(275, 400) / 100,
                    'total_sks' => rand(140, 160),
                    'judul_skripsi' => $judulSkripsiList[array_rand($judulSkripsiList)],
                    'tanggal_lulus_sidang' => Carbon::now()->subDays(rand(30, 180)),
                    'foto_formal' => 'photos/wisuda_' . $mhs->nim . '.jpg',
                    'bukti_bebas_pustaka' => rand(0, 1) ? 'documents/bebas_pustaka_' . $mhs->nim . '.pdf' : null,
                    'bukti_bebas_keuangan' => rand(0, 1) ? 'documents/bebas_keuangan_' . $mhs->nim . '.pdf' : null,
                    'bukti_pembayaran_wisuda' => rand(0, 1) ? 'documents/pembayaran_wisuda_' . $mhs->nim . '.pdf' : null,
                    'status' => $status,
                    'catatan_verifikasi' => $status === 'Ditolak' ? 'Mohon lengkapi dokumen yang diperlukan' : null,
                ];

                if (in_array($status, ['Lolos Yudisium', 'Lulus', 'Ditolak'])) {
                    $data['diverifikasi_oleh'] = $admin?->id;
                    $data['tanggal_verifikasi'] = Carbon::now()->subDays(rand(1, 10));
                }

                PendaftaranWisuda::create($data);
            }
        }

        $this->command->info('Created ' . $pendaftaranCount . ' Pendaftaran Wisuda records.');
    }

    private function seedYudisium($mahasiswas, $admin): void
    {
        $this->command->info('Creating Yudisium...');

        // Get pendaftaran wisuda yang sudah Lolos Yudisium
        $pendaftaranLolos = PendaftaranWisuda::where('status', 'Lolos Yudisium')->get();

        if ($pendaftaranLolos->isEmpty()) {
            // Jika tidak ada, update beberapa pendaftaran menjadi Lolos Yudisium
            $pendaftaranUpdate = PendaftaranWisuda::take(5)->get();
            foreach ($pendaftaranUpdate as $p) {
                $p->update(['status' => 'Lolos Yudisium']);
            }
            $pendaftaranLolos = PendaftaranWisuda::where('status', 'Lolos Yudisium')->get();
        }

        $statusYudisium = ['Pending', 'Disetujui', 'Ditolak'];
        $counter = Yudisium::count();

        foreach ($pendaftaranLolos as $pendaftaran) {
            $counter++;
            $ipk = $pendaftaran->ipk ?? (rand(275, 400) / 100);
            $masaStudiBulan = rand(36, 60);
            $totalSks = $pendaftaran->total_sks ?? rand(140, 160);
            $predikat = Yudisium::hitungPredikat($ipk, $masaStudiBulan, $totalSks);
            $status = $statusYudisium[array_rand($statusYudisium)];
            
            $tanggalMasuk = Carbon::now()->subMonths($masaStudiBulan);
            $tanggalLulus = Carbon::now()->subDays(rand(30, 90));

            $data = [
                'pendaftaran_wisuda_id' => $pendaftaran->id,
                'mahasiswa_id' => $pendaftaran->mahasiswa_id,
                'tanggal_yudisium' => Carbon::now()->subDays(rand(1, 30)),
                'ipk_akhir' => $ipk,
                'total_sks_lulus' => $totalSks,
                'masa_studi_bulan' => $masaStudiBulan,
                'tanggal_masuk' => $tanggalMasuk,
                'tanggal_lulus' => $tanggalLulus,
                'predikat' => $predikat,
                'status' => $status,
                'catatan' => 'Proses yudisium mahasiswa dengan predikat ' . $predikat,
                'diproses_oleh' => $admin?->id,
                'tanggal_proses' => now(),
            ];

            if ($status === 'Disetujui') {
                $data['no_ijazah'] = 'IJZ/' . date('Y') . '/' . str_pad($counter, 5, '0', STR_PAD_LEFT);
                $data['no_transkrip'] = 'TRK/' . date('Y') . '/' . str_pad($counter, 5, '0', STR_PAD_LEFT);
            }

            Yudisium::create($data);
        }

        $this->command->info('Created ' . $counter . ' Yudisium records.');
    }
}
