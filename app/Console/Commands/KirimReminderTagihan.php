<?php

namespace App\Console\Commands;

use App\Models\Tagihan;
use App\Models\DetailCicilan;
use App\Models\NotifikasiKeuangan;
use App\Mail\TagihanReminderMail;
use App\Mail\CicilanReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class KirimReminderTagihan extends Command
{
    protected $signature = 'keuangan:kirim-reminder 
                            {--hari=7 : Kirim reminder H-berapa hari sebelum jatuh tempo}
                            {--email : Kirim juga via email}';
    
    protected $description = 'Kirim reminder tagihan dan cicilan yang akan jatuh tempo';

    public function handle()
    {
        $hariBefore = (int) $this->option('hari');
        $sendEmail = $this->option('email');

        $this->info("Mengirim reminder untuk tagihan H-{$hariBefore}...");

        // 1. Reminder Tagihan
        $this->reminderTagihan($hariBefore, $sendEmail);

        // 2. Reminder Cicilan
        $this->reminderCicilan($hariBefore, $sendEmail);

        // 3. Reminder Overdue
        $this->reminderOverdue($sendEmail);

        $this->info('Selesai!');
        return Command::SUCCESS;
    }

    private function reminderTagihan($hariBefore, $sendEmail)
    {
        $tanggalTarget = now()->addDays($hariBefore)->toDateString();

        $tagihan = Tagihan::with('mahasiswa.user')
            ->whereIn('status', ['Belum Bayar', 'Cicilan'])
            ->whereDate('tanggal_jatuh_tempo', $tanggalTarget)
            ->get();

        $this->info("Ditemukan {$tagihan->count()} tagihan jatuh tempo H-{$hariBefore}");

        $notifCount = 0;
        $emailCount = 0;

        foreach ($tagihan as $t) {
            // Cek apakah sudah ada notifikasi hari ini
            $existingNotif = NotifikasiKeuangan::where('mahasiswa_id', $t->mahasiswa_id)
                ->where('reference_type', 'tagihan')
                ->where('reference_id', $t->id)
                ->where('jenis', 'tagihan_jatuh_tempo')
                ->whereDate('created_at', today())
                ->exists();

            if ($existingNotif) continue;

            // Buat notifikasi database
            NotifikasiKeuangan::create([
                'mahasiswa_id' => $t->mahasiswa_id,
                'jenis' => 'tagihan_jatuh_tempo',
                'judul' => 'Tagihan Akan Jatuh Tempo',
                'pesan' => "Tagihan {$t->jenis_tagihan} sebesar Rp " . number_format($t->sisa_tagihan, 0, ',', '.') . " akan jatuh tempo pada " . $t->tanggal_jatuh_tempo->format('d M Y') . ". Segera lakukan pembayaran untuk menghindari denda.",
                'reference_type' => 'tagihan',
                'reference_id' => $t->id,
                'channel' => 'database',
                'status' => 'sent',
                'sent_at' => now(),
            ]);
            $notifCount++;

            // Kirim email jika diminta
            if ($sendEmail && $t->mahasiswa->user && $t->mahasiswa->user->email) {
                try {
                    Mail::to($t->mahasiswa->user->email)->queue(new TagihanReminderMail($t));
                    
                    NotifikasiKeuangan::create([
                        'mahasiswa_id' => $t->mahasiswa_id,
                        'jenis' => 'tagihan_jatuh_tempo',
                        'judul' => 'Tagihan Akan Jatuh Tempo (Email)',
                        'pesan' => "Email reminder terkirim ke {$t->mahasiswa->user->email}",
                        'reference_type' => 'tagihan',
                        'reference_id' => $t->id,
                        'channel' => 'email',
                        'status' => 'sent',
                        'sent_at' => now(),
                    ]);
                    $emailCount++;
                } catch (\Exception $e) {
                    Log::error("Gagal kirim email reminder tagihan: " . $e->getMessage());
                }
            }

            $this->line("  - {$t->mahasiswa->nim}: {$t->jenis_tagihan}");
        }

        $this->info("✓ {$notifCount} notifikasi dibuat" . ($sendEmail ? ", {$emailCount} email terkirim" : ""));
    }

    private function reminderCicilan($hariBefore, $sendEmail)
    {
        $tanggalTarget = now()->addDays($hariBefore)->toDateString();

        $cicilan = DetailCicilan::with(['cicilan.tagihan.mahasiswa.user'])
            ->where('status', 'Belum Bayar')
            ->whereDate('jatuh_tempo', $tanggalTarget)
            ->get();

        $this->info("Ditemukan {$cicilan->count()} cicilan jatuh tempo H-{$hariBefore}");

        $notifCount = 0;

        foreach ($cicilan as $detail) {
            $tagihan = $detail->cicilan->tagihan;
            $mahasiswa = $tagihan->mahasiswa;

            // Cek existing
            $existingNotif = NotifikasiKeuangan::where('mahasiswa_id', $mahasiswa->id)
                ->where('reference_type', 'detail_cicilan')
                ->where('reference_id', $detail->id)
                ->where('jenis', 'cicilan_jatuh_tempo')
                ->whereDate('created_at', today())
                ->exists();

            if ($existingNotif) continue;

            NotifikasiKeuangan::create([
                'mahasiswa_id' => $mahasiswa->id,
                'jenis' => 'cicilan_jatuh_tempo',
                'judul' => "Cicilan ke-{$detail->cicilan_ke} Akan Jatuh Tempo",
                'pesan' => "Cicilan ke-{$detail->cicilan_ke} sebesar Rp " . number_format($detail->nominal, 0, ',', '.') . " untuk {$tagihan->jenis_tagihan} akan jatuh tempo pada " . $detail->jatuh_tempo->format('d M Y'),
                'reference_type' => 'detail_cicilan',
                'reference_id' => $detail->id,
                'channel' => 'database',
                'status' => 'sent',
                'sent_at' => now(),
            ]);
            $notifCount++;

            $this->line("  - {$mahasiswa->nim}: Cicilan ke-{$detail->cicilan_ke}");
        }

        $this->info("✓ {$notifCount} notifikasi cicilan dibuat");
    }

    private function reminderOverdue($sendEmail)
    {
        // Tagihan yang sudah overdue tapi belum dapat reminder hari ini
        $tagihanOverdue = Tagihan::with('mahasiswa.user')
            ->whereIn('status', ['Belum Bayar', 'Cicilan'])
            ->where('tanggal_jatuh_tempo', '<', now())
            ->get();

        $notifCount = 0;

        foreach ($tagihanOverdue as $t) {
            $hariTerlambat = now()->diffInDays($t->tanggal_jatuh_tempo);
            
            // Kirim reminder setiap 7 hari
            if ($hariTerlambat % 7 !== 0) continue;

            $existingNotif = NotifikasiKeuangan::where('mahasiswa_id', $t->mahasiswa_id)
                ->where('reference_type', 'tagihan')
                ->where('reference_id', $t->id)
                ->where('jenis', 'reminder')
                ->whereDate('created_at', today())
                ->exists();

            if ($existingNotif) continue;

            NotifikasiKeuangan::create([
                'mahasiswa_id' => $t->mahasiswa_id,
                'jenis' => 'reminder',
                'judul' => 'Tagihan Sudah Jatuh Tempo',
                'pesan' => "Tagihan {$t->jenis_tagihan} sebesar Rp " . number_format($t->sisa_tagihan, 0, ',', '.') . " sudah melewati jatuh tempo {$hariTerlambat} hari. Denda saat ini: Rp " . number_format($t->denda, 0, ',', '.') . ". Segera lakukan pembayaran!",
                'reference_type' => 'tagihan',
                'reference_id' => $t->id,
                'channel' => 'database',
                'status' => 'sent',
                'sent_at' => now(),
            ]);
            $notifCount++;
        }

        $this->info("✓ {$notifCount} reminder overdue dibuat");
    }
}
