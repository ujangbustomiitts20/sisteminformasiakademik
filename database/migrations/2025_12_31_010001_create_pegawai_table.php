<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Pegawai (Tenaga Kependidikan)
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nip', 30)->unique()->nullable();
            $table->string('nik', 20)->unique()->nullable();
            $table->string('nama');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'])->nullable();
            $table->enum('status_pernikahan', ['Belum Menikah', 'Menikah', 'Cerai Hidup', 'Cerai Mati'])->nullable();
            
            // Kontak
            $table->string('email')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('telepon', 20)->nullable();
            
            // Alamat
            $table->text('alamat')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->foreignId('provinsi_id')->nullable()->constrained('provinsi')->onDelete('set null');
            $table->foreignId('kabupaten_id')->nullable()->constrained('kabupaten')->onDelete('set null');
            $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatan')->onDelete('set null');
            $table->foreignId('kelurahan_id')->nullable()->constrained('kelurahan')->onDelete('set null');
            $table->string('kode_pos', 10)->nullable();
            
            // Kepegawaian
            $table->foreignId('unit_kerja_id')->nullable();
            $table->string('jabatan')->nullable();
            $table->enum('jenis_pegawai', ['PNS', 'PPPK', 'Honorer', 'Kontrak', 'Tetap Yayasan'])->default('Honorer');
            $table->string('golongan', 10)->nullable();
            $table->string('pangkat', 50)->nullable();
            $table->date('tmt_pegawai')->nullable(); // Tanggal Mulai Tugas
            $table->date('tmt_jabatan')->nullable();
            $table->enum('status', ['Aktif', 'Cuti', 'Non-Aktif', 'Pensiun'])->default('Aktif');
            
            // Finansial
            $table->string('no_rekening')->nullable();
            $table->string('nama_bank')->nullable();
            $table->string('npwp', 30)->nullable();
            $table->string('no_bpjs_kesehatan', 30)->nullable();
            $table->string('no_bpjs_ketenagakerjaan', 30)->nullable();
            
            // Dokumen
            $table->string('foto')->nullable();
            
            $table->timestamps();
        });

        // Update tabel riwayat untuk mendukung dosen dan pegawai (polymorphic)
        Schema::table('riwayat_pendidikan', function (Blueprint $table) {
            $table->foreignId('pegawai_id')->nullable()->after('dosen_id')->constrained('pegawai')->onDelete('cascade');
            $table->dropForeign(['dosen_id']);
            $table->foreignId('dosen_id')->nullable()->change();
            $table->foreign('dosen_id')->references('id')->on('dosen')->onDelete('cascade');
        });

        Schema::table('riwayat_jabatan', function (Blueprint $table) {
            $table->foreignId('pegawai_id')->nullable()->after('dosen_id')->constrained('pegawai')->onDelete('cascade');
            $table->dropForeign(['dosen_id']);
            $table->foreignId('dosen_id')->nullable()->change();
            $table->foreign('dosen_id')->references('id')->on('dosen')->onDelete('cascade');
        });

        Schema::table('riwayat_pangkat', function (Blueprint $table) {
            $table->foreignId('pegawai_id')->nullable()->after('dosen_id')->constrained('pegawai')->onDelete('cascade');
            $table->dropForeign(['dosen_id']);
            $table->foreignId('dosen_id')->nullable()->change();
            $table->foreign('dosen_id')->references('id')->on('dosen')->onDelete('cascade');
        });

        Schema::table('riwayat_pelatihan', function (Blueprint $table) {
            $table->foreignId('pegawai_id')->nullable()->after('dosen_id')->constrained('pegawai')->onDelete('cascade');
            $table->dropForeign(['dosen_id']);
            $table->foreignId('dosen_id')->nullable()->change();
            $table->foreign('dosen_id')->references('id')->on('dosen')->onDelete('cascade');
        });

        Schema::table('dokumen_kepegawaian', function (Blueprint $table) {
            $table->foreignId('pegawai_id')->nullable()->after('dosen_id')->constrained('pegawai')->onDelete('cascade');
            $table->dropForeign(['dosen_id']);
            $table->foreignId('dosen_id')->nullable()->change();
            $table->foreign('dosen_id')->references('id')->on('dosen')->onDelete('cascade');
        });

        // Tabel Unit Kerja
        Schema::create('unit_kerja', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama');
            $table->foreignId('parent_id')->nullable()->constrained('unit_kerja')->onDelete('set null');
            $table->foreignId('kepala_id')->nullable(); // Kepala unit kerja
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tambahkan foreign key untuk unit_kerja_id di pegawai
        Schema::table('pegawai', function (Blueprint $table) {
            $table->foreign('unit_kerja_id')->references('id')->on('unit_kerja')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('dokumen_kepegawaian', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
            $table->dropColumn('pegawai_id');
        });

        Schema::table('riwayat_pelatihan', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
            $table->dropColumn('pegawai_id');
        });

        Schema::table('riwayat_pangkat', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
            $table->dropColumn('pegawai_id');
        });

        Schema::table('riwayat_jabatan', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
            $table->dropColumn('pegawai_id');
        });

        Schema::table('riwayat_pendidikan', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
            $table->dropColumn('pegawai_id');
        });

        Schema::dropIfExists('pegawai');
        Schema::dropIfExists('unit_kerja');
    }
};
