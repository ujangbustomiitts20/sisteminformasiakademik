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
        // Kontrak Kerja
        Schema::create('kontrak_kerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->string('nomor_kontrak', 100)->unique();
            $table->enum('jenis_kontrak', ['tetap', 'kontrak', 'honorer', 'paruh_waktu']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir')->nullable();
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->string('file_kontrak')->nullable();
            $table->enum('status', ['draft', 'aktif', 'berakhir', 'diperpanjang', 'dibatalkan'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['dosen_id', 'status']);
            $table->index(['pegawai_id', 'status']);
        });

        // Evaluasi Kinerja
        Schema::create('periode_evaluasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->year('tahun');
            $table->enum('semester', ['Ganjil', 'Genap', 'Tahunan']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('draft');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('kriteria_evaluasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->text('deskripsi')->nullable();
            $table->decimal('bobot', 5, 2)->default(0); // dalam persen
            $table->enum('kategori', ['dosen', 'pegawai', 'semua'])->default('semua');
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('evaluasi_kinerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_evaluasi_id')->constrained('periode_evaluasi')->cascadeOnDelete();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->foreignId('penilai_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('nilai_total', 5, 2)->default(0);
            $table->enum('predikat', ['sangat_baik', 'baik', 'cukup', 'kurang', 'sangat_kurang'])->nullable();
            $table->text('catatan')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->enum('status', ['draft', 'diajukan', 'disetujui', 'ditolak'])->default('draft');
            $table->timestamp('tanggal_penilaian')->nullable();
            $table->timestamps();
            
            $table->index(['periode_evaluasi_id', 'dosen_id']);
            $table->index(['periode_evaluasi_id', 'pegawai_id']);
        });

        Schema::create('evaluasi_kinerja_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluasi_kinerja_id')->constrained('evaluasi_kinerja')->cascadeOnDelete();
            $table->foreignId('kriteria_evaluasi_id')->constrained('kriteria_evaluasi')->cascadeOnDelete();
            $table->decimal('nilai', 5, 2)->default(0); // 0-100
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Sertifikasi Dosen
        Schema::create('sertifikasi_dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('dosen')->cascadeOnDelete();
            $table->enum('jenis_sertifikasi', ['serdos', 'kompetensi', 'profesi', 'keahlian', 'lainnya']);
            $table->string('nama_sertifikasi', 200);
            $table->string('nomor_sertifikat', 100)->nullable();
            $table->string('penerbit', 200)->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->date('tanggal_berlaku')->nullable();
            $table->date('tanggal_expired')->nullable();
            $table->string('bidang_studi', 200)->nullable();
            $table->string('file_sertifikat')->nullable();
            $table->enum('status', ['aktif', 'expired', 'dicabut'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->index(['dosen_id', 'jenis_sertifikasi']);
        });

        // Tunjangan
        Schema::create('jenis_tunjangan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 100);
            $table->text('deskripsi')->nullable();
            $table->enum('tipe_perhitungan', ['tetap', 'persentase', 'variabel']);
            $table->decimal('nilai_default', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tunjangan_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->foreignId('jenis_tunjangan_id')->constrained('jenis_tunjangan')->cascadeOnDelete();
            $table->decimal('nominal', 15, 2)->default(0);
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir')->nullable();
            $table->string('no_sk', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'tidak_aktif', 'dicabut'])->default('aktif');
            $table->timestamps();
            
            $table->index(['dosen_id', 'jenis_tunjangan_id']);
            $table->index(['pegawai_id', 'jenis_tunjangan_id']);
        });

        // Pelanggaran & Sanksi
        Schema::create('jenis_pelanggaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 100);
            $table->text('deskripsi')->nullable();
            $table->enum('tingkat', ['ringan', 'sedang', 'berat', 'sangat_berat']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('pelanggaran_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->foreignId('jenis_pelanggaran_id')->constrained('jenis_pelanggaran')->cascadeOnDelete();
            $table->date('tanggal_pelanggaran');
            $table->text('deskripsi');
            $table->text('bukti')->nullable();
            $table->string('file_bukti')->nullable();
            $table->enum('status', ['dilaporkan', 'investigasi', 'terbukti', 'tidak_terbukti', 'selesai'])->default('dilaporkan');
            $table->foreignId('dilaporkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['dosen_id', 'status']);
            $table->index(['pegawai_id', 'status']);
        });

        Schema::create('sanksi_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggaran_pegawai_id')->constrained('pelanggaran_pegawai')->cascadeOnDelete();
            $table->enum('jenis_sanksi', ['teguran_lisan', 'teguran_tertulis', 'penundaan_kgb', 'penundaan_pangkat', 'penurunan_pangkat', 'pembebasan_jabatan', 'pemberhentian_hormat', 'pemberhentian_tidak_hormat']);
            $table->string('nomor_sk', 100)->nullable();
            $table->date('tanggal_sk')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('file_sk')->nullable();
            $table->enum('status', ['draft', 'aktif', 'selesai', 'dibatalkan'])->default('draft');
            $table->foreignId('ditetapkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Izin Keluar
        Schema::create('izin_keluar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->date('tanggal');
            $table->time('jam_keluar');
            $table->time('jam_kembali')->nullable();
            $table->time('jam_kembali_aktual')->nullable();
            $table->enum('keperluan', ['dinas', 'pribadi', 'kesehatan', 'keluarga', 'lainnya']);
            $table->text('keterangan');
            $table->string('tujuan', 200)->nullable();
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak', 'selesai'])->default('diajukan');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->text('catatan_approval')->nullable();
            $table->timestamps();
            
            $table->index(['dosen_id', 'tanggal']);
            $table->index(['pegawai_id', 'tanggal']);
        });

        // Lembur
        Schema::create('pengajuan_lembur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->decimal('durasi_jam', 5, 2)->default(0);
            $table->text('alasan');
            $table->text('pekerjaan_yang_dilakukan')->nullable();
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak', 'selesai'])->default('diajukan');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->text('catatan_approval')->nullable();
            $table->decimal('tarif_per_jam', 15, 2)->default(0);
            $table->decimal('total_bayar', 15, 2)->default(0);
            $table->timestamps();
            
            $table->index(['dosen_id', 'tanggal']);
            $table->index(['pegawai_id', 'tanggal']);
        });

        // Pengaturan Tarif Lembur
        Schema::create('tarif_lembur', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->enum('jenis_hari', ['kerja', 'libur', 'libur_nasional']);
            $table->enum('jenis_jam', ['jam_pertama', 'jam_kedua_dst', 'semua']);
            $table->decimal('persentase', 5, 2)->default(100); // dari gaji pokok
            $table->decimal('nominal_tetap', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarif_lembur');
        Schema::dropIfExists('pengajuan_lembur');
        Schema::dropIfExists('izin_keluar');
        Schema::dropIfExists('sanksi_pegawai');
        Schema::dropIfExists('pelanggaran_pegawai');
        Schema::dropIfExists('jenis_pelanggaran');
        Schema::dropIfExists('tunjangan_pegawai');
        Schema::dropIfExists('jenis_tunjangan');
        Schema::dropIfExists('sertifikasi_dosen');
        Schema::dropIfExists('evaluasi_kinerja_detail');
        Schema::dropIfExists('evaluasi_kinerja');
        Schema::dropIfExists('kriteria_evaluasi');
        Schema::dropIfExists('periode_evaluasi');
        Schema::dropIfExists('kontrak_kerja');
    }
};
