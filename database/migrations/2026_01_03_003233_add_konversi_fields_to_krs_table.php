<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menambahkan field untuk mendukung KRS dari konversi kegiatan (RPL)
     * - is_konversi: penanda bahwa KRS ini berasal dari konversi kegiatan
     * - detail_konversi_kegiatan_id: referensi ke detail kegiatan yang dikonversi
     */
    public function up(): void
    {
        Schema::table('krs', function (Blueprint $table) {
            $table->boolean('is_konversi')->default(false)->after('status');
            $table->foreignId('detail_konversi_kegiatan_id')
                ->nullable()
                ->after('is_konversi')
                ->constrained('detail_konversi_kegiatan')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('krs', function (Blueprint $table) {
            $table->dropForeign(['detail_konversi_kegiatan_id']);
            $table->dropColumn(['is_konversi', 'detail_konversi_kegiatan_id']);
        });
    }
};
