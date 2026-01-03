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
        Schema::table('calon_mahasiswa', function (Blueprint $table) {
            $table->string('status')->default('mendaftar')->after('foto');
        });
        
        // Update existing records to use status from status_pendaftaran
        DB::statement('UPDATE calon_mahasiswa SET status = COALESCE(status_pendaftaran, "mendaftar") WHERE status = "mendaftar" OR status IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calon_mahasiswa', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
