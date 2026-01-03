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
        Schema::table('dosen', function (Blueprint $table) {
            // 1. Data Akademik
            $table->string('pendidikan_terakhir', 10)->nullable()->after('foto'); // S1/S2/S3
            $table->string('gelar_depan', 50)->nullable()->after('pendidikan_terakhir'); // Prof./Dr./Ir.
            $table->string('gelar_belakang', 100)->nullable()->after('gelar_depan'); // M.Kom., Ph.D.
            $table->string('bidang_keahlian')->nullable()->after('gelar_belakang');
            $table->string('rumpun_ilmu', 100)->nullable()->after('bidang_keahlian');
            $table->string('sinta_id', 50)->nullable()->after('rumpun_ilmu');
            $table->string('scopus_id', 50)->nullable()->after('sinta_id');
            $table->string('google_scholar_id', 50)->nullable()->after('scopus_id');
            $table->string('orcid', 50)->nullable()->after('google_scholar_id');

            // 2. Data Sertifikasi
            $table->string('no_sertifikasi_dosen', 50)->nullable()->after('orcid');
            $table->year('tahun_sertifikasi')->nullable()->after('no_sertifikasi_dosen');
            $table->string('no_registrasi_dikti', 50)->nullable()->after('tahun_sertifikasi');

            // 3. Alamat Lengkap
            $table->foreignId('provinsi_id')->nullable()->after('alamat')->constrained('provinsi')->nullOnDelete();
            $table->foreignId('kabupaten_id')->nullable()->after('provinsi_id')->constrained('kabupaten')->nullOnDelete();
            $table->foreignId('kecamatan_id')->nullable()->after('kabupaten_id')->constrained('kecamatan')->nullOnDelete();
            $table->foreignId('kelurahan_id')->nullable()->after('kecamatan_id')->constrained('kelurahan')->nullOnDelete();
            $table->string('rt', 5)->nullable()->after('kelurahan_id');
            $table->string('rw', 5)->nullable()->after('rt');
            $table->string('kode_pos', 10)->nullable()->after('rw');

            // 4. Data Kontak & Bank
            $table->string('no_hp', 20)->nullable()->after('telepon');
            $table->string('no_npwp', 25)->nullable()->after('kode_pos');
            $table->string('no_rekening', 30)->nullable()->after('no_npwp');
            $table->string('nama_bank', 50)->nullable()->after('no_rekening');
            $table->string('atas_nama_rekening')->nullable()->after('nama_bank');
            $table->string('no_bpjs_kesehatan', 20)->nullable()->after('atas_nama_rekening');
            $table->string('no_bpjs_ketenagakerjaan', 20)->nullable()->after('no_bpjs_kesehatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['provinsi_id']);
            $table->dropForeign(['kabupaten_id']);
            $table->dropForeign(['kecamatan_id']);
            $table->dropForeign(['kelurahan_id']);

            // Drop columns
            $table->dropColumn([
                // Data Akademik
                'pendidikan_terakhir',
                'gelar_depan',
                'gelar_belakang',
                'bidang_keahlian',
                'rumpun_ilmu',
                'sinta_id',
                'scopus_id',
                'google_scholar_id',
                'orcid',
                // Data Sertifikasi
                'no_sertifikasi_dosen',
                'tahun_sertifikasi',
                'no_registrasi_dikti',
                // Alamat Lengkap
                'provinsi_id',
                'kabupaten_id',
                'kecamatan_id',
                'kelurahan_id',
                'rt',
                'rw',
                'kode_pos',
                // Data Kontak & Bank
                'no_hp',
                'no_npwp',
                'no_rekening',
                'nama_bank',
                'atas_nama_rekening',
                'no_bpjs_kesehatan',
                'no_bpjs_ketenagakerjaan',
            ]);
        });
    }
};
