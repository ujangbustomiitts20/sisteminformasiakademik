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
        Schema::create('nama_jabatan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique()->comment('Kode jabatan: rektor, dekan, kaprodi, dll');
            $table->string('nama')->comment('Nama jabatan lengkap');
            $table->string('nama_singkat', 100)->nullable()->comment('Nama singkat jabatan');
            $table->string('kategori')->default('umum')->comment('pimpinan, akademik, keuangan, sdm, kemahasiswaan, umum');
            $table->integer('level')->default(0)->comment('Level hierarki jabatan (0=tertinggi)');
            $table->text('deskripsi')->nullable();
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->index(['kode', 'aktif']);
            $table->index('kategori');
        });

        // Add nama_jabatan_id to pejabat_penandatangan
        Schema::table('pejabat_penandatangan', function (Blueprint $table) {
            $table->foreignId('nama_jabatan_id')->nullable()->after('id')->constrained('nama_jabatan')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pejabat_penandatangan', function (Blueprint $table) {
            $table->dropForeign(['nama_jabatan_id']);
            $table->dropColumn('nama_jabatan_id');
        });
        
        Schema::dropIfExists('nama_jabatan');
    }
};
