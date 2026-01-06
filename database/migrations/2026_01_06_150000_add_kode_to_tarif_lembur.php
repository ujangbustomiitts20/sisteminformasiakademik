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
        Schema::table('tarif_lembur', function (Blueprint $table) {
            $table->string('kode', 20)->nullable()->after('id');
            $table->text('deskripsi')->nullable()->after('nama');
        });

        // Update existing records with auto-generated kode
        $tarifs = DB::table('tarif_lembur')->whereNull('kode')->orWhere('kode', '')->get();
        foreach ($tarifs as $index => $tarif) {
            $kode = 'TL' . str_pad($tarif->id, 4, '0', STR_PAD_LEFT);
            DB::table('tarif_lembur')->where('id', $tarif->id)->update(['kode' => $kode]);
        }

        // Now add unique constraint
        Schema::table('tarif_lembur', function (Blueprint $table) {
            $table->unique('kode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tarif_lembur', function (Blueprint $table) {
            $table->dropUnique(['kode']);
            $table->dropColumn(['kode', 'deskripsi']);
        });
    }
};
