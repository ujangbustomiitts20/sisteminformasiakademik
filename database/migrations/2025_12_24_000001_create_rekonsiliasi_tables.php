<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Akun Bank
        Schema::create('akun_bank', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bank');
            $table->string('nomor_rekening');
            $table->string('nama_rekening');
            $table->string('cabang')->nullable();
            $table->string('kode_bank')->nullable();
            $table->enum('tipe', ['penampungan', 'operasional', 'beasiswa'])->default('penampungan');
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->decimal('saldo_sistem', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel Mutasi Bank (Import dari Bank Statement)
        Schema::create('mutasi_bank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('akun_bank_id')->constrained('akun_bank')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('nomor_referensi')->nullable();
            $table->enum('tipe', ['kredit', 'debit']); // kredit = masuk, debit = keluar
            $table->decimal('nominal', 15, 2);
            $table->decimal('saldo', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('nama_pengirim')->nullable();
            $table->enum('status', ['pending', 'matched', 'unmatched', 'manual'])->default('pending');
            $table->foreignId('transaksi_pembayaran_id')->nullable()->constrained('transaksi_pembayaran')->onDelete('set null');
            $table->foreignId('matched_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('matched_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->index(['akun_bank_id', 'tanggal']);
            $table->index('status');
        });

        // Tabel Rekonsiliasi (Session rekonsiliasi)
        Schema::create('rekonsiliasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('akun_bank_id')->constrained('akun_bank')->onDelete('cascade');
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->decimal('saldo_awal_bank', 15, 2);
            $table->decimal('saldo_akhir_bank', 15, 2);
            $table->decimal('saldo_sistem', 15, 2);
            $table->decimal('selisih', 15, 2)->default(0);
            $table->integer('total_mutasi')->default(0);
            $table->integer('total_matched')->default(0);
            $table->integer('total_unmatched')->default(0);
            $table->enum('status', ['draft', 'in_progress', 'completed', 'approved'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Tabel Detail Rekonsiliasi
        Schema::create('detail_rekonsiliasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekonsiliasi_id')->constrained('rekonsiliasi')->onDelete('cascade');
            $table->foreignId('mutasi_bank_id')->constrained('mutasi_bank')->onDelete('cascade');
            $table->foreignId('transaksi_pembayaran_id')->nullable()->constrained('transaksi_pembayaran')->onDelete('set null');
            $table->enum('status', ['matched', 'unmatched', 'manual', 'ignored'])->default('unmatched');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_rekonsiliasi');
        Schema::dropIfExists('rekonsiliasi');
        Schema::dropIfExists('mutasi_bank');
        Schema::dropIfExists('akun_bank');
    }
};
