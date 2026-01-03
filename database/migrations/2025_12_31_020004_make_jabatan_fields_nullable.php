<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah jabatan_fungsional menjadi nullable di riwayat_jabatan
        Schema::table('riwayat_jabatan', function (Blueprint $table) {
            $table->string('jabatan_fungsional')->nullable()->change();
        });
        
        // Ubah no_sk dan tanggal_sk menjadi nullable juga
        Schema::table('riwayat_jabatan', function (Blueprint $table) {
            $table->string('no_sk')->nullable()->change();
            $table->date('tanggal_sk')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_jabatan', function (Blueprint $table) {
            $table->string('jabatan_fungsional')->nullable(false)->change();
            $table->string('no_sk')->nullable(false)->change();
            $table->date('tanggal_sk')->nullable(false)->change();
        });
    }
};
