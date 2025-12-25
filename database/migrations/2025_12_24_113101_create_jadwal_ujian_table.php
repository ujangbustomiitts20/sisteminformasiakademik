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
        Schema::create('jadwal_ujian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->onDelete('cascade');
            $table->foreignId('jadwal_kuliah_id')->nullable()->constrained('jadwal_kuliah')->onDelete('set null');
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->onDelete('set null');
            $table->enum('jenis_ujian', ['UTS', 'UAS', 'Quiz', 'Remedial', 'Susulan'])->default('UTS');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('ruangan', 50)->nullable();
            $table->integer('durasi_menit')->default(90);
            $table->text('keterangan')->nullable();
            $table->enum('status', ['Terjadwal', 'Berlangsung', 'Selesai', 'Ditunda', 'Dibatalkan'])->default('Terjadwal');
            $table->timestamps();
        });

        // Tabel untuk rekap kehadiran mahasiswa (summary per mata kuliah)
        Schema::create('rekap_kehadiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->onDelete('cascade');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->foreignId('jadwal_kuliah_id')->nullable()->constrained('jadwal_kuliah')->onDelete('set null');
            $table->integer('total_pertemuan')->default(0);
            $table->integer('jumlah_hadir')->default(0);
            $table->integer('jumlah_izin')->default(0);
            $table->integer('jumlah_sakit')->default(0);
            $table->integer('jumlah_alpa')->default(0);
            $table->decimal('persentase_kehadiran', 5, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['mahasiswa_id', 'mata_kuliah_id', 'tahun_akademik_id'], 'rekap_kehadiran_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_kehadiran');
        Schema::dropIfExists('jadwal_ujian');
    }
};
