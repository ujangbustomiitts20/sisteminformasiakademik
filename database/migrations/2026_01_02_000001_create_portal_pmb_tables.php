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
        // Konten Halaman PMB (Hero, About, dll)
        Schema::create('konten_pmb', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // hero_title, hero_subtitle, about_section, dll
            $table->string('type')->default('text'); // text, html, image, json
            $table->text('value')->nullable();
            $table->string('label')->nullable(); // Label untuk admin
            $table->string('group')->default('general'); // hero, about, contact, seo
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Slider/Banner PMB
        Schema::create('slider_pmb', function (Blueprint $table) {
            $table->id();
            $table->string('judul')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->string('link')->nullable();
            $table->string('button_text')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // FAQ PMB
        Schema::create('faq_pmb', function (Blueprint $table) {
            $table->id();
            $table->string('pertanyaan');
            $table->text('jawaban');
            $table->string('kategori')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Testimoni Alumni/Mahasiswa
        Schema::create('testimoni_pmb', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('angkatan')->nullable();
            $table->string('program_studi')->nullable();
            $table->text('testimoni');
            $table->string('foto')->nullable();
            $table->string('pekerjaan')->nullable(); // Untuk alumni
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Galeri PMB
        Schema::create('galeri_pmb', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('gambar');
            $table->string('kategori')->nullable(); // kampus, kegiatan, fasilitas
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Berita/Pengumuman PMB
        Schema::create('berita_pmb', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('ringkasan')->nullable();
            $table->longText('konten');
            $table->string('gambar')->nullable();
            $table->string('kategori')->default('berita'); // berita, pengumuman, info
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // Keunggulan/Why Choose Us
        Schema::create('keunggulan_pmb', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('icon')->nullable(); // Bootstrap icon name
            $table->string('gambar')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Fasilitas Kampus
        Schema::create('fasilitas_pmb', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->string('icon')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Kontak & Sosial Media
        Schema::create('kontak_pmb', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // phone, email, whatsapp, address, facebook, instagram, youtube, twitter
            $table->string('label');
            $table->string('value');
            $table->string('icon')->nullable();
            $table->string('link')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontak_pmb');
        Schema::dropIfExists('fasilitas_pmb');
        Schema::dropIfExists('keunggulan_pmb');
        Schema::dropIfExists('berita_pmb');
        Schema::dropIfExists('galeri_pmb');
        Schema::dropIfExists('testimoni_pmb');
        Schema::dropIfExists('faq_pmb');
        Schema::dropIfExists('slider_pmb');
        Schema::dropIfExists('konten_pmb');
    }
};
