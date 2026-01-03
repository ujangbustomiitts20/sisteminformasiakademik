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
        Schema::create('kenaikan_pangkat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            
            // Periode kenaikan pangkat
            $table->enum('periode', ['april', 'oktober']);
            $table->year('tahun');
            
            // Pangkat lama
            $table->string('pangkat_lama')->nullable();
            $table->string('golongan_lama')->nullable();
            $table->date('tmt_pangkat_lama')->nullable();
            
            // Pangkat baru
            $table->string('pangkat_baru');
            $table->string('golongan_baru');
            $table->date('tmt_pangkat_baru');
            
            // Jenis kenaikan pangkat
            $table->enum('jenis', [
                'reguler',
                'pilihan', 
                'struktural',
                'fungsional',
                'penyesuaian_ijazah',
                'pengabdian',
                'anumerta',
                'tugas_belajar'
            ])->default('reguler');
            
            // Informasi SK
            $table->string('no_sk')->nullable();
            $table->date('tanggal_sk')->nullable();
            $table->string('pejabat_penandatangan')->nullable();
            
            // Persyaratan
            $table->integer('masa_kerja_tahun')->default(0);
            $table->integer('masa_kerja_bulan')->default(0);
            $table->string('pendidikan_terakhir')->nullable();
            $table->decimal('angka_kredit', 8, 2)->nullable();
            $table->string('penilaian_kinerja')->nullable();
            
            // Dokumen pendukung
            $table->string('dokumen_sk')->nullable();
            $table->string('dokumen_pak')->nullable(); // Penetapan Angka Kredit
            $table->string('dokumen_skp')->nullable(); // Sasaran Kinerja Pegawai
            
            $table->enum('status', ['draft', 'diusulkan', 'verifikasi', 'disetujui', 'ditolak'])->default('draft');
            $table->text('catatan')->nullable();
            $table->foreignId('diusulkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['dosen_id', 'periode', 'tahun']);
            $table->index(['pegawai_id', 'periode', 'tahun']);
            $table->index('status');
            $table->index('tmt_pangkat_baru');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kenaikan_pangkat');
    }
};
