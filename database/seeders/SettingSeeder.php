<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ================================
            // GENERAL SETTINGS
            // ================================
            [
                'key' => 'app_name',
                'value' => 'SIAKAD',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Nama Aplikasi',
                'description' => 'Nama aplikasi yang ditampilkan di header',
                'order' => 1
            ],
            [
                'key' => 'app_description',
                'value' => 'Sistem Informasi Akademik',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Deskripsi Aplikasi',
                'description' => 'Deskripsi singkat aplikasi',
                'order' => 2
            ],
            [
                'key' => 'app_version',
                'value' => '1.0.0',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Versi Aplikasi',
                'description' => 'Versi aplikasi saat ini',
                'order' => 3
            ],

            // ================================
            // INSTITUTION SETTINGS (Identitas Kampus)
            // ================================
            [
                'key' => 'institution_yayasan',
                'value' => 'Yayasan Pendidikan Telkom',
                'type' => 'text',
                'group' => 'institution',
                'label' => 'Nama Yayasan',
                'description' => 'Nama yayasan penyelenggara (untuk kop surat)',
                'order' => 1
            ],
            [
                'key' => 'institution_name',
                'value' => 'Institut Teknologi Telkom Surabaya',
                'type' => 'text',
                'group' => 'institution',
                'label' => 'Nama Institusi',
                'description' => 'Nama lengkap institusi/universitas',
                'order' => 2
            ],
            [
                'key' => 'institution_short_name',
                'value' => 'ITTelkom Surabaya',
                'type' => 'text',
                'group' => 'institution',
                'label' => 'Singkatan Institusi',
                'description' => 'Nama singkat institusi',
                'order' => 3
            ],
            [
                'key' => 'institution_logo',
                'value' => null,
                'type' => 'image',
                'group' => 'institution',
                'label' => 'Logo Institusi (Kop Surat)',
                'description' => 'Logo untuk kop surat dan cetakan PDF (PNG/JPG, max 2MB, disarankan persegi 500x500px)',
                'order' => 4
            ],
            [
                'key' => 'institution_logo_sidebar',
                'value' => null,
                'type' => 'image',
                'group' => 'institution',
                'label' => 'Logo Sidebar/Menu',
                'description' => 'Logo untuk sidebar menu aplikasi (PNG/JPG, max 1MB, disarankan persegi atau horizontal)',
                'order' => 5
            ],
            [
                'key' => 'institution_logo_wide',
                'value' => null,
                'type' => 'image',
                'group' => 'institution',
                'label' => 'Logo Horizontal',
                'description' => 'Logo horizontal untuk header laporan dan banner (PNG/JPG, max 2MB)',
                'order' => 6
            ],
            [
                'key' => 'institution_favicon',
                'value' => null,
                'type' => 'image',
                'group' => 'institution',
                'label' => 'Favicon',
                'description' => 'Favicon untuk browser (ICO/PNG, max 512KB, 32x32px atau 64x64px)',
                'order' => 7
            ],
            [
                'key' => 'institution_address',
                'value' => 'Jl. Ketintang No. 156, Gayungan, Surabaya, Jawa Timur 60231',
                'type' => 'textarea',
                'group' => 'institution',
                'label' => 'Alamat Institusi',
                'description' => 'Alamat lengkap institusi',
                'order' => 7
            ],
            [
                'key' => 'institution_city',
                'value' => 'Surabaya',
                'type' => 'text',
                'group' => 'institution',
                'label' => 'Kota',
                'description' => 'Kota lokasi institusi',
                'order' => 8
            ],
            [
                'key' => 'institution_province',
                'value' => 'Jawa Timur',
                'type' => 'text',
                'group' => 'institution',
                'label' => 'Provinsi',
                'description' => 'Provinsi lokasi institusi',
                'order' => 9
            ],
            [
                'key' => 'institution_postal_code',
                'value' => '60231',
                'type' => 'text',
                'group' => 'institution',
                'label' => 'Kode Pos',
                'description' => 'Kode pos institusi',
                'order' => 10
            ],
            [
                'key' => 'institution_accreditation',
                'value' => 'Baik Sekali',
                'type' => 'text',
                'group' => 'institution',
                'label' => 'Akreditasi Institusi',
                'description' => 'Status akreditasi institusi (A/B/C/Baik Sekali/Unggul)',
                'order' => 11
            ],
            [
                'key' => 'institution_sk_akreditasi',
                'value' => '',
                'type' => 'text',
                'group' => 'institution',
                'label' => 'SK Akreditasi',
                'description' => 'Nomor SK Akreditasi institusi',
                'order' => 12
            ],
            [
                'key' => 'institution_sk_pendirian',
                'value' => '',
                'type' => 'text',
                'group' => 'institution',
                'label' => 'SK Pendirian',
                'description' => 'Nomor SK Pendirian institusi',
                'order' => 13
            ],
            [
                'key' => 'institution_npsn',
                'value' => '',
                'type' => 'text',
                'group' => 'institution',
                'label' => 'NPSN',
                'description' => 'Nomor Pokok Sekolah Nasional',
                'order' => 14
            ],

            // ================================
            // CONTACT SETTINGS
            // ================================
            [
                'key' => 'contact_email',
                'value' => 'info@ittelkom-sby.ac.id',
                'type' => 'email',
                'group' => 'contact',
                'label' => 'Email Resmi',
                'description' => 'Email resmi institusi',
                'order' => 1
            ],
            [
                'key' => 'contact_phone',
                'value' => '(031) 8280800',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Telepon',
                'description' => 'Nomor telepon institusi',
                'order' => 2
            ],
            [
                'key' => 'contact_fax',
                'value' => '(031) 8280800',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Fax',
                'description' => 'Nomor fax institusi',
                'order' => 3
            ],
            [
                'key' => 'contact_whatsapp',
                'value' => '',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'WhatsApp',
                'description' => 'Nomor WhatsApp institusi (format: 62812xxx)',
                'order' => 4
            ],
            [
                'key' => 'contact_website',
                'value' => 'https://ittelkom-sby.ac.id',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Website',
                'description' => 'Website resmi institusi',
                'order' => 5
            ],
            [
                'key' => 'contact_instagram',
                'value' => '',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Instagram',
                'description' => 'Username Instagram (tanpa @)',
                'order' => 6
            ],
            [
                'key' => 'contact_facebook',
                'value' => '',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Facebook',
                'description' => 'URL halaman Facebook',
                'order' => 7
            ],
            [
                'key' => 'contact_twitter',
                'value' => '',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Twitter/X',
                'description' => 'Username Twitter/X (tanpa @)',
                'order' => 8
            ],
            [
                'key' => 'contact_youtube',
                'value' => '',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'YouTube',
                'description' => 'URL channel YouTube',
                'order' => 9
            ],

            // ================================
            // EMAIL / MAIL SERVER SETTINGS
            // ================================
            [
                'key' => 'mail_driver',
                'value' => 'smtp',
                'type' => 'select',
                'group' => 'email',
                'label' => 'Mail Driver',
                'description' => 'Driver untuk pengiriman email',
                'order' => 1
            ],
            [
                'key' => 'mail_host',
                'value' => 'smtp.gmail.com',
                'type' => 'text',
                'group' => 'email',
                'label' => 'SMTP Host',
                'description' => 'Server SMTP untuk pengiriman email',
                'order' => 2
            ],
            [
                'key' => 'mail_port',
                'value' => '587',
                'type' => 'number',
                'group' => 'email',
                'label' => 'SMTP Port',
                'description' => 'Port SMTP (587 untuk TLS, 465 untuk SSL)',
                'order' => 3
            ],
            [
                'key' => 'mail_username',
                'value' => '',
                'type' => 'text',
                'group' => 'email',
                'label' => 'SMTP Username',
                'description' => 'Username untuk autentikasi SMTP',
                'order' => 4
            ],
            [
                'key' => 'mail_password',
                'value' => '',
                'type' => 'password',
                'group' => 'email',
                'label' => 'SMTP Password',
                'description' => 'Password untuk autentikasi SMTP',
                'order' => 5
            ],
            [
                'key' => 'mail_encryption',
                'value' => 'tls',
                'type' => 'select',
                'group' => 'email',
                'label' => 'Enkripsi',
                'description' => 'Metode enkripsi (TLS/SSL)',
                'order' => 6
            ],
            [
                'key' => 'mail_from_address',
                'value' => 'noreply@ittelkom-sby.ac.id',
                'type' => 'email',
                'group' => 'email',
                'label' => 'From Address',
                'description' => 'Alamat email pengirim',
                'order' => 7
            ],
            [
                'key' => 'mail_from_name',
                'value' => 'SIAKAD ITTelkom Surabaya',
                'type' => 'text',
                'group' => 'email',
                'label' => 'From Name',
                'description' => 'Nama pengirim email',
                'order' => 8
            ],

            // ================================
            // ACADEMIC SETTINGS
            // ================================
            [
                'key' => 'academic_year_format',
                'value' => 'YYYY/YYYY',
                'type' => 'text',
                'group' => 'academic',
                'label' => 'Format Tahun Akademik',
                'description' => 'Format penulisan tahun akademik',
                'order' => 1
            ],
            [
                'key' => 'max_sks_per_semester',
                'value' => '24',
                'type' => 'number',
                'group' => 'academic',
                'label' => 'Maks SKS per Semester',
                'description' => 'Maksimal SKS yang bisa diambil per semester',
                'order' => 2
            ],
            [
                'key' => 'min_sks_per_semester',
                'value' => '12',
                'type' => 'number',
                'group' => 'academic',
                'label' => 'Min SKS per Semester',
                'description' => 'Minimal SKS yang harus diambil per semester',
                'order' => 3
            ],
            [
                'key' => 'mode_krs',
                'value' => 'pilihan',
                'type' => 'select',
                'group' => 'academic',
                'label' => 'Mode Pengisian KRS',
                'description' => 'Mode pengisian KRS (pilihan/paket)',
                'order' => 4
            ],
            [
                'key' => 'passing_grade',
                'value' => '60',
                'type' => 'number',
                'group' => 'academic',
                'label' => 'Nilai Kelulusan',
                'description' => 'Nilai minimum untuk lulus mata kuliah',
                'order' => 5
            ],
            [
                'key' => 'max_study_years',
                'value' => '7',
                'type' => 'number',
                'group' => 'academic',
                'label' => 'Maks Tahun Studi',
                'description' => 'Maksimal tahun studi (untuk S1)',
                'order' => 6
            ],
            [
                'key' => 'attendance_minimum',
                'value' => '75',
                'type' => 'number',
                'group' => 'academic',
                'label' => 'Kehadiran Minimum (%)',
                'description' => 'Persentase kehadiran minimum untuk mengikuti ujian',
                'order' => 7
            ],
            [
                'key' => 'total_pertemuan',
                'value' => '16',
                'type' => 'number',
                'group' => 'academic',
                'label' => 'Total Pertemuan',
                'description' => 'Jumlah total pertemuan per semester',
                'order' => 8
            ],

            // ================================
            // APPEARANCE SETTINGS
            // ================================
            [
                'key' => 'primary_color',
                'value' => '#dc2626',
                'type' => 'color',
                'group' => 'appearance',
                'label' => 'Warna Primer',
                'description' => 'Warna utama tema aplikasi',
                'order' => 1
            ],
            [
                'key' => 'secondary_color',
                'value' => '#1e293b',
                'type' => 'color',
                'group' => 'appearance',
                'label' => 'Warna Sekunder',
                'description' => 'Warna sekunder tema aplikasi',
                'order' => 2
            ],
            [
                'key' => 'sidebar_dark',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'appearance',
                'label' => 'Sidebar Gelap',
                'description' => 'Gunakan tema gelap untuk sidebar',
                'order' => 3
            ],
            [
                'key' => 'footer_text',
                'value' => '© 2025 SIAKAD ITTelkom Surabaya. All rights reserved.',
                'type' => 'text',
                'group' => 'appearance',
                'label' => 'Teks Footer',
                'description' => 'Teks yang ditampilkan di footer aplikasi',
                'order' => 4
            ],
            [
                'key' => 'login_background',
                'value' => null,
                'type' => 'image',
                'group' => 'appearance',
                'label' => 'Background Login',
                'description' => 'Gambar background halaman login (PNG/JPG, max 2MB)',
                'order' => 5
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'group' => $setting['group'],
                    'label' => $setting['label'],
                    'description' => $setting['description'] ?? null,
                    'order' => $setting['order'] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Settings seeded successfully!');
    }
}
