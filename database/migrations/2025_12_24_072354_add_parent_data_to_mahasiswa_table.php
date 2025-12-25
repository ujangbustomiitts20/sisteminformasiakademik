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
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->string('no_hp', 15)->nullable()->after('email');
            $table->string('nama_ayah')->nullable()->after('foto');
            $table->string('nama_ibu')->nullable()->after('nama_ayah');
            $table->string('pekerjaan_ayah')->nullable()->after('nama_ibu');
            $table->string('pekerjaan_ibu')->nullable()->after('pekerjaan_ayah');
            $table->string('no_hp_ortu', 15)->nullable()->after('pekerjaan_ibu');
            $table->string('penghasilan_ortu')->nullable()->after('no_hp_ortu');
            $table->text('alamat_ortu')->nullable()->after('penghasilan_ortu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropColumn([
                'no_hp',
                'nama_ayah',
                'nama_ibu',
                'pekerjaan_ayah',
                'pekerjaan_ibu',
                'no_hp_ortu',
                'penghasilan_ortu',
                'alamat_ortu',
            ]);
        });
    }
};
