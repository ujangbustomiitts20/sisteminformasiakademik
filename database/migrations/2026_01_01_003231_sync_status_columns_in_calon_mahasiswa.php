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
        // Update kolom status agar memiliki enum yang sama dengan status_pendaftaran
        DB::statement("ALTER TABLE calon_mahasiswa MODIFY COLUMN status ENUM(
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
        ) NULL DEFAULT NULL");

        // Sync nilai status dari status_pendaftaran
        DB::statement("UPDATE calon_mahasiswa SET status = status_pendaftaran WHERE status IS NULL OR status != status_pendaftaran");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to reverse
    }
};
