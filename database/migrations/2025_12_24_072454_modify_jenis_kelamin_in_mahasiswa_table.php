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
            DB::statement("ALTER TABLE mahasiswa MODIFY COLUMN jenis_kelamin VARCHAR(10) NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            DB::statement("ALTER TABLE mahasiswa MODIFY COLUMN jenis_kelamin ENUM('L', 'P') NOT NULL");
        });
    }
};
