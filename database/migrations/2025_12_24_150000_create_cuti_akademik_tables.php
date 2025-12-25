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
        // Tabel Cuti Akademik
        Schema::create('cuti_akademik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->string('nomor_surat')->nullable();
            $table->enum('alasan', ['Keuangan', 'Kesehatan', 'Keluarga', 'Pekerjaan', 'Lainnya']);
            $table->text('keterangan');
            $table->string('dokumen_pendukung')->nullable(); // Upload file
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('jumlah_semester')->default(1);
            $table->enum('status', ['Pending', 'Disetujui Kaprodi', 'Disetujui Dekan', 'Ditolak', 'Selesai'])->default('Pending');
            $table->foreignId('disetujui_kaprodi_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('tanggal_persetujuan_kaprodi')->nullable();
            $table->text('catatan_kaprodi')->nullable();
            $table->foreignId('disetujui_dekan_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('tanggal_persetujuan_dekan')->nullable();
            $table->text('catatan_dekan')->nullable();
            $table->timestamps();

            $table->unique(['mahasiswa_id', 'tahun_akademik_id'], 'cuti_akademik_unique');
        });

        // Tabel History Status Mahasiswa
        Schema::create('history_status_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->string('status_lama', 20);
            $table->string('status_baru', 20);
            $table->text('keterangan')->nullable();
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_status_mahasiswa');
        Schema::dropIfExists('cuti_akademik');
    }
};
