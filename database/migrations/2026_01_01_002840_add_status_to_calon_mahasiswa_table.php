<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Untuk MySQL, kita perlu mengubah ENUM dengan menambahkan nilai baru
        DB::statement("ALTER TABLE calon_mahasiswa MODIFY COLUMN status_pendaftaran ENUM(
            'draft', 
            'mendaftar',
            'menunggu_bayar', 
            'terdaftar', 
            'verifikasi_dokumen',
            'lulus_administrasi',
            'mengikuti_ujian', 
            'lulus', 
            'tidak_lulus', 
            'daftar_ulang', 
            'menjadi_mahasiswa', 
            'batal'
        ) DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke enum semula
        DB::statement("ALTER TABLE calon_mahasiswa MODIFY COLUMN status_pendaftaran ENUM(
            'draft', 
            'menunggu_bayar', 
            'terdaftar', 
            'mengikuti_ujian', 
            'lulus', 
            'tidak_lulus', 
            'daftar_ulang', 
            'menjadi_mahasiswa', 
            'batal'
        ) DEFAULT 'draft'");
    }
};
