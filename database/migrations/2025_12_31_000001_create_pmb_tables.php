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
        // 1. Periode PMB
        Schema::create('periode_pmb', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100); // e.g., "PMB 2025/2026"
            $table->string('tahun_akademik', 20); // e.g., "2025/2026"
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // 2. Gelombang PMB
        Schema::create('gelombang_pmb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_pmb_id')->constrained('periode_pmb')->onDelete('cascade');
            $table->string('nama', 50); // e.g., "Gelombang 1", "Gelombang 2"
            $table->integer('nomor_gelombang');
            $table->date('tanggal_mulai_daftar');
            $table->date('tanggal_selesai_daftar');
            $table->date('tanggal_ujian')->nullable();
            $table->date('tanggal_pengumuman')->nullable();
            $table->date('tanggal_daftar_ulang_mulai')->nullable();
            $table->date('tanggal_daftar_ulang_selesai')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // 3. Jalur Seleksi
        Schema::create('jalur_seleksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 100); // SNBP, SNBT, Mandiri, dll
            $table->text('deskripsi')->nullable();
            $table->text('persyaratan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Biaya Pendaftaran
        Schema::create('biaya_pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gelombang_pmb_id')->constrained('gelombang_pmb')->onDelete('cascade');
            $table->foreignId('jalur_seleksi_id')->constrained('jalur_seleksi')->onDelete('cascade');
            $table->foreignId('program_studi_id')->nullable()->constrained('program_studi')->onDelete('set null');
            $table->decimal('biaya_formulir', 15, 2)->default(0);
            $table->decimal('biaya_ujian', 15, 2)->default(0);
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->timestamps();
        });

        // 5. Kuota Prodi
        Schema::create('kuota_pmb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gelombang_pmb_id')->constrained('gelombang_pmb')->onDelete('cascade');
            $table->foreignId('program_studi_id')->constrained('program_studi')->onDelete('cascade');
            $table->foreignId('jalur_seleksi_id')->constrained('jalur_seleksi')->onDelete('cascade');
            $table->integer('kuota')->default(0);
            $table->integer('terisi')->default(0);
            $table->decimal('passing_grade', 5, 2)->nullable();
            $table->timestamps();
            
            $table->unique(['gelombang_pmb_id', 'program_studi_id', 'jalur_seleksi_id'], 'kuota_unique');
        });

        // 6. Calon Mahasiswa (Pendaftar)
        Schema::create('calon_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('no_pendaftaran', 30)->unique();
            $table->foreignId('gelombang_pmb_id')->constrained('gelombang_pmb')->onDelete('cascade');
            $table->foreignId('jalur_seleksi_id')->constrained('jalur_seleksi')->onDelete('cascade');
            $table->foreignId('program_studi_id')->constrained('program_studi')->onDelete('cascade');
            $table->foreignId('program_studi_2_id')->nullable()->constrained('program_studi')->onDelete('set null');
            
            // Data Pribadi
            $table->string('nama_lengkap');
            $table->string('nik', 20)->nullable();
            $table->string('nisn', 20)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->string('agama', 20)->nullable();
            $table->string('kewarganegaraan', 50)->default('Indonesia');
            
            // Kontak
            $table->string('email')->unique();
            $table->string('no_hp', 20);
            $table->string('no_wa', 20)->nullable();
            
            // Alamat
            $table->text('alamat');
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kabupaten', 100)->nullable();
            $table->string('provinsi', 100)->nullable();
            $table->string('kode_pos', 10)->nullable();
            
            // Data Asal Sekolah
            $table->string('asal_sekolah');
            $table->string('npsn_sekolah', 20)->nullable();
            $table->string('jurusan_sekolah', 100)->nullable();
            $table->year('tahun_lulus');
            $table->decimal('nilai_rata_rata', 5, 2)->nullable();
            
            // Data Orang Tua
            $table->string('nama_ayah')->nullable();
            $table->string('pekerjaan_ayah', 100)->nullable();
            $table->string('no_hp_ayah', 20)->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('pekerjaan_ibu', 100)->nullable();
            $table->string('no_hp_ibu', 20)->nullable();
            $table->decimal('penghasilan_ortu', 15, 2)->nullable();
            
            // Foto & Dokumen
            $table->string('foto')->nullable();
            $table->string('password'); // Untuk login portal camaba
            
            // Status
            $table->enum('status_pendaftaran', [
                'draft', 'menunggu_bayar', 'terdaftar', 'mengikuti_ujian', 
                'lulus', 'tidak_lulus', 'daftar_ulang', 'menjadi_mahasiswa', 'batal'
            ])->default('draft');
            $table->boolean('is_dokumen_lengkap')->default(false);
            $table->boolean('is_bayar_pendaftaran')->default(false);
            $table->datetime('tanggal_bayar')->nullable();
            
            $table->timestamps();
        });

        // 7. Dokumen Calon Mahasiswa
        Schema::create('dokumen_camaba', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_mahasiswa_id')->constrained('calon_mahasiswa')->onDelete('cascade');
            $table->string('jenis_dokumen', 50); // ijazah, ktp, kk, akta, foto, dll
            $table->string('nama_file');
            $table->string('path_file');
            $table->enum('status_verifikasi', ['pending', 'valid', 'tidak_valid'])->default('pending');
            $table->text('catatan_verifikasi')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('verified_at')->nullable();
            $table->timestamps();
        });

        // 8. Pembayaran PMB
        Schema::create('pembayaran_pmb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_mahasiswa_id')->constrained('calon_mahasiswa')->onDelete('cascade');
            $table->string('no_pembayaran', 30)->unique();
            $table->string('jenis_pembayaran', 50); // pendaftaran, daftar_ulang
            $table->decimal('jumlah', 15, 2);
            $table->string('metode_pembayaran', 50)->nullable(); // transfer, va, midtrans
            $table->string('no_va', 50)->nullable();
            $table->string('bank', 50)->nullable();
            $table->string('bukti_bayar')->nullable();
            $table->enum('status', ['pending', 'menunggu_verifikasi', 'terverifikasi', 'ditolak', 'expired'])->default('pending');
            $table->datetime('tanggal_bayar')->nullable();
            $table->datetime('tanggal_expired')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('verified_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 9. Jadwal Ujian PMB
        Schema::create('jadwal_ujian_pmb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gelombang_pmb_id')->constrained('gelombang_pmb')->onDelete('cascade');
            $table->string('nama_ujian', 100);
            $table->date('tanggal_ujian');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('lokasi')->nullable();
            $table->string('ruangan')->nullable();
            $table->integer('kapasitas')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 10. Peserta Ujian PMB
        Schema::create('peserta_ujian_pmb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_mahasiswa_id')->constrained('calon_mahasiswa')->onDelete('cascade');
            $table->foreignId('jadwal_ujian_pmb_id')->constrained('jadwal_ujian_pmb')->onDelete('cascade');
            $table->string('no_peserta', 30)->nullable();
            $table->string('ruangan', 50)->nullable();
            $table->integer('no_kursi')->nullable();
            $table->boolean('hadir')->default(false);
            $table->datetime('waktu_hadir')->nullable();
            $table->timestamps();
            
            $table->unique(['calon_mahasiswa_id', 'jadwal_ujian_pmb_id']);
        });

        // 11. Nilai Seleksi
        Schema::create('nilai_seleksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_mahasiswa_id')->constrained('calon_mahasiswa')->onDelete('cascade');
            $table->string('komponen_nilai', 100); // TPA, Bahasa Inggris, Wawancara, dll
            $table->decimal('nilai', 5, 2);
            $table->decimal('bobot', 5, 2)->default(1);
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->foreignId('input_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 12. Hasil Seleksi
        Schema::create('hasil_seleksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_mahasiswa_id')->constrained('calon_mahasiswa')->onDelete('cascade');
            $table->foreignId('gelombang_pmb_id')->constrained('gelombang_pmb')->onDelete('cascade');
            $table->decimal('nilai_total', 5, 2)->nullable();
            $table->integer('ranking')->nullable();
            $table->enum('status', ['lulus', 'tidak_lulus', 'cadangan'])->nullable();
            $table->foreignId('program_studi_diterima_id')->nullable()->constrained('program_studi')->onDelete('set null');
            $table->text('catatan')->nullable();
            $table->datetime('tanggal_pengumuman')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 13. Daftar Ulang
        Schema::create('daftar_ulang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_mahasiswa_id')->constrained('calon_mahasiswa')->onDelete('cascade');
            $table->string('no_daftar_ulang', 30)->unique();
            $table->decimal('biaya_daftar_ulang', 15, 2);
            $table->decimal('biaya_ukt', 15, 2)->nullable();
            $table->decimal('total_biaya', 15, 2);
            $table->enum('status', ['pending', 'lunas', 'verifikasi_dokumen', 'selesai', 'batal'])->default('pending');
            $table->datetime('tanggal_bayar')->nullable();
            $table->datetime('tanggal_verifikasi')->nullable();
            $table->string('nim_generated', 20)->nullable();
            $table->foreignId('mahasiswa_id')->nullable()->constrained('mahasiswa')->onDelete('set null');
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 14. Setting Dokumen Wajib
        Schema::create('setting_dokumen_pmb', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama_dokumen', 100);
            $table->text('deskripsi')->nullable();
            $table->string('format_file', 100)->default('pdf,jpg,jpeg,png');
            $table->integer('max_size_kb')->default(2048);
            $table->boolean('is_wajib')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_dokumen_pmb');
        Schema::dropIfExists('daftar_ulang');
        Schema::dropIfExists('hasil_seleksi');
        Schema::dropIfExists('nilai_seleksi');
        Schema::dropIfExists('peserta_ujian_pmb');
        Schema::dropIfExists('jadwal_ujian_pmb');
        Schema::dropIfExists('pembayaran_pmb');
        Schema::dropIfExists('dokumen_camaba');
        Schema::dropIfExists('calon_mahasiswa');
        Schema::dropIfExists('kuota_pmb');
        Schema::dropIfExists('biaya_pendaftaran');
        Schema::dropIfExists('jalur_seleksi');
        Schema::dropIfExists('gelombang_pmb');
        Schema::dropIfExists('periode_pmb');
    }
};
