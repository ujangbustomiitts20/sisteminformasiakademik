<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AkunBank;
use App\Models\MutasiBank;
use App\Models\Rekonsiliasi;
use App\Models\DetailRekonsiliasi;
use App\Models\TransaksiPembayaran;
use Carbon\Carbon;

class RekonsiliasiBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Bank Accounts
        $akunPenampungan = AkunBank::create([
            'nama_bank' => 'Bank Mandiri',
            'kode_bank' => '008',
            'nomor_rekening' => '1234567890',
            'nama_rekening' => 'Universitas Contoh - VA',
            'cabang' => 'KCP Kampus',
            'tipe' => 'penampungan',
            'saldo_awal' => 100000000,
            'saldo_sistem' => 100000000,
            'is_active' => true,
        ]);

        $akunOperasional = AkunBank::create([
            'nama_bank' => 'Bank BCA',
            'kode_bank' => '014',
            'nomor_rekening' => '0987654321',
            'nama_rekening' => 'Universitas Contoh - Operasional',
            'cabang' => 'KCP Sudirman',
            'tipe' => 'operasional',
            'saldo_awal' => 50000000,
            'saldo_sistem' => 50000000,
            'is_active' => true,
        ]);

        $akunBeasiswa = AkunBank::create([
            'nama_bank' => 'Bank BNI',
            'kode_bank' => '009',
            'nomor_rekening' => '5678901234',
            'nama_rekening' => 'Universitas Contoh - Beasiswa',
            'cabang' => 'KCU Jakarta',
            'tipe' => 'beasiswa',
            'saldo_awal' => 200000000,
            'saldo_sistem' => 200000000,
            'is_active' => true,
        ]);

        $this->command->info('Akun Bank berhasil dibuat');

        // Get verified transactions
        $transaksiList = TransaksiPembayaran::where('status', 'verified')
            ->whereMonth('tanggal_bayar', Carbon::now()->month)
            ->get();

        // Create sample mutasi for penampungan account
        $saldoAkumulasi = $akunPenampungan->saldo_awal;
        $mutasiData = [];

        foreach ($transaksiList->take(10) as $index => $transaksi) {
            $saldoAkumulasi += $transaksi->jumlah;
            
            $mutasi = MutasiBank::create([
                'akun_bank_id' => $akunPenampungan->id,
                'tanggal' => $transaksi->tanggal_bayar,
                'tipe' => 'kredit',
                'nominal' => $transaksi->jumlah,
                'saldo' => $saldoAkumulasi,
                'keterangan' => 'VA ' . ($transaksi->tagihan->mahasiswa->nim ?? ''),
                'nomor_referensi' => 'TRF' . str_pad($index + 1, 8, '0', STR_PAD_LEFT),
                'status' => 'matched',
                'transaksi_pembayaran_id' => $transaksi->id,
                'matched_at' => now(),
            ]);
            
            $mutasiData[] = $mutasi;
        }

        // Create some pending mutasi (not matched yet)
        $pendingMutasi = [];
        for ($i = 0; $i < 5; $i++) {
            $nominal = rand(5, 20) * 100000; // Random 500k - 2M
            $saldoAkumulasi += $nominal;
            
            $pendingMutasi[] = MutasiBank::create([
                'akun_bank_id' => $akunPenampungan->id,
                'tanggal' => Carbon::now()->subDays(rand(1, 15)),
                'tipe' => 'kredit',
                'nominal' => $nominal,
                'saldo' => $saldoAkumulasi,
                'keterangan' => 'Transfer dari ' . $this->randomName(),
                'nomor_referensi' => 'TRF' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                'status' => 'pending',
            ]);
        }

        // Create some debit mutasi (outgoing)
        for ($i = 0; $i < 3; $i++) {
            $nominal = rand(1, 5) * 1000000; // Random 1M - 5M
            $saldoAkumulasi -= $nominal;
            
            MutasiBank::create([
                'akun_bank_id' => $akunPenampungan->id,
                'tanggal' => Carbon::now()->subDays(rand(1, 15)),
                'tipe' => 'debit',
                'nominal' => $nominal,
                'saldo' => $saldoAkumulasi,
                'keterangan' => 'Transfer ke ' . $akunOperasional->nomor_rekening,
                'nomor_referensi' => 'DB' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                'status' => 'manual',
                'catatan' => 'Transfer operasional',
            ]);
        }

        // Update saldo sistem
        $akunPenampungan->updateSaldoSistem();

        $this->command->info('Mutasi Bank berhasil dibuat');

        // Create sample rekonsiliasi
        $rekonsiliasi = Rekonsiliasi::create([
            'akun_bank_id' => $akunPenampungan->id,
            'nomor_rekonsiliasi' => 'RKN-' . date('Ym') . '-0001',
            'periode_awal' => Carbon::now()->startOfMonth(),
            'periode_akhir' => Carbon::now()->endOfMonth(),
            'saldo_awal_bank' => $akunPenampungan->saldo_awal,
            'saldo_akhir_bank' => $saldoAkumulasi,
            'saldo_sistem' => $akunPenampungan->saldo_sistem,
            'total_mutasi' => count($mutasiData) + count($pendingMutasi) + 3,
            'total_matched' => count($mutasiData),
            'total_unmatched' => count($pendingMutasi) + 3,
            'selisih' => $saldoAkumulasi - $akunPenampungan->saldo_sistem,
            'status' => 'in_progress',
            'created_by' => 1,
        ]);

        // Create detail rekonsiliasi for matched items
        foreach ($mutasiData as $mutasi) {
            DetailRekonsiliasi::create([
                'rekonsiliasi_id' => $rekonsiliasi->id,
                'mutasi_bank_id' => $mutasi->id,
                'transaksi_pembayaran_id' => $mutasi->transaksi_pembayaran_id,
                'status' => 'matched',
                'matched_at' => now(),
            ]);
        }

        // Create detail for pending items
        foreach ($pendingMutasi as $mutasi) {
            DetailRekonsiliasi::create([
                'rekonsiliasi_id' => $rekonsiliasi->id,
                'mutasi_bank_id' => $mutasi->id,
                'status' => 'unmatched', // pending tidak ada di enum
            ]);
        }

        // Update rekonsiliasi statistik
        $rekonsiliasi->updateStatistik();

        $this->command->info('Rekonsiliasi berhasil dibuat');
        $this->command->info('');
        $this->command->info('Summary:');
        $this->command->info('- 3 Akun Bank dibuat');
        $this->command->info('- ' . MutasiBank::count() . ' Mutasi Bank dibuat');
        $this->command->info('- 1 Rekonsiliasi dibuat dengan status In Progress');
    }

    private function randomName(): string
    {
        $names = [
            'AHMAD RIZKI',
            'SITI NURHALIZA',
            'BUDI SANTOSO',
            'DEWI LESTARI',
            'FIRMAN HIDAYAT',
            'INDAH PERMATA',
            'JOKO WIDODO',
            'KARTINI PUTRI',
            'LUKMAN HAKIM',
            'MAYA SARI',
        ];
        
        return $names[array_rand($names)];
    }
}
