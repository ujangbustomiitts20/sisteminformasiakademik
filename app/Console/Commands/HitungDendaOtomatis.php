<?php

namespace App\Console\Commands;

use App\Models\Tagihan;
use App\Models\DetailCicilan;
use App\Models\PengaturanDenda;
use App\Models\NotifikasiKeuangan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HitungDendaOtomatis extends Command
{
    protected $signature = 'keuangan:hitung-denda {--force : Force update semua denda}';
    protected $description = 'Menghitung dan update denda otomatis untuk tagihan yang jatuh tempo';

    public function handle()
    {
        $this->info('Memulai perhitungan denda otomatis...');

        // 1. Hitung denda untuk Tagihan
        $this->hitungDendaTagihan();

        // 2. Hitung denda untuk Cicilan
        $this->hitungDendaCicilan();

        $this->info('Selesai!');
        return Command::SUCCESS;
    }

    private function hitungDendaTagihan()
    {
        $pengaturanDenda = PengaturanDenda::where('is_active', true)->get()->keyBy('jenis_tagihan');

        if ($pengaturanDenda->isEmpty()) {
            $this->warn('Tidak ada pengaturan denda aktif');
            return;
        }

        // Ambil tagihan yang overdue
        $tagihanOverdue = Tagihan::with('mahasiswa')
            ->whereIn('status', ['Belum Bayar', 'Cicilan'])
            ->where('tanggal_jatuh_tempo', '<', now())
            ->get();

        $this->info("Ditemukan {$tagihanOverdue->count()} tagihan overdue");

        $updated = 0;
        $notifCreated = 0;

        foreach ($tagihanOverdue as $tagihan) {
            $setting = $pengaturanDenda->get($tagihan->jenis_tagihan);
            
            // Jika tidak ada setting spesifik, cari yang umum
            if (!$setting) {
                $setting = $pengaturanDenda->first();
            }

            if (!$setting) continue;

            $hariTerlambat = now()->diffInDays($tagihan->tanggal_jatuh_tempo);
            
            // Kurangi grace period
            $hariEfektif = $hariTerlambat - ($setting->grace_period ?? 0);
            if ($hariEfektif <= 0) continue;

            // Hitung multiplier berdasarkan periode
            $multiplier = match($setting->periode ?? 'Harian') {
                'Mingguan' => ceil($hariEfektif / 7),
                'Bulanan' => ceil($hariEfektif / 30),
                default => $hariEfektif,
            };

            // Hitung denda
            if (($setting->tipe_denda ?? $setting->tipe) === 'persentase' || ($setting->tipe_denda ?? $setting->tipe) === 'Persen') {
                $denda = ($tagihan->nominal * ($setting->nilai_denda ?? $setting->nilai) / 100) * $multiplier;
            } else {
                $denda = ($setting->nilai_denda ?? $setting->nilai) * $multiplier;
            }

            // Apply max limit
            $maxDenda = $setting->maksimal_denda ?? null;
            if ($maxDenda && $denda > $maxDenda) {
                $denda = $maxDenda;
            }

            // Update jika denda berubah
            if ($tagihan->denda != $denda) {
                $oldDenda = $tagihan->denda;
                $tagihan->denda = $denda;
                $tagihan->save();
                $updated++;

                // Buat notifikasi jika denda baru atau bertambah signifikan
                if ($denda > $oldDenda && ($denda - $oldDenda) >= 10000) {
                    NotifikasiKeuangan::create([
                        'mahasiswa_id' => $tagihan->mahasiswa_id,
                        'jenis' => 'denda',
                        'judul' => 'Denda Keterlambatan',
                        'pesan' => "Tagihan {$tagihan->jenis_tagihan} Anda telah dikenakan denda sebesar Rp " . number_format($denda, 0, ',', '.') . " karena keterlambatan {$hariTerlambat} hari.",
                        'reference_type' => 'tagihan',
                        'reference_id' => $tagihan->id,
                        'channel' => 'database',
                        'status' => 'pending',
                    ]);
                    $notifCreated++;
                }

                $this->line("  - {$tagihan->mahasiswa->nim}: Denda diupdate menjadi Rp " . number_format($denda, 0, ',', '.'));
            }
        }

        $this->info("✓ {$updated} tagihan denda diupdate, {$notifCreated} notifikasi dibuat");
    }

    private function hitungDendaCicilan()
    {
        // Ambil detail cicilan yang overdue
        $cicilanOverdue = DetailCicilan::with(['cicilan.tagihan.mahasiswa'])
            ->where('status', 'Belum Bayar')
            ->where('jatuh_tempo', '<', now())
            ->get();

        $this->info("Ditemukan {$cicilanOverdue->count()} cicilan overdue");

        $updated = 0;

        foreach ($cicilanOverdue as $detail) {
            $hariTerlambat = now()->diffInDays($detail->jatuh_tempo);
            
            // Denda cicilan: Rp 10.000 per hari, max Rp 200.000
            $denda = min($hariTerlambat * 10000, 200000);

            if ($detail->denda != $denda) {
                $detail->denda = $denda;
                $detail->save();
                $updated++;
            }
        }

        $this->info("✓ {$updated} cicilan denda diupdate");
    }
}
