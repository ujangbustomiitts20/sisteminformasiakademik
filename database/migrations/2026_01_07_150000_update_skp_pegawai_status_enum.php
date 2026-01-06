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
        // Update enum to include new statuses: revisi, realisasi
        DB::statement("ALTER TABLE skp_pegawai MODIFY COLUMN status ENUM('draft', 'diajukan', 'disetujui', 'revisi', 'realisasi', 'dinilai', 'final') NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum
        DB::statement("ALTER TABLE skp_pegawai MODIFY COLUMN status ENUM('draft', 'diajukan', 'dinilai', 'disetujui', 'final') NOT NULL DEFAULT 'draft'");
    }
};
