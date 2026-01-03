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
        Schema::table('mahasiswa', function (Blueprint $table) {
            // Relasi ke tabel sekolah (menggantikan asal_sekolah string)
            $table->foreignId('sekolah_id')->nullable()->after('no_ijazah_sma')->constrained('sekolah')->nullOnDelete();

            // Alamat Lengkap Mahasiswa
            $table->foreignId('provinsi_id')->nullable()->after('alamat')->constrained('provinsi')->nullOnDelete();
            $table->foreignId('kabupaten_id')->nullable()->after('provinsi_id')->constrained('kabupaten')->nullOnDelete();
            $table->foreignId('kecamatan_id')->nullable()->after('kabupaten_id')->constrained('kecamatan')->nullOnDelete();
            $table->foreignId('kelurahan_id')->nullable()->after('kecamatan_id')->constrained('kelurahan')->nullOnDelete();
            $table->string('rt', 5)->nullable()->after('kelurahan_id');
            $table->string('rw', 5)->nullable()->after('rt');
            $table->string('kode_pos', 10)->nullable()->after('rw');

            // Alamat Lengkap Orang Tua
            $table->foreignId('provinsi_ortu_id')->nullable()->after('alamat_ortu')->constrained('provinsi')->nullOnDelete();
            $table->foreignId('kabupaten_ortu_id')->nullable()->after('provinsi_ortu_id')->constrained('kabupaten')->nullOnDelete();
            $table->foreignId('kecamatan_ortu_id')->nullable()->after('kabupaten_ortu_id')->constrained('kecamatan')->nullOnDelete();
            $table->foreignId('kelurahan_ortu_id')->nullable()->after('kecamatan_ortu_id')->constrained('kelurahan')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['sekolah_id']);
            $table->dropColumn('sekolah_id');

            $table->dropForeign(['provinsi_id']);
            $table->dropForeign(['kabupaten_id']);
            $table->dropForeign(['kecamatan_id']);
            $table->dropForeign(['kelurahan_id']);
            $table->dropColumn(['provinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'rt', 'rw', 'kode_pos']);

            $table->dropForeign(['provinsi_ortu_id']);
            $table->dropForeign(['kabupaten_ortu_id']);
            $table->dropForeign(['kecamatan_ortu_id']);
            $table->dropForeign(['kelurahan_ortu_id']);
            $table->dropColumn(['provinsi_ortu_id', 'kabupaten_ortu_id', 'kecamatan_ortu_id', 'kelurahan_ortu_id']);
        });
    }
};
