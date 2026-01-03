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
        Schema::create('cuti_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->string('no_pengajuan')->unique();
            $table->enum('jenis_cuti', ['tahunan', 'sakit', 'melahirkan', 'besar', 'alasan_penting', 'di_luar_tanggungan']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('jumlah_hari');
            $table->text('alasan');
            $table->string('alamat_selama_cuti')->nullable();
            $table->string('no_telepon_selama_cuti')->nullable();
            $table->string('dokumen_pendukung')->nullable();
            $table->enum('status', ['draft', 'diajukan', 'disetujui_atasan', 'disetujui', 'ditolak', 'dibatalkan'])->default('draft');
            $table->foreignId('atasan_langsung_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->text('catatan_atasan')->nullable();
            $table->date('tanggal_persetujuan_atasan')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_disetujui')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->integer('sisa_cuti_sebelum')->nullable();
            $table->integer('sisa_cuti_sesudah')->nullable();
            $table->timestamps();
            
            $table->index(['dosen_id', 'jenis_cuti']);
            $table->index(['pegawai_id', 'jenis_cuti']);
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
            $table->index('status');
        });

        // Tabel saldo cuti tahunan pegawai
        Schema::create('saldo_cuti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->year('tahun');
            $table->integer('jatah_cuti')->default(12);
            $table->integer('cuti_digunakan')->default(0);
            $table->integer('sisa_cuti')->default(12);
            $table->integer('cuti_hangus')->default(0);
            $table->timestamps();
            
            $table->unique(['dosen_id', 'tahun']);
            $table->unique(['pegawai_id', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldo_cuti');
        Schema::dropIfExists('cuti_pegawai');
    }
};
