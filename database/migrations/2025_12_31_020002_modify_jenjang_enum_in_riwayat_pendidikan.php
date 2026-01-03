<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum jenjang untuk mendukung lebih banyak pilihan
        DB::statement("ALTER TABLE riwayat_pendidikan MODIFY COLUMN jenjang ENUM('SD', 'SMP', 'SMA', 'SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3', 'Profesi', 'Spesialis') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE riwayat_pendidikan MODIFY COLUMN jenjang ENUM('D3', 'D4', 'S1', 'S2', 'S3', 'Profesi', 'Spesialis') NOT NULL");
    }
};
