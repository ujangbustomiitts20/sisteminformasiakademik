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
        // Modify the role enum to include kaprodi and dekan
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'dosen', 'mahasiswa', 'kaprodi', 'dekan') DEFAULT 'mahasiswa'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values (data might be lost!)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'dosen', 'mahasiswa') DEFAULT 'mahasiswa'");
    }
};
