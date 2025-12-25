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
        // Tabel Bimbingan Akademik
        Schema::create('bimbingan_akademik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->date('tanggal_bimbingan');
            $table->enum('jenis', ['KRS', 'Akademik', 'Pribadi', 'Karir', 'Lainnya'])->default('Akademik');
            $table->text('topik');
            $table->text('catatan_mahasiswa')->nullable();
            $table->text('catatan_dosen')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->enum('status', ['Dijadwalkan', 'Selesai', 'Dibatalkan'])->default('Dijadwalkan');
            $table->timestamps();
        });

        // Tabel Persetujuan KRS oleh Dosen Wali
        Schema::create('persetujuan_krs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->enum('status', ['Pending', 'Disetujui', 'Ditolak', 'Revisi'])->default('Pending');
            $table->text('catatan_mahasiswa')->nullable();
            $table->text('catatan_dosen')->nullable();
            $table->integer('total_sks')->default(0);
            $table->timestamp('tanggal_pengajuan')->nullable();
            $table->timestamp('tanggal_persetujuan')->nullable();
            $table->timestamps();

            $table->unique(['mahasiswa_id', 'tahun_akademik_id'], 'persetujuan_krs_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persetujuan_krs');
        Schema::dropIfExists('bimbingan_akademik');
    }
};
