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
        Schema::create('refund', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_refund')->unique();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('tagihan_id')->nullable()->constrained('tagihan')->onDelete('set null');
            $table->foreignId('transaksi_pembayaran_id')->nullable()->constrained('transaksi_pembayaran')->onDelete('set null');
            $table->string('jenis'); // kelebihan_bayar, pembatalan, cuti, do, pindah, lainnya
            $table->decimal('jumlah_pengajuan', 15, 2);
            $table->decimal('jumlah_disetujui', 15, 2)->nullable();
            $table->text('alasan');
            $table->string('metode_refund'); // transfer, tunai, potong_tagihan
            $table->string('nama_bank')->nullable();
            $table->string('nomor_rekening')->nullable();
            $table->string('nama_pemilik_rekening')->nullable();
            $table->string('dokumen_pendukung')->nullable();
            $table->string('status')->default('pending'); // pending, diproses, disetujui, ditolak, selesai, dibatalkan
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('diproses_at')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('disetujui_at')->nullable();
            $table->foreignId('diselesaikan_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('diselesaikan_at')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->string('bukti_refund')->nullable();
            $table->string('nomor_referensi_refund')->nullable();
            $table->timestamp('tanggal_refund')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('status');
            $table->index('jenis');
            $table->index('created_at');
        });

        Schema::create('refund_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('refund_id')->constrained('refund')->onDelete('cascade');
            $table->string('status_lama')->nullable();
            $table->string('status_baru');
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('refund_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund_history');
        Schema::dropIfExists('refund');
    }
};
