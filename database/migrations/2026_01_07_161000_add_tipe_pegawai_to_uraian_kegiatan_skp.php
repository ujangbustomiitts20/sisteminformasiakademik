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
        Schema::table('uraian_kegiatan_skp', function (Blueprint $table) {
            $table->enum('tipe_pegawai', ['dosen', 'tendik', 'semua'])->default('semua')->after('kode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uraian_kegiatan_skp', function (Blueprint $table) {
            $table->dropColumn('tipe_pegawai');
        });
    }
};
