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
        Schema::table('skp_pegawai', function (Blueprint $table) {
            // Info tambahan
            $table->string('jabatan', 100)->nullable()->after('tanggal_skp');
            $table->string('unit_kerja', 100)->nullable()->after('jabatan');
            $table->string('atasan_penilai', 100)->nullable()->after('unit_kerja');
            $table->string('jabatan_penilai', 100)->nullable()->after('atasan_penilai');
            
            // Detail penilaian perilaku
            $table->decimal('orientasi_pelayanan', 5, 2)->nullable()->after('nilai_perilaku');
            $table->decimal('integritas', 5, 2)->nullable()->after('orientasi_pelayanan');
            $table->decimal('komitmen', 5, 2)->nullable()->after('integritas');
            $table->decimal('disiplin', 5, 2)->nullable()->after('komitmen');
            $table->decimal('kerjasama', 5, 2)->nullable()->after('disiplin');
            $table->decimal('kepemimpinan', 5, 2)->nullable()->after('kerjasama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skp_pegawai', function (Blueprint $table) {
            $table->dropColumn([
                'jabatan',
                'unit_kerja', 
                'atasan_penilai',
                'jabatan_penilai',
                'orientasi_pelayanan',
                'integritas',
                'komitmen',
                'disiplin',
                'kerjasama',
                'kepemimpinan',
            ]);
        });
    }
};
