<?php

namespace Database\Seeders;

use App\Models\SliderPmb;
use App\Models\BeritaPmb;
use App\Models\FaqPmb;
use App\Models\TestimoniPmb;
use App\Models\GaleriPmb;
use App\Models\KeunggulanPmb;
use App\Models\FasilitasPmb;
use App\Models\KontakPmb;
use App\Models\KontenPmb;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KontenPmbSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Konten PMB...');

        // Get admin user for berita
        $adminUser = User::where('role', 'admin')->first();

        // ==================
        // SLIDER
        // ==================
        if (SliderPmb::count() == 0) {
            $sliders = [
                [
                    'judul' => 'Selamat Datang di PMB Institut Teknologi Tangerang Selatan',
                    'deskripsi' => 'Wujudkan mimpimu bersama ITTS. Pendaftaran mahasiswa baru tahun akademik 2026/2027 telah dibuka!',
                    'link' => '/pmb/pendaftaran',
                    'button_text' => 'Daftar Sekarang',
                    'gambar' => 'pmb/slider/slider-1.jpg',
                    'urutan' => 1,
                    'is_active' => true,
                ],
                [
                    'judul' => 'Beasiswa Prestasi & Bantuan Pendidikan',
                    'deskripsi' => 'Dapatkan kesempatan beasiswa hingga 100% untuk mahasiswa berprestasi. Informasi selengkapnya hubungi panitia PMB.',
                    'link' => '/pmb/beasiswa',
                    'button_text' => 'Info Beasiswa',
                    'gambar' => 'pmb/slider/slider-2.jpg',
                    'urutan' => 2,
                    'is_active' => true,
                ],
                [
                    'judul' => 'Fasilitas Modern & Lingkungan Kondusif',
                    'deskripsi' => 'Kampus dengan fasilitas lengkap: laboratorium komputer, perpustakaan digital, ruang kelas ber-AC, dan area parkir luas.',
                    'link' => '/pmb/fasilitas',
                    'button_text' => 'Lihat Fasilitas',
                    'gambar' => 'pmb/slider/slider-3.jpg',
                    'urutan' => 3,
                    'is_active' => true,
                ],
            ];

            foreach ($sliders as $slider) {
                SliderPmb::create($slider);
            }
            $this->command->info('✓ Slider PMB: ' . count($sliders) . ' data');
        }

        // ==================
        // BERITA
        // ==================
        if (BeritaPmb::count() == 0) {
            $beritas = [
                [
                    'judul' => 'Pendaftaran Mahasiswa Baru Gelombang 1 Tahun 2026 Resmi Dibuka',
                    'ringkasan' => 'PMB ITTS membuka pendaftaran gelombang pertama dengan berbagai keuntungan bagi pendaftar awal.',
                    'konten' => '<p>Institut Teknologi Tangerang Selatan (ITTS) resmi membuka pendaftaran mahasiswa baru untuk tahun akademik 2026/2027. Pendaftaran gelombang pertama ini berlangsung mulai 2 Januari hingga 28 Februari 2026.</p><p>Calon mahasiswa yang mendaftar di gelombang pertama akan mendapatkan potongan biaya pendaftaran sebesar 50% dan berkesempatan mendapatkan beasiswa prestasi.</p><p>Program studi yang tersedia antara lain: Teknik Informatika, Sistem Informasi, Teknik Elektro, dan Manajemen Informatika.</p>',
                    'kategori' => 'pengumuman',
                    'is_featured' => true,
                    'is_published' => true,
                    'published_at' => now()->subDays(5),
                ],
                [
                    'judul' => 'Workshop Persiapan Karir untuk Mahasiswa Tingkat Akhir',
                    'ringkasan' => 'Career Development Center ITTS mengadakan workshop persiapan karir bekerja sama dengan perusahaan teknologi terkemuka.',
                    'konten' => '<p>Career Development Center ITTS akan mengadakan workshop persiapan karir pada tanggal 15-16 Januari 2026. Workshop ini bekerja sama dengan beberapa perusahaan teknologi terkemuka di Indonesia.</p><p>Materi yang akan dibahas meliputi: pembuatan CV yang menarik, teknik wawancara kerja, personal branding di LinkedIn, dan tips sukses magang.</p><p>Workshop ini terbuka untuk mahasiswa tingkat akhir dan alumni ITTS. Pendaftaran gratis melalui portal mahasiswa.</p>',
                    'kategori' => 'berita',
                    'is_featured' => false,
                    'is_published' => true,
                    'published_at' => now()->subDays(3),
                ],
                [
                    'judul' => 'Info Jadwal Ujian Seleksi Masuk Gelombang 1',
                    'ringkasan' => 'Jadwal ujian seleksi masuk untuk pendaftar gelombang 1 telah ditetapkan.',
                    'konten' => '<p>Bagi calon mahasiswa yang telah mendaftar di gelombang 1, berikut jadwal ujian seleksi masuk:</p><ul><li>Ujian Tertulis: 5 Maret 2026, pukul 08.00-12.00 WIB</li><li>Tes Wawancara: 6-7 Maret 2026</li><li>Pengumuman Hasil: 12 Maret 2026</li></ul><p>Peserta wajib membawa kartu peserta dan KTP/Kartu Pelajar asli saat ujian.</p>',
                    'kategori' => 'info',
                    'is_featured' => false,
                    'is_published' => true,
                    'published_at' => now()->subDays(2),
                ],
                [
                    'judul' => 'Prestasi Mahasiswa ITTS di Kompetisi Nasional',
                    'ringkasan' => 'Tim mahasiswa ITTS berhasil meraih juara 2 dalam kompetisi programming tingkat nasional.',
                    'konten' => '<p>Tim mahasiswa ITTS berhasil meraih prestasi membanggakan dalam Kompetisi Programming Nasional 2025 yang diselenggarakan di Jakarta.</p><p>Tim yang terdiri dari 3 mahasiswa program studi Teknik Informatika ini berhasil meraih juara 2 setelah bersaing dengan 150 tim dari berbagai perguruan tinggi di Indonesia.</p><p>Prestasi ini membuktikan kualitas pendidikan dan pembinaan mahasiswa di ITTS.</p>',
                    'kategori' => 'berita',
                    'is_featured' => true,
                    'is_published' => true,
                    'published_at' => now()->subDays(1),
                ],
            ];

            foreach ($beritas as $berita) {
                $berita['slug'] = Str::slug($berita['judul']);
                $berita['user_id'] = $adminUser?->id;
                $berita['gambar'] = 'pmb/berita/berita-' . (array_search($berita, $beritas) + 1) . '.jpg';
                BeritaPmb::create($berita);
            }
            $this->command->info('✓ Berita PMB: ' . count($beritas) . ' data');
        }

        // ==================
        // FAQ
        // ==================
        if (FaqPmb::count() == 0) {
            $faqs = [
                ['pertanyaan' => 'Bagaimana cara mendaftar sebagai mahasiswa baru?', 'jawaban' => 'Pendaftaran dapat dilakukan secara online melalui website PMB atau datang langsung ke kampus. Siapkan dokumen: KTP, ijazah/SKL, foto, dan bukti pembayaran pendaftaran.', 'kategori' => 'Pendaftaran', 'urutan' => 1],
                ['pertanyaan' => 'Berapa biaya pendaftaran mahasiswa baru?', 'jawaban' => 'Biaya pendaftaran sebesar Rp 350.000. Untuk gelombang awal mendapat potongan 50% menjadi Rp 175.000.', 'kategori' => 'Biaya', 'urutan' => 2],
                ['pertanyaan' => 'Apa saja program studi yang tersedia?', 'jawaban' => 'ITTS menyediakan program studi: Teknik Informatika (S1), Sistem Informasi (S1), Teknik Elektro (S1), dan Manajemen Informatika (D3).', 'kategori' => 'Program Studi', 'urutan' => 3],
                ['pertanyaan' => 'Apakah tersedia beasiswa?', 'jawaban' => 'Ya, tersedia beberapa jenis beasiswa: Beasiswa Prestasi Akademik, Beasiswa Kurang Mampu, Beasiswa Hafidz Quran, dan Beasiswa Atlet Berprestasi.', 'kategori' => 'Beasiswa', 'urutan' => 4],
                ['pertanyaan' => 'Bagaimana sistem pembayaran kuliah?', 'jawaban' => 'Pembayaran dapat dilakukan per semester atau dicicil maksimal 4x per semester. Pembayaran melalui bank yang ditunjuk atau virtual account.', 'kategori' => 'Biaya', 'urutan' => 5],
                ['pertanyaan' => 'Kapan jadwal kuliah dimulai?', 'jawaban' => 'Perkuliahan semester ganjil dimulai bulan September, semester genap dimulai bulan Februari. Jadwal detail akan diinformasikan saat daftar ulang.', 'kategori' => 'Akademik', 'urutan' => 6],
                ['pertanyaan' => 'Apakah ada kelas karyawan?', 'jawaban' => 'Ya, tersedia kelas reguler (Senin-Jumat pagi) dan kelas karyawan (Sabtu-Minggu atau malam hari) untuk beberapa program studi.', 'kategori' => 'Program Studi', 'urutan' => 7],
                ['pertanyaan' => 'Apa syarat kelulusan?', 'jawaban' => 'Syarat kelulusan: menyelesaikan seluruh SKS (minimal 144 SKS untuk S1), IPK minimal 2.00, lulus sidang skripsi/tugas akhir, dan bebas administrasi.', 'kategori' => 'Akademik', 'urutan' => 8],
                ['pertanyaan' => 'Apakah ijazah ITTS terakreditasi?', 'jawaban' => 'Ya, semua program studi ITTS telah terakreditasi oleh BAN-PT. Teknik Informatika terakreditasi B, program studi lainnya minimal C.', 'kategori' => 'Akademik', 'urutan' => 9],
                ['pertanyaan' => 'Bagaimana cara menghubungi panitia PMB?', 'jawaban' => 'Hubungi kami via WhatsApp: 0812-3456-7890, Email: pmb@itts.ac.id, atau datang langsung ke Gedung A Lt. 1 kampus ITTS.', 'kategori' => 'Kontak', 'urutan' => 10],
            ];

            foreach ($faqs as $faq) {
                $faq['is_active'] = true;
                FaqPmb::create($faq);
            }
            $this->command->info('✓ FAQ PMB: ' . count($faqs) . ' data');
        }

        // ==================
        // TESTIMONI
        // ==================
        if (TestimoniPmb::count() == 0) {
            $testimonis = [
                [
                    'nama' => 'Ahmad Fadillah',
                    'angkatan' => '2020',
                    'program_studi' => 'Teknik Informatika',
                    'pekerjaan' => 'Software Engineer di Tokopedia',
                    'testimoni' => 'Kuliah di ITTS memberikan saya fondasi yang kuat dalam pemrograman. Dosen-dosennya kompeten dan kurikulumnya up-to-date dengan kebutuhan industri. Sekarang saya bekerja di salah satu startup unicorn Indonesia.',
                    'foto' => 'pmb/testimoni/testimoni-1.jpg',
                    'urutan' => 1,
                ],
                [
                    'nama' => 'Siti Nurhaliza',
                    'angkatan' => '2019',
                    'program_studi' => 'Sistem Informasi',
                    'pekerjaan' => 'Business Analyst di Bank Mandiri',
                    'testimoni' => 'ITTS mengajarkan saya tidak hanya teori tapi juga praktik langsung. Program magang dan kerjasama dengan industri sangat membantu saya mendapatkan pekerjaan impian.',
                    'foto' => 'pmb/testimoni/testimoni-2.jpg',
                    'urutan' => 2,
                ],
                [
                    'nama' => 'Budi Santoso',
                    'angkatan' => '2021',
                    'program_studi' => 'Teknik Informatika',
                    'pekerjaan' => 'Full Stack Developer di Gojek',
                    'testimoni' => 'Fasilitas lab komputer yang lengkap dan akses ke berbagai platform pembelajaran online membuat proses belajar sangat menyenangkan. Dosen juga selalu siap membantu di luar jam kuliah.',
                    'foto' => 'pmb/testimoni/testimoni-3.jpg',
                    'urutan' => 3,
                ],
                [
                    'nama' => 'Diana Putri',
                    'angkatan' => '2020',
                    'program_studi' => 'Manajemen Informatika',
                    'pekerjaan' => 'IT Support Specialist di Pertamina',
                    'testimoni' => 'Meskipun D3, ilmu yang didapat sangat aplikatif. Setelah lulus langsung dapat kerja karena skill yang diajarkan sesuai kebutuhan perusahaan.',
                    'foto' => 'pmb/testimoni/testimoni-4.jpg',
                    'urutan' => 4,
                ],
            ];

            foreach ($testimonis as $testimoni) {
                $testimoni['is_active'] = true;
                TestimoniPmb::create($testimoni);
            }
            $this->command->info('✓ Testimoni PMB: ' . count($testimonis) . ' data');
        }

        // ==================
        // GALERI
        // ==================
        if (GaleriPmb::count() == 0) {
            $galeris = [
                ['judul' => 'Gedung Utama Kampus', 'deskripsi' => 'Tampak depan gedung utama ITTS yang modern dan representatif', 'kategori' => 'Kampus', 'gambar' => 'pmb/galeri/galeri-1.jpg', 'urutan' => 1],
                ['judul' => 'Laboratorium Komputer', 'deskripsi' => 'Lab komputer dengan 50 unit PC terbaru untuk praktikum mahasiswa', 'kategori' => 'Fasilitas', 'gambar' => 'pmb/galeri/galeri-2.jpg', 'urutan' => 2],
                ['judul' => 'Perpustakaan Digital', 'deskripsi' => 'Perpustakaan dengan koleksi buku dan akses jurnal internasional', 'kategori' => 'Fasilitas', 'gambar' => 'pmb/galeri/galeri-3.jpg', 'urutan' => 3],
                ['judul' => 'Wisuda Angkatan 2024', 'deskripsi' => 'Momen bahagia wisuda angkatan 2024 di Auditorium ITTS', 'kategori' => 'Kegiatan', 'gambar' => 'pmb/galeri/galeri-4.jpg', 'urutan' => 4],
                ['judul' => 'Seminar Teknologi', 'deskripsi' => 'Seminar nasional teknologi informasi dengan pembicara dari industri', 'kategori' => 'Kegiatan', 'gambar' => 'pmb/galeri/galeri-5.jpg', 'urutan' => 5],
                ['judul' => 'Ruang Kelas Modern', 'deskripsi' => 'Ruang kelas ber-AC dengan fasilitas multimedia lengkap', 'kategori' => 'Fasilitas', 'gambar' => 'pmb/galeri/galeri-6.jpg', 'urutan' => 6],
                ['judul' => 'Area Parkir Luas', 'deskripsi' => 'Area parkir yang dapat menampung ratusan kendaraan', 'kategori' => 'Kampus', 'gambar' => 'pmb/galeri/galeri-7.jpg', 'urutan' => 7],
                ['judul' => 'Kantin Kampus', 'deskripsi' => 'Kantin bersih dengan berbagai pilihan makanan terjangkau', 'kategori' => 'Fasilitas', 'gambar' => 'pmb/galeri/galeri-8.jpg', 'urutan' => 8],
            ];

            foreach ($galeris as $galeri) {
                $galeri['is_active'] = true;
                GaleriPmb::create($galeri);
            }
            $this->command->info('✓ Galeri PMB: ' . count($galeris) . ' data');
        }

        // ==================
        // KEUNGGULAN
        // ==================
        if (KeunggulanPmb::count() == 0) {
            $keunggulans = [
                ['judul' => 'Kurikulum Berbasis Industri', 'deskripsi' => 'Kurikulum yang dirancang bersama praktisi industri untuk memastikan relevansi dengan kebutuhan dunia kerja. Mahasiswa dibekali skill yang langsung dapat diterapkan.', 'icon' => 'book', 'urutan' => 1],
                ['judul' => 'Dosen Berpengalaman', 'deskripsi' => 'Dosen dengan latar belakang akademik dan pengalaman industri yang kuat. Banyak dosen yang juga aktif sebagai praktisi di perusahaan teknologi.', 'icon' => 'person-workspace', 'urutan' => 2],
                ['judul' => 'Fasilitas Modern', 'deskripsi' => 'Laboratorium komputer terkini, perpustakaan digital, ruang kelas ber-AC, WiFi kampus, dan berbagai fasilitas pendukung pembelajaran.', 'icon' => 'building', 'urutan' => 3],
                ['judul' => 'Program Magang', 'deskripsi' => 'Kerjasama dengan lebih dari 50 perusahaan untuk program magang. Mahasiswa mendapat pengalaman kerja nyata sebelum lulus.', 'icon' => 'briefcase', 'urutan' => 4],
                ['judul' => 'Sertifikasi Profesional', 'deskripsi' => 'Program sertifikasi Microsoft, Oracle, dan Cisco yang terintegrasi dengan kurikulum. Lulus kuliah sekaligus mendapat sertifikasi internasional.', 'icon' => 'award', 'urutan' => 5],
                ['judul' => 'Biaya Terjangkau', 'deskripsi' => 'Biaya kuliah yang kompetitif dengan berbagai opsi beasiswa dan cicilan. Investasi pendidikan berkualitas dengan harga terjangkau.', 'icon' => 'cash-stack', 'urutan' => 6],
            ];

            foreach ($keunggulans as $keunggulan) {
                $keunggulan['is_active'] = true;
                $keunggulan['gambar'] = 'pmb/keunggulan/keunggulan-' . $keunggulan['urutan'] . '.jpg';
                KeunggulanPmb::create($keunggulan);
            }
            $this->command->info('✓ Keunggulan PMB: ' . count($keunggulans) . ' data');
        }

        // ==================
        // FASILITAS
        // ==================
        if (FasilitasPmb::count() == 0) {
            $fasilitass = [
                ['nama' => 'Laboratorium Komputer', 'deskripsi' => '5 lab komputer dengan total 200 unit PC spesifikasi tinggi untuk praktikum programming, jaringan, dan multimedia.', 'icon' => 'pc-display', 'urutan' => 1],
                ['nama' => 'Perpustakaan', 'deskripsi' => 'Perpustakaan dengan ribuan koleksi buku, e-book, dan akses ke jurnal internasional seperti IEEE dan Springer.', 'icon' => 'book', 'urutan' => 2],
                ['nama' => 'Ruang Kelas Ber-AC', 'deskripsi' => '30 ruang kelas dilengkapi AC, proyektor, sound system, dan akses WiFi untuk kenyamanan belajar.', 'icon' => 'door-open', 'urutan' => 3],
                ['nama' => 'Auditorium', 'deskripsi' => 'Auditorium berkapasitas 500 orang untuk seminar, wisuda, dan kegiatan kampus lainnya.', 'icon' => 'mic', 'urutan' => 4],
                ['nama' => 'Mushola', 'deskripsi' => 'Mushola yang luas dan nyaman untuk ibadah mahasiswa dan civitas akademika.', 'icon' => 'building', 'urutan' => 5],
                ['nama' => 'Kantin', 'deskripsi' => 'Kantin bersih dengan berbagai pilihan makanan dan minuman dengan harga mahasiswa.', 'icon' => 'cup-hot', 'urutan' => 6],
                ['nama' => 'Area Parkir', 'deskripsi' => 'Area parkir luas untuk motor dan mobil dengan keamanan 24 jam.', 'icon' => 'car-front', 'urutan' => 7],
                ['nama' => 'WiFi Kampus', 'deskripsi' => 'Akses internet WiFi gratis di seluruh area kampus dengan kecepatan tinggi.', 'icon' => 'wifi', 'urutan' => 8],
            ];

            foreach ($fasilitass as $fasilitas) {
                $fasilitas['is_active'] = true;
                $fasilitas['gambar'] = 'pmb/fasilitas/fasilitas-' . $fasilitas['urutan'] . '.jpg';
                FasilitasPmb::create($fasilitas);
            }
            $this->command->info('✓ Fasilitas PMB: ' . count($fasilitass) . ' data');
        }

        // ==================
        // KONTAK
        // ==================
        if (KontakPmb::count() == 0) {
            $kontaks = [
                ['type' => 'phone', 'label' => 'Telepon Kantor', 'value' => '(021) 7412-3456', 'icon' => 'telephone', 'link' => 'tel:02174123456', 'urutan' => 1],
                ['type' => 'whatsapp', 'label' => 'WhatsApp PMB', 'value' => '0812-3456-7890', 'icon' => 'whatsapp', 'link' => 'https://wa.me/6281234567890', 'urutan' => 2],
                ['type' => 'email', 'label' => 'Email PMB', 'value' => 'pmb@itts.ac.id', 'icon' => 'envelope', 'link' => 'mailto:pmb@itts.ac.id', 'urutan' => 3],
                ['type' => 'address', 'label' => 'Alamat Kampus', 'value' => 'Jl. Raya Serpong KM 7, Tangerang Selatan, Banten 15310', 'icon' => 'geo-alt', 'link' => null, 'urutan' => 4],
                ['type' => 'jam_operasional', 'label' => 'Jam Layanan', 'value' => 'Senin - Jumat: 08.00 - 16.00 WIB, Sabtu: 08.00 - 12.00 WIB', 'icon' => 'clock', 'link' => null, 'urutan' => 5],
                ['type' => 'instagram', 'label' => 'Instagram', 'value' => '@pmb.itts', 'icon' => 'instagram', 'link' => 'https://instagram.com/pmb.itts', 'urutan' => 6],
                ['type' => 'facebook', 'label' => 'Facebook', 'value' => 'PMB ITTS Official', 'icon' => 'facebook', 'link' => 'https://facebook.com/pmbitts', 'urutan' => 7],
                ['type' => 'youtube', 'label' => 'YouTube', 'value' => 'ITTS Official', 'icon' => 'youtube', 'link' => 'https://youtube.com/@ittsofficial', 'urutan' => 8],
            ];

            foreach ($kontaks as $kontak) {
                $kontak['is_active'] = true;
                KontakPmb::create($kontak);
            }
            $this->command->info('✓ Kontak PMB: ' . count($kontaks) . ' data');
        }

        // ==================
        // PENGATURAN/KONTEN
        // ==================
        $settings = [
            ['key' => 'nama_institusi', 'value' => 'Institut Teknologi Tangerang Selatan', 'label' => 'Nama Institusi', 'group' => 'general'],
            ['key' => 'singkatan_institusi', 'value' => 'ITTS', 'label' => 'Singkatan Institusi', 'group' => 'general'],
            ['key' => 'tagline', 'value' => 'Membangun Generasi Digital yang Kompeten dan Berintegritas', 'label' => 'Tagline', 'group' => 'general'],
            ['key' => 'tahun_berdiri', 'value' => '2010', 'label' => 'Tahun Berdiri', 'group' => 'general'],
            ['key' => 'total_prodi', 'value' => '4', 'label' => 'Total Prodi', 'group' => 'stats'],
            ['key' => 'total_mahasiswa', 'value' => '2500', 'label' => 'Total Mahasiswa', 'group' => 'stats'],
            ['key' => 'total_dosen', 'value' => '85', 'label' => 'Total Dosen', 'group' => 'stats'],
            ['key' => 'hero_title', 'value' => 'Raih Masa Depan Cemerlang Bersama ITTS', 'label' => 'Hero Title', 'group' => 'hero'],
            ['key' => 'hero_subtitle', 'value' => 'Pendaftaran Mahasiswa Baru 2026/2027 Telah Dibuka', 'label' => 'Hero Subtitle', 'group' => 'hero'],
            ['key' => 'meta_description', 'value' => 'Pendaftaran Mahasiswa Baru (PMB) Institut Teknologi Tangerang Selatan. Daftar sekarang dan raih masa depan cerah di bidang teknologi informasi.', 'label' => 'Meta Description', 'group' => 'seo'],
            ['key' => 'meta_keywords', 'value' => 'pmb itts, kuliah tangerang selatan, teknik informatika, sistem informasi, universitas swasta tangerang', 'label' => 'Meta Keywords', 'group' => 'seo'],
        ];

        foreach ($settings as $setting) {
            KontenPmb::updateOrCreate(
                ['key' => $setting['key']],
                array_merge($setting, ['is_active' => true])
            );
        }
        $this->command->info('✓ Pengaturan PMB: ' . count($settings) . ' data');

        $this->command->info('');
        $this->command->info('Seeding Konten PMB selesai!');
    }
}
