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
        Schema::create('uraian_kegiatan_skp', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('kategori', 50); // tri_dharma, penunjang, tambahan
            $table->string('sub_kategori', 100)->nullable(); // pendidikan, penelitian, pengabdian, dll
            $table->text('uraian_kegiatan');
            $table->string('satuan', 50)->nullable(); // SKS, dokumen, kegiatan, dll
            $table->decimal('target_default', 10, 2)->nullable();
            $table->decimal('bobot', 5, 2)->nullable(); // bobot nilai
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->index('kategori');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uraian_kegiatan_skp');
    }
};
