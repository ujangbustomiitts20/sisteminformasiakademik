<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Riwayat Pendidikan Dosen
        Schema::create('riwayat_pendidikan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->enum('jenjang', ['D3', 'D4', 'S1', 'S2', 'S3', 'Profesi', 'Spesialis']);
            $table->string('nama_institusi');
            $table->string('program_studi');
            $table->string('no_ijazah')->nullable();
            $table->date('tanggal_ijazah')->nullable();
            $table->year('tahun_masuk')->nullable();
            $table->year('tahun_lulus');
            $table->string('judul_tugas_akhir')->nullable();
            $table->decimal('ipk', 3, 2)->nullable();
            $table->string('file_ijazah')->nullable();
            $table->string('file_transkrip')->nullable();
            $table->timestamps();
        });

        // Riwayat Jabatan Fungsional
        Schema::create('riwayat_jabatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->string('jabatan_fungsional'); // Tenaga Pengajar, Asisten Ahli, Lektor, Lektor Kepala, Guru Besar
            $table->string('no_sk');
            $table->date('tanggal_sk');
            $table->date('tmt_jabatan'); // Tanggal Mulai Tugas
            $table->string('pejabat_penetap')->nullable();
            $table->decimal('angka_kredit', 6, 2)->nullable();
            $table->string('file_sk')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Riwayat Pangkat/Golongan
        Schema::create('riwayat_pangkat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->string('pangkat'); // Penata Muda, Penata Muda Tk.I, dst
            $table->string('golongan'); // III/a, III/b, dst
            $table->string('no_sk');
            $table->date('tanggal_sk');
            $table->date('tmt_pangkat');
            $table->string('pejabat_penetap')->nullable();
            $table->decimal('masa_kerja_tahun', 4, 2)->nullable();
            $table->decimal('masa_kerja_bulan', 4, 2)->nullable();
            $table->string('file_sk')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Riwayat Pelatihan/Diklat
        Schema::create('riwayat_pelatihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->enum('jenis', ['Diklat', 'Workshop', 'Seminar', 'Sertifikasi', 'Kursus', 'Lainnya']);
            $table->string('nama_pelatihan');
            $table->string('penyelenggara');
            $table->string('tempat')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->integer('jumlah_jam')->nullable();
            $table->string('no_sertifikat')->nullable();
            $table->year('tahun')->nullable();
            $table->string('file_sertifikat')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Dokumen Kepegawaian
        Schema::create('dokumen_kepegawaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->string('jenis_dokumen'); // KTP, KK, NPWP, BPJS, SK CPNS, SK PNS, Sertifikasi, dll
            $table->string('nama_dokumen');
            $table->string('no_dokumen')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->date('tanggal_berlaku')->nullable(); // Masa berlaku sampai
            $table->string('penerbit')->nullable();
            $table->string('file_dokumen');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_kepegawaian');
        Schema::dropIfExists('riwayat_pelatihan');
        Schema::dropIfExists('riwayat_pangkat');
        Schema::dropIfExists('riwayat_jabatan');
        Schema::dropIfExists('riwayat_pendidikan');
    }
};
