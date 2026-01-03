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
        // Tabel pengajuan konversi nilai (mahasiswa pindahan)
        Schema::create('pengajuan_konversi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengajuan')->unique();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->string('universitas_asal');
            $table->string('program_studi_asal');
            $table->string('nim_asal');
            $table->year('tahun_masuk_asal');
            $table->string('dokumen_transkrip')->nullable(); // Path file transkrip
            $table->string('dokumen_silabus')->nullable(); // Path file silabus
            $table->string('dokumen_pendukung')->nullable(); // Dokumen lainnya
            $table->enum('status', ['draft', 'diajukan', 'diproses', 'disetujui', 'ditolak'])->default('draft');
            $table->text('catatan')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('tanggal_diproses')->nullable();
            $table->timestamps();
        });

        // Detail konversi per mata kuliah
        Schema::create('detail_konversi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_konversi_id')->constrained('pengajuan_konversi')->onDelete('cascade');
            // Mata kuliah asal
            $table->string('kode_mk_asal');
            $table->string('nama_mk_asal');
            $table->integer('sks_asal');
            $table->string('nilai_asal'); // A, B, C, dll
            $table->decimal('bobot_asal', 3, 2)->nullable(); // 4.00, 3.00, dll
            // Mata kuliah tujuan (ekuivalensi)
            $table->foreignId('mata_kuliah_id')->nullable()->constrained('mata_kuliah')->onDelete('set null');
            $table->string('nilai_konversi')->nullable(); // Nilai yang dikonversi
            $table->decimal('bobot_konversi', 3, 2)->nullable();
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'tidak_dikonversi'])->default('pending');
            $table->text('alasan')->nullable(); // Alasan jika ditolak
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_konversi');
        Schema::dropIfExists('pengajuan_konversi');
    }
};
