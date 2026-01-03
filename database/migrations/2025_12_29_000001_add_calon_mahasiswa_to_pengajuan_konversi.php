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
        Schema::table('pengajuan_konversi', function (Blueprint $table) {
            // Ubah mahasiswa_id menjadi nullable (karena calon mahasiswa belum terdaftar)
            $table->foreignId('mahasiswa_id')->nullable()->change();
            
            // Tambah kolom untuk data calon mahasiswa (input manual)
            $table->string('nama_calon_mahasiswa')->nullable()->after('nomor_pengajuan');
            $table->string('email_calon')->nullable()->after('nama_calon_mahasiswa');
            $table->string('no_hp_calon')->nullable()->after('email_calon');
            $table->foreignId('program_studi_tujuan_id')->nullable()->after('no_hp_calon');
            
            // Tambah kolom untuk approval kaprodi
            $table->foreignId('diproses_kaprodi_oleh')->nullable()->after('diproses_oleh');
            $table->timestamp('tanggal_diproses_kaprodi')->nullable()->after('tanggal_diproses');
            $table->text('catatan_kaprodi')->nullable()->after('catatan');
        });

        // Tambah foreign key untuk program_studi_tujuan_id
        Schema::table('pengajuan_konversi', function (Blueprint $table) {
            $table->foreign('program_studi_tujuan_id')->references('id')->on('program_studi')->onDelete('set null');
            $table->foreign('diproses_kaprodi_oleh')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_konversi', function (Blueprint $table) {
            $table->dropForeign(['program_studi_tujuan_id']);
            $table->dropForeign(['diproses_kaprodi_oleh']);
            
            $table->dropColumn([
                'nama_calon_mahasiswa',
                'email_calon',
                'no_hp_calon',
                'program_studi_tujuan_id',
                'diproses_kaprodi_oleh',
                'tanggal_diproses_kaprodi',
                'catatan_kaprodi',
            ]);
        });
    }
};
