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
        Schema::table('nilai_seleksi', function (Blueprint $table) {
            $table->foreignId('gelombang_pmb_id')->nullable()->after('calon_mahasiswa_id')->constrained('gelombang_pmb')->nullOnDelete();
            $table->decimal('nilai_tpa', 5, 2)->nullable()->after('nilai_akhir');
            $table->decimal('nilai_bahasa', 5, 2)->nullable()->after('nilai_tpa');
            $table->decimal('nilai_matematika', 5, 2)->nullable()->after('nilai_bahasa');
            $table->decimal('nilai_wawancara', 5, 2)->nullable()->after('nilai_matematika');
            $table->decimal('nilai_total', 5, 2)->nullable()->after('nilai_wawancara');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_seleksi', function (Blueprint $table) {
            $table->dropForeign(['gelombang_pmb_id']);
            $table->dropColumn(['gelombang_pmb_id', 'nilai_tpa', 'nilai_bahasa', 'nilai_matematika', 'nilai_wawancara', 'nilai_total']);
        });
    }
};
