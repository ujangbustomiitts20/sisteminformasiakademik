<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PortalPmbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Konten PMB
        $konten = [
            ['key' => 'nama_institusi', 'value' => 'Universitas Contoh Indonesia', 'group' => 'general'],
            ['key' => 'singkatan_institusi', 'value' => 'UCI', 'group' => 'general'],
            ['key' => 'tagline', 'value' => 'Membangun Generasi Unggul untuk Indonesia', 'group' => 'general'],
            ['key' => 'hero_title', 'value' => 'Raih Masa Depanmu Bersama Kami', 'group' => 'hero'],
            ['key' => 'hero_subtitle', 'value' => 'Bergabunglah dengan ribuan mahasiswa yang telah mewujudkan mimpi mereka di universitas kami', 'group' => 'hero'],
            ['key' => 'tahun_berdiri', 'value' => '1985', 'group' => 'statistik'],
            ['key' => 'total_prodi', 'value' => '45', 'group' => 'statistik'],
            ['key' => 'total_mahasiswa', 'value' => '15000', 'group' => 'statistik'],
            ['key' => 'total_dosen', 'value' => '500', 'group' => 'statistik'],
            ['key' => 'meta_description', 'value' => 'Penerimaan Mahasiswa Baru Universitas Contoh Indonesia - Daftar sekarang dan wujudkan impianmu!', 'group' => 'seo'],
            ['key' => 'meta_keywords', 'value' => 'pmb, pendaftaran mahasiswa baru, universitas, kuliah', 'group' => 'seo'],
        ];

        foreach ($konten as $item) {
            DB::table('konten_pmb')->updateOrInsert(
                ['key' => $item['key']],
                array_merge($item, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // Seed Slider PMB
        $sliders = [
            [
                'judul' => 'Penerimaan Mahasiswa Baru 2026',
                'deskripsi' => 'Daftarkan dirimu sekarang dan jadilah bagian dari keluarga besar UCI',
                'link' => '/pmb-online/pendaftaran',
                'button_text' => 'Daftar Sekarang',
                'urutan' => 1,
                'is_active' => true,
            ],
            [
                'judul' => 'Beasiswa Prestasi 100%',
                'deskripsi' => 'Raih beasiswa penuh untuk mahasiswa berprestasi akademik dan non-akademik',
                'link' => '/pmb-online/berita',
                'button_text' => 'Info Selengkapnya',
                'urutan' => 2,
                'is_active' => true,
            ],
            [
                'judul' => 'Fasilitas Modern & Lengkap',
                'deskripsi' => 'Nikmati fasilitas kampus berstandar internasional untuk menunjang proses belajar',
                'link' => '/pmb-online/fasilitas',
                'button_text' => 'Lihat Fasilitas',
                'urutan' => 3,
                'is_active' => true,
            ],
        ];

        DB::table('slider_pmb')->truncate();
        foreach ($sliders as $slider) {
            DB::table('slider_pmb')->insert(array_merge($slider, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        // Seed FAQ PMB
        $faqs = [
            ['pertanyaan' => 'Bagaimana cara mendaftar sebagai mahasiswa baru?', 'jawaban' => 'Pendaftaran dapat dilakukan secara online melalui website PMB. Isi formulir pendaftaran, upload dokumen yang diperlukan, dan lakukan pembayaran biaya pendaftaran.', 'kategori' => 'Pendaftaran', 'urutan' => 1],
            ['pertanyaan' => 'Apa saja persyaratan untuk mendaftar?', 'jawaban' => "Persyaratan umum:\n1. Lulusan SMA/SMK/MA sederajat\n2. Pas foto 3x4 berlatar biru\n3. Scan KTP/KK\n4. Scan Ijazah/SKL\n5. Scan rapor semester 1-5", 'kategori' => 'Pendaftaran', 'urutan' => 2],
            ['pertanyaan' => 'Berapa biaya pendaftaran?', 'jawaban' => 'Biaya pendaftaran bervariasi tergantung jalur seleksi yang dipilih. Untuk jalur reguler Rp 300.000, jalur prestasi Rp 200.000, dan jalur undangan gratis.', 'kategori' => 'Biaya', 'urutan' => 3],
            ['pertanyaan' => 'Apakah ada beasiswa yang tersedia?', 'jawaban' => 'Ya, tersedia berbagai jenis beasiswa seperti Beasiswa Prestasi Akademik, Beasiswa Kurang Mampu, Beasiswa Hafidz Quran, dan Beasiswa Atlet Berprestasi.', 'kategori' => 'Beasiswa', 'urutan' => 4],
            ['pertanyaan' => 'Kapan pengumuman hasil seleksi?', 'jawaban' => 'Pengumuman hasil seleksi biasanya diumumkan 1-2 minggu setelah pelaksanaan ujian seleksi. Hasil dapat dicek melalui website PMB atau email yang terdaftar.', 'kategori' => 'Seleksi', 'urutan' => 5],
            ['pertanyaan' => 'Bagaimana jika saya tidak lulus di pilihan pertama?', 'jawaban' => 'Jika tidak lulus di pilihan pertama, Anda masih memiliki kesempatan untuk diterima di pilihan kedua (jika memenuhi passing grade). Jika keduanya tidak lulus, Anda dapat mendaftar di gelombang berikutnya.', 'kategori' => 'Seleksi', 'urutan' => 6],
            ['pertanyaan' => 'Apakah bisa membayar secara cicilan?', 'jawaban' => 'Ya, pembayaran biaya kuliah dapat dilakukan secara cicilan. Tersedia skema cicilan 2x, 3x, atau 4x per semester sesuai dengan kebijakan yang berlaku.', 'kategori' => 'Biaya', 'urutan' => 7],
            ['pertanyaan' => 'Dimana lokasi kampus?', 'jawaban' => 'Kampus utama berlokasi di Jl. Pendidikan No. 123, Kota Pendidikan. Terdapat juga kampus 2 di daerah selatan kota untuk beberapa program studi.', 'kategori' => 'Umum', 'urutan' => 8],
        ];

        DB::table('faq_pmb')->truncate();
        foreach ($faqs as $faq) {
            DB::table('faq_pmb')->insert(array_merge($faq, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        // Seed Testimoni PMB
        $testimoni = [
            [
                'nama' => 'Ahmad Fadli',
                'program_studi' => 'Teknik Informatika',
                'angkatan' => '2023',
                'testimoni' => 'Saya sangat puas kuliah di sini. Dosen-dosennya kompeten dan fasilitas laboratorium sangat memadai untuk praktikum.',
            ],
            [
                'nama' => 'Siti Nurhaliza',
                'program_studi' => 'Manajemen',
                'angkatan' => '2022',
                'testimoni' => 'Kampus ini memberikan banyak kesempatan magang di perusahaan ternama. Sangat membantu untuk karir saya setelah lulus nanti.',
            ],
            [
                'nama' => 'Budi Santoso',
                'program_studi' => 'Akuntansi',
                'angkatan' => '2021',
                'testimoni' => 'Alumni UCI banyak yang sukses di dunia kerja. Network yang dibangun selama kuliah sangat bermanfaat.',
            ],
        ];

        DB::table('testimoni_pmb')->truncate();
        foreach ($testimoni as $item) {
            DB::table('testimoni_pmb')->insert(array_merge($item, [
                'is_active' => true,
                'urutan' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        // Seed Keunggulan PMB
        $keunggulan = [
            ['judul' => 'Akreditasi Unggul', 'deskripsi' => 'Institusi terakreditasi A dengan standar pendidikan tinggi nasional dan internasional', 'icon' => 'award'],
            ['judul' => 'Dosen Berkualitas', 'deskripsi' => '80% dosen bergelar S3 dan berpengalaman di bidangnya masing-masing', 'icon' => 'mortarboard'],
            ['judul' => 'Fasilitas Modern', 'deskripsi' => 'Laboratorium, perpustakaan digital, dan fasilitas olahraga berstandar internasional', 'icon' => 'building'],
            ['judul' => 'Koneksi Industri', 'deskripsi' => 'Kerjasama dengan 100+ perusahaan nasional dan multinasional untuk magang dan rekrutmen', 'icon' => 'briefcase'],
            ['judul' => 'Beasiswa Lengkap', 'deskripsi' => 'Tersedia berbagai jenis beasiswa untuk mahasiswa berprestasi dan kurang mampu', 'icon' => 'cash-coin'],
            ['judul' => 'Lingkungan Kondusif', 'deskripsi' => 'Kampus hijau dan asri dengan suasana yang mendukung proses belajar mengajar', 'icon' => 'tree'],
        ];

        DB::table('keunggulan_pmb')->truncate();
        foreach ($keunggulan as $index => $item) {
            DB::table('keunggulan_pmb')->insert(array_merge($item, [
                'is_active' => true,
                'urutan' => $index + 1,
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        // Seed Fasilitas PMB
        $fasilitas = [
            ['nama' => 'Perpustakaan Digital', 'deskripsi' => 'Akses 24 jam ke ribuan jurnal dan buku elektronik', 'icon' => 'book'],
            ['nama' => 'Laboratorium Komputer', 'deskripsi' => 'Dilengkapi PC terbaru dan software berlisensi', 'icon' => 'pc-display'],
            ['nama' => 'Auditorium', 'deskripsi' => 'Kapasitas 1000 orang untuk kegiatan akademik dan non-akademik', 'icon' => 'building'],
            ['nama' => 'Klinik Kesehatan', 'deskripsi' => 'Layanan kesehatan gratis untuk mahasiswa', 'icon' => 'hospital'],
            ['nama' => 'Gedung Olahraga', 'deskripsi' => 'Lapangan indoor dan outdoor untuk berbagai cabang olahraga', 'icon' => 'trophy'],
            ['nama' => 'Kantin & Kafetaria', 'deskripsi' => 'Makanan sehat dan terjangkau untuk mahasiswa', 'icon' => 'cup-hot'],
            ['nama' => 'WiFi Kampus', 'deskripsi' => 'Internet berkecepatan tinggi di seluruh area kampus', 'icon' => 'wifi'],
            ['nama' => 'Parkir Luas', 'deskripsi' => 'Area parkir aman untuk motor dan mobil', 'icon' => 'car-front'],
            ['nama' => 'Masjid Kampus', 'deskripsi' => 'Sarana ibadah yang nyaman dan representatif', 'icon' => 'building'],
            ['nama' => 'Student Center', 'deskripsi' => 'Ruang kegiatan dan berkumpul mahasiswa', 'icon' => 'people'],
            ['nama' => 'Co-Working Space', 'deskripsi' => 'Area kerja kolaboratif untuk mahasiswa dan startup', 'icon' => 'laptop'],
            ['nama' => 'ATM Center', 'deskripsi' => 'Tersedia ATM dari berbagai bank', 'icon' => 'credit-card'],
        ];

        DB::table('fasilitas_pmb')->truncate();
        foreach ($fasilitas as $index => $item) {
            DB::table('fasilitas_pmb')->insert(array_merge($item, [
                'is_active' => true,
                'urutan' => $index + 1,
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        // Seed Berita PMB
        $berita = [
            [
                'judul' => 'Pendaftaran Mahasiswa Baru Gelombang 1 Tahun 2026 Resmi Dibuka',
                'slug' => 'pendaftaran-mahasiswa-baru-gelombang-1-tahun-2026-resmi-dibuka',
                'ringkasan' => 'Universitas Contoh Indonesia membuka pendaftaran mahasiswa baru gelombang pertama untuk tahun akademik 2026/2027.',
                'konten' => '<p>Universitas Contoh Indonesia dengan bangga mengumumkan pembukaan pendaftaran mahasiswa baru gelombang pertama untuk tahun akademik 2026/2027.</p><p>Pendaftaran dapat dilakukan secara online melalui website PMB mulai tanggal 2 Januari 2026 hingga 28 Februari 2026.</p><h3>Jalur Pendaftaran yang Tersedia:</h3><ul><li>Jalur Undangan (Bebas Biaya Pendaftaran)</li><li>Jalur Prestasi</li><li>Jalur Reguler</li></ul><p>Untuk informasi lebih lanjut, silakan hubungi panitia PMB melalui WhatsApp atau kunjungi website resmi kami.</p>',
                'kategori' => 'pengumuman',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(1),
            ],
            [
                'judul' => 'Tips Sukses Menghadapi Ujian Masuk Perguruan Tinggi',
                'slug' => 'tips-sukses-menghadapi-ujian-masuk-perguruan-tinggi',
                'ringkasan' => 'Berikut adalah tips dan trik untuk mempersiapkan diri menghadapi ujian masuk perguruan tinggi.',
                'konten' => '<p>Menghadapi ujian masuk perguruan tinggi membutuhkan persiapan yang matang. Berikut beberapa tips yang bisa membantu:</p><h3>1. Pahami Materi Ujian</h3><p>Pelajari kisi-kisi dan materi yang akan diujikan. Fokus pada mata pelajaran yang menjadi kelemahan Anda.</p><h3>2. Latihan Soal</h3><p>Kerjakan soal-soal latihan dari tahun-tahun sebelumnya untuk membiasakan diri dengan format ujian.</p><h3>3. Jaga Kesehatan</h3><p>Istirahat yang cukup dan makan makanan bergizi sangat penting untuk menjaga konsentrasi.</p><h3>4. Manajemen Waktu</h3><p>Buat jadwal belajar yang teratur dan patuhi jadwal tersebut.</p>',
                'kategori' => 'tips',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(3),
            ],
            [
                'judul' => 'Beasiswa Penuh untuk 100 Mahasiswa Berprestasi',
                'slug' => 'beasiswa-penuh-untuk-100-mahasiswa-berprestasi',
                'ringkasan' => 'UCI menyediakan beasiswa penuh untuk 100 mahasiswa berprestasi di bidang akademik dan non-akademik.',
                'konten' => '<p>Dalam rangka mendukung pendidikan berkualitas, Universitas Contoh Indonesia menyediakan beasiswa penuh untuk 100 mahasiswa berprestasi.</p><h3>Kategori Beasiswa:</h3><ul><li>Beasiswa Prestasi Akademik (50 kuota)</li><li>Beasiswa Prestasi Non-Akademik (30 kuota)</li><li>Beasiswa Hafidz Quran (20 kuota)</li></ul><h3>Persyaratan:</h3><ul><li>Nilai rapor rata-rata minimal 85</li><li>Memiliki prestasi yang dibuktikan dengan sertifikat</li><li>Lolos seleksi administrasi dan wawancara</li></ul>',
                'kategori' => 'berita',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(5),
            ],
        ];

        DB::table('berita_pmb')->truncate();
        foreach ($berita as $item) {
            DB::table('berita_pmb')->insert(array_merge($item, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        // Seed Kontak PMB
        DB::table('kontak_pmb')->truncate();
        $kontakItems = [
            ['type' => 'address', 'label' => 'Alamat', 'value' => 'Jl. Pendidikan No. 123, Kota Pendidikan', 'icon' => 'geo-alt', 'urutan' => 1],
            ['type' => 'phone', 'label' => 'Telepon', 'value' => '(021) 123-4567', 'icon' => 'telephone', 'urutan' => 2],
            ['type' => 'whatsapp', 'label' => 'WhatsApp', 'value' => '081234567890', 'icon' => 'whatsapp', 'link' => 'https://wa.me/6281234567890', 'urutan' => 3],
            ['type' => 'email', 'label' => 'Email', 'value' => 'pmb@uci.ac.id', 'icon' => 'envelope', 'link' => 'mailto:pmb@uci.ac.id', 'urutan' => 4],
            ['type' => 'jam_operasional', 'label' => 'Jam Operasional', 'value' => 'Sen-Jum: 08.00-16.00, Sab: 08.00-12.00', 'icon' => 'clock', 'urutan' => 5],
            ['type' => 'facebook', 'label' => 'Facebook', 'value' => 'UCI Official', 'icon' => 'facebook', 'link' => 'https://facebook.com/uciofficial', 'urutan' => 6],
            ['type' => 'instagram', 'label' => 'Instagram', 'value' => '@uci_official', 'icon' => 'instagram', 'link' => 'https://instagram.com/uci_official', 'urutan' => 7],
            ['type' => 'youtube', 'label' => 'YouTube', 'value' => 'UCI Official', 'icon' => 'youtube', 'link' => 'https://youtube.com/@uciofficial', 'urutan' => 8],
        ];
        
        foreach ($kontakItems as $item) {
            DB::table('kontak_pmb')->insert(array_merge($item, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        $this->command->info('Portal PMB seeder completed successfully!');
    }
}
