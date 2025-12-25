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
        Schema::table('rekonsiliasi', function (Blueprint $table) {
            $table->string('nomor_rekonsiliasi')->unique()->after('akun_bank_id');
        });

        Schema::table('detail_rekonsiliasi', function (Blueprint $table) {
            $table->text('catatan')->nullable()->after('keterangan');
            $table->timestamp('matched_at')->nullable()->after('catatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekonsiliasi', function (Blueprint $table) {
            $table->dropColumn('nomor_rekonsiliasi');
        });

        Schema::table('detail_rekonsiliasi', function (Blueprint $table) {
            $table->dropColumn(['catatan', 'matched_at']);
        });
    }
};
