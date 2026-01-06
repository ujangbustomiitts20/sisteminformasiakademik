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
        Schema::table('pengajuan_lembur', function (Blueprint $table) {
            $table->foreignId('tarif_lembur_id')->nullable()->after('pegawai_id')->constrained('tarif_lembur')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_lembur', function (Blueprint $table) {
            $table->dropForeign(['tarif_lembur_id']);
            $table->dropColumn('tarif_lembur_id');
        });
    }
};
