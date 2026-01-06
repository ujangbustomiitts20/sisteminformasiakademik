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
        Schema::create('pejabat_penandatangan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique()->comment('Kode unik untuk referensi di setting, misal: rektor, dekan_fti, kepala_keuangan');
            $table->string('jabatan')->comment('Nama jabatan, misal: Rektor, Dekan Fakultas Teknologi Industri');
            $table->string('nama')->comment('Nama pejabat');
            $table->string('nip')->nullable()->comment('NIP/NIDN pejabat');
            $table->string('pangkat_golongan')->nullable()->comment('Pangkat/Golongan');
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->string('tanda_tangan')->nullable()->comment('Path file tanda tangan digital');
            $table->string('stempel')->nullable()->comment('Path file stempel');
            $table->string('kategori')->default('umum')->comment('Kategori: akademik, keuangan, sdm, umum');
            $table->text('dokumen_terkait')->nullable()->comment('JSON array dokumen yang ditandatangani');
            $table->date('berlaku_mulai')->nullable();
            $table->date('berlaku_sampai')->nullable();
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0)->comment('Urutan tampil');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['kategori', 'aktif']);
            $table->index('kode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pejabat_penandatangan');
    }
};
