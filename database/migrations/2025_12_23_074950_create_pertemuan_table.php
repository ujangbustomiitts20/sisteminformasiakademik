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
        // Add jumlah_pertemuan column to mata_kuliah table using raw SQL
        $columns = DB::select("SHOW COLUMNS FROM mata_kuliah LIKE 'jumlah_pertemuan'");
        if (empty($columns)) {
            DB::statement('ALTER TABLE mata_kuliah ADD COLUMN jumlah_pertemuan INT DEFAULT 16 AFTER sks');
        }

        // Create pertemuan table for detailed meeting info
        Schema::create('pertemuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_kuliah_id')->constrained('jadwal_kuliah')->onDelete('cascade');
            $table->integer('pertemuan_ke');
            $table->string('judul')->nullable();
            $table->enum('jenis', ['Materi', 'Tugas', 'Quiz', 'UTS', 'UAS', 'Praktikum', 'Diskusi', 'Presentasi', 'Forum', 'Lainnya'])->default('Materi');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('file_materi')->nullable();
            $table->string('link_materi')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->unique(['jadwal_kuliah_id', 'pertemuan_ke']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertemuan');
        
        $columns = DB::select("SHOW COLUMNS FROM mata_kuliah LIKE 'jumlah_pertemuan'");
        if (!empty($columns)) {
            DB::statement('ALTER TABLE mata_kuliah DROP COLUMN jumlah_pertemuan');
        }
    }
};
