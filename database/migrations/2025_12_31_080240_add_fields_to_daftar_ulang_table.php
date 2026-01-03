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
        Schema::table('daftar_ulang', function (Blueprint $table) {
            // Add program_studi_id
            $table->foreignId('program_studi_id')->nullable()->after('calon_mahasiswa_id')->constrained('program_studi')->nullOnDelete();
            
            // Add biaya (single biaya field)
            $table->decimal('biaya', 15, 2)->default(0)->after('no_daftar_ulang');
            
            // Add tanggal_expired
            $table->date('tanggal_expired')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_ulang', function (Blueprint $table) {
            $table->dropForeign(['program_studi_id']);
            $table->dropColumn(['program_studi_id', 'biaya', 'tanggal_expired']);
        });
    }
};
