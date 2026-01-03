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
        Schema::create('penugasan_mutasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->enum('jenis', ['penugasan', 'mutasi', 'promosi', 'demosi', 'rotasi']);
            $table->string('no_sk');
            $table->date('tanggal_sk');
            $table->date('tmt'); // Terhitung Mulai Tanggal
            $table->date('tanggal_selesai')->nullable();
            
            // Unit asal dan tujuan
            $table->foreignId('unit_kerja_asal_id')->nullable()->constrained('unit_kerja')->nullOnDelete();
            $table->foreignId('unit_kerja_tujuan_id')->nullable()->constrained('unit_kerja')->nullOnDelete();
            $table->string('jabatan_asal')->nullable();
            $table->string('jabatan_tujuan')->nullable();
            
            // Detail penugasan khusus
            $table->string('nama_tugas')->nullable();
            $table->text('deskripsi_tugas')->nullable();
            $table->string('lokasi_penugasan')->nullable();
            
            $table->text('alasan')->nullable();
            $table->string('dokumen_sk')->nullable();
            $table->enum('status', ['draft', 'aktif', 'selesai', 'dibatalkan'])->default('draft');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['dosen_id', 'jenis']);
            $table->index(['pegawai_id', 'jenis']);
            $table->index('tmt');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penugasan_mutasi');
    }
};
