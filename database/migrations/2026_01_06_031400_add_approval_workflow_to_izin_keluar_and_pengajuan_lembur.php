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
        // Tambah kolom untuk approval workflow di izin_keluar
        Schema::table('izin_keluar', function (Blueprint $table) {
            $table->foreignId('kaprodi_id')->nullable()->after('disetujui_oleh')->constrained('dosen')->nullOnDelete();
            $table->enum('approval_level', ['kaprodi', 'admin'])->default('kaprodi')->after('status');
            $table->enum('status_kaprodi', ['pending', 'disetujui', 'ditolak'])->nullable()->after('approval_level');
            $table->datetime('tanggal_approval_kaprodi')->nullable()->after('status_kaprodi');
            $table->text('catatan_kaprodi')->nullable()->after('tanggal_approval_kaprodi');
        });

        // Tambah kolom untuk approval workflow di pengajuan_lembur
        Schema::table('pengajuan_lembur', function (Blueprint $table) {
            $table->foreignId('kaprodi_id')->nullable()->after('disetujui_oleh')->constrained('dosen')->nullOnDelete();
            $table->enum('approval_level', ['kaprodi', 'admin'])->default('kaprodi')->after('status');
            $table->enum('status_kaprodi', ['pending', 'disetujui', 'ditolak'])->nullable()->after('approval_level');
            $table->datetime('tanggal_approval_kaprodi')->nullable()->after('status_kaprodi');
            $table->text('catatan_kaprodi')->nullable()->after('tanggal_approval_kaprodi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('izin_keluar', function (Blueprint $table) {
            $table->dropForeign(['kaprodi_id']);
            $table->dropColumn(['kaprodi_id', 'approval_level', 'status_kaprodi', 'tanggal_approval_kaprodi', 'catatan_kaprodi']);
        });

        Schema::table('pengajuan_lembur', function (Blueprint $table) {
            $table->dropForeign(['kaprodi_id']);
            $table->dropColumn(['kaprodi_id', 'approval_level', 'status_kaprodi', 'tanggal_approval_kaprodi', 'catatan_kaprodi']);
        });
    }
};
