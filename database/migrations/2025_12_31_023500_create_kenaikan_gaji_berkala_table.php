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
        Schema::create('kenaikan_gaji_berkala', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            
            // Informasi KGB
            $table->string('no_sk')->nullable();
            $table->date('tanggal_sk')->nullable();
            $table->date('tmt_kgb'); // TMT Kenaikan Gaji Berkala
            $table->date('tmt_kgb_berikutnya')->nullable();
            
            // Golongan/Ruang
            $table->string('golongan_ruang');
            $table->integer('masa_kerja_golongan_tahun')->default(0);
            $table->integer('masa_kerja_golongan_bulan')->default(0);
            
            // Gaji
            $table->decimal('gaji_pokok_lama', 15, 2)->nullable();
            $table->decimal('gaji_pokok_baru', 15, 2);
            
            // Masa Kerja Total
            $table->integer('masa_kerja_total_tahun')->default(0);
            $table->integer('masa_kerja_total_bulan')->default(0);
            
            $table->enum('status', ['pending', 'diproses', 'disetujui', 'ditolak'])->default('pending');
            $table->string('dokumen_sk')->nullable();
            $table->text('catatan')->nullable();
            $table->boolean('is_otomatis')->default(true);
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_diproses')->nullable();
            $table->timestamps();
            
            $table->index(['dosen_id', 'tmt_kgb']);
            $table->index(['pegawai_id', 'tmt_kgb']);
            $table->index('status');
            $table->index('tmt_kgb_berikutnya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kenaikan_gaji_berkala');
    }
};
