<?php

namespace Database\Seeders;

use App\Models\NotifikasiKeuangan;
use App\Models\Tagihan;
use App\Models\Cicilan;
use App\Models\DetailCicilan;
use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class NotifikasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing notifications
        NotifikasiKeuangan::truncate();

        $now = Carbon::now();
        $notifikasi = [];

        // Get all mahasiswa with tagihan
        $tagihans = Tagihan::with('mahasiswa')
            ->whereIn('status', ['belum_bayar', 'sebagian'])
            ->limit(20)
            ->get();

        foreach ($tagihans as $tagihan) {
            if (!$tagihan->mahasiswa) continue;

            $jatuhTempo = Carbon::parse($tagihan->jatuh_tempo);
            $sisaTagihan = $tagihan->sisa_tagihan ?? ($tagihan->jumlah_tagihan - $tagihan->jumlah_dibayar);

            // Reminder H-7
            if ($jatuhTempo->isFuture() && $jatuhTempo->diffInDays($now) <= 7) {
                $notifikasi[] = [
                    'mahasiswa_id' => $tagihan->mahasiswa_id,
                    'jenis' => NotifikasiKeuangan::JENIS_REMINDER,
                    'judul' => 'Reminder: Tagihan Akan Jatuh Tempo',
                    'pesan' => "Tagihan {$tagihan->jenis_tagihan} sebesar Rp " . number_format($sisaTagihan, 0, ',', '.') . " akan jatuh tempo pada " . $jatuhTempo->format('d/m/Y') . ". Mohon segera lakukan pembayaran.",
                    'reference_type' => Tagihan::class,
                    'reference_id' => $tagihan->id,
                    'channel' => rand(0, 1) ? 'email' : 'database',
                    'status' => rand(0, 1) ? 'sent' : 'read',
                    'sent_at' => $now->copy()->subHours(rand(1, 48)),
                    'read_at' => rand(0, 1) ? $now->copy()->subHours(rand(1, 24)) : null,
                    'created_at' => $now->copy()->subDays(rand(1, 5)),
                    'updated_at' => $now,
                ];
            }

            // Overdue notification
            if ($jatuhTempo->isPast()) {
                $hariTerlambat = $now->diffInDays($jatuhTempo);
                
                $notifikasi[] = [
                    'mahasiswa_id' => $tagihan->mahasiswa_id,
                    'jenis' => NotifikasiKeuangan::JENIS_TAGIHAN_JATUH_TEMPO,
                    'judul' => 'PENTING: Tagihan Melewati Jatuh Tempo',
                    'pesan' => "Tagihan {$tagihan->jenis_tagihan} telah melewati jatuh tempo selama {$hariTerlambat} hari. Total yang harus dibayar: Rp " . number_format($sisaTagihan + ($tagihan->denda ?? 0), 0, ',', '.') . ". Mohon segera lakukan pembayaran untuk menghindari denda tambahan.",
                    'reference_type' => Tagihan::class,
                    'reference_id' => $tagihan->id,
                    'channel' => 'email',
                    'status' => 'sent',
                    'sent_at' => $jatuhTempo->copy()->addDays(1),
                    'read_at' => rand(0, 1) ? $now->copy()->subHours(rand(1, 12)) : null,
                    'created_at' => $jatuhTempo->copy()->addDays(1),
                    'updated_at' => $now,
                ];

                // Denda notification
                if ($tagihan->denda && $tagihan->denda > 0) {
                    $notifikasi[] = [
                        'mahasiswa_id' => $tagihan->mahasiswa_id,
                        'jenis' => NotifikasiKeuangan::JENIS_DENDA,
                        'judul' => 'Denda Keterlambatan Dikenakan',
                        'pesan' => "Denda keterlambatan sebesar Rp " . number_format($tagihan->denda, 0, ',', '.') . " telah dikenakan pada tagihan {$tagihan->jenis_tagihan}. Total tagihan sekarang: Rp " . number_format($sisaTagihan + $tagihan->denda, 0, ',', '.'),
                        'reference_type' => Tagihan::class,
                        'reference_id' => $tagihan->id,
                        'channel' => 'email',
                        'status' => 'sent',
                        'sent_at' => $jatuhTempo->copy()->addDays(rand(1, 3)),
                        'read_at' => rand(0, 1) ? $now->copy()->subHours(rand(1, 6)) : null,
                        'created_at' => $jatuhTempo->copy()->addDays(rand(1, 3)),
                        'updated_at' => $now,
                    ];
                }
            }
        }

        // Get cicilan with details
        $cicilanList = Cicilan::with(['detailCicilan', 'tagihan.mahasiswa'])
            ->where('status', 'aktif')
            ->limit(10)
            ->get();

        foreach ($cicilanList as $cicilan) {
            if (!$cicilan->tagihan || !$cicilan->tagihan->mahasiswa) continue;

            $mahasiswaId = $cicilan->tagihan->mahasiswa_id;

            foreach ($cicilan->detailCicilan as $detail) {
                if ($detail->status === 'lunas') continue;

                $jatuhTempo = Carbon::parse($detail->jatuh_tempo);

                // Reminder cicilan H-7
                if ($jatuhTempo->isFuture() && $jatuhTempo->diffInDays($now) <= 7) {
                    $notifikasi[] = [
                        'mahasiswa_id' => $mahasiswaId,
                        'jenis' => NotifikasiKeuangan::JENIS_CICILAN_JATUH_TEMPO,
                        'judul' => "Reminder: Cicilan Ke-{$detail->cicilan_ke} Akan Jatuh Tempo",
                        'pesan' => "Cicilan ke-{$detail->cicilan_ke} sebesar Rp " . number_format($detail->jumlah, 0, ',', '.') . " akan jatuh tempo pada " . $jatuhTempo->format('d/m/Y') . ". Mohon segera lakukan pembayaran.",
                        'reference_type' => Cicilan::class,
                        'reference_id' => $cicilan->id,
                        'channel' => rand(0, 1) ? 'email' : 'database',
                        'status' => rand(0, 1) ? 'sent' : 'pending',
                        'sent_at' => rand(0, 1) ? $now->copy()->subHours(rand(1, 48)) : null,
                        'read_at' => rand(0, 1) ? $now->copy()->subHours(rand(1, 24)) : null,
                        'created_at' => $now->copy()->subDays(rand(1, 5)),
                        'updated_at' => $now,
                    ];
                }

                // Overdue cicilan
                if ($jatuhTempo->isPast() && $detail->status === 'belum_bayar') {
                    $hariTerlambat = $now->diffInDays($jatuhTempo);
                    $denda = $detail->denda ?? 0;
                    
                    $notifikasi[] = [
                        'mahasiswa_id' => $mahasiswaId,
                        'jenis' => NotifikasiKeuangan::JENIS_TAGIHAN_JATUH_TEMPO,
                        'judul' => "Cicilan Ke-{$detail->cicilan_ke} Melewati Jatuh Tempo",
                        'pesan' => "Cicilan ke-{$detail->cicilan_ke} telah melewati jatuh tempo selama {$hariTerlambat} hari. Total yang harus dibayar: Rp " . number_format($detail->jumlah + $denda, 0, ',', '.') . ".",
                        'reference_type' => Cicilan::class,
                        'reference_id' => $cicilan->id,
                        'channel' => 'email',
                        'status' => 'sent',
                        'sent_at' => $jatuhTempo->copy()->addDays(1),
                        'read_at' => rand(0, 1) ? $now->copy()->subHours(rand(1, 12)) : null,
                        'created_at' => $jatuhTempo->copy()->addDays(1),
                        'updated_at' => $now,
                    ];
                }
            }
        }

        // Pembayaran confirmation notifications (from recent transactions)
        $paidTagihans = Tagihan::with('mahasiswa')
            ->where('status', 'lunas')
            ->limit(5)
            ->get();

        foreach ($paidTagihans as $tagihan) {
            if (!$tagihan->mahasiswa) continue;

            $notifikasi[] = [
                'mahasiswa_id' => $tagihan->mahasiswa_id,
                'jenis' => NotifikasiKeuangan::JENIS_PEMBAYARAN_BERHASIL,
                'judul' => 'Pembayaran Berhasil Dikonfirmasi',
                'pesan' => "Pembayaran tagihan {$tagihan->jenis_tagihan} sebesar Rp " . number_format($tagihan->jumlah_tagihan, 0, ',', '.') . " telah berhasil dikonfirmasi. Terima kasih atas pembayarannya.",
                'reference_type' => Tagihan::class,
                'reference_id' => $tagihan->id,
                'channel' => 'email',
                'status' => 'read',
                'sent_at' => $now->copy()->subDays(rand(1, 14)),
                'read_at' => $now->copy()->subDays(rand(1, 10)),
                'created_at' => $now->copy()->subDays(rand(1, 14)),
                'updated_at' => $now,
            ];
        }

        // Insert all notifications
        foreach ($notifikasi as $data) {
            NotifikasiKeuangan::create($data);
        }

        // Summary
        $totalNotifikasi = NotifikasiKeuangan::count();
        $belumDibaca = NotifikasiKeuangan::whereNull('read_at')->count();
        
        $byJenis = NotifikasiKeuangan::selectRaw('jenis, COUNT(*) as total')
            ->groupBy('jenis')
            ->pluck('total', 'jenis')
            ->toArray();

        $this->command->info("=== Notifikasi Seeder Complete ===");
        $this->command->info("Total Notifikasi: {$totalNotifikasi}");
        $this->command->info("Belum Dibaca: {$belumDibaca}");
        $this->command->info("");
        $this->command->info("Per Jenis:");
        foreach ($byJenis as $jenis => $count) {
            $this->command->info("  - {$jenis}: {$count}");
        }
    }
}
