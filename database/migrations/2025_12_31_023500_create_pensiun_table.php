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
        Schema::create('pensiun', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            
            // Informasi Pensiun
            $table->enum('jenis_pensiun', [
                'bup', // Batas Usia Pensiun
                'atas_permintaan_sendiri',
                'uzur', // Tidak cakap jasmani/rohani
                'meninggal',
                'penyederhanaan_organisasi',
                'hukuman_disiplin',
                'dini' // Pensiun dini
            ]);
            
            // BUP berdasarkan jabatan
            $table->integer('usia_bup')->default(60); // Default 60 tahun, bisa 65 untuk Guru Besar
            $table->date('tanggal_lahir');
            $table->date('tanggal_pensiun'); // TMT Pensiun
            $table->date('tanggal_bup'); // Tanggal BUP otomatis dihitung
            
            // Status kepegawaian saat pensiun
            $table->string('pangkat_terakhir')->nullable();
            $table->string('golongan_terakhir')->nullable();
            $table->string('jabatan_terakhir')->nullable();
            $table->integer('masa_kerja_tahun')->default(0);
            $table->integer('masa_kerja_bulan')->default(0);
            
            // SK Pensiun
            $table->string('no_sk')->nullable();
            $table->date('tanggal_sk')->nullable();
            $table->string('pejabat_penandatangan')->nullable();
            
            // Tunjangan/Benefit
            $table->decimal('gaji_pokok_terakhir', 15, 2)->nullable();
            $table->decimal('dana_pensiun', 15, 2)->nullable();
            $table->string('no_taspen')->nullable();
            
            // Alamat & Kontak setelah pensiun
            $table->text('alamat_pensiun')->nullable();
            $table->string('no_telepon_pensiun')->nullable();
            $table->string('no_rekening_pensiun')->nullable();
            $table->string('nama_bank')->nullable();
            
            // Dokumen
            $table->string('dokumen_sk')->nullable();
            $table->string('dokumen_karpeg')->nullable();
            $table->string('dokumen_taspen')->nullable();
            
            $table->enum('status', ['prediksi', 'proses', 'selesai', 'dibatalkan'])->default('prediksi');
            $table->text('catatan')->nullable();
            $table->boolean('sudah_serah_terima')->default(false);
            $table->date('tanggal_serah_terima')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['dosen_id', 'status']);
            $table->index(['pegawai_id', 'status']);
            $table->index('tanggal_pensiun');
            $table->index('tanggal_bup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pensiun');
    }
};
