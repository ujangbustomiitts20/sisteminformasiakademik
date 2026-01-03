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
        // Tabel utama pengajuan
        Schema::create('pengajuan_konversi_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->string('no_pengajuan')->unique();
            $table->date('tanggal_pengajuan');
            $table->enum('status', ['Draft', 'Diajukan', 'Diproses', 'Disetujui', 'Ditolak'])->default('Draft');
            $table->text('catatan_mahasiswa')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_diproses')->nullable();
            $table->timestamps();
        });

        // Tabel detail kegiatan yang diajukan
        Schema::create('detail_konversi_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_konversi_kegiatan_id')->constrained('pengajuan_konversi_kegiatan')->onDelete('cascade');
            $table->enum('jenis_kegiatan', ['Sertifikasi', 'Lomba', 'Magang', 'Kursus', 'Pelatihan', 'Pengalaman Kerja', 'Organisasi', 'Lainnya']);
            $table->string('nama_kegiatan');
            $table->string('penyelenggara');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->string('no_sertifikat')->nullable();
            $table->integer('durasi_jam')->nullable()->comment('Durasi kegiatan dalam jam');
            $table->text('deskripsi_kegiatan')->nullable();
            $table->string('bukti_dokumen')->nullable()->comment('Path file bukti');
            $table->foreignId('mata_kuliah_id')->nullable()->constrained('mata_kuliah')->nullOnDelete()->comment('MK tujuan konversi');
            $table->integer('sks_diakui')->nullable();
            $table->string('nilai_huruf')->nullable();
            $table->decimal('nilai_angka', 4, 2)->nullable();
            $table->enum('status_detail', ['Pending', 'Disetujui', 'Ditolak'])->default('Pending');
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_konversi_kegiatan');
        Schema::dropIfExists('pengajuan_konversi_kegiatan');
    }
};
