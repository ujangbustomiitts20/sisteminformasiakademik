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
        // Tabel Prasyarat Mata Kuliah (relasi many-to-many)
        Schema::create('prasyarat_mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->onDelete('cascade');
            $table->foreignId('mata_kuliah_prasyarat_id')->constrained('mata_kuliah')->onDelete('cascade');
            $table->enum('jenis_prasyarat', ['wajib', 'pilihan'])->default('wajib');
            // wajib = harus lulus dulu
            // pilihan = minimal sudah pernah mengambil (tidak harus lulus)
            $table->string('nilai_minimal', 2)->nullable()->default('D'); // Nilai minimal yang harus dicapai (A, B, C, D)
            $table->timestamps();
            
            // Unique constraint: satu MK tidak boleh punya prasyarat yang sama dua kali
            $table->unique(['mata_kuliah_id', 'mata_kuliah_prasyarat_id'], 'prasyarat_unique');
        });

        // Tambah kolom jumlah_pertemuan jika belum ada
        try {
            Schema::table('mata_kuliah', function (Blueprint $table) {
                $table->integer('jumlah_pertemuan')->default(16)->after('semester');
            });
        } catch (\Exception $e) {
            // Kolom sudah ada, skip
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prasyarat_mata_kuliah');
    }
};
