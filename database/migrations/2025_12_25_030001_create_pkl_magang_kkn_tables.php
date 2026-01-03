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
        // Jenis kegiatan: PKL, Magang, KKN
        Schema::create('jenis_kegiatan_lapangan', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama'); // PKL, Magang, KKN
            $table->text('deskripsi')->nullable();
            $table->integer('sks')->default(0);
            $table->integer('durasi_minggu')->default(8);
            $table->integer('semester_minimal')->default(6); // Minimal semester untuk mendaftar
            $table->integer('sks_minimal')->default(100); // Minimal SKS untuk mendaftar
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Periode kegiatan lapangan
        Schema::create('periode_kegiatan_lapangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_kegiatan_id')->constrained('jenis_kegiatan_lapangan')->onDelete('cascade');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->string('nama');
            $table->date('tanggal_mulai_daftar');
            $table->date('tanggal_selesai_daftar');
            $table->date('tanggal_mulai_kegiatan');
            $table->date('tanggal_selesai_kegiatan');
            $table->integer('kuota')->nullable();
            $table->enum('status', ['draft', 'dibuka', 'ditutup', 'selesai'])->default('draft');
            $table->timestamps();
        });

        // Mitra/Lokasi kegiatan
        Schema::create('mitra_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->string('alamat');
            $table->string('kota');
            $table->string('provinsi');
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('nama_kontak')->nullable(); // Contact person
            $table->string('jabatan_kontak')->nullable();
            $table->text('bidang_usaha')->nullable();
            $table->integer('kuota_mahasiswa')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pendaftaran kegiatan lapangan
        Schema::create('pendaftaran_kegiatan_lapangan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pendaftaran')->unique();
            $table->foreignId('periode_id')->constrained('periode_kegiatan_lapangan')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('mitra_pilihan_1')->nullable()->constrained('mitra_kegiatan')->onDelete('set null');
            $table->foreignId('mitra_pilihan_2')->nullable()->constrained('mitra_kegiatan')->onDelete('set null');
            $table->foreignId('mitra_pilihan_3')->nullable()->constrained('mitra_kegiatan')->onDelete('set null');
            $table->foreignId('mitra_diterima')->nullable()->constrained('mitra_kegiatan')->onDelete('set null');
            $table->foreignId('dosen_pembimbing_id')->nullable()->constrained('dosen')->onDelete('set null');
            $table->string('pembimbing_lapangan')->nullable();
            $table->string('jabatan_pembimbing_lapangan')->nullable();
            $table->text('rencana_kegiatan')->nullable();
            $table->string('surat_pengantar')->nullable(); // Path file
            $table->string('dokumen_pendukung')->nullable();
            $table->enum('status', ['draft', 'diajukan', 'disetujui', 'ditolak', 'berlangsung', 'selesai', 'tidak_lulus'])->default('draft');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['periode_id', 'mahasiswa_id']);
        });

        // Log monitoring kegiatan
        Schema::create('log_kegiatan_lapangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran_kegiatan_lapangan')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->text('kegiatan');
            $table->text('hasil')->nullable();
            $table->text('kendala')->nullable();
            $table->string('dokumentasi')->nullable(); // Path file foto
            $table->enum('status', ['draft', 'diajukan', 'disetujui', 'revisi'])->default('draft');
            $table->text('komentar_pembimbing')->nullable();
            $table->timestamps();
        });

        // Penilaian kegiatan lapangan
        Schema::create('penilaian_kegiatan_lapangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran_kegiatan_lapangan')->onDelete('cascade');
            $table->enum('jenis_penilai', ['dosen', 'lapangan']); // Dosen pembimbing atau pembimbing lapangan
            // Komponen nilai
            $table->decimal('nilai_kedisiplinan', 5, 2)->nullable();
            $table->decimal('nilai_kerjasama', 5, 2)->nullable();
            $table->decimal('nilai_inisiatif', 5, 2)->nullable();
            $table->decimal('nilai_keterampilan', 5, 2)->nullable();
            $table->decimal('nilai_hasil_kerja', 5, 2)->nullable();
            $table->decimal('nilai_laporan', 5, 2)->nullable();
            $table->decimal('nilai_presentasi', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->string('grade')->nullable(); // A, B, C, D, E
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_penilaian')->nullable();
            $table->timestamps();

            $table->unique(['pendaftaran_id', 'jenis_penilai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_kegiatan_lapangan');
        Schema::dropIfExists('log_kegiatan_lapangan');
        Schema::dropIfExists('pendaftaran_kegiatan_lapangan');
        Schema::dropIfExists('mitra_kegiatan');
        Schema::dropIfExists('periode_kegiatan_lapangan');
        Schema::dropIfExists('jenis_kegiatan_lapangan');
    }
};
