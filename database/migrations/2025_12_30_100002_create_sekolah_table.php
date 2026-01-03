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
        Schema::create('sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('npsn', 20)->unique()->nullable();
            $table->string('nama');
            $table->enum('jenjang', ['SMA', 'SMK', 'MA', 'MAK', 'Paket C', 'Lainnya'])->default('SMA');
            $table->enum('status', ['Negeri', 'Swasta'])->default('Negeri');
            $table->foreignId('provinsi_id')->nullable()->constrained('provinsi')->nullOnDelete();
            $table->foreignId('kabupaten_id')->nullable()->constrained('kabupaten')->nullOnDelete();
            $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatan')->nullOnDelete();
            $table->string('alamat')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['provinsi_id', 'kabupaten_id']);
            $table->index('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sekolah');
    }
};
