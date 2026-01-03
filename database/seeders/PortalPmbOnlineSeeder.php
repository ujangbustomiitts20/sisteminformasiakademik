<?php

namespace Database\Seeders;

use App\Models\PeriodePmb;
use App\Models\GelombangPmb;
use App\Models\JalurSeleksi;
use App\Models\KuotaPmb;
use App\Models\BiayaPendaftaran;
use App\Models\ProgramStudi;
use App\Models\Fakultas;
use App\Models\KontenPmb;
use App\Models\SliderPmb;
use App\Models\FaqPmb;
use App\Models\TestimoniPmb;
use App\Models\KeunggulanPmb;
use App\Models\FasilitasPmb;
use App\Models\BeritaPmb;
use App\Models\KontakPmb;
use App\Models\GaleriPmb;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PortalPmbOnlineSeeder extends Seeder
{
    /**
     * Seeder lengkap untuk Portal PMB Online
     * Menambahkan semua data yang diperlukan agar portal tidak error
     */
    public function run(): void
    {
        $this->command->info('Seeding Portal PMB Online data...');

        // 1. Pastikan ada Fakultas & Program Studi
        $this->seedFakultasProdi();

        // 2. Seed Jalur Seleksi
        $this->seedJalurSeleksi();

        // 3. Seed Periode PMB (2025/2026 - aktif)
        $this->seedPeriodePmb();

        // 4. Seed Gelombang PMB
        $this->seedGelombangPmb();

        // 5. Seed Kuota PMB
        $this->seedKuotaPmb();

        // 6. Seed Biaya Pendaftaran
        $this->seedBiayaPendaftaran();

        // 7. Seed Konten PMB
        $this->seedKontenPmb();

        // 8. Seed Slider
        $this->seedSlider();

        // 9. Seed Keunggulan
        $this->seedKeunggulan();

        // 10. Seed Fasilitas
        $this->seedFasilitas();

        // 11. Seed Testimoni
        $this->seedTestimoni();

        // 12. Seed FAQ
        $this->seedFaq();

        // 13. Seed Berita
        $this->seedBerita();

        // 14. Seed Kontak
        $this->seedKontak();

        // 15. Seed Galeri
        $this->seedGaleri();

        $this->command->info('Portal PMB Online seeder completed!');
    }

    private function seedFakultasProdi()
    {
        $this->command->info('  → Checking Fakultas & Program Studi...');

        if (Fakultas::count() == 0) {
            $this->command->info('    Creating sample fakultas...');
            $fakultas = [
                Fakultas::create(['kode' => 'FTI', 'nama' => 'Fakultas Teknologi Informasi']),
                Fakultas::create(['kode' => 'FEB', 'nama' => 'Fakultas Ekonomi dan Bisnis']),
                Fakultas::create(['kode' => 'FT', 'nama' => 'Fakultas Teknik']),
            ];
        } else {
            $fakultas = Fakultas::all();
        }

        if (ProgramStudi::count() == 0) {
            $this->command->info('    Creating sample program studi...');
            $fakultasMap = Fakultas::pluck('id', 'kode')->toArray();
            
            $prodiData = [
                ['kode' => 'TI', 'nama' => 'Teknik Informatika', 'jenjang' => 'S1', 'fakultas_kode' => 'FTI', 'total_sks' => 144],
                ['kode' => 'SI', 'nama' => 'Sistem Informasi', 'jenjang' => 'S1', 'fakultas_kode' => 'FTI', 'total_sks' => 144],
                ['kode' => 'MN', 'nama' => 'Manajemen', 'jenjang' => 'S1', 'fakultas_kode' => 'FEB', 'total_sks' => 144],
                ['kode' => 'AK', 'nama' => 'Akuntansi', 'jenjang' => 'S1', 'fakultas_kode' => 'FEB', 'total_sks' => 144],
                ['kode' => 'TS', 'nama' => 'Teknik Sipil', 'jenjang' => 'S1', 'fakultas_kode' => 'FT', 'total_sks' => 144],
            ];

            foreach ($prodiData as $prodi) {
                $fakultasId = $fakultasMap[$prodi['fakultas_kode']] ?? Fakultas::first()->id;
                ProgramStudi::create([
                    'kode' => $prodi['kode'],
                    'nama' => $prodi['nama'],
                    'jenjang' => $prodi['jenjang'],
                    'fakultas_id' => $fakultasId,
                    'total_sks' => $prodi['total_sks'],
                ]);
            }
        }
    }

    private function seedJalurSeleksi()
    {
        $this->command->info('  → Seeding Jalur Seleksi...');

        $jalurData = [
            [
                'kode' => 'REG',
                'nama' => 'Jalur Reguler',
                'deskripsi' => 'Jalur seleksi reguler dengan ujian tertulis CBT (Computer Based Test). Peserta mengikuti ujian Tes Potensi Akademik, Matematika, dan Bahasa Inggris.',
                'persyaratan' => "- Lulusan SMA/SMK/MA sederajat\n- Pas foto 3x4 berlatar merah\n- Scan KTP\n- Scan Ijazah/SKL\n- Scan rapor semester 1-5",
                'is_active' => true,
            ],
            [
                'kode' => 'PRS',
                'nama' => 'Jalur Prestasi',
                'deskripsi' => 'Jalur khusus bagi siswa berprestasi di bidang akademik maupun non-akademik. Seleksi berdasarkan portofolio dan wawancara.',
                'persyaratan' => "- Lulusan SMA/SMK/MA sederajat\n- Nilai rapor rata-rata minimal 80\n- Memiliki prestasi tingkat kabupaten/kota atau lebih tinggi\n- Sertifikat prestasi\n- Surat rekomendasi sekolah",
                'is_active' => true,
            ],
            [
                'kode' => 'UND',
                'nama' => 'Jalur Undangan',
                'deskripsi' => 'Jalur tanpa tes untuk siswa dengan nilai rapor tinggi dari sekolah mitra. Seleksi berdasarkan nilai rapor dan rekomendasi sekolah.',
                'persyaratan' => "- Siswa kelas 12 dari sekolah mitra\n- Nilai rapor rata-rata minimal 85\n- Ranking 1-10 di kelas\n- Surat rekomendasi kepala sekolah",
                'is_active' => true,
            ],
            [
                'kode' => 'KIP',
                'nama' => 'Jalur KIP-Kuliah',
                'deskripsi' => 'Jalur khusus penerima Kartu Indonesia Pintar Kuliah. Bebas biaya pendidikan bagi mahasiswa dari keluarga kurang mampu.',
                'persyaratan' => "- Terdaftar di DTKS Kemensos\n- Memiliki KIP/KKS\n- Lulusan SMA/SMK/MA sederajat maksimal 3 tahun terakhir\n- Nilai rapor rata-rata minimal 75",
                'is_active' => true,
            ],
        ];

        foreach ($jalurData as $jalur) {
            JalurSeleksi::firstOrCreate(
                ['kode' => $jalur['kode']],
                $jalur
            );
        }
    }

    private function seedPeriodePmb()
    {
        $this->command->info('  → Seeding Periode PMB...');

        // Buat atau update periode baru 2025/2026
        $periode = PeriodePmb::updateOrCreate(
            ['tahun_akademik' => '2025/2026'],
            [
                'nama' => 'PMB Tahun Akademik 2025/2026',
                'tanggal_mulai' => '2025-11-01',
                'tanggal_selesai' => '2026-08-31',
                'deskripsi' => 'Penerimaan Mahasiswa Baru untuk Tahun Akademik 2025/2026',
                'is_active' => true,
            ]
        );

        // Non-aktifkan periode lain
        PeriodePmb::where('id', '!=', $periode->id)->update(['is_active' => false]);
    }

    private function seedGelombangPmb()
    {
        $this->command->info('  → Seeding Gelombang PMB...');

        $periode = PeriodePmb::where('is_active', true)->first();
        if (!$periode) {
            $this->command->warn('    Periode PMB tidak ditemukan!');
            return;
        }

        $gelombangData = [
            [
                'nama' => 'Gelombang 1 (Early Bird)',
                'nomor_gelombang' => 1,
                'tanggal_mulai_daftar' => '2025-11-01',
                'tanggal_selesai_daftar' => '2026-01-31',
                'tanggal_ujian' => '2026-02-10',
                'tanggal_pengumuman' => '2026-02-17',
                'tanggal_daftar_ulang_mulai' => '2026-02-18',
                'tanggal_daftar_ulang_selesai' => '2026-02-28',
                'is_active' => true,
            ],
            [
                'nama' => 'Gelombang 2',
                'nomor_gelombang' => 2,
                'tanggal_mulai_daftar' => '2026-02-01',
                'tanggal_selesai_daftar' => '2026-03-31',
                'tanggal_ujian' => '2026-04-10',
                'tanggal_pengumuman' => '2026-04-17',
                'tanggal_daftar_ulang_mulai' => '2026-04-18',
                'tanggal_daftar_ulang_selesai' => '2026-04-30',
                'is_active' => true,
            ],
            [
                'nama' => 'Gelombang 3',
                'nomor_gelombang' => 3,
                'tanggal_mulai_daftar' => '2026-04-01',
                'tanggal_selesai_daftar' => '2026-05-31',
                'tanggal_ujian' => '2026-06-10',
                'tanggal_pengumuman' => '2026-06-17',
                'tanggal_daftar_ulang_mulai' => '2026-06-18',
                'tanggal_daftar_ulang_selesai' => '2026-06-30',
                'is_active' => false,
            ],
            [
                'nama' => 'Gelombang 4 (Terakhir)',
                'nomor_gelombang' => 4,
                'tanggal_mulai_daftar' => '2026-06-01',
                'tanggal_selesai_daftar' => '2026-07-31',
                'tanggal_ujian' => '2026-08-05',
                'tanggal_pengumuman' => '2026-08-10',
                'tanggal_daftar_ulang_mulai' => '2026-08-11',
                'tanggal_daftar_ulang_selesai' => '2026-08-20',
                'is_active' => false,
            ],
        ];

        foreach ($gelombangData as $data) {
            GelombangPmb::firstOrCreate(
                ['periode_pmb_id' => $periode->id, 'nomor_gelombang' => $data['nomor_gelombang']],
                array_merge($data, ['periode_pmb_id' => $periode->id])
            );
        }
    }

    private function seedKuotaPmb()
    {
        $this->command->info('  → Seeding Kuota PMB...');

        $gelombangs = GelombangPmb::whereHas('periodePmb', fn($q) => $q->where('is_active', true))->get();
        $prodis = ProgramStudi::all();
        $jalurs = JalurSeleksi::where('is_active', true)->get();

        if ($gelombangs->isEmpty() || $prodis->isEmpty() || $jalurs->isEmpty()) {
            $this->command->warn('    Data gelombang/prodi/jalur tidak lengkap!');
            return;
        }

        foreach ($gelombangs as $gelombang) {
            foreach ($prodis as $prodi) {
                foreach ($jalurs as $jalur) {
                    KuotaPmb::firstOrCreate(
                        [
                            'gelombang_pmb_id' => $gelombang->id,
                            'program_studi_id' => $prodi->id,
                            'jalur_seleksi_id' => $jalur->id,
                        ],
                        [
                            'kuota' => rand(30, 60),
                            'terisi' => 0,
                        ]
                    );
                }
            }
        }
    }

    private function seedBiayaPendaftaran()
    {
        $this->command->info('  → Seeding Biaya Pendaftaran...');

        $gelombangs = GelombangPmb::whereHas('periodePmb', fn($q) => $q->where('is_active', true))->get();
        $jalurs = JalurSeleksi::where('is_active', true)->get();

        $biayaPerJalur = [
            'REG' => ['formulir' => 250000, 'ujian' => 100000],
            'PRS' => ['formulir' => 200000, 'ujian' => 50000],
            'UND' => ['formulir' => 0, 'ujian' => 0],
            'KIP' => ['formulir' => 0, 'ujian' => 0],
        ];

        foreach ($gelombangs as $gelombang) {
            foreach ($jalurs as $jalur) {
                $biaya = $biayaPerJalur[$jalur->kode] ?? ['formulir' => 200000, 'ujian' => 100000];
                BiayaPendaftaran::firstOrCreate(
                    [
                        'gelombang_pmb_id' => $gelombang->id,
                        'jalur_seleksi_id' => $jalur->id,
                        'program_studi_id' => null,
                    ],
                    [
                        'biaya_formulir' => $biaya['formulir'],
                        'biaya_ujian' => $biaya['ujian'],
                    ]
                );
            }
        }
    }

    private function seedKontenPmb()
    {
        $this->command->info('  → Seeding Konten PMB...');

        $konten = [
            // General
            ['key' => 'nama_institusi', 'value' => 'Universitas Contoh Indonesia', 'label' => 'Nama Institusi', 'group' => 'general', 'type' => 'text', 'order' => 1],
            ['key' => 'singkatan_institusi', 'value' => 'UCI', 'label' => 'Singkatan', 'group' => 'general', 'type' => 'text', 'order' => 2],
            ['key' => 'tagline', 'value' => 'Membangun Generasi Unggul untuk Indonesia', 'label' => 'Tagline', 'group' => 'general', 'type' => 'text', 'order' => 3],
            ['key' => 'tahun_berdiri', 'value' => '1985', 'label' => 'Tahun Berdiri', 'group' => 'general', 'type' => 'text', 'order' => 4],
            
            // Hero
            ['key' => 'hero_title', 'value' => 'Raih Masa Depanmu Bersama Kami', 'label' => 'Hero Title', 'group' => 'hero', 'type' => 'text', 'order' => 1],
            ['key' => 'hero_subtitle', 'value' => 'Bergabunglah dengan ribuan mahasiswa yang telah mewujudkan mimpi mereka di universitas kami', 'label' => 'Hero Subtitle', 'group' => 'hero', 'type' => 'textarea', 'order' => 2],
            
            // SEO
            ['key' => 'meta_description', 'value' => 'Penerimaan Mahasiswa Baru Universitas Contoh Indonesia - Daftar sekarang dan wujudkan impianmu!', 'label' => 'Meta Description', 'group' => 'seo', 'type' => 'textarea', 'order' => 1],
            ['key' => 'meta_keywords', 'value' => 'pmb, pendaftaran mahasiswa baru, universitas, kuliah, kampus', 'label' => 'Meta Keywords', 'group' => 'seo', 'type' => 'text', 'order' => 2],
            
            // Alur Pendaftaran
            ['key' => 'alur_step_1', 'value' => 'Registrasi akun di website PMB', 'label' => 'Langkah 1', 'group' => 'alur', 'type' => 'text', 'order' => 1],
            ['key' => 'alur_step_2', 'value' => 'Lengkapi formulir pendaftaran online', 'label' => 'Langkah 2', 'group' => 'alur', 'type' => 'text', 'order' => 2],
            ['key' => 'alur_step_3', 'value' => 'Upload dokumen persyaratan', 'label' => 'Langkah 3', 'group' => 'alur', 'type' => 'text', 'order' => 3],
            ['key' => 'alur_step_4', 'value' => 'Bayar biaya pendaftaran', 'label' => 'Langkah 4', 'group' => 'alur', 'type' => 'text', 'order' => 4],
            ['key' => 'alur_step_5', 'value' => 'Ikuti ujian seleksi', 'label' => 'Langkah 5', 'group' => 'alur', 'type' => 'text', 'order' => 5],
            ['key' => 'alur_step_6', 'value' => 'Cek pengumuman hasil seleksi', 'label' => 'Langkah 6', 'group' => 'alur', 'type' => 'text', 'order' => 6],
            ['key' => 'alur_step_7', 'value' => 'Daftar ulang jika dinyatakan lulus', 'label' => 'Langkah 7', 'group' => 'alur', 'type' => 'text', 'order' => 7],
        ];

        foreach ($konten as $item) {
            KontenPmb::firstOrCreate(
                ['key' => $item['key']],
                array_merge($item, ['is_active' => true])
            );
        }
    }

    private function seedSlider()
    {
        $this->command->info('  → Seeding Slider...');

        $sliders = [
            [
                'judul' => 'Penerimaan Mahasiswa Baru 2025/2026',
                'deskripsi' => 'Daftarkan dirimu sekarang dan jadilah bagian dari keluarga besar UCI. Gelombang 1 sudah dibuka!',
                'link' => '/pmb-online/pendaftaran',
                'button_text' => 'Daftar Sekarang',
                'urutan' => 1,
                'is_active' => true,
            ],
            [
                'judul' => 'Beasiswa Prestasi hingga 100%',
                'deskripsi' => 'Raih beasiswa penuh untuk mahasiswa berprestasi akademik dan non-akademik. Tersedia lebih dari 500 kuota beasiswa!',
                'link' => '/pmb-online/berita',
                'button_text' => 'Info Beasiswa',
                'urutan' => 2,
                'is_active' => true,
            ],
            [
                'judul' => 'Fasilitas Kampus Modern & Lengkap',
                'deskripsi' => 'Nikmati fasilitas kampus berstandar internasional untuk menunjang proses belajar mengajar yang optimal.',
                'link' => '/pmb-online/fasilitas',
                'button_text' => 'Lihat Fasilitas',
                'urutan' => 3,
                'is_active' => true,
            ],
        ];

        SliderPmb::truncate();
        foreach ($sliders as $slider) {
            SliderPmb::create($slider);
        }
    }

    private function seedKeunggulan()
    {
        $this->command->info('  → Seeding Keunggulan...');

        $keunggulan = [
            ['judul' => 'Akreditasi Unggul', 'deskripsi' => 'Institusi terakreditasi "Unggul" dari BAN-PT dengan standar pendidikan tinggi nasional dan internasional.', 'icon' => 'award', 'urutan' => 1],
            ['judul' => 'Dosen Berkualitas', 'deskripsi' => 'Lebih dari 80% dosen bergelar Doktor (S3) dan berpengalaman di bidangnya masing-masing.', 'icon' => 'mortarboard', 'urutan' => 2],
            ['judul' => 'Fasilitas Modern', 'deskripsi' => 'Laboratorium canggih, perpustakaan digital, dan fasilitas olahraga berstandar internasional.', 'icon' => 'building', 'urutan' => 3],
            ['judul' => 'Koneksi Industri', 'deskripsi' => 'Kerjasama dengan 200+ perusahaan nasional dan multinasional untuk magang dan rekrutmen.', 'icon' => 'briefcase', 'urutan' => 4],
            ['judul' => 'Beasiswa Lengkap', 'deskripsi' => 'Tersedia berbagai jenis beasiswa untuk mahasiswa berprestasi dan dari keluarga kurang mampu.', 'icon' => 'cash-coin', 'urutan' => 5],
            ['judul' => 'Kurikulum Terkini', 'deskripsi' => 'Kurikulum MBKM terintegrasi dengan kebutuhan industri dan perkembangan teknologi terbaru.', 'icon' => 'journal-code', 'urutan' => 6],
        ];

        KeunggulanPmb::truncate();
        foreach ($keunggulan as $item) {
            KeunggulanPmb::create(array_merge($item, ['is_active' => true]));
        }
    }

    private function seedFasilitas()
    {
        $this->command->info('  → Seeding Fasilitas...');

        $fasilitas = [
            ['nama' => 'Perpustakaan Digital', 'deskripsi' => 'Akses 24 jam ke ribuan jurnal internasional, e-book, dan database akademik premium.', 'icon' => 'book'],
            ['nama' => 'Laboratorium Komputer', 'deskripsi' => 'Dilengkapi 500+ PC terbaru dengan software berlisensi lengkap untuk praktikum.', 'icon' => 'pc-display'],
            ['nama' => 'Auditorium', 'deskripsi' => 'Kapasitas 2000 orang dengan sound system dan lighting profesional untuk berbagai event.', 'icon' => 'building'],
            ['nama' => 'Klinik Kesehatan', 'deskripsi' => 'Layanan kesehatan 24 jam dengan dokter umum dan dokter gigi untuk seluruh civitas akademika.', 'icon' => 'hospital'],
            ['nama' => 'Sports Center', 'deskripsi' => 'Gym modern, kolam renang olimpiade, lapangan futsal, basket, dan badminton indoor.', 'icon' => 'trophy'],
            ['nama' => 'Kantin & Food Court', 'deskripsi' => 'Area kuliner luas dengan berbagai pilihan makanan sehat dan terjangkau.', 'icon' => 'cup-hot'],
            ['nama' => 'WiFi Kampus', 'deskripsi' => 'Internet berkecepatan tinggi hingga 1 Gbps di seluruh area kampus.', 'icon' => 'wifi'],
            ['nama' => 'Parkir Luas', 'deskripsi' => 'Area parkir tertutup dan terbuka dengan kapasitas 5000+ kendaraan.', 'icon' => 'car-front'],
            ['nama' => 'Masjid Kampus', 'deskripsi' => 'Masjid 3 lantai dengan kapasitas 3000 jamaah dan fasilitas wudhu lengkap.', 'icon' => 'building'],
            ['nama' => 'Student Center', 'deskripsi' => 'Pusat kegiatan mahasiswa dengan ruang diskusi, lounge, dan area kreatif.', 'icon' => 'people'],
            ['nama' => 'Coworking Space', 'deskripsi' => 'Area kerja kolaboratif modern untuk mahasiswa dan inkubator startup.', 'icon' => 'laptop'],
            ['nama' => 'Asrama Mahasiswa', 'deskripsi' => 'Asrama nyaman dengan fasilitas lengkap untuk mahasiswa dari luar kota.', 'icon' => 'house'],
        ];

        FasilitasPmb::truncate();
        foreach ($fasilitas as $index => $item) {
            FasilitasPmb::create(array_merge($item, ['is_active' => true, 'urutan' => $index + 1]));
        }
    }

    private function seedTestimoni()
    {
        $this->command->info('  → Seeding Testimoni...');

        $testimoni = [
            [
                'nama' => 'Ahmad Fadillah Rahman',
                'program_studi' => 'Teknik Informatika',
                'angkatan' => '2023',
                'pekerjaan' => 'Mahasiswa Semester 5',
                'testimoni' => 'Saya sangat puas kuliah di UCI. Dosen-dosennya kompeten dan fasilitas laboratorium sangat memadai. Program magang di perusahaan tech unicorn benar-benar membuka peluang karir saya.',
            ],
            [
                'nama' => 'Siti Nurhaliza Putri',
                'program_studi' => 'Manajemen',
                'angkatan' => '2022',
                'pekerjaan' => 'Management Trainee di Bank BUMN',
                'testimoni' => 'UCI memberikan banyak kesempatan magang di perusahaan ternama. Network alumni yang kuat sangat membantu saya mendapatkan pekerjaan impian sebelum wisuda.',
            ],
            [
                'nama' => 'Budi Santoso Wijaya',
                'program_studi' => 'Akuntansi',
                'angkatan' => '2021',
                'pekerjaan' => 'Auditor di Big Four',
                'testimoni' => 'Kurikulum yang up-to-date dan sertifikasi yang difasilitasi kampus membuat saya siap bersaing di dunia kerja. Program CPA preparation sangat membantu!',
            ],
            [
                'nama' => 'Diana Kusuma Dewi',
                'program_studi' => 'Sistem Informasi',
                'angkatan' => '2024',
                'pekerjaan' => 'Mahasiswa Semester 3',
                'testimoni' => 'Atmosfer kampus yang kondusif dan supportive membuat proses belajar menjadi menyenangkan. Organisasi dan UKM juga sangat aktif!',
            ],
            [
                'nama' => 'Eko Prasetyo Nugroho',
                'program_studi' => 'Teknik Sipil',
                'angkatan' => '2020',
                'pekerjaan' => 'Site Engineer di Kontraktor BUMN',
                'testimoni' => 'Pengalaman kuliah di UCI membentuk karakter dan skill profesional saya. Project-based learning yang diterapkan sangat relevan dengan dunia kerja.',
            ],
            [
                'nama' => 'Fitria Rahmawati',
                'program_studi' => 'Teknik Informatika',
                'angkatan' => '2022',
                'pekerjaan' => 'Software Engineer di Startup',
                'testimoni' => 'Dosen yang industry-experienced dan kurikulum berbasis MBKM membuat saya memiliki portofolio kuat. Sekarang saya bekerja di startup impian saya!',
            ],
        ];

        TestimoniPmb::truncate();
        foreach ($testimoni as $index => $item) {
            TestimoniPmb::create(array_merge($item, ['is_active' => true, 'urutan' => $index + 1]));
        }
    }

    private function seedFaq()
    {
        $this->command->info('  → Seeding FAQ...');

        $faqs = [
            // Pendaftaran
            ['pertanyaan' => 'Bagaimana cara mendaftar sebagai mahasiswa baru?', 'jawaban' => "Pendaftaran dapat dilakukan secara online melalui website PMB:\n1. Klik tombol 'Daftar Sekarang'\n2. Buat akun dengan email aktif\n3. Login dan isi formulir pendaftaran\n4. Upload dokumen yang diperlukan\n5. Lakukan pembayaran biaya pendaftaran\n6. Cetak kartu peserta ujian", 'kategori' => 'Pendaftaran', 'urutan' => 1],
            ['pertanyaan' => 'Apa saja persyaratan untuk mendaftar?', 'jawaban' => "Persyaratan umum pendaftaran:\n• Lulusan SMA/SMK/MA/sederajat\n• Pas foto 3x4 berlatar merah (format JPG/PNG)\n• Scan KTP (atau surat keterangan domisili)\n• Scan Kartu Keluarga\n• Scan Ijazah atau Surat Keterangan Lulus\n• Scan rapor semester 1-5\n• Sertifikat prestasi (untuk jalur prestasi)", 'kategori' => 'Pendaftaran', 'urutan' => 2],
            ['pertanyaan' => 'Apakah bisa mendaftar di lebih dari satu program studi?', 'jawaban' => 'Ya, Anda dapat memilih 2 program studi sebagai pilihan 1 dan pilihan 2. Jika tidak diterima di pilihan 1, Anda masih berkesempatan diterima di pilihan 2 selama memenuhi passing grade.', 'kategori' => 'Pendaftaran', 'urutan' => 3],
            
            // Biaya
            ['pertanyaan' => 'Berapa biaya pendaftaran?', 'jawaban' => "Biaya pendaftaran bervariasi:\n• Jalur Reguler: Rp 350.000\n• Jalur Prestasi: Rp 250.000\n• Jalur Undangan: GRATIS\n• Jalur KIP-Kuliah: GRATIS\n\nPembayaran dapat dilakukan via transfer bank atau virtual account.", 'kategori' => 'Biaya', 'urutan' => 4],
            ['pertanyaan' => 'Apakah bisa membayar UKT secara cicilan?', 'jawaban' => 'Ya, pembayaran UKT dapat dilakukan secara cicilan dengan skema:\n• Cicilan 2x per semester\n• Cicilan 4x per semester (dengan persetujuan)\n\nSilakan hubungi bagian keuangan untuk informasi lebih lanjut.', 'kategori' => 'Biaya', 'urutan' => 5],
            
            // Beasiswa
            ['pertanyaan' => 'Apakah ada beasiswa yang tersedia?', 'jawaban' => "Tersedia berbagai jenis beasiswa:\n• Beasiswa Prestasi Akademik (hingga 100%)\n• Beasiswa Prestasi Non-Akademik\n• Beasiswa Kurang Mampu\n• Beasiswa Hafidz/Hafidzah Quran\n• Beasiswa Atlet Berprestasi\n• KIP-Kuliah\n• Beasiswa dari mitra industri", 'kategori' => 'Beasiswa', 'urutan' => 6],
            ['pertanyaan' => 'Bagaimana cara mendaftar beasiswa?', 'jawaban' => 'Pendaftaran beasiswa dapat dilakukan setelah dinyatakan lulus seleksi dan melakukan daftar ulang. Lengkapi formulir beasiswa dan lampirkan dokumen pendukung sesuai jenis beasiswa yang dipilih.', 'kategori' => 'Beasiswa', 'urutan' => 7],
            
            // Seleksi
            ['pertanyaan' => 'Kapan pengumuman hasil seleksi?', 'jawaban' => 'Pengumuman hasil seleksi diumumkan sesuai jadwal masing-masing gelombang, biasanya 1 minggu setelah pelaksanaan ujian. Hasil dapat dicek melalui:\n• Website PMB (menu Cek Pengumuman)\n• Email terdaftar\n• SMS notifikasi', 'kategori' => 'Seleksi', 'urutan' => 8],
            ['pertanyaan' => 'Materi apa saja yang diujikan?', 'jawaban' => "Materi ujian untuk Jalur Reguler:\n• Tes Potensi Akademik (TPA)\n• Matematika Dasar\n• Bahasa Inggris\n• Pengetahuan Umum\n\nUntuk jalur lain, seleksi berdasarkan nilai rapor dan/atau wawancara.", 'kategori' => 'Seleksi', 'urutan' => 9],
            
            // Umum
            ['pertanyaan' => 'Dimana lokasi kampus?', 'jawaban' => "Kampus Utama:\nJl. Pendidikan No. 123, Kota Pendidikan\n\nKampus 2:\nJl. Ilmu Pengetahuan No. 456, Kota Selatan\n\nKedua kampus mudah dijangkau dengan transportasi umum dan memiliki akses tol.", 'kategori' => 'Umum', 'urutan' => 10],
            ['pertanyaan' => 'Apakah tersedia asrama mahasiswa?', 'jawaban' => 'Ya, tersedia asrama mahasiswa dengan fasilitas lengkap:\n• Kamar ber-AC\n• WiFi 24 jam\n• Laundry\n• Kantin\n• Ruang belajar bersama\n• Keamanan 24 jam\n\nKapasitas terbatas, prioritas untuk mahasiswa dari luar kota.', 'kategori' => 'Umum', 'urutan' => 11],
            ['pertanyaan' => 'Bagaimana sistem perkuliahan?', 'jawaban' => 'Perkuliahan menggunakan sistem SKS dengan metode:\n• Tatap muka di kelas\n• Praktikum di laboratorium\n• E-learning melalui LMS\n• Project-based learning\n• Magang industri (MBKM)\n\nKurikulum terintegrasi dengan kebutuhan industri.', 'kategori' => 'Umum', 'urutan' => 12],
        ];

        FaqPmb::truncate();
        foreach ($faqs as $faq) {
            FaqPmb::create(array_merge($faq, ['is_active' => true]));
        }
    }

    private function seedBerita()
    {
        $this->command->info('  → Seeding Berita...');

        $berita = [
            [
                'judul' => 'Pendaftaran Mahasiswa Baru Gelombang 1 Tahun 2025/2026 Resmi Dibuka!',
                'slug' => 'pendaftaran-mahasiswa-baru-gelombang-1-tahun-2025-2026-resmi-dibuka',
                'ringkasan' => 'Universitas Contoh Indonesia membuka pendaftaran mahasiswa baru gelombang pertama untuk tahun akademik 2025/2026. Dapatkan potongan biaya pendaftaran khusus early bird!',
                'konten' => '<p>Universitas Contoh Indonesia dengan bangga mengumumkan pembukaan pendaftaran mahasiswa baru gelombang pertama untuk tahun akademik 2025/2026.</p><p>Pendaftaran dapat dilakukan secara online melalui website PMB mulai tanggal <strong>1 November 2025</strong> hingga <strong>31 Januari 2026</strong>.</p><h3>Keuntungan Mendaftar di Gelombang 1:</h3><ul><li>Potongan biaya pendaftaran 20%</li><li>Prioritas pemilihan kelas</li><li>Kesempatan beasiswa early bird</li><li>Free merchandise eksklusif</li></ul><h3>Jalur Pendaftaran yang Tersedia:</h3><ul><li>Jalur Reguler</li><li>Jalur Prestasi</li><li>Jalur Undangan (Bebas Biaya Pendaftaran)</li><li>Jalur KIP-Kuliah</li></ul><p>Untuk informasi lebih lanjut, silakan hubungi panitia PMB melalui WhatsApp atau kunjungi website resmi kami.</p>',
                'kategori' => 'pengumuman',
                'is_featured' => true,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(1),
            ],
            [
                'judul' => 'Beasiswa Penuh untuk 500 Mahasiswa Berprestasi',
                'slug' => 'beasiswa-penuh-untuk-500-mahasiswa-berprestasi',
                'ringkasan' => 'UCI menyediakan beasiswa penuh untuk 500 mahasiswa berprestasi di bidang akademik dan non-akademik tahun ini.',
                'konten' => '<p>Dalam rangka mendukung pendidikan berkualitas, Universitas Contoh Indonesia menyediakan <strong>beasiswa penuh untuk 500 mahasiswa berprestasi</strong>.</p><h3>Kategori Beasiswa:</h3><ul><li>Beasiswa Prestasi Akademik (200 kuota)</li><li>Beasiswa Prestasi Non-Akademik (150 kuota)</li><li>Beasiswa Hafidz/Hafidzah Quran (50 kuota)</li><li>Beasiswa Atlet Berprestasi (50 kuota)</li><li>Beasiswa Putra/Putri Daerah (50 kuota)</li></ul><h3>Persyaratan Umum:</h3><ul><li>Diterima sebagai mahasiswa baru UCI</li><li>Nilai rapor rata-rata minimal sesuai ketentuan masing-masing beasiswa</li><li>Memiliki prestasi yang dibuktikan dengan sertifikat</li><li>Lolos seleksi administrasi dan wawancara</li></ul><h3>Cakupan Beasiswa:</h3><ul><li>Bebas biaya kuliah (UKT) 100%</li><li>Biaya hidup bulanan</li><li>Biaya buku dan perlengkapan</li><li>Asuransi kesehatan</li></ul>',
                'kategori' => 'berita',
                'is_featured' => true,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(3),
            ],
            [
                'judul' => 'Tips Sukses Menghadapi Ujian Masuk Perguruan Tinggi',
                'slug' => 'tips-sukses-menghadapi-ujian-masuk-perguruan-tinggi',
                'ringkasan' => 'Persiapkan dirimu dengan tips dan trik jitu untuk menghadapi ujian masuk perguruan tinggi.',
                'konten' => '<p>Menghadapi ujian masuk perguruan tinggi membutuhkan persiapan yang matang. Berikut beberapa tips yang bisa membantu Anda sukses:</p><h3>1. Pahami Materi Ujian</h3><p>Pelajari kisi-kisi dan materi yang akan diujikan. Fokus pada mata pelajaran yang menjadi kelemahan Anda.</p><h3>2. Latihan Soal Secara Rutin</h3><p>Kerjakan soal-soal latihan dari tahun-tahun sebelumnya untuk membiasakan diri dengan format dan tingkat kesulitan ujian.</p><h3>3. Manajemen Waktu</h3><p>Buat jadwal belajar yang teratur dan realistis. Alokasikan waktu untuk setiap mata pelajaran.</p><h3>4. Jaga Kesehatan</h3><p>Istirahat yang cukup (7-8 jam per hari), makan makanan bergizi, dan olahraga ringan sangat penting untuk menjaga fokus.</p><h3>5. Simulasi Ujian</h3><p>Lakukan try out atau simulasi ujian dalam kondisi yang mirip dengan ujian sesungguhnya.</p><h3>6. Kelola Stres</h3><p>Gunakan teknik relaksasi seperti deep breathing atau meditasi untuk mengurangi kecemasan.</p>',
                'kategori' => 'tips',
                'is_featured' => false,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(5),
            ],
            [
                'judul' => 'UCI Raih Akreditasi Unggul dari BAN-PT',
                'slug' => 'uci-raih-akreditasi-unggul-dari-ban-pt',
                'ringkasan' => 'Universitas Contoh Indonesia berhasil meraih akreditasi "Unggul" dari BAN-PT, menegaskan komitmen terhadap mutu pendidikan.',
                'konten' => '<p>Universitas Contoh Indonesia dengan bangga mengumumkan pencapaian <strong>Akreditasi "Unggul"</strong> dari Badan Akreditasi Nasional Perguruan Tinggi (BAN-PT).</p><p>Akreditasi ini merupakan bukti komitmen UCI dalam menyelenggarakan pendidikan tinggi yang berkualitas dan relevan dengan kebutuhan industri.</p><h3>Indikator Penilaian:</h3><ul><li>Visi, Misi, Tujuan dan Strategi</li><li>Tata Pamong, Tata Kelola dan Kerjasama</li><li>Mahasiswa</li><li>Sumber Daya Manusia</li><li>Keuangan, Sarana dan Prasarana</li><li>Pendidikan</li><li>Penelitian</li><li>Pengabdian kepada Masyarakat</li><li>Luaran dan Capaian Tridharma</li></ul><p>Pencapaian ini semakin memperkuat posisi UCI sebagai perguruan tinggi pilihan untuk mencetak generasi unggul Indonesia.</p>',
                'kategori' => 'berita',
                'is_featured' => true,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(7),
            ],
            [
                'judul' => 'Program MBKM: Magang di 200+ Perusahaan Mitra',
                'slug' => 'program-mbkm-magang-di-200-perusahaan-mitra',
                'ringkasan' => 'UCI membuka kesempatan magang di lebih dari 200 perusahaan mitra melalui program Merdeka Belajar Kampus Merdeka.',
                'konten' => '<p>Melalui program <strong>Merdeka Belajar Kampus Merdeka (MBKM)</strong>, UCI memberikan kesempatan bagi mahasiswa untuk mendapatkan pengalaman langsung di dunia industri.</p><h3>Perusahaan Mitra:</h3><ul><li>Perusahaan Teknologi: Google, Microsoft, Tokopedia, Gojek, dll.</li><li>Perbankan: Bank Mandiri, BCA, BNI, BRI, dll.</li><li>Manufaktur: Astra, Unilever, P&G, dll.</li><li>Konsultan: McKinsey, BCG, Deloitte, PwC, dll.</li><li>Startup: Berbagai startup unicorn dan decacorn</li></ul><h3>Benefit Program Magang:</h3><ul><li>Konversi SKS (hingga 20 SKS)</li><li>Uang saku bulanan</li><li>Sertifikat magang</li><li>Kesempatan full-time offer</li><li>Networking profesional</li></ul>',
                'kategori' => 'info',
                'is_featured' => false,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(10),
            ],
        ];

        BeritaPmb::truncate();
        foreach ($berita as $item) {
            BeritaPmb::create($item);
        }
    }

    private function seedKontak()
    {
        $this->command->info('  → Seeding Kontak...');

        $kontakItems = [
            ['type' => 'address', 'label' => 'Alamat Kampus', 'value' => 'Jl. Pendidikan No. 123, Kota Pendidikan 12345', 'icon' => 'geo-alt', 'urutan' => 1],
            ['type' => 'phone', 'label' => 'Telepon', 'value' => '(021) 123-4567', 'icon' => 'telephone', 'urutan' => 2],
            ['type' => 'whatsapp', 'label' => 'WhatsApp PMB', 'value' => '0812-3456-7890', 'icon' => 'whatsapp', 'link' => 'https://wa.me/6281234567890', 'urutan' => 3],
            ['type' => 'email', 'label' => 'Email PMB', 'value' => 'pmb@uci.ac.id', 'icon' => 'envelope', 'link' => 'mailto:pmb@uci.ac.id', 'urutan' => 4],
            ['type' => 'jam_operasional', 'label' => 'Jam Operasional', 'value' => 'Senin-Jumat: 08.00-16.00 WIB, Sabtu: 08.00-12.00 WIB', 'icon' => 'clock', 'urutan' => 5],
            ['type' => 'facebook', 'label' => 'Facebook', 'value' => 'UCI Official', 'icon' => 'facebook', 'link' => 'https://facebook.com/uciofficial', 'urutan' => 6],
            ['type' => 'instagram', 'label' => 'Instagram', 'value' => '@uci_official', 'icon' => 'instagram', 'link' => 'https://instagram.com/uci_official', 'urutan' => 7],
            ['type' => 'youtube', 'label' => 'YouTube', 'value' => 'UCI Official', 'icon' => 'youtube', 'link' => 'https://youtube.com/@uciofficial', 'urutan' => 8],
            ['type' => 'twitter', 'label' => 'Twitter/X', 'value' => '@uci_official', 'icon' => 'twitter-x', 'link' => 'https://twitter.com/uci_official', 'urutan' => 9],
            ['type' => 'tiktok', 'label' => 'TikTok', 'value' => '@uci_official', 'icon' => 'tiktok', 'link' => 'https://tiktok.com/@uci_official', 'urutan' => 10],
        ];
        
        KontakPmb::truncate();
        foreach ($kontakItems as $item) {
            KontakPmb::create(array_merge($item, ['is_active' => true]));
        }
    }

    private function seedGaleri()
    {
        $this->command->info('  → Seeding Galeri...');

        $galeriItems = [
            ['judul' => 'Gedung Utama Kampus', 'deskripsi' => 'Tampak depan gedung utama kampus yang megah dan modern', 'kategori' => 'Kampus', 'urutan' => 1],
            ['judul' => 'Perpustakaan Digital', 'deskripsi' => 'Perpustakaan modern dengan koleksi buku dan jurnal digital lengkap', 'kategori' => 'Fasilitas', 'urutan' => 2],
            ['judul' => 'Laboratorium Komputer', 'deskripsi' => 'Lab komputer dengan peralatan terbaru untuk praktikum', 'kategori' => 'Fasilitas', 'urutan' => 3],
            ['judul' => 'Wisuda Angkatan 2024', 'deskripsi' => 'Momen kebahagiaan wisudawan/wisudawati angkatan 2024', 'kategori' => 'Kegiatan', 'urutan' => 4],
            ['judul' => 'Sports Center', 'deskripsi' => 'Fasilitas olahraga lengkap untuk mahasiswa', 'kategori' => 'Fasilitas', 'urutan' => 5],
            ['judul' => 'Seminar Nasional', 'deskripsi' => 'Kegiatan seminar nasional dengan pembicara dari berbagai industri', 'kategori' => 'Kegiatan', 'urutan' => 6],
            ['judul' => 'Taman Kampus', 'deskripsi' => 'Area hijau yang asri untuk bersantai dan berdiskusi', 'kategori' => 'Kampus', 'urutan' => 7],
            ['judul' => 'Student Center', 'deskripsi' => 'Pusat kegiatan mahasiswa dengan berbagai fasilitas', 'kategori' => 'Fasilitas', 'urutan' => 8],
        ];
        
        GaleriPmb::truncate();
        foreach ($galeriItems as $item) {
            // Gunakan placeholder image untuk galeri
            GaleriPmb::create(array_merge($item, [
                'is_active' => true, 
                'gambar' => 'pmb/galeri/placeholder.jpg'
            ]));
        }
    }
}
