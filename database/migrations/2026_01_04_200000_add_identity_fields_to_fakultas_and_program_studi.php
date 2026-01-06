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
        // Add identity fields to fakultas
        Schema::table('fakultas', function (Blueprint $table) {
            $table->string('singkatan', 20)->nullable()->after('nama');
            $table->string('alamat')->nullable()->after('dekan');
            $table->string('telepon', 20)->nullable()->after('alamat');
            $table->string('email', 100)->nullable()->after('telepon');
            $table->string('website')->nullable()->after('email');
            $table->string('akreditasi', 10)->nullable()->after('website');
            $table->date('tanggal_akreditasi')->nullable()->after('akreditasi');
            $table->date('tanggal_berdiri')->nullable()->after('tanggal_akreditasi');
            $table->string('sk_pendirian')->nullable()->after('tanggal_berdiri');
            $table->text('visi')->nullable()->after('sk_pendirian');
            $table->text('misi')->nullable()->after('visi');
            $table->string('logo')->nullable()->after('misi');
        });

        // Add identity fields to program_studi
        Schema::table('program_studi', function (Blueprint $table) {
            $table->string('singkatan', 20)->nullable()->after('nama');
            $table->string('akreditasi', 10)->nullable()->after('kaprodi');
            $table->date('tanggal_akreditasi')->nullable()->after('akreditasi');
            $table->string('no_sk_akreditasi')->nullable()->after('tanggal_akreditasi');
            $table->date('tanggal_berdiri')->nullable()->after('no_sk_akreditasi');
            $table->string('sk_pendirian')->nullable()->after('tanggal_berdiri');
            $table->string('gelar_lulusan', 50)->nullable()->after('sk_pendirian');
            $table->text('visi')->nullable()->after('gelar_lulusan');
            $table->text('misi')->nullable()->after('visi');
            $table->text('kompetensi')->nullable()->after('misi');
            $table->integer('kuota')->nullable()->after('kompetensi');
            $table->string('alamat')->nullable()->after('kuota');
            $table->string('telepon', 20)->nullable()->after('alamat');
            $table->string('email', 100)->nullable()->after('telepon');
            $table->string('website')->nullable()->after('email');
            $table->string('logo')->nullable()->after('website');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fakultas', function (Blueprint $table) {
            $table->dropColumn([
                'singkatan', 'alamat', 'telepon', 'email', 'website',
                'akreditasi', 'tanggal_akreditasi', 'tanggal_berdiri',
                'sk_pendirian', 'visi', 'misi', 'logo'
            ]);
        });

        Schema::table('program_studi', function (Blueprint $table) {
            $table->dropColumn([
                'singkatan', 'akreditasi', 'tanggal_akreditasi', 'no_sk_akreditasi',
                'tanggal_berdiri', 'sk_pendirian', 'gelar_lulusan',
                'visi', 'misi', 'kompetensi', 'kuota',
                'alamat', 'telepon', 'email', 'website', 'logo'
            ]);
        });
    }
};
