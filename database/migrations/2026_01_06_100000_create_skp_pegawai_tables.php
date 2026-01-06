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
        // Tabel SKP Pegawai
        Schema::create('skp_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->year('tahun');
            $table->string('periode', 100)->nullable(); // Semester 1, Semester 2, dll
            $table->string('no_skp', 50)->unique();
            $table->date('tanggal_skp')->nullable();
            $table->foreignId('pejabat_penilai_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('atasan_penilai_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->decimal('nilai_skp', 5, 2)->nullable();
            $table->decimal('nilai_perilaku', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->enum('predikat', ['sangat_baik', 'baik', 'cukup', 'kurang', 'buruk'])->nullable();
            $table->enum('status', ['draft', 'diajukan', 'dinilai', 'disetujui', 'final'])->default('draft');
            $table->text('catatan')->nullable();
            $table->date('tanggal_penilaian')->nullable();
            $table->string('file_skp')->nullable();
            $table->timestamps();

            $table->index(['dosen_id', 'tahun']);
            $table->index(['pegawai_id', 'tahun']);
        });

        // Tabel Target SKP
        Schema::create('target_skp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skp_pegawai_id')->constrained('skp_pegawai')->cascadeOnDelete();
            $table->integer('urutan')->default(1);
            $table->text('uraian_kegiatan');
            $table->string('satuan', 50)->nullable();
            $table->decimal('target_kuantitas', 10, 2)->nullable();
            $table->decimal('target_kualitas', 5, 2)->nullable();
            $table->decimal('target_waktu', 10, 2)->nullable(); // dalam bulan
            $table->decimal('target_biaya', 15, 2)->nullable();
            $table->decimal('realisasi_kuantitas', 10, 2)->nullable();
            $table->decimal('realisasi_kualitas', 5, 2)->nullable();
            $table->decimal('realisasi_waktu', 10, 2)->nullable();
            $table->decimal('realisasi_biaya', 15, 2)->nullable();
            $table->decimal('nilai_capaian', 5, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('skp_pegawai_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_skp');
        Schema::dropIfExists('skp_pegawai');
    }
};
