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
        Schema::create('pengajuan_surat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->enum('jenis_surat', [
                'Surat Keterangan Aktif Kuliah',
                'Surat Pengantar Penelitian',
                'Surat Pengantar Magang/PKL',
                'Surat Keterangan Lulus',
                'Surat Keterangan Berkelakuan Baik',
                'Surat Rekomendasi',
                'Surat Keterangan Sedang Cuti',
                'Lainnya'
            ]);
            $table->text('keperluan'); // Untuk apa surat ini
            $table->string('ditujukan_kepada')->nullable(); // Ditujukan ke instansi/perusahaan
            $table->text('keterangan_tambahan')->nullable();
            $table->enum('status', ['pending', 'diproses', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable(); // Catatan jika ditolak
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('tanggal_diproses')->nullable();
            $table->string('nomor_surat')->nullable(); // Nomor surat setelah disetujui
            $table->string('file_surat')->nullable(); // Path ke file PDF
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_surat');
    }
};
