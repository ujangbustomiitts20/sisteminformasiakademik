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
        Schema::create('presensi_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->enum('status', ['hadir', 'terlambat', 'sakit', 'izin', 'cuti', 'alpha', 'dinas_luar'])->default('hadir');
            $table->string('lokasi_masuk')->nullable();
            $table->string('lokasi_keluar')->nullable();
            $table->decimal('latitude_masuk', 10, 8)->nullable();
            $table->decimal('longitude_masuk', 11, 8)->nullable();
            $table->decimal('latitude_keluar', 10, 8)->nullable();
            $table->decimal('longitude_keluar', 11, 8)->nullable();
            $table->string('foto_masuk')->nullable();
            $table->string('foto_keluar')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('device_info')->nullable();
            $table->string('ip_address')->nullable();
            $table->boolean('is_manual')->default(false);
            $table->foreignId('diinput_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->unique(['dosen_id', 'tanggal']);
            $table->unique(['pegawai_id', 'tanggal']);
            $table->index('tanggal');
            $table->index('status');
        });

        // Setting jam kerja
        Schema::create('setting_jam_kerja', function (Blueprint $table) {
            $table->id();
            $table->string('nama_setting');
            $table->time('jam_masuk')->default('08:00');
            $table->time('jam_keluar')->default('16:00');
            $table->integer('toleransi_terlambat')->default(15); // menit
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Rekap presensi bulanan
        Schema::create('rekap_presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->year('tahun');
            $table->tinyInteger('bulan');
            $table->integer('total_hari_kerja')->default(0);
            $table->integer('hadir')->default(0);
            $table->integer('terlambat')->default(0);
            $table->integer('sakit')->default(0);
            $table->integer('izin')->default(0);
            $table->integer('cuti')->default(0);
            $table->integer('alpha')->default(0);
            $table->integer('dinas_luar')->default(0);
            $table->decimal('persentase_kehadiran', 5, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['dosen_id', 'tahun', 'bulan']);
            $table->unique(['pegawai_id', 'tahun', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_presensi');
        Schema::dropIfExists('setting_jam_kerja');
        Schema::dropIfExists('presensi_pegawai');
    }
};
