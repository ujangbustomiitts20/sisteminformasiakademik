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
        // Tabel Jenis Potongan (Master Data)
        Schema::create('jenis_potongan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 100);
            $table->enum('kategori', ['diskon', 'potongan_khusus', 'promo', 'keringanan'])->default('diskon');
            $table->text('deskripsi')->nullable();
            $table->enum('tipe_nilai', ['persen', 'nominal'])->default('nominal');
            $table->decimal('nilai_default', 15, 2)->default(0);
            $table->decimal('nilai_max', 15, 2)->nullable(); // Maksimal potongan jika persen
            $table->boolean('is_stackable')->default(false); // Bisa digabung dengan potongan lain
            $table->integer('prioritas')->default(0); // Urutan prioritas aplikasi
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel Periode Diskon (Promo periode tertentu)
        Schema::create('periode_diskon', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama', 150);
            $table->foreignId('jenis_potongan_id')->constrained('jenis_potongan')->onDelete('cascade');
            $table->enum('tipe_nilai', ['persen', 'nominal'])->default('nominal');
            $table->decimal('nilai', 15, 2);
            $table->decimal('nilai_max', 15, 2)->nullable();
            $table->decimal('min_transaksi', 15, 2)->nullable(); // Minimal tagihan untuk dapat diskon
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('kuota')->nullable(); // Null = unlimited
            $table->integer('kuota_terpakai')->default(0);
            $table->text('berlaku_untuk')->nullable(); // program_studi_id, angkatan, jenis_tagihan (JSON)
            $table->text('syarat_ketentuan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // Tabel Potongan Mahasiswa (Individual)
        Schema::create('potongan_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('jenis_potongan_id')->constrained('jenis_potongan')->onDelete('cascade');
            $table->foreignId('periode_diskon_id')->nullable()->constrained('periode_diskon')->onDelete('set null');
            $table->enum('tipe_nilai', ['persen', 'nominal'])->default('nominal');
            $table->decimal('nilai', 15, 2);
            $table->decimal('nilai_max', 15, 2)->nullable();
            $table->string('alasan', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->text('berlaku_untuk_tagihan')->nullable(); // Spesifik tagihan_id atau jenis tagihan (JSON)
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'kadaluarsa', 'dibatalkan'])->default('pending');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('disetujui_at')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // Tabel Riwayat Penggunaan Potongan
        Schema::create('riwayat_potongan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('potongan_mahasiswa_id')->nullable()->constrained('potongan_mahasiswa')->onDelete('set null');
            $table->foreignId('periode_diskon_id')->nullable()->constrained('periode_diskon')->onDelete('set null');
            $table->foreignId('tagihan_id')->constrained('tagihan')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->string('kode_potongan', 30);
            $table->string('nama_potongan', 150);
            $table->enum('tipe_nilai', ['persen', 'nominal']);
            $table->decimal('nilai', 15, 2);
            $table->decimal('nominal_tagihan', 15, 2);
            $table->decimal('nominal_potongan', 15, 2);
            $table->text('keterangan')->nullable();
            $table->foreignId('applied_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_potongan');
        Schema::dropIfExists('potongan_mahasiswa');
        Schema::dropIfExists('periode_diskon');
        Schema::dropIfExists('jenis_potongan');
    }
};
