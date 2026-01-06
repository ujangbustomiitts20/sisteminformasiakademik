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
        // Drop tunjangan tables - replaced by komponen_gaji and slip_gaji
        Schema::dropIfExists('tunjangan_pegawai');
        Schema::dropIfExists('jenis_tunjangan');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate jenis_tunjangan table
        Schema::create('jenis_tunjangan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 100);
            $table->text('deskripsi')->nullable();
            $table->decimal('nominal_default', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Recreate tunjangan_pegawai table
        Schema::create('tunjangan_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->nullable()->constrained('dosens')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawais')->nullOnDelete();
            $table->foreignId('jenis_tunjangan_id')->constrained('jenis_tunjangan')->cascadeOnDelete();
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir')->nullable();
            $table->string('no_sk')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->timestamps();
            
            $table->index(['dosen_id', 'jenis_tunjangan_id']);
            $table->index(['pegawai_id', 'jenis_tunjangan_id']);
        });
    }
};
