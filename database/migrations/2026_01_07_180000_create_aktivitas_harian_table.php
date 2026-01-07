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
        Schema::create('aktivitas_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->foreignId('uraian_kegiatan_skp_id')->nullable()->constrained('uraian_kegiatan_skp')->nullOnDelete();
            $table->date('tanggal');
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('uraian_kegiatan', 1000);
            $table->string('output_hasil', 500)->nullable();
            $table->decimal('volume', 10, 2)->nullable();
            $table->string('satuan', 50)->nullable();
            $table->string('lokasi', 255)->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['draft', 'diajukan', 'disetujui', 'ditolak'])->default('draft');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->text('catatan_atasan')->nullable();
            $table->timestamps();

            $table->index(['dosen_id', 'tanggal']);
            $table->index(['pegawai_id', 'tanggal']);
            $table->index('tanggal');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aktivitas_harian');
    }
};
