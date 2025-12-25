<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Tarif - Pengaturan biaya per prodi/angkatan
        Schema::create('tarif', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_studi_id')->nullable()->constrained('program_studi')->onDelete('cascade');
            $table->string('angkatan', 4)->nullable(); // tahun angkatan, null = semua
            $table->enum('jenis', ['SPP', 'Herregistrasi', 'Praktikum', 'SKS', 'Wisuda', 'Almamater', 'KKN', 'PKL', 'Lainnya']);
            $table->string('nama_tarif');
            $table->decimal('nominal', 15, 2);
            $table->enum('periode', ['Semester', 'Tahunan', 'Sekali'])->default('Semester');
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Tagihan - Detail tagihan per mahasiswa
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->string('no_tagihan')->unique();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->foreignId('tarif_id')->nullable()->constrained('tarif')->onDelete('set null');
            $table->string('jenis_tagihan'); // SPP, Herregistrasi, dll
            $table->string('keterangan_tagihan')->nullable();
            $table->decimal('nominal', 15, 2);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('denda', 15, 2)->default(0);
            $table->decimal('total_bayar', 15, 2); // nominal - diskon + denda
            $table->decimal('jumlah_dibayar', 15, 2)->default(0);
            $table->decimal('sisa_tagihan', 15, 2);
            $table->date('tanggal_jatuh_tempo');
            $table->enum('status', ['Belum Bayar', 'Cicilan', 'Lunas', 'Batal'])->default('Belum Bayar');
            $table->timestamps();
        });

        // 3. Tabel Transaksi Pembayaran - History pembayaran
        Schema::create('transaksi_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi')->unique();
            $table->foreignId('tagihan_id')->constrained('tagihan')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal_bayar');
            $table->enum('metode_pembayaran', ['Tunai', 'Transfer Bank', 'Virtual Account', 'QRIS', 'Kartu Kredit', 'Lainnya'])->default('Transfer Bank');
            $table->string('bank')->nullable();
            $table->string('no_referensi')->nullable(); // no rekening/VA/ref
            $table->string('bukti_bayar')->nullable(); // file upload
            $table->enum('status', ['Pending', 'Verified', 'Rejected'])->default('Pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 4. Tabel Beasiswa/Potongan
        Schema::create('beasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->enum('jenis', ['Beasiswa', 'Potongan', 'Keringanan']);
            $table->enum('tipe_potongan', ['Persen', 'Nominal'])->default('Persen');
            $table->decimal('nilai_potongan', 15, 2); // persen atau nominal
            $table->string('sumber_dana')->nullable(); // internal, pemerintah, swasta
            $table->integer('kuota')->nullable();
            $table->text('persyaratan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Tabel Penerima Beasiswa
        Schema::create('penerima_beasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beasiswa_id')->constrained('beasiswa')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->enum('status', ['Diajukan', 'Disetujui', 'Ditolak', 'Dicabut'])->default('Diajukan');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->unique(['beasiswa_id', 'mahasiswa_id', 'tahun_akademik_id'], 'unique_beasiswa_mhs_ta');
        });

        // 6. Tabel Pengaturan Denda
        Schema::create('pengaturan_denda', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('tipe', ['Persen', 'Nominal'])->default('Persen');
            $table->decimal('nilai', 15, 2); // nilai denda per hari/periode
            $table->enum('periode', ['Harian', 'Mingguan', 'Bulanan'])->default('Harian');
            $table->decimal('maksimal_denda', 15, 2)->nullable(); // batas maksimal denda
            $table->integer('grace_period')->default(0); // hari toleransi sebelum denda
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 7. Tabel Virtual Account
        Schema::create('virtual_account', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->string('bank_code'); // BCA, BNI, BRI, Mandiri, dll
            $table->string('va_number')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Update tabel pembayaran lama jika perlu (add foreign key to tagihan)
        $columns = DB::select("SHOW COLUMNS FROM pembayaran LIKE 'tagihan_id'");
        if (empty($columns)) {
            DB::statement("ALTER TABLE pembayaran ADD COLUMN tagihan_id BIGINT UNSIGNED NULL AFTER id");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove column from pembayaran
        $columns = DB::select("SHOW COLUMNS FROM pembayaran LIKE 'tagihan_id'");
        if (!empty($columns)) {
            DB::statement("ALTER TABLE pembayaran DROP COLUMN tagihan_id");
        }

        Schema::dropIfExists('virtual_account');
        Schema::dropIfExists('pengaturan_denda');
        Schema::dropIfExists('penerima_beasiswa');
        Schema::dropIfExists('beasiswa');
        Schema::dropIfExists('transaksi_pembayaran');
        Schema::dropIfExists('tagihan');
        Schema::dropIfExists('tarif');
    }
};
