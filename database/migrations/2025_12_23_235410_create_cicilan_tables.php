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
        // Tabel Skema Cicilan
        Schema::create('skema_cicilan', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Contoh: "Cicilan 3x", "Cicilan 6x"
            $table->integer('jumlah_cicilan'); // 3, 6, 12
            $table->decimal('biaya_admin', 15, 2)->default(0); // Biaya admin flat
            $table->decimal('persentase_bunga', 5, 2)->default(0); // Persentase bunga per cicilan
            $table->decimal('minimal_tagihan', 15, 2)->default(0); // Minimal tagihan untuk bisa cicilan
            $table->integer('interval_hari')->default(30); // Jarak antar cicilan (hari)
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel Cicilan (Detail cicilan per tagihan)
        Schema::create('cicilan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihan')->onDelete('cascade');
            $table->foreignId('skema_cicilan_id')->constrained('skema_cicilan')->onDelete('restrict');
            $table->decimal('total_tagihan_awal', 15, 2); // Total tagihan sebelum cicilan
            $table->decimal('biaya_admin', 15, 2)->default(0);
            $table->decimal('total_bunga', 15, 2)->default(0);
            $table->decimal('total_harus_dibayar', 15, 2); // Total tagihan + admin + bunga
            $table->decimal('nominal_per_cicilan', 15, 2);
            $table->integer('jumlah_cicilan');
            $table->integer('cicilan_terbayar')->default(0);
            $table->enum('status', ['Aktif', 'Lunas', 'Gagal', 'Batal'])->default('Aktif');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->timestamps();
        });

        // Tabel Detail Cicilan (Jadwal pembayaran per cicilan)
        Schema::create('detail_cicilan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cicilan_id')->constrained('cicilan')->onDelete('cascade');
            $table->integer('cicilan_ke');
            $table->decimal('nominal', 15, 2);
            $table->date('jatuh_tempo');
            $table->date('tanggal_bayar')->nullable();
            $table->foreignId('transaksi_pembayaran_id')->nullable()->constrained('transaksi_pembayaran')->onDelete('set null');
            $table->decimal('denda', 15, 2)->default(0);
            $table->enum('status', ['Belum Bayar', 'Dibayar', 'Terlambat', 'Dibayar Terlambat'])->default('Belum Bayar');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Tabel Notifikasi Keuangan (untuk reminder)
        Schema::create('notifikasi_keuangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->string('jenis'); // tagihan_jatuh_tempo, cicilan_jatuh_tempo, denda, reminder
            $table->string('judul');
            $table->text('pesan');
            $table->string('reference_type')->nullable(); // tagihan, cicilan, detail_cicilan
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->enum('channel', ['database', 'email', 'whatsapp'])->default('database');
            $table->enum('status', ['pending', 'sent', 'failed', 'read'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['mahasiswa_id', 'status']);
            $table->index(['jenis', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi_keuangan');
        Schema::dropIfExists('detail_cicilan');
        Schema::dropIfExists('cicilan');
        Schema::dropIfExists('skema_cicilan');
    }
};
