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
        Schema::create('konfigurasi_cetak', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // krs, khs, transkrip, invoice, dll
            $table->string('nama'); // Nama dokumen
            $table->string('judul')->nullable(); // Judul yang ditampilkan
            $table->enum('ukuran_kertas', ['a4', 'letter', 'legal', 'f4'])->default('a4');
            $table->enum('orientasi', ['portrait', 'landscape'])->default('portrait');
            $table->string('margin_top')->default('15');
            $table->string('margin_bottom')->default('20');
            $table->string('margin_left')->default('15');
            $table->string('margin_right')->default('15');
            $table->boolean('tampilkan_kop')->default(true);
            $table->boolean('tampilkan_logo')->default(true);
            $table->boolean('tampilkan_ttd')->default(true);
            $table->string('jabatan_ttd')->nullable();
            $table->string('nama_ttd')->nullable();
            $table->string('nip_ttd')->nullable();
            $table->boolean('tampilkan_footer')->default(true);
            $table->text('catatan_kaki')->nullable();
            $table->text('pengaturan_tambahan')->nullable(); // JSON string untuk konfigurasi khusus
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konfigurasi_cetak');
    }
};
