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
        // Tabel master template dokumen
        Schema::create('template_dokumen', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique()->comment('Kode dokumen: transkrip, krs, slip_gaji, surat_aktif, dll');
            $table->string('nama')->comment('Nama dokumen');
            $table->string('kategori')->default('akademik')->comment('akademik, keuangan, sdm, kemahasiswaan, umum');
            $table->text('deskripsi')->nullable();
            
            // Template konten dengan placeholder
            $table->text('template_judul')->nullable()->comment('Judul dokumen dengan placeholder');
            $table->text('template_nomor')->nullable()->comment('Format nomor surat, misal: {nomor}/UN.{kode_prodi}/{bulan}/{tahun}');
            $table->longText('template_isi')->nullable()->comment('Isi dokumen dengan placeholder {nama}, {nim}, dll');
            $table->text('template_penutup')->nullable()->comment('Kalimat penutup');
            
            // Pengaturan Header/Kop Surat
            $table->boolean('tampilkan_kop')->default(true);
            $table->boolean('tampilkan_logo')->default(true);
            
            // Pengaturan Tanda Tangan
            $table->foreignId('pejabat_1_id')->nullable()->constrained('pejabat_penandatangan')->nullOnDelete();
            $table->string('pejabat_1_label')->nullable()->comment('Label, misal: Mengetahui');
            $table->foreignId('pejabat_2_id')->nullable()->constrained('pejabat_penandatangan')->nullOnDelete();
            $table->string('pejabat_2_label')->nullable()->comment('Label, misal: Yang Membuat');
            $table->boolean('tampilkan_ttd_digital')->default(false);
            $table->boolean('tampilkan_stempel')->default(false);
            
            // Pengaturan Cetak
            $table->string('ukuran_kertas')->default('A4');
            $table->string('orientasi')->default('portrait');
            
            $table->text('catatan_bawah')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            
            $table->index(['kode', 'aktif']);
            $table->index('kategori');
        });

        // Tabel field/variabel yang tersedia untuk setiap template
        Schema::create('template_dokumen_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_dokumen_id')->constrained('template_dokumen')->cascadeOnDelete();
            $table->string('kode_field', 50)->comment('Kode placeholder: nama, nim, tanggal_lahir');
            $table->string('label')->comment('Label untuk form: Nama Lengkap, NIM');
            $table->string('tipe')->default('text')->comment('text, textarea, date, number, select, file');
            $table->text('opsi')->nullable()->comment('JSON opsi untuk select');
            $table->text('nilai_default')->nullable();
            $table->string('sumber_data')->nullable()->comment('mahasiswa.nama, dosen.nidn, dll - auto fill dari relasi');
            $table->boolean('wajib')->default(false);
            $table->integer('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            
            $table->unique(['template_dokumen_id', 'kode_field']);
            $table->index('urutan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_dokumen_fields');
        Schema::dropIfExists('template_dokumen');
    }
};
