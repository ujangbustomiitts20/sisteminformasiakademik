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
        // Periode Ujian
        if (!Schema::hasTable('periode_ujian')) {
            Schema::create('periode_ujian', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
                $table->enum('jenis', ['UTS', 'UAS', 'Susulan', 'Remedial']);
                $table->date('tanggal_mulai');
                $table->date('tanggal_selesai');
                $table->date('tanggal_cetak_kartu')->nullable();
                $table->integer('minimal_kehadiran')->default(75); // Persentase minimal kehadiran
                $table->boolean('cek_pembayaran')->default(true);
                $table->enum('status', ['draft', 'aktif', 'selesai'])->default('draft');
                $table->timestamps();
            });
        }

        // Update tabel jadwal_ujian jika belum ada field yang dibutuhkan
        try {
            Schema::table('jadwal_ujian', function (Blueprint $table) {
                $table->foreignId('periode_ujian_id')->nullable()->after('id')->constrained('periode_ujian')->onDelete('cascade');
                $table->string('pengawas_1')->nullable()->after('ruangan_id');
                $table->string('pengawas_2')->nullable()->after('pengawas_1');
            });
        } catch (\Exception $e) {
            // Kolom mungkin sudah ada, abaikan error
        }

        // Kartu Ujian
        if (!Schema::hasTable('kartu_ujian')) {
            Schema::create('kartu_ujian', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_kartu')->unique();
                $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
                $table->foreignId('periode_ujian_id')->constrained('periode_ujian')->onDelete('cascade');
                $table->decimal('persentase_kehadiran', 5, 2)->default(0);
                $table->boolean('eligible')->default(false);
                $table->string('alasan_tidak_eligible')->nullable();
                $table->boolean('pembayaran_lunas')->default(false);
                $table->datetime('tanggal_cetak')->nullable();
                $table->string('qr_code')->nullable();
                $table->enum('status', ['pending', 'approved', 'printed', 'revoked'])->default('pending');
                $table->timestamps();

                $table->unique(['mahasiswa_id', 'periode_ujian_id']);
            });
        }

        // Detail Kartu Ujian (mata kuliah yang boleh diujikan)
        if (!Schema::hasTable('detail_kartu_ujian')) {
            Schema::create('detail_kartu_ujian', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kartu_ujian_id')->constrained('kartu_ujian')->onDelete('cascade');
                $table->foreignId('jadwal_ujian_id')->constrained('jadwal_ujian')->onDelete('cascade');
                $table->foreignId('krs_id')->constrained('krs')->onDelete('cascade');
                $table->decimal('persentase_kehadiran', 5, 2)->default(0);
                $table->boolean('eligible')->default(false);
                $table->string('alasan_tidak_eligible')->nullable();
                $table->string('paraf_pengawas')->nullable();
                $table->timestamps();

                $table->unique(['kartu_ujian_id', 'jadwal_ujian_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_kartu_ujian');
        Schema::dropIfExists('kartu_ujian');
        
        try {
            Schema::table('jadwal_ujian', function (Blueprint $table) {
                $table->dropForeign(['periode_ujian_id']);
                $table->dropColumn(['periode_ujian_id', 'pengawas_1', 'pengawas_2']);
            });
        } catch (\Exception $e) {
            // Kolom mungkin tidak ada, abaikan error
        }
        
        Schema::dropIfExists('periode_ujian');
    }
};
