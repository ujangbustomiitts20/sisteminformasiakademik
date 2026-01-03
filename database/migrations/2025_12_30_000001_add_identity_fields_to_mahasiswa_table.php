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
        Schema::table('mahasiswa', function (Blueprint $table) {
            // 1. Data Kependudukan
            $table->string('nik', 16)->nullable()->after('email');
            $table->string('no_kk', 16)->nullable()->after('nik');
            $table->string('agama', 20)->nullable()->after('no_kk');
            $table->string('kewarganegaraan', 10)->default('WNI')->after('agama');
            $table->string('golongan_darah', 5)->nullable()->after('kewarganegaraan');

            // 2. Data Akademik Tambahan
            $table->string('jalur_masuk', 50)->nullable()->after('angkatan');
            $table->string('asal_sekolah')->nullable()->after('jalur_masuk');
            $table->string('jurusan_asal', 100)->nullable()->after('asal_sekolah');
            $table->year('tahun_lulus_sekolah')->nullable()->after('jurusan_asal');
            $table->decimal('nilai_un', 5, 2)->nullable()->after('tahun_lulus_sekolah');
            $table->string('no_ijazah_sma', 50)->nullable()->after('nilai_un');

            // 3. Data Tambahan Orang Tua
            $table->string('nik_ayah', 16)->nullable()->after('nama_ayah');
            $table->string('pendidikan_ayah', 50)->nullable()->after('pekerjaan_ayah');
            $table->string('nik_ibu', 16)->nullable()->after('nama_ibu');
            $table->string('pendidikan_ibu', 50)->nullable()->after('pekerjaan_ibu');
            $table->string('email_ortu')->nullable()->after('no_hp_ortu');

            // 4. Data Wali
            $table->string('nama_wali')->nullable()->after('alamat_ortu');
            $table->string('hubungan_wali', 50)->nullable()->after('nama_wali');
            $table->string('pekerjaan_wali', 100)->nullable()->after('hubungan_wali');
            $table->string('no_hp_wali', 20)->nullable()->after('pekerjaan_wali');
            $table->string('alamat_wali')->nullable()->after('no_hp_wali');

            // 5. Data Finansial & Beasiswa
            $table->string('no_rekening', 30)->nullable()->after('alamat_wali');
            $table->string('nama_bank', 50)->nullable()->after('no_rekening');
            $table->string('atas_nama_rekening')->nullable()->after('nama_bank');
            $table->boolean('penerima_kip')->default(false)->after('atas_nama_rekening');
            $table->string('no_kip', 20)->nullable()->after('penerima_kip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            // 1. Data Kependudukan
            $table->dropColumn(['nik', 'no_kk', 'agama', 'kewarganegaraan', 'golongan_darah']);

            // 2. Data Akademik Tambahan
            $table->dropColumn(['jalur_masuk', 'asal_sekolah', 'jurusan_asal', 'tahun_lulus_sekolah', 'nilai_un', 'no_ijazah_sma']);

            // 3. Data Tambahan Orang Tua
            $table->dropColumn(['nik_ayah', 'pendidikan_ayah', 'nik_ibu', 'pendidikan_ibu', 'email_ortu']);

            // 4. Data Wali
            $table->dropColumn(['nama_wali', 'hubungan_wali', 'pekerjaan_wali', 'no_hp_wali', 'alamat_wali']);

            // 5. Data Finansial & Beasiswa
            $table->dropColumn(['no_rekening', 'nama_bank', 'atas_nama_rekening', 'penerima_kip', 'no_kip']);
        });
    }
};
