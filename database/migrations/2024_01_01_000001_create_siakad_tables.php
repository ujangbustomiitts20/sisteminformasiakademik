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
        // Tabel Fakultas
        Schema::create('fakultas', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();
            $table->string('nama');
            $table->string('dekan')->nullable();
            $table->timestamps();
        });

        // Tabel Program Studi
        Schema::create('program_studi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fakultas_id')->constrained('fakultas')->onDelete('cascade');
            $table->string('kode', 10)->unique();
            $table->string('nama');
            $table->string('jenjang', 10); // S1, S2, S3, D3
            $table->string('kaprodi')->nullable();
            $table->integer('total_sks')->default(144);
            $table->timestamps();
        });

        // Tabel Dosen
        Schema::create('dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('program_studi_id')->constrained('program_studi')->onDelete('cascade');
            $table->string('nidn', 20)->unique();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email')->unique();
            $table->string('jabatan_fungsional')->nullable();
            $table->string('golongan')->nullable();
            $table->enum('status', ['Aktif', 'Cuti', 'Nonaktif'])->default('Aktif');
            $table->string('foto')->nullable();
            $table->timestamps();
        });

        // Tabel Mahasiswa
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('program_studi_id')->constrained('program_studi')->onDelete('cascade');
            $table->foreignId('dosen_wali_id')->nullable()->constrained('dosen')->onDelete('set null');
            $table->string('nim', 20)->unique();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email')->unique();
            $table->integer('angkatan');
            $table->integer('semester_aktif')->default(1);
            $table->enum('status', ['Aktif', 'Cuti', 'Lulus', 'DO'])->default('Aktif');
            $table->string('foto')->nullable();
            $table->timestamps();
        });

        // Tabel Ruangan
        Schema::create('ruangan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama');
            $table->integer('kapasitas');
            $table->string('gedung')->nullable();
            $table->string('lantai')->nullable();
            $table->enum('jenis', ['Kelas', 'Lab', 'Aula', 'Lainnya'])->default('Kelas');
            $table->timestamps();
        });

        // Tabel Mata Kuliah
        Schema::create('mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_studi_id')->constrained('program_studi')->onDelete('cascade');
            $table->string('kode', 20)->unique();
            $table->string('nama');
            $table->integer('sks');
            $table->integer('semester');
            $table->enum('jenis', ['Wajib', 'Pilihan'])->default('Wajib');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Tabel Tahun Akademik
        Schema::create('tahun_akademik', function (Blueprint $table) {
            $table->id();
            $table->string('tahun', 10); // contoh: 2024/2025
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->date('mulai_krs')->nullable();
            $table->date('selesai_krs')->nullable();
            $table->boolean('is_aktif')->default(false);
            $table->timestamps();
        });

        // Tabel Jadwal Kuliah
        Schema::create('jadwal_kuliah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->foreignId('ruangan_id')->constrained('ruangan')->onDelete('cascade');
            $table->string('kelas', 5); // A, B, C
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('kuota')->default(40);
            $table->timestamps();
        });

        // Tabel KRS (Kartu Rencana Studi)
        Schema::create('krs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->foreignId('jadwal_kuliah_id')->constrained('jadwal_kuliah')->onDelete('cascade');
            $table->enum('status', ['Pending', 'Disetujui', 'Ditolak'])->default('Pending');
            $table->timestamp('tanggal_pengajuan')->nullable();
            $table->timestamp('tanggal_persetujuan')->nullable();
            $table->timestamps();

            $table->unique(['mahasiswa_id', 'tahun_akademik_id', 'jadwal_kuliah_id'], 'krs_unique');
        });

        // Tabel Nilai (KHS)
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krs_id')->constrained('krs')->onDelete('cascade');
            $table->decimal('tugas', 5, 2)->nullable();
            $table->decimal('uts', 5, 2)->nullable();
            $table->decimal('uas', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->string('huruf', 2)->nullable(); // A, B+, B, C+, C, D, E
            $table->decimal('bobot', 3, 2)->nullable(); // 4.00, 3.50, etc
            $table->timestamps();
        });

        // Tabel Absensi
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krs_id')->constrained('krs')->onDelete('cascade');
            $table->date('tanggal');
            $table->integer('pertemuan');
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpha'])->default('Hadir');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Tabel Pembayaran
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->enum('jenis', ['SPP', 'Herregistrasi', 'Wisuda', 'Lainnya']);
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal_bayar')->nullable();
            $table->string('metode_bayar')->nullable();
            $table->string('bukti_bayar')->nullable();
            $table->enum('status', ['Belum Lunas', 'Lunas'])->default('Belum Lunas');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Tabel Pengumuman
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('judul');
            $table->text('isi');
            $table->enum('kategori', ['Umum', 'Akademik', 'Keuangan', 'Kemahasiswaan']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('absensi');
        Schema::dropIfExists('nilai');
        Schema::dropIfExists('krs');
        Schema::dropIfExists('jadwal_kuliah');
        Schema::dropIfExists('tahun_akademik');
        Schema::dropIfExists('mata_kuliah');
        Schema::dropIfExists('ruangan');
        Schema::dropIfExists('mahasiswa');
        Schema::dropIfExists('dosen');
        Schema::dropIfExists('program_studi');
        Schema::dropIfExists('fakultas');
    }
};
