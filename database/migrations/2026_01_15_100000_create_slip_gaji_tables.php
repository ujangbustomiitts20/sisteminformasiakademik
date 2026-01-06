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
        // Komponen Gaji (Tunjangan, Potongan, dll)
        Schema::create('komponen_gaji', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama');
            $table->enum('jenis', ['pendapatan', 'potongan'])->default('pendapatan');
            $table->enum('tipe_nilai', ['tetap', 'persentase'])->default('tetap');
            $table->decimal('nilai_default', 15, 2)->default(0);
            $table->boolean('wajib')->default(false);
            $table->text('keterangan')->nullable();
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // Slip Gaji
        Schema::create('slip_gaji', function (Blueprint $table) {
            $table->id();
            $table->string('no_slip', 50)->unique();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->year('tahun');
            $table->tinyInteger('bulan');
            $table->date('tanggal_slip');
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->decimal('total_tunjangan', 15, 2)->default(0);
            $table->decimal('total_potongan', 15, 2)->default(0);
            $table->decimal('gaji_kotor', 15, 2)->default(0); // gaji_pokok + total_tunjangan
            $table->decimal('gaji_bersih', 15, 2)->default(0); // gaji_kotor - total_potongan
            $table->enum('status', ['draft', 'diproses', 'disetujui', 'dibayar', 'dibatalkan'])->default('draft');
            $table->date('tanggal_bayar')->nullable();
            $table->string('metode_pembayaran')->nullable(); // transfer, tunai
            $table->string('no_referensi')->nullable(); // no. transfer/bukti bayar
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['tahun', 'bulan']);
            $table->index(['dosen_id', 'tahun', 'bulan']);
            $table->index(['pegawai_id', 'tahun', 'bulan']);
        });

        // Detail Slip Gaji (komponen-komponen)
        Schema::create('slip_gaji_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slip_gaji_id')->constrained('slip_gaji')->cascadeOnDelete();
            $table->foreignId('komponen_gaji_id')->nullable()->constrained('komponen_gaji')->nullOnDelete();
            $table->string('nama_komponen'); // simpan nama untuk historis
            $table->enum('jenis', ['pendapatan', 'potongan']);
            $table->decimal('nilai', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->index(['slip_gaji_id', 'jenis']);
        });

        // Pengaturan Gaji per Pegawai (untuk override komponen default)
        Schema::create('pengaturan_gaji', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->cascadeOnDelete();
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->boolean('aktif')->default(true);
            $table->date('berlaku_mulai')->nullable();
            $table->date('berlaku_sampai')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->index(['dosen_id']);
            $table->index(['pegawai_id']);
        });

        // Detail Pengaturan Gaji (komponen per pegawai)
        Schema::create('pengaturan_gaji_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengaturan_gaji_id')->constrained('pengaturan_gaji')->cascadeOnDelete();
            $table->foreignId('komponen_gaji_id')->constrained('komponen_gaji')->cascadeOnDelete();
            $table->decimal('nilai', 15, 2)->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_gaji_detail');
        Schema::dropIfExists('pengaturan_gaji');
        Schema::dropIfExists('slip_gaji_detail');
        Schema::dropIfExists('slip_gaji');
        Schema::dropIfExists('komponen_gaji');
    }
};
