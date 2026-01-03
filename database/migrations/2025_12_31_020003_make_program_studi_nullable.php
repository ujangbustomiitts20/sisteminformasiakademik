<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah program_studi menjadi nullable
        Schema::table('riwayat_pendidikan', function (Blueprint $table) {
            $table->string('program_studi')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_pendidikan', function (Blueprint $table) {
            $table->string('program_studi')->nullable(false)->change();
        });
    }
};
