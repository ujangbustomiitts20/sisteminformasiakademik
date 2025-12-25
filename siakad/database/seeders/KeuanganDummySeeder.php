<?php

namespace Database\Seeders;

use App\Models\Beasiswa;
use App\Models\Mahasiswa;
use App\Models\PenerimaBeasiswa;
use App\Models\Tagihan;
use App\Models\TahunAkademik;
use App\Models\Tarif;
use App\Models\TransaksiPembayaran;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KeuanganDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mahasiswas = Mahasiswa::all();
        $tahunAkademik = TahunAkademik::first();
        $tarifs = Tarif::all();
        $beasiswas = Beasiswa::all();

        if ($mahasiswas->isEmpty() || !$tahunAkademik || $tarifs->isEmpty()) {
            $this->command->warn('Data master (Mahasiswa, TahunAkademik, Tarif) belum tersedia!');
            return;
        }

        $this->command->info('Membuat data dummy keuangan...');

        // 1. Tambah Tagihan untuk mahasiswa yang belum punya
        $this->command->info('Creating Tagihan...');
        $tagihanCount = 0;
        foreach ($mahasiswas->take(30) as $mahasiswa) {
            // Cek apakah sudah ada tagihan untuk mahasiswa ini
            $existingTagihan = Tagihan::where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->count();
            
            if ($existingTagihan < 2) {
                // Buat tagihan SPP
                $tarifSpp = $tarifs->where('nama', 'like', '%SPP%')->first() ?? $tarifs->first();
                $nominal = rand(3, 8) * 1000000; // 3-8 juta
                $diskon = rand(0, 1) ? rand(1, 5) * 100000 : 0;
                
                Tagihan::create([
                    'no_tagihan' => 'INV' . date('Ym') . str_pad(Tagihan::count() + 1, 5, '0', STR_PAD_LEFT),
                    'mahasiswa_id' => $mahasiswa->id,
                    'tahun_akademik_id' => $tahunAkademik->id,
                    'tarif_id' => $tarifSpp->id,
                    'jenis_tagihan' => 'SPP',
                    'keterangan_tagihan' => 'Tagihan SPP ' . $tahunAkademik->nama,
                    'nominal' => $nominal,
                    'diskon' => $diskon,
                    'denda' => 0,
                    'jumlah_dibayar' => 0,
                    'sisa_tagihan' => $nominal - $diskon,
                    'total_bayar' => $nominal - $diskon,
                    'status' => 'Belum Bayar',
                    'tanggal_jatuh_tempo' => Carbon::now()->addMonth(),
                ]);
                $tagihanCount++;
            }
        }
        $this->command->info("Created {$tagihanCount} new Tagihan");

        // 2. Buat Transaksi Pembayaran untuk beberapa tagihan
        $this->command->info('Creating TransaksiPembayaran...');
        $tagihanBelumBayar = Tagihan::where('status', 'Belum Bayar')
            ->where('sisa_tagihan', '>', 0)
            ->take(20)
            ->get();

        $metodePembayaran = ['Tunai', 'Transfer Bank', 'Virtual Account', 'QRIS', 'Kartu Kredit'];
        $banks = ['BCA', 'BNI', 'BRI', 'Mandiri', 'BSI'];
        $transactionCount = 0;
        
        foreach ($tagihanBelumBayar as $tagihan) {
            // Random: bayar lunas atau sebagian
            $bayarLunas = rand(0, 1);
            $jumlahBayar = $bayarLunas ? $tagihan->sisa_tagihan : rand(1, (int)($tagihan->sisa_tagihan / 1000000)) * 1000000;
            
            if ($jumlahBayar <= 0) continue;

            $metode = $metodePembayaran[array_rand($metodePembayaran)];
            $transaksi = TransaksiPembayaran::create([
                'no_transaksi' => 'TRX' . date('Ymd') . str_pad(TransaksiPembayaran::count() + 1, 4, '0', STR_PAD_LEFT),
                'tagihan_id' => $tagihan->id,
                'mahasiswa_id' => $tagihan->mahasiswa_id,
                'jumlah' => $jumlahBayar,
                'metode_pembayaran' => $metode,
                'bank' => in_array($metode, ['Transfer Bank', 'Virtual Account']) ? $banks[array_rand($banks)] : null,
                'no_referensi' => 'REF' . Str::random(10),
                'tanggal_bayar' => Carbon::now()->subDays(rand(0, 30)),
                'bukti_bayar' => null,
                'status' => 'Verified',
                'catatan' => 'Pembayaran tagihan ' . $tagihan->no_tagihan,
                'verified_by' => 1,
                'verified_at' => Carbon::now(),
            ]);

            // Update tagihan
            $tagihan->jumlah_dibayar += $jumlahBayar;
            $tagihan->sisa_tagihan = $tagihan->nominal - $tagihan->jumlah_dibayar;
            $tagihan->status = $tagihan->sisa_tagihan <= 0 ? 'Lunas' : 'Cicilan';
            $tagihan->save();

            $transactionCount++;
        }
        $this->command->info("Created {$transactionCount} new TransaksiPembayaran");

        // 3. Buat Penerima Beasiswa
        $this->command->info('Creating PenerimaBeasiswa...');
        if ($beasiswas->isNotEmpty()) {
            $mahasiswaForBeasiswa = $mahasiswas->random(min(15, $mahasiswas->count()));
            $beasiswaCount = 0;
            
            foreach ($mahasiswaForBeasiswa as $mahasiswa) {
                // Cek apakah sudah punya beasiswa
                $existing = PenerimaBeasiswa::where('mahasiswa_id', $mahasiswa->id)
                    ->where('tahun_akademik_id', $tahunAkademik->id)
                    ->exists();
                
                if (!$existing) {
                    $beasiswa = $beasiswas->random();
                    $statuses = ['Diajukan', 'Disetujui', 'Ditolak', 'Disetujui', 'Disetujui'];
                    $status = $statuses[array_rand($statuses)];
                    
                    PenerimaBeasiswa::create([
                        'mahasiswa_id' => $mahasiswa->id,
                        'beasiswa_id' => $beasiswa->id,
                        'tahun_akademik_id' => $tahunAkademik->id,
                        'status' => $status,
                        'tanggal_mulai' => Carbon::now()->subDays(rand(10, 60)),
                        'tanggal_selesai' => Carbon::now()->addMonths(rand(6, 12)),
                        'keterangan' => 'Penerima beasiswa ' . $beasiswa->nama,
                        'approved_by' => $status === 'Disetujui' ? 1 : null,
                        'approved_at' => $status === 'Disetujui' ? Carbon::now()->subDays(rand(1, 10)) : null,
                        'catatan' => $status === 'Ditolak' ? 'IPK tidak memenuhi syarat' : null,
                    ]);
                    $beasiswaCount++;
                }
            }
            $this->command->info("Created {$beasiswaCount} new PenerimaBeasiswa");
        }

        // 4. Buat lebih banyak transaksi dengan tanggal berbeda untuk grafik
        $this->command->info('Creating historical transactions for charts...');
        $tagihanLunas = Tagihan::where('status', 'Lunas')->take(10)->get();
        $historicalCount = 0;

        for ($i = 1; $i <= 6; $i++) {
            $tanggal = Carbon::now()->subMonths($i);
            
            foreach ($tagihanLunas->take(rand(3, 6)) as $tagihan) {
                $jumlah = rand(1, 5) * 500000;
                $metode = $metodePembayaran[array_rand($metodePembayaran)];
                
                TransaksiPembayaran::create([
                    'no_transaksi' => 'TRX' . $tanggal->format('Ymd') . str_pad(TransaksiPembayaran::count() + 1, 4, '0', STR_PAD_LEFT),
                    'tagihan_id' => $tagihan->id,
                    'mahasiswa_id' => $tagihan->mahasiswa_id,
                    'jumlah' => $jumlah,
                    'metode_pembayaran' => $metode,
                    'bank' => in_array($metode, ['Transfer Bank', 'Virtual Account']) ? $banks[array_rand($banks)] : null,
                    'no_referensi' => 'REF' . Str::random(10),
                    'tanggal_bayar' => $tanggal->copy()->addDays(rand(1, 25)),
                    'bukti_bayar' => null,
                    'status' => 'Verified',
                    'catatan' => 'Pembayaran historis',
                    'verified_by' => 1,
                    'verified_at' => $tanggal->copy()->addDays(rand(1, 25)),
                ]);
                $historicalCount++;
            }
        }
        $this->command->info("Created {$historicalCount} historical transactions");

        $this->command->info('Data dummy keuangan berhasil dibuat!');
        
        // Summary
        $this->command->newLine();
        $this->command->info('=== SUMMARY ===');
        $this->command->info('Total Tagihan: ' . Tagihan::count());
        $this->command->info('Total Transaksi: ' . TransaksiPembayaran::count());
        $this->command->info('Total Penerima Beasiswa: ' . PenerimaBeasiswa::count());
        $this->command->info('Total Tunggakan: Rp ' . number_format(Tagihan::sum('sisa_tagihan'), 0, ',', '.'));
        $this->command->info('Total Pendapatan: Rp ' . number_format(TransaksiPembayaran::where('status', 'Verified')->sum('jumlah'), 0, ',', '.'));
    }
}