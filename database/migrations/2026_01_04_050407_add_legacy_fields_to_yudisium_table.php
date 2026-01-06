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
        Schema::table('yudisium', function (Blueprint $table) {
            // Legacy fields from kelulusan_mhs
            $table->string('no_sk_yudisium')->nullable()->after('no_yudisium'); // NOSKYUDISIUM
            $table->date('tanggal_sk_yudisium')->nullable()->after('no_sk_yudisium'); // TANGGALSKYUDISIUM
            $table->string('no_sk_rektor')->nullable()->after('no_transkrip'); // NOSKREKTOR
            $table->date('tanggal_sk_rektor')->nullable()->after('no_sk_rektor'); // TANGGALSKREKTOR
            $table->integer('no_blanko')->nullable()->after('tanggal_sk_rektor'); // NOBLANKO
            $table->string('no_pin', 100)->nullable()->after('no_blanko'); // NOPIN
            $table->string('no_nirl', 100)->nullable()->after('no_pin'); // NONIRL
            $table->string('url_pddikti', 500)->nullable()->after('no_nirl'); // URLPDDIKTI
            $table->string('feeder_aktivitas')->nullable()->after('url_pddikti'); // FEEDERAKTIVITAS
            $table->char('status_keluar', 1)->nullable()->after('status'); // STATUSKELUAR (L=Lulus, D=DO, etc)
            $table->string('tahun_semester', 10)->nullable()->after('status_keluar'); // TAHUNSEMESTER
            $table->decimal('nilai_kompre', 5, 2)->nullable()->after('ipk_akhir'); // NILAIKOMPRE
            $table->decimal('nilai_uap_tulis', 5, 2)->nullable()->after('nilai_kompre'); // NILAIUAPTULIS
            $table->decimal('nilai_uap_praktek', 5, 2)->nullable()->after('nilai_uap_tulis'); // NILAIUAPPRAKTEK
            $table->string('simbol_uap_tulis', 5)->nullable()->after('nilai_uap_praktek'); // SIMBOLUAPTULIS
            $table->string('simbol_uap_praktek', 5)->nullable()->after('simbol_uap_tulis'); // SIMBOLUAPPRAKTEK
            $table->string('peminatan', 50)->nullable()->after('simbol_uap_praktek'); // PEMINATAN
            $table->string('legacy_id', 50)->nullable()->after('catatan'); // Original IDMAHASISWA for tracking
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('yudisium', function (Blueprint $table) {
            $table->dropColumn([
                'no_sk_yudisium', 'tanggal_sk_yudisium', 'no_sk_rektor', 'tanggal_sk_rektor',
                'no_blanko', 'no_pin', 'no_nirl', 'url_pddikti', 'feeder_aktivitas',
                'status_keluar', 'tahun_semester', 'nilai_kompre', 'nilai_uap_tulis',
                'nilai_uap_praktek', 'simbol_uap_tulis', 'simbol_uap_praktek', 'peminatan', 'legacy_id'
            ]);
        });
    }
};
