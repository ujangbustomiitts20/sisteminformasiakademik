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
        // Tabel Kurikulum
        Schema::create('kurikulum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_studi_id')->constrained('program_studi')->onDelete('cascade');
            $table->string('kode', 20)->unique();
            $table->string('nama'); // e.g., "Kurikulum 2020", "Kurikulum 2024"
            $table->year('tahun_mulai');
            $table->year('tahun_selesai')->nullable();
            $table->integer('total_sks_wajib')->default(0);
            $table->integer('total_sks_pilihan')->default(0);
            $table->integer('total_sks_lulus')->default(144);
            $table->integer('minimal_semester')->default(8);
            $table->integer('maksimal_semester')->default(14);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // Tabel Kurikulum Mata Kuliah (mapping MK ke Kurikulum)
        Schema::create('kurikulum_mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kurikulum_id')->constrained('kurikulum')->onDelete('cascade');
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->onDelete('cascade');
            $table->integer('semester_rekomendasi'); // Semester yang direkomendasikan
            $table->enum('kategori', ['Wajib', 'Pilihan', 'Wajib Prodi', 'Pilihan Prodi', 'MKU'])->default('Wajib');
            $table->timestamps();

            $table->unique(['kurikulum_id', 'mata_kuliah_id'], 'kurikulum_mk_unique');
        });

        // Tambah kolom kurikulum_id ke mahasiswa
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->foreignId('kurikulum_id')->nullable()->after('dosen_wali_id')->constrained('kurikulum')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['kurikulum_id']);
            $table->dropColumn('kurikulum_id');
        });
        Schema::dropIfExists('kurikulum_mata_kuliah');
        Schema::dropIfExists('kurikulum');
    }
};
