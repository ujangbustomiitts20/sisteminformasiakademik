<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah enum status untuk mendukung workflow baru dengan kaprodi
        DB::statement("ALTER TABLE pengajuan_konversi MODIFY status ENUM('draft', 'diajukan', 'menunggu_kaprodi', 'diproses_kaprodi', 'disetujui_kaprodi', 'ditolak_kaprodi', 'diproses', 'disetujui', 'ditolak') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke enum lama
        DB::statement("ALTER TABLE pengajuan_konversi MODIFY status ENUM('draft', 'diajukan', 'diproses', 'disetujui', 'ditolak') DEFAULT 'draft'");
    }
};
