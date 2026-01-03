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
        // Fix nilai_seleksi columns - increase precision to handle larger values
        Schema::table('nilai_seleksi', function (Blueprint $table) {
            $table->decimal('nilai', 8, 2)->nullable()->change();
            $table->decimal('bobot', 8, 4)->nullable()->change();
            $table->decimal('nilai_akhir', 10, 2)->nullable()->change();
            $table->decimal('nilai_tpa', 8, 2)->nullable()->change();
            $table->decimal('nilai_bahasa', 8, 2)->nullable()->change();
            $table->decimal('nilai_matematika', 8, 2)->nullable()->change();
            $table->decimal('nilai_wawancara', 8, 2)->nullable()->change();
            $table->decimal('nilai_total', 10, 2)->nullable()->change();
        });

        // Fix hasil_seleksi columns
        Schema::table('hasil_seleksi', function (Blueprint $table) {
            $table->decimal('nilai_total', 10, 2)->nullable()->change();
        });

        // Reset nilai_seleksi data yang salah (bobot > 1 berarti masih dalam persentase)
        // Konversi bobot dari persentase ke decimal dan recalculate nilai_akhir
        DB::statement('UPDATE nilai_seleksi SET bobot = bobot / 100 WHERE bobot > 1');
        DB::statement('UPDATE nilai_seleksi SET nilai_akhir = nilai * bobot');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original precision
        Schema::table('nilai_seleksi', function (Blueprint $table) {
            $table->decimal('nilai', 5, 2)->nullable()->change();
            $table->decimal('bobot', 5, 2)->default(1)->change();
            $table->decimal('nilai_akhir', 5, 2)->nullable()->change();
            $table->decimal('nilai_tpa', 5, 2)->nullable()->change();
            $table->decimal('nilai_bahasa', 5, 2)->nullable()->change();
            $table->decimal('nilai_matematika', 5, 2)->nullable()->change();
            $table->decimal('nilai_wawancara', 5, 2)->nullable()->change();
            $table->decimal('nilai_total', 5, 2)->nullable()->change();
        });

        Schema::table('hasil_seleksi', function (Blueprint $table) {
            $table->decimal('nilai_total', 5, 2)->nullable()->change();
        });
    }
};
