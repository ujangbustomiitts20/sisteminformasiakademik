<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, image, boolean, number, email, color
            $table->string('group')->default('general'); // general, institution, academic, contact, appearance
            $table->string('label');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Insert default settings
        $settings = [
            // General Settings
            ['key' => 'app_name', 'value' => 'SIAKAD', 'type' => 'text', 'group' => 'general', 'label' => 'Nama Aplikasi', 'description' => 'Nama aplikasi yang ditampilkan di header', 'order' => 1],
            ['key' => 'app_description', 'value' => 'Sistem Informasi Akademik', 'type' => 'text', 'group' => 'general', 'label' => 'Deskripsi Aplikasi', 'description' => 'Deskripsi singkat aplikasi', 'order' => 2],
            ['key' => 'app_version', 'value' => '1.0.0', 'type' => 'text', 'group' => 'general', 'label' => 'Versi Aplikasi', 'description' => 'Versi aplikasi saat ini', 'order' => 3],
            
            // Institution Settings
            ['key' => 'institution_name', 'value' => 'Universitas Contoh', 'type' => 'text', 'group' => 'institution', 'label' => 'Nama Institusi', 'description' => 'Nama lengkap institusi/universitas', 'order' => 1],
            ['key' => 'institution_short_name', 'value' => 'UNIV', 'type' => 'text', 'group' => 'institution', 'label' => 'Singkatan Institusi', 'description' => 'Nama singkat institusi', 'order' => 2],
            ['key' => 'institution_logo', 'value' => null, 'type' => 'image', 'group' => 'institution', 'label' => 'Logo Institusi', 'description' => 'Logo institusi (PNG/JPG, max 2MB)', 'order' => 3],
            ['key' => 'institution_favicon', 'value' => null, 'type' => 'image', 'group' => 'institution', 'label' => 'Favicon', 'description' => 'Favicon untuk browser (ICO/PNG, max 500KB)', 'order' => 4],
            ['key' => 'institution_address', 'value' => 'Jl. Contoh No. 123, Kota, Provinsi 12345', 'type' => 'textarea', 'group' => 'institution', 'label' => 'Alamat Institusi', 'description' => 'Alamat lengkap institusi', 'order' => 5],
            ['key' => 'institution_accreditation', 'value' => 'A', 'type' => 'text', 'group' => 'institution', 'label' => 'Akreditasi', 'description' => 'Status akreditasi institusi', 'order' => 6],
            
            // Contact Settings
            ['key' => 'contact_email', 'value' => 'info@universitas.ac.id', 'type' => 'email', 'group' => 'contact', 'label' => 'Email', 'description' => 'Email resmi institusi', 'order' => 1],
            ['key' => 'contact_phone', 'value' => '(021) 123-4567', 'type' => 'text', 'group' => 'contact', 'label' => 'Telepon', 'description' => 'Nomor telepon institusi', 'order' => 2],
            ['key' => 'contact_fax', 'value' => '(021) 123-4568', 'type' => 'text', 'group' => 'contact', 'label' => 'Fax', 'description' => 'Nomor fax institusi', 'order' => 3],
            ['key' => 'contact_website', 'value' => 'https://www.universitas.ac.id', 'type' => 'text', 'group' => 'contact', 'label' => 'Website', 'description' => 'Website resmi institusi', 'order' => 4],
            
            // Academic Settings
            ['key' => 'academic_year_format', 'value' => 'YYYY/YYYY', 'type' => 'text', 'group' => 'academic', 'label' => 'Format Tahun Akademik', 'description' => 'Format penulisan tahun akademik', 'order' => 1],
            ['key' => 'max_sks_per_semester', 'value' => '24', 'type' => 'number', 'group' => 'academic', 'label' => 'Maks SKS per Semester', 'description' => 'Maksimal SKS yang bisa diambil per semester', 'order' => 2],
            ['key' => 'min_sks_per_semester', 'value' => '12', 'type' => 'number', 'group' => 'academic', 'label' => 'Min SKS per Semester', 'description' => 'Minimal SKS yang harus diambil per semester', 'order' => 3],
            ['key' => 'passing_grade', 'value' => '60', 'type' => 'number', 'group' => 'academic', 'label' => 'Nilai Kelulusan', 'description' => 'Nilai minimum untuk lulus mata kuliah', 'order' => 4],
            ['key' => 'max_study_years', 'value' => '7', 'type' => 'number', 'group' => 'academic', 'label' => 'Maks Tahun Studi', 'description' => 'Maksimal tahun studi (untuk S1)', 'order' => 5],
            ['key' => 'attendance_minimum', 'value' => '75', 'type' => 'number', 'group' => 'academic', 'label' => 'Kehadiran Minimum (%)', 'description' => 'Persentase kehadiran minimum untuk mengikuti ujian', 'order' => 6],
            
            // Appearance Settings
            ['key' => 'primary_color', 'value' => '#0d6efd', 'type' => 'color', 'group' => 'appearance', 'label' => 'Warna Primer', 'description' => 'Warna utama tema aplikasi', 'order' => 1],
            ['key' => 'secondary_color', 'value' => '#6c757d', 'type' => 'color', 'group' => 'appearance', 'label' => 'Warna Sekunder', 'description' => 'Warna sekunder tema aplikasi', 'order' => 2],
            ['key' => 'sidebar_dark', 'value' => '1', 'type' => 'boolean', 'group' => 'appearance', 'label' => 'Sidebar Gelap', 'description' => 'Gunakan tema gelap untuk sidebar', 'order' => 3],
            ['key' => 'footer_text', 'value' => '© 2025 SIAKAD. All rights reserved.', 'type' => 'text', 'group' => 'appearance', 'label' => 'Teks Footer', 'description' => 'Teks yang ditampilkan di footer', 'order' => 4],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
