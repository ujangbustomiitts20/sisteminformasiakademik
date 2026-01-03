<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan kolom untuk riwayat_jabatan agar mendukung format Tendik
        Schema::table('riwayat_jabatan', function (Blueprint $table) {
            // Kolom untuk pegawai/tendik
            $table->string('nama_jabatan')->nullable()->after('pegawai_id');
            $table->enum('jenis_jabatan', ['Struktural', 'Fungsional', 'Akademik'])->nullable()->after('nama_jabatan');
            $table->string('unit_kerja_jabatan')->nullable()->after('jenis_jabatan'); // unit kerja saat menjabat
            $table->date('tmt_selesai')->nullable()->after('tmt_jabatan');
            $table->string('pejabat_sk')->nullable()->after('pejabat_penetap');
        });
        
        // Update riwayat_pendidikan untuk mendukung jenjang lebih lengkap
        Schema::table('riwayat_pendidikan', function (Blueprint $table) {
            $table->string('jurusan')->nullable()->after('program_studi');
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_jabatan', function (Blueprint $table) {
            $table->dropColumn(['nama_jabatan', 'jenis_jabatan', 'unit_kerja_jabatan', 'tmt_selesai', 'pejabat_sk']);
        });
        
        Schema::table('riwayat_pendidikan', function (Blueprint $table) {
            $table->dropColumn('jurusan');
        });
    }
};
