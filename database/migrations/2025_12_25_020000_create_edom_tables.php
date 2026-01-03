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
        // Periode EDOM
        Schema::create('periode_edom', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('draft');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Pertanyaan EDOM
        Schema::create('pertanyaan_edom', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->text('pertanyaan');
            $table->enum('kategori', ['kompetensi_pedagogik', 'kompetensi_profesional', 'kompetensi_kepribadian', 'kompetensi_sosial']);
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Jawaban EDOM
        Schema::create('jawaban_edom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_edom_id')->constrained('periode_edom')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('jadwal_kuliah_id')->constrained('jadwal_kuliah')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->foreignId('pertanyaan_edom_id')->constrained('pertanyaan_edom')->onDelete('cascade');
            $table->integer('nilai'); // 1-5
            $table->timestamps();

            // Unique constraint - 1 mahasiswa hanya bisa mengisi 1x per pertanyaan per jadwal
            $table->unique(['periode_edom_id', 'mahasiswa_id', 'jadwal_kuliah_id', 'pertanyaan_edom_id'], 'unique_jawaban_edom');
        });

        // Rekap EDOM per Dosen per Periode
        Schema::create('rekap_edom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_edom_id')->constrained('periode_edom')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->foreignId('jadwal_kuliah_id')->nullable()->constrained('jadwal_kuliah')->onDelete('cascade');
            $table->decimal('rata_rata_pedagogik', 4, 2)->default(0);
            $table->decimal('rata_rata_profesional', 4, 2)->default(0);
            $table->decimal('rata_rata_kepribadian', 4, 2)->default(0);
            $table->decimal('rata_rata_sosial', 4, 2)->default(0);
            $table->decimal('rata_rata_total', 4, 2)->default(0);
            $table->integer('jumlah_responden')->default(0);
            $table->timestamps();

            $table->unique(['periode_edom_id', 'dosen_id', 'jadwal_kuliah_id'], 'unique_rekap_edom');
        });

        // Komentar EDOM (opsional dari mahasiswa)
        Schema::create('komentar_edom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_edom_id')->constrained('periode_edom')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('jadwal_kuliah_id')->constrained('jadwal_kuliah')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->text('komentar');
            $table->text('saran')->nullable();
            $table->timestamps();

            $table->unique(['periode_edom_id', 'mahasiswa_id', 'jadwal_kuliah_id'], 'unique_komentar_edom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komentar_edom');
        Schema::dropIfExists('rekap_edom');
        Schema::dropIfExists('jawaban_edom');
        Schema::dropIfExists('pertanyaan_edom');
        Schema::dropIfExists('periode_edom');
    }
};
