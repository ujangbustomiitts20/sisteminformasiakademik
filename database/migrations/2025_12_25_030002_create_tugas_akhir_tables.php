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
        // Pendaftaran Tugas Akhir / Skripsi
        Schema::create('tugas_akhir', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_ta')->unique();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->string('judul');
            $table->text('abstrak')->nullable();
            $table->string('bidang_kajian')->nullable();
            $table->text('latar_belakang')->nullable();
            $table->text('rumusan_masalah')->nullable();
            $table->text('metodologi')->nullable();
            // Dosen pembimbing
            $table->foreignId('pembimbing_1_id')->nullable()->constrained('dosen')->onDelete('set null');
            $table->foreignId('pembimbing_2_id')->nullable()->constrained('dosen')->onDelete('set null');
            // Status dan approval
            $table->enum('status', [
                'draft', 
                'diajukan', 
                'judul_disetujui', 
                'judul_ditolak',
                'proposal_diajukan',
                'proposal_disetujui',
                'proposal_revisi',
                'penelitian',
                'sidang_diajukan',
                'sidang_dijadwalkan',
                'lulus',
                'lulus_revisi',
                'tidak_lulus',
                'selesai'
            ])->default('draft');
            $table->text('catatan_pembimbing')->nullable();
            $table->string('dokumen_proposal')->nullable();
            $table->string('dokumen_skripsi')->nullable();
            $table->string('dokumen_final')->nullable();
            $table->timestamp('tanggal_pengajuan')->nullable();
            $table->timestamp('tanggal_approval_judul')->nullable();
            $table->timestamp('tanggal_lulus')->nullable();
            $table->timestamps();
        });

        // Log bimbingan TA
        Schema::create('bimbingan_ta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_akhir_id')->constrained('tugas_akhir')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->string('tempat')->nullable();
            $table->text('materi_bimbingan');
            $table->text('hasil_bimbingan')->nullable();
            $table->text('catatan_dosen')->nullable();
            $table->text('rencana_selanjutnya')->nullable();
            $table->enum('status', ['dijadwalkan', 'selesai', 'dibatalkan'])->default('dijadwalkan');
            $table->integer('persentase_progress')->default(0);
            $table->string('dokumen_pendukung')->nullable();
            $table->timestamps();
        });

        // Seminar Proposal
        Schema::create('seminar_proposal', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_seminar')->unique();
            $table->foreignId('tugas_akhir_id')->constrained('tugas_akhir')->onDelete('cascade');
            $table->date('tanggal')->nullable();
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->string('ruangan')->nullable();
            $table->foreignId('penguji_1_id')->nullable()->constrained('dosen')->onDelete('set null');
            $table->foreignId('penguji_2_id')->nullable()->constrained('dosen')->onDelete('set null');
            $table->enum('status', ['diajukan', 'dijadwalkan', 'berlangsung', 'selesai', 'ditunda', 'dibatalkan'])->default('diajukan');
            // Penilaian
            $table->decimal('nilai_penguji_1', 5, 2)->nullable();
            $table->decimal('nilai_penguji_2', 5, 2)->nullable();
            $table->decimal('nilai_pembimbing_1', 5, 2)->nullable();
            $table->decimal('nilai_pembimbing_2', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->enum('hasil', ['lulus', 'lulus_revisi', 'tidak_lulus'])->nullable();
            $table->text('catatan_revisi')->nullable();
            $table->date('deadline_revisi')->nullable();
            $table->timestamps();
        });

        // Sidang Tugas Akhir
        Schema::create('sidang_ta', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_sidang')->unique();
            $table->foreignId('tugas_akhir_id')->constrained('tugas_akhir')->onDelete('cascade');
            $table->date('tanggal')->nullable();
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->string('ruangan')->nullable();
            // Penguji
            $table->foreignId('ketua_penguji_id')->nullable()->constrained('dosen')->onDelete('set null');
            $table->foreignId('penguji_1_id')->nullable()->constrained('dosen')->onDelete('set null');
            $table->foreignId('penguji_2_id')->nullable()->constrained('dosen')->onDelete('set null');
            $table->enum('status', ['diajukan', 'dijadwalkan', 'berlangsung', 'selesai', 'ditunda', 'dibatalkan'])->default('diajukan');
            // Penilaian detail
            $table->decimal('nilai_presentasi', 5, 2)->nullable();
            $table->decimal('nilai_penguasaan_materi', 5, 2)->nullable();
            $table->decimal('nilai_tanya_jawab', 5, 2)->nullable();
            $table->decimal('nilai_dokumen', 5, 2)->nullable();
            $table->decimal('nilai_ketua', 5, 2)->nullable();
            $table->decimal('nilai_penguji_1', 5, 2)->nullable();
            $table->decimal('nilai_penguji_2', 5, 2)->nullable();
            $table->decimal('nilai_pembimbing_1', 5, 2)->nullable();
            $table->decimal('nilai_pembimbing_2', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->string('grade')->nullable();
            $table->enum('hasil', ['lulus', 'lulus_revisi', 'tidak_lulus'])->nullable();
            $table->text('catatan_revisi')->nullable();
            $table->date('deadline_revisi')->nullable();
            $table->boolean('revisi_selesai')->default(false);
            $table->timestamp('tanggal_revisi_selesai')->nullable();
            $table->timestamps();
        });

        // Dokumen revisi sidang
        Schema::create('revisi_ta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sidang_ta_id')->constrained('sidang_ta')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade'); // Penguji yang memberi revisi
            $table->text('catatan_revisi');
            $table->boolean('sudah_diperbaiki')->default(false);
            $table->timestamp('tanggal_perbaikan')->nullable();
            $table->text('catatan_perbaikan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revisi_ta');
        Schema::dropIfExists('sidang_ta');
        Schema::dropIfExists('seminar_proposal');
        Schema::dropIfExists('bimbingan_ta');
        Schema::dropIfExists('tugas_akhir');
    }
};
