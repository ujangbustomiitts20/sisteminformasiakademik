<?php

namespace Database\Seeders;

use App\Models\PeriodePmb;
use App\Models\GelombangPmb;
use App\Models\JalurSeleksi;
use App\Models\BiayaPendaftaran;
use App\Models\KuotaPmb;
use App\Models\SettingDokumenPmb;
use App\Models\CalonMahasiswa;
use App\Models\DokumenCamaba;
use App\Models\PembayaranPmb;
use App\Models\NilaiSeleksi;
use App\Models\HasilSeleksi;
use App\Models\DaftarUlang;
use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PmbDemoSeeder extends Seeder
{
    private $faker;
    private $noPembayaranCounter = 1;

    public function run(): void
    {
        $this->faker = \Faker\Factory::create('id_ID');
        $this->command->info('Memulai seeding data PMB...');

        // STEP 1: SETUP MASTER DATA
        $this->command->info('Step 1: Setup Master Data');

        // 1.1 Buat Periode PMB
        $periode = PeriodePmb::create([
            'nama' => 'PMB 2025/2026',
            'tahun_akademik' => '2025/2026',
            'tanggal_mulai' => '2025-01-01',
            'tanggal_selesai' => '2025-08-31',
            'is_active' => true,
            'deskripsi' => 'Penerimaan Mahasiswa Baru Tahun Akademik 2025/2026',
        ]);
        $this->command->info("   Periode: {$periode->nama}");

        // 1.2 Buat Jalur Seleksi
        $jalurReguler = JalurSeleksi::create([
            'kode' => 'REG',
            'nama' => 'Reguler',
            'deskripsi' => 'Jalur seleksi reguler dengan ujian tulis dan wawancara',
            'persyaratan' => "1. Lulusan SMA/SMK/MA sederajat\n2. Nilai rapor minimal 7.0\n3. Usia maksimal 25 tahun",
            'is_active' => true,
        ]);

        $jalurPrestasi = JalurSeleksi::create([
            'kode' => 'PRES',
            'nama' => 'Prestasi',
            'deskripsi' => 'Jalur seleksi berdasarkan prestasi akademik atau non-akademik',
            'persyaratan' => "1. Lulusan SMA/SMK/MA sederajat\n2. Memiliki prestasi tingkat kab/kota atau lebih tinggi",
            'is_active' => true,
        ]);

        $jalurBeasiswa = JalurSeleksi::create([
            'kode' => 'BEA',
            'nama' => 'Beasiswa',
            'deskripsi' => 'Jalur seleksi untuk penerima beasiswa penuh',
            'persyaratan' => "1. Lulusan SMA/SMK/MA sederajat\n2. Nilai rapor minimal 8.0",
            'is_active' => true,
        ]);
        $this->command->info("   Jalur Seleksi: Reguler, Prestasi, Beasiswa");

        // 1.3 Buat Gelombang PMB
        $gelombang1 = GelombangPmb::create([
            'periode_pmb_id' => $periode->id,
            'nama' => 'Gelombang 1',
            'nomor_gelombang' => 1,
            'tanggal_mulai_daftar' => '2025-01-01',
            'tanggal_selesai_daftar' => '2025-03-31',
            'tanggal_ujian' => '2025-04-15',
            'tanggal_pengumuman' => '2025-04-25',
            'tanggal_daftar_ulang_mulai' => '2025-04-26',
            'tanggal_daftar_ulang_selesai' => '2025-05-15',
            'is_active' => true,
        ]);

        $gelombang2 = GelombangPmb::create([
            'periode_pmb_id' => $periode->id,
            'nama' => 'Gelombang 2',
            'nomor_gelombang' => 2,
            'tanggal_mulai_daftar' => '2025-04-01',
            'tanggal_selesai_daftar' => '2025-06-30',
            'tanggal_ujian' => '2025-07-15',
            'tanggal_pengumuman' => '2025-07-25',
            'tanggal_daftar_ulang_mulai' => '2025-07-26',
            'tanggal_daftar_ulang_selesai' => '2025-08-15',
            'is_active' => true,
        ]);
        $this->command->info("   Gelombang: Gelombang 1, Gelombang 2");

        $jalurs = [$jalurReguler, $jalurPrestasi, $jalurBeasiswa];

        // 1.4 Biaya Pendaftaran
        foreach ($jalurs as $jalur) {
            BiayaPendaftaran::create([
                'gelombang_pmb_id' => $gelombang1->id,
                'jalur_seleksi_id' => $jalur->id,
                'biaya_formulir' => 150000,
                'biaya_ujian' => 100000,
                'total_biaya' => 250000,
            ]);
            BiayaPendaftaran::create([
                'gelombang_pmb_id' => $gelombang2->id,
                'jalur_seleksi_id' => $jalur->id,
                'biaya_formulir' => 175000,
                'biaya_ujian' => 125000,
                'total_biaya' => 300000,
            ]);
        }
        $this->command->info("   Biaya Pendaftaran: Gel 1 = Rp 250.000, Gel 2 = Rp 300.000");

        // 1.5 Kuota per Prodi
        $prodis = ProgramStudi::take(4)->get();
        if ($prodis->isEmpty()) {
            $this->command->warn("   Tidak ada Program Studi, selesai.");
            return;
        }

        foreach ($prodis as $prodi) {
            foreach ($jalurs as $jalur) {
                KuotaPmb::create([
                    'gelombang_pmb_id' => $gelombang1->id,
                    'program_studi_id' => $prodi->id,
                    'jalur_seleksi_id' => $jalur->id,
                    'kuota' => 30,
                    'terisi' => 0,
                ]);
                KuotaPmb::create([
                    'gelombang_pmb_id' => $gelombang2->id,
                    'program_studi_id' => $prodi->id,
                    'jalur_seleksi_id' => $jalur->id,
                    'kuota' => 20,
                    'terisi' => 0,
                ]);
            }
        }
        $this->command->info("   Kuota: Gel 1 = 30/prodi, Gel 2 = 20/prodi");

        // 1.6 Setting Dokumen
        $this->seedSettingDokumen();
        $this->command->info("   Setting Dokumen: 10 jenis dokumen");

        // STEP 2-8: BUAT CALON MAHASISWA
        $this->command->info('');
        $this->command->info('Membuat Calon Mahasiswa dengan berbagai status...');

        // GROUP A: Status DRAFT (5 orang)
        $this->command->info('   Group A: Status DRAFT (5 orang)');
        for ($i = 1; $i <= 5; $i++) {
            $this->createCamaba($gelombang1, $jalurs, $prodis, '0001' . str_pad($i, 2, '0', STR_PAD_LEFT), 'draft');
        }

        // GROUP B: Status MENUNGGU_BAYAR (5 orang)
        $this->command->info('   Group B: Status MENUNGGU_BAYAR (5 orang)');
        for ($i = 1; $i <= 5; $i++) {
            $camaba = $this->createCamaba($gelombang1, $jalurs, $prodis, '0002' . str_pad($i, 2, '0', STR_PAD_LEFT), 'menunggu_bayar');
            $this->createPembayaran($camaba, 'pendaftaran', 250000, 'pending');
        }

        // GROUP C: Status TERDAFTAR (5 orang)
        $this->command->info('   Group C: Status TERDAFTAR (5 orang)');
        for ($i = 1; $i <= 5; $i++) {
            $camaba = $this->createCamaba($gelombang1, $jalurs, $prodis, '0003' . str_pad($i, 2, '0', STR_PAD_LEFT), 'terdaftar', [
                'is_bayar_pendaftaran' => true,
                'tanggal_bayar' => now()->subDays(rand(1, 10)),
            ]);
            $this->createPembayaran($camaba, 'pendaftaran', 250000, 'terverifikasi', rand(1, 10));
        }

        // GROUP D: Status TERDAFTAR + Dokumen Pending (5 orang)
        $this->command->info('   Group D: Status TERDAFTAR + Dokumen Pending (5 orang)');
        for ($i = 1; $i <= 5; $i++) {
            $camaba = $this->createCamaba($gelombang1, $jalurs, $prodis, '0004' . str_pad($i, 2, '0', STR_PAD_LEFT), 'terdaftar', [
                'is_bayar_pendaftaran' => true,
                'tanggal_bayar' => now()->subDays(rand(5, 15)),
            ]);
            $this->createPembayaran($camaba, 'pendaftaran', 250000, 'terverifikasi', rand(5, 15));
            $this->uploadDokumen($camaba, ['kk', 'ijazah', 'foto'], 'pending');
        }

        // GROUP E: Status TERDAFTAR + Dokumen Lengkap (5 orang)
        $this->command->info('   Group E: Status TERDAFTAR + Dokumen Lengkap (5 orang)');
        for ($i = 1; $i <= 5; $i++) {
            $camaba = $this->createCamaba($gelombang1, $jalurs, $prodis, '0005' . str_pad($i, 2, '0', STR_PAD_LEFT), 'terdaftar', [
                'is_bayar_pendaftaran' => true,
                'is_dokumen_lengkap' => true,
                'tanggal_bayar' => now()->subDays(rand(10, 20)),
            ]);
            $this->createPembayaran($camaba, 'pendaftaran', 250000, 'terverifikasi', rand(10, 20));
            $this->uploadDokumen($camaba, ['kk', 'akta', 'ijazah', 'foto'], 'valid');
        }

        // GROUP F: Status MENGIKUTI_UJIAN (10 orang)
        $this->command->info('   Group F: Status MENGIKUTI_UJIAN (10 orang)');
        for ($i = 1; $i <= 10; $i++) {
            $camaba = $this->createCamaba($gelombang1, $jalurs, $prodis, '0006' . str_pad($i, 2, '0', STR_PAD_LEFT), 'mengikuti_ujian', [
                'is_bayar_pendaftaran' => true,
                'is_dokumen_lengkap' => true,
                'tanggal_bayar' => now()->subDays(rand(15, 30)),
            ]);
            $this->createPembayaran($camaba, 'pendaftaran', 250000, 'terverifikasi', rand(15, 30));
            $this->uploadDokumen($camaba, ['kk', 'akta', 'ijazah', 'foto'], 'valid');
            $this->inputNilai($camaba);
        }

        // GROUP G: Status LULUS (5 orang)
        $this->command->info('   Group G: Status LULUS (5 orang)');
        for ($i = 1; $i <= 5; $i++) {
            $prodi = $prodis->random();
            $camaba = $this->createCamaba($gelombang1, $jalurs, $prodis, '0007' . str_pad($i, 2, '0', STR_PAD_LEFT), 'lulus', [
                'is_bayar_pendaftaran' => true,
                'is_dokumen_lengkap' => true,
                'tanggal_bayar' => now()->subDays(rand(20, 40)),
            ], $prodi->id);
            $this->createPembayaran($camaba, 'pendaftaran', 250000, 'terverifikasi', rand(20, 40));
            $this->uploadDokumen($camaba, ['kk', 'akta', 'ijazah', 'foto'], 'valid');
            $this->inputNilai($camaba);
            HasilSeleksi::create([
                'calon_mahasiswa_id' => $camaba->id,
                'gelombang_pmb_id' => $gelombang1->id,
                'nilai_total' => rand(75, 95),
                'ranking' => $i,
                'status' => 'lulus',
                'program_studi_diterima_id' => $prodi->id,
                'tanggal_pengumuman' => now()->subDays(5),
                'diproses_oleh' => 1,
            ]);
        }

        // GROUP H: Status TIDAK_LULUS (3 orang)
        $this->command->info('   Group H: Status TIDAK_LULUS (3 orang)');
        for ($i = 1; $i <= 3; $i++) {
            $camaba = $this->createCamaba($gelombang1, $jalurs, $prodis, '0008' . str_pad($i, 2, '0', STR_PAD_LEFT), 'tidak_lulus', [
                'is_bayar_pendaftaran' => true,
                'is_dokumen_lengkap' => true,
                'tanggal_bayar' => now()->subDays(rand(20, 40)),
            ]);
            $this->createPembayaran($camaba, 'pendaftaran', 250000, 'terverifikasi', rand(20, 40));
            $this->uploadDokumen($camaba, ['kk', 'akta', 'ijazah', 'foto'], 'valid');
            $this->inputNilai($camaba, true);
            HasilSeleksi::create([
                'calon_mahasiswa_id' => $camaba->id,
                'gelombang_pmb_id' => $gelombang1->id,
                'nilai_total' => rand(40, 55),
                'ranking' => 30 + $i,
                'status' => 'tidak_lulus',
                'program_studi_diterima_id' => null,
                'tanggal_pengumuman' => now()->subDays(5),
                'diproses_oleh' => 1,
            ]);
        }

        // GROUP I: Status DAFTAR_ULANG (3 orang)
        $this->command->info('   Group I: Status DAFTAR_ULANG (3 orang)');
        for ($i = 1; $i <= 3; $i++) {
            $prodi = $prodis->random();
            $camaba = $this->createCamaba($gelombang1, $jalurs, $prodis, '0009' . str_pad($i, 2, '0', STR_PAD_LEFT), 'daftar_ulang', [
                'is_bayar_pendaftaran' => true,
                'is_dokumen_lengkap' => true,
                'tanggal_bayar' => now()->subDays(rand(25, 45)),
            ], $prodi->id);
            $this->createPembayaran($camaba, 'pendaftaran', 250000, 'terverifikasi', rand(25, 45));
            $this->createPembayaran($camaba, 'daftar_ulang', 5000000, 'pending');
            $this->uploadDokumen($camaba, ['kk', 'akta', 'ijazah', 'foto'], 'valid');
            $this->inputNilai($camaba);
            HasilSeleksi::create([
                'calon_mahasiswa_id' => $camaba->id,
                'gelombang_pmb_id' => $gelombang1->id,
                'nilai_total' => rand(75, 90),
                'ranking' => $i,
                'status' => 'lulus',
                'program_studi_diterima_id' => $prodi->id,
                'tanggal_pengumuman' => now()->subDays(10),
                'diproses_oleh' => 1,
            ]);
            // Buat record DaftarUlang dengan status pending
            DaftarUlang::create([
                'calon_mahasiswa_id' => $camaba->id,
                'program_studi_id' => $prodi->id,
                'biaya_daftar_ulang' => 2500000,
                'biaya_ukt' => 2500000,
                'status' => 'pending',
                'tanggal_expired' => now()->addDays(14),
            ]);
        }

        // GROUP J: Status MENJADI_MAHASISWA (5 orang)
        $this->command->info('   Group J: Status MENJADI_MAHASISWA (5 orang)');
        for ($i = 1; $i <= 5; $i++) {
            $prodi = $prodis->random();
            $camaba = $this->createCamaba($gelombang1, $jalurs, $prodis, '0010' . str_pad($i, 2, '0', STR_PAD_LEFT), 'menjadi_mahasiswa', [
                'is_bayar_pendaftaran' => true,
                'is_dokumen_lengkap' => true,
                'tanggal_bayar' => now()->subDays(rand(30, 60)),
            ], $prodi->id);
            $this->createPembayaran($camaba, 'pendaftaran', 250000, 'terverifikasi', rand(30, 60));
            $this->createPembayaran($camaba, 'daftar_ulang', 5000000, 'terverifikasi', rand(5, 15));
            $this->uploadDokumen($camaba, ['kk', 'akta', 'ijazah', 'foto'], 'valid');
            $this->inputNilai($camaba);
            HasilSeleksi::create([
                'calon_mahasiswa_id' => $camaba->id,
                'gelombang_pmb_id' => $gelombang1->id,
                'nilai_total' => rand(80, 95),
                'ranking' => $i,
                'status' => 'lulus',
                'program_studi_diterima_id' => $prodi->id,
                'tanggal_pengumuman' => now()->subDays(20),
                'diproses_oleh' => 1,
            ]);
            // Buat record DaftarUlang dengan status selesai
            DaftarUlang::create([
                'calon_mahasiswa_id' => $camaba->id,
                'program_studi_id' => $prodi->id,
                'biaya_daftar_ulang' => 2500000,
                'biaya_ukt' => 2500000,
                'status' => 'selesai',
                'tanggal_bayar' => now()->subDays(rand(5, 15)),
                'tanggal_verifikasi' => now()->subDays(rand(3, 10)),
                'tanggal_expired' => now()->addDays(14),
                'diproses_oleh' => 1,
            ]);
        }

        $this->command->info('');
        $this->command->info('Seeding PMB selesai!');
        $this->command->info('Total Calon Mahasiswa: 51 orang');
    }

    private function createCamaba($gelombang, array $jalurs, $prodis, string $noSuffix, string $status, array $extra = [], ?int $prodiId = null): CalonMahasiswa
    {
        $jalur = $jalurs[array_rand($jalurs)];
        $prodi = $prodiId ? ProgramStudi::find($prodiId) : $prodis->random();

        return CalonMahasiswa::create(array_merge([
            'no_pendaftaran' => 'PMB' . date('Y') . $noSuffix,
            'gelombang_pmb_id' => $gelombang->id,
            'jalur_seleksi_id' => $jalur->id,
            'program_studi_id' => $prodi->id,
            'nama_lengkap' => $this->faker->name,
            'nik' => $this->faker->numerify('################'),
            'nisn' => $this->faker->numerify('##########'),
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'tempat_lahir' => $this->faker->city,
            'tanggal_lahir' => $this->faker->dateTimeBetween('-25 years', '-17 years')->format('Y-m-d'),
            'agama' => $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
            'email' => $this->faker->unique()->safeEmail,
            'no_hp' => $this->faker->numerify('08##########'),
            'alamat' => $this->faker->address,
            'rt' => str_pad(rand(1, 20), 3, '0', STR_PAD_LEFT),
            'rw' => str_pad(rand(1, 10), 3, '0', STR_PAD_LEFT),
            'kelurahan' => $this->faker->streetName,
            'kecamatan' => $this->faker->citySuffix,
            'kabupaten' => $this->faker->city,
            'provinsi' => $this->faker->state,
            'kode_pos' => $this->faker->postcode,
            'asal_sekolah' => 'SMA ' . $this->faker->city,
            'tahun_lulus' => $this->faker->randomElement([2023, 2024, 2025]),
            'nilai_rata_rata' => $this->faker->randomFloat(2, 70, 95),
            'nama_ayah' => $this->faker->name('male'),
            'pekerjaan_ayah' => $this->faker->jobTitle,
            'nama_ibu' => $this->faker->name('female'),
            'pekerjaan_ibu' => $this->faker->jobTitle,
            'penghasilan_ortu' => $this->faker->randomElement([2000000, 3500000, 5000000, 7500000, 10000000]),
            'password' => Hash::make('password'),
            'status_pendaftaran' => $status,
        ], $extra));
    }

    private function createPembayaran($camaba, string $jenis, int $jumlah, string $status, int $daysAgo = 0): PembayaranPmb
    {
        $noPembayaran = 'PAY' . date('Ymd') . str_pad($this->noPembayaranCounter++, 5, '0', STR_PAD_LEFT);

        $data = [
            'calon_mahasiswa_id' => $camaba->id,
            'no_pembayaran' => $noPembayaran,
            'jenis_pembayaran' => $jenis,
            'jumlah' => $jumlah,
            'status' => $status,
        ];

        if ($status === 'terverifikasi') {
            $data['metode_pembayaran'] = 'transfer';
            $data['bank'] = $this->faker->randomElement(['BCA', 'BNI', 'Mandiri', 'BRI']);
            $data['tanggal_bayar'] = now()->subDays($daysAgo);
            $data['verified_by'] = 1;
            $data['verified_at'] = now()->subDays(max(0, $daysAgo - 1));
        } elseif ($status === 'pending') {
            $data['tanggal_expired'] = now()->addDays(7);
        }

        return PembayaranPmb::create($data);
    }

    private function seedSettingDokumen(): void
    {
        $dokumen = [
            ['kode' => 'kk', 'nama_dokumen' => 'Kartu Keluarga', 'is_wajib' => true, 'urutan' => 1],
            ['kode' => 'akta', 'nama_dokumen' => 'Akta Kelahiran', 'is_wajib' => true, 'urutan' => 2],
            ['kode' => 'ijazah', 'nama_dokumen' => 'Ijazah/SKL', 'is_wajib' => true, 'urutan' => 3],
            ['kode' => 'foto', 'nama_dokumen' => 'Pas Foto 3x4', 'is_wajib' => true, 'urutan' => 4],
            ['kode' => 'ktp', 'nama_dokumen' => 'KTP', 'is_wajib' => false, 'urutan' => 5],
            ['kode' => 'skhun', 'nama_dokumen' => 'SKHUN', 'is_wajib' => false, 'urutan' => 6],
            ['kode' => 'rapor', 'nama_dokumen' => 'Rapor Semester 1-5', 'is_wajib' => false, 'urutan' => 7],
            ['kode' => 'surat_sehat', 'nama_dokumen' => 'Surat Keterangan Sehat', 'is_wajib' => false, 'urutan' => 8],
            ['kode' => 'skck', 'nama_dokumen' => 'SKCK', 'is_wajib' => false, 'urutan' => 9],
            ['kode' => 'sertifikat', 'nama_dokumen' => 'Sertifikat Prestasi', 'is_wajib' => false, 'urutan' => 10],
        ];

        foreach ($dokumen as $d) {
            SettingDokumenPmb::updateOrCreate(
                ['kode' => $d['kode']],
                array_merge($d, [
                    'deskripsi' => 'Dokumen ' . $d['nama_dokumen'],
                    'is_active' => true,
                ])
            );
        }
    }

    private function uploadDokumen($camaba, array $jenisDokumen, string $status): void
    {
        foreach ($jenisDokumen as $jenis) {
            DokumenCamaba::create([
                'calon_mahasiswa_id' => $camaba->id,
                'jenis_dokumen' => $jenis,
                'nama_file' => $jenis . '_' . $camaba->id . '.pdf',
                'path_file' => 'dokumen_pmb/' . $jenis . '_' . $camaba->id . '.pdf',
                'status_verifikasi' => $status,
                'verified_by' => $status !== 'pending' ? 1 : null,
                'verified_at' => $status !== 'pending' ? now() : null,
            ]);
        }
    }

    private function inputNilai($camaba, bool $lowScore = false): void
    {
        $komponenNilai = [
            'Nilai Rapor' => ['bobot' => 0.30, 'min' => $lowScore ? 50 : 70, 'max' => $lowScore ? 65 : 95],
            'Ujian Tulis' => ['bobot' => 0.40, 'min' => $lowScore ? 40 : 65, 'max' => $lowScore ? 60 : 95],
            'Wawancara' => ['bobot' => 0.30, 'min' => $lowScore ? 50 : 70, 'max' => $lowScore ? 65 : 95],
        ];

        foreach ($komponenNilai as $komponen => $config) {
            $nilai = rand($config['min'], $config['max']);
            NilaiSeleksi::create([
                'calon_mahasiswa_id' => $camaba->id,
                'komponen_nilai' => $komponen,
                'nilai' => $nilai,
                'bobot' => $config['bobot'],
                'nilai_akhir' => $nilai * $config['bobot'],
                'input_by' => 1,
            ]);
        }
    }
}
