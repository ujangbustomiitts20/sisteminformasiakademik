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
        // Tabel Periode Wisuda
        Schema::create('periode_wisuda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->string('nama'); // Wisuda Periode I 2024/2025
            $table->date('tanggal_wisuda');
            $table->date('tanggal_buka_pendaftaran');
            $table->date('tanggal_tutup_pendaftaran');
            $table->date('tanggal_yudisium')->nullable();
            $table->string('lokasi')->nullable();
            $table->integer('kuota')->nullable();
            $table->decimal('biaya_wisuda', 12, 2)->default(0);
            $table->text('persyaratan')->nullable();
            $table->enum('status', ['Draft', 'Dibuka', 'Ditutup', 'Selesai'])->default('Draft');
            $table->timestamps();
        });

        // Tabel Pendaftaran Wisuda
        Schema::create('pendaftaran_wisuda', function (Blueprint $table) {
            $table->id();
            $table->string('no_pendaftaran')->unique();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('periode_wisuda_id')->constrained('periode_wisuda')->onDelete('cascade');
            $table->date('tanggal_daftar');
            $table->decimal('ipk', 3, 2);
            $table->integer('total_sks');
            $table->string('judul_skripsi')->nullable();
            $table->date('tanggal_lulus_sidang')->nullable();
            $table->enum('status', ['Pending', 'Verifikasi Berkas', 'Lolos Yudisium', 'Ditolak', 'Lulus', 'Batal'])->default('Pending');
            $table->string('foto_formal')->nullable();
            $table->string('bukti_bebas_pustaka')->nullable();
            $table->string('bukti_bebas_keuangan')->nullable();
            $table->string('bukti_pembayaran_wisuda')->nullable();
            $table->text('catatan_verifikasi')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->timestamps();
            
            $table->unique(['mahasiswa_id', 'periode_wisuda_id']);
        });

        // Tabel Yudisium
        Schema::create('yudisium', function (Blueprint $table) {
            $table->id();
            $table->string('no_yudisium')->unique();
            $table->foreignId('pendaftaran_wisuda_id')->constrained('pendaftaran_wisuda')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->date('tanggal_yudisium');
            $table->decimal('ipk_akhir', 3, 2);
            $table->integer('total_sks_lulus');
            $table->string('predikat'); // Cum Laude, Sangat Memuaskan, Memuaskan, Cukup
            $table->date('tanggal_masuk'); // Tanggal mulai kuliah
            $table->date('tanggal_lulus');
            $table->integer('masa_studi_bulan'); // Dalam bulan
            $table->string('no_ijazah')->nullable();
            $table->string('no_transkrip')->nullable();
            $table->enum('status', ['Pending', 'Disetujui', 'Ditolak'])->default('Pending');
            $table->text('catatan')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('tanggal_proses')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yudisium');
        Schema::dropIfExists('pendaftaran_wisuda');
        Schema::dropIfExists('periode_wisuda');
    }
};
