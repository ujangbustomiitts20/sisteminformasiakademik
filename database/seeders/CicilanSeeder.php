<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SkemaCicilan;
use App\Models\Cicilan;
use App\Models\DetailCicilan;
use App\Models\Tagihan;
use App\Models\NotifikasiKeuangan;
use Carbon\Carbon;

class CicilanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Skema Cicilan
        $skemaList = [
            [
                'nama' => 'Cicilan 3 Bulan',
                'jumlah_cicilan' => 3,
                'biaya_admin' => 50000,
                'persentase_bunga' => 0,
                'minimal_tagihan' => 1000000,
                'interval_hari' => 30,
                'keterangan' => 'Cicilan 3x tanpa bunga, cocok untuk tagihan ringan',
                'is_active' => true,
            ],
            [
                'nama' => 'Cicilan 6 Bulan',
                'jumlah_cicilan' => 6,
                'biaya_admin' => 75000,
                'persentase_bunga' => 0.5,
                'minimal_tagihan' => 3000000,
                'interval_hari' => 30,
                'keterangan' => 'Cicilan 6x dengan bunga 0.5% per cicilan',
                'is_active' => true,
            ],
            [
                'nama' => 'Cicilan 12 Bulan',
                'jumlah_cicilan' => 12,
                'biaya_admin' => 100000,
                'persentase_bunga' => 0.75,
                'minimal_tagihan' => 5000000,
                'interval_hari' => 30,
                'keterangan' => 'Cicilan 12x untuk tagihan besar, bunga 0.75% per cicilan',
                'is_active' => true,
            ],
            [
                'nama' => 'Cicilan Express 2x',
                'jumlah_cicilan' => 2,
                'biaya_admin' => 25000,
                'persentase_bunga' => 0,
                'minimal_tagihan' => 500000,
                'interval_hari' => 14,
                'keterangan' => 'Cicilan cepat 2x bayar dalam 1 bulan',
                'is_active' => true,
            ],
        ];

        foreach ($skemaList as $skema) {
            SkemaCicilan::create($skema);
        }

        $this->command->info('✓ 4 Skema Cicilan berhasil dibuat');

        // 2. Buat Cicilan untuk beberapa tagihan
        $tagihanBelumBayar = Tagihan::where('status', 'Belum Bayar')
            ->where('sisa_tagihan', '>=', 1000000)
            ->inRandomOrder()
            ->take(10)
            ->get();

        if ($tagihanBelumBayar->isEmpty()) {
            $this->command->warn('Tidak ada tagihan yang memenuhi syarat untuk cicilan');
            return;
        }

        $skemaCicilan = SkemaCicilan::where('is_active', true)->get();
        $cicilanCount = 0;
        $detailCount = 0;

        foreach ($tagihanBelumBayar as $index => $tagihan) {
            // Pilih skema yang sesuai dengan nominal
            $skema = $skemaCicilan->filter(fn($s) => $tagihan->sisa_tagihan >= $s->minimal_tagihan)->random();
            
            if (!$skema) continue;

            $sisaTagihan = $tagihan->sisa_tagihan;
            $biayaAdmin = $skema->biaya_admin;
            $totalBunga = $sisaTagihan * ($skema->persentase_bunga / 100) * $skema->jumlah_cicilan;
            $totalHarusDibayar = $sisaTagihan + $biayaAdmin + $totalBunga;
            $nominalPerCicilan = ceil($totalHarusDibayar / $skema->jumlah_cicilan);

            // Random tanggal mulai (1-3 bulan lalu)
            $tanggalMulai = Carbon::now()->subMonths(rand(1, 3))->addDays(rand(1, 15));

            $cicilan = Cicilan::create([
                'tagihan_id' => $tagihan->id,
                'skema_cicilan_id' => $skema->id,
                'total_tagihan_awal' => $sisaTagihan,
                'biaya_admin' => $biayaAdmin,
                'total_bunga' => $totalBunga,
                'total_harus_dibayar' => $totalHarusDibayar,
                'nominal_per_cicilan' => $nominalPerCicilan,
                'jumlah_cicilan' => $skema->jumlah_cicilan,
                'cicilan_terbayar' => 0,
                'status' => 'Aktif',
                'tanggal_mulai' => $tanggalMulai,
            ]);

            $cicilanCount++;

            // Buat detail cicilan
            $tanggalJatuhTempo = $tanggalMulai->copy();
            $cicilanTerbayar = 0;

            for ($i = 1; $i <= $skema->jumlah_cicilan; $i++) {
                $status = 'Belum Bayar';
                $tanggalBayar = null;
                $denda = 0;

                // Simulasi pembayaran untuk cicilan yang sudah lewat
                if ($tanggalJatuhTempo < now()) {
                    // 70% kemungkinan sudah bayar jika sudah lewat jatuh tempo
                    if (rand(1, 100) <= 70) {
                        $status = 'Dibayar';
                        $tanggalBayar = $tanggalJatuhTempo->copy()->addDays(rand(0, 5));
                        $cicilanTerbayar++;
                        
                        // Jika bayar telat
                        if ($tanggalBayar > $tanggalJatuhTempo) {
                            $status = 'Dibayar Terlambat';
                            $hariTerlambat = $tanggalJatuhTempo->diffInDays($tanggalBayar);
                            $denda = min($hariTerlambat * 10000, 100000); // Max denda 100rb
                        }
                    } else {
                        // Masih belum bayar tapi sudah lewat = terlambat
                        $status = 'Belum Bayar'; // akan terdeteksi terlambat di view
                    }
                }

                DetailCicilan::create([
                    'cicilan_id' => $cicilan->id,
                    'cicilan_ke' => $i,
                    'nominal' => $nominalPerCicilan,
                    'jatuh_tempo' => $tanggalJatuhTempo->copy(),
                    'tanggal_bayar' => $tanggalBayar,
                    'denda' => $denda,
                    'status' => $status,
                ]);

                $detailCount++;
                $tanggalJatuhTempo->addDays($skema->interval_hari);
            }

            // Update cicilan
            $cicilan->cicilan_terbayar = $cicilanTerbayar;
            if ($cicilanTerbayar >= $skema->jumlah_cicilan) {
                $cicilan->status = 'Lunas';
                $cicilan->tanggal_selesai = now();
            }
            $cicilan->save();

            // Update tagihan status
            $tagihan->status = 'Cicilan';
            $tagihan->save();

            // Buat notifikasi
            NotifikasiKeuangan::create([
                'mahasiswa_id' => $tagihan->mahasiswa_id,
                'jenis' => 'reminder',
                'judul' => 'Cicilan Aktif',
                'pesan' => "Cicilan {$skema->nama} untuk {$tagihan->jenis_tagihan} telah aktif. Total {$skema->jumlah_cicilan}x cicilan @ Rp " . number_format($nominalPerCicilan, 0, ',', '.'),
                'reference_type' => 'cicilan',
                'reference_id' => $cicilan->id,
                'channel' => 'database',
                'status' => 'sent',
                'sent_at' => $tanggalMulai,
            ]);
        }

        $this->command->info("✓ {$cicilanCount} Cicilan berhasil dibuat");
        $this->command->info("✓ {$detailCount} Detail Cicilan berhasil dibuat");

        // 3. Buat notifikasi reminder untuk cicilan yang akan jatuh tempo
        $detailAkanJatuhTempo = DetailCicilan::where('status', 'Belum Bayar')
            ->whereDate('jatuh_tempo', '>=', now())
            ->whereDate('jatuh_tempo', '<=', now()->addDays(7))
            ->with('cicilan.tagihan')
            ->get();

        $notifCount = 0;
        foreach ($detailAkanJatuhTempo as $detail) {
            NotifikasiKeuangan::create([
                'mahasiswa_id' => $detail->cicilan->tagihan->mahasiswa_id,
                'jenis' => 'cicilan_jatuh_tempo',
                'judul' => 'Cicilan Akan Jatuh Tempo',
                'pesan' => "Cicilan ke-{$detail->cicilan_ke} sebesar Rp " . number_format($detail->nominal, 0, ',', '.') . " akan jatuh tempo pada " . $detail->jatuh_tempo->format('d M Y'),
                'reference_type' => 'detail_cicilan',
                'reference_id' => $detail->id,
                'channel' => 'database',
                'status' => 'pending',
            ]);
            $notifCount++;
        }

        $this->command->info("✓ {$notifCount} Notifikasi reminder berhasil dibuat");

        // Summary
        $this->command->newLine();
        $this->command->info('=== RINGKASAN DATA CICILAN ===');
        $this->command->info('Total Skema Cicilan: ' . SkemaCicilan::count());
        $this->command->info('Total Cicilan Aktif: ' . Cicilan::where('status', 'Aktif')->count());
        $this->command->info('Total Cicilan Lunas: ' . Cicilan::where('status', 'Lunas')->count());
        $this->command->info('Total Piutang Cicilan: Rp ' . number_format(DetailCicilan::belumBayar()->sum('nominal'), 0, ',', '.'));
    }
}
