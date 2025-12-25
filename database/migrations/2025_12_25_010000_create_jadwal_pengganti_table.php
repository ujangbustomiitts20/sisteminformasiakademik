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
        Schema::create('jadwal_pengganti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_kuliah_id')->constrained('jadwal_kuliah')->onDelete('cascade');
            $table->date('tanggal_asli');
            $table->date('tanggal_pengganti');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->foreignId('ruangan_id')->constrained('ruangan')->onDelete('cascade');
            $table->enum('alasan', ['Libur Nasional', 'Dosen Berhalangan', 'Kegiatan Kampus', 'Force Majeure', 'Lainnya'])->default('Lainnya');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['Pending', 'Disetujui', 'Ditolak', 'Selesai'])->default('Pending');
            $table->foreignId('diajukan_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('tanggal_persetujuan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_pengganti');
    }
};
