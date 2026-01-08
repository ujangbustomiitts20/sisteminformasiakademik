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
        // Add nama_jabatan_id to pegawai table
        Schema::table('pegawai', function (Blueprint $table) {
            $table->foreignId('nama_jabatan_id')->nullable()->after('jabatan')->constrained('nama_jabatan')->nullOnDelete();
        });

        // Add unit_kerja_id and nama_jabatan_id to dosen table
        Schema::table('dosen', function (Blueprint $table) {
            $table->foreignId('unit_kerja_id')->nullable()->after('program_studi_id')->constrained('unit_kerja')->nullOnDelete();
            $table->foreignId('nama_jabatan_id')->nullable()->after('jabatan_fungsional')->constrained('nama_jabatan')->nullOnDelete();
            $table->string('jabatan_struktural', 100)->nullable()->after('nama_jabatan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropForeign(['nama_jabatan_id']);
            $table->dropColumn('nama_jabatan_id');
        });

        Schema::table('dosen', function (Blueprint $table) {
            $table->dropForeign(['unit_kerja_id']);
            $table->dropForeign(['nama_jabatan_id']);
            $table->dropColumn(['unit_kerja_id', 'nama_jabatan_id', 'jabatan_struktural']);
        });
    }
};
