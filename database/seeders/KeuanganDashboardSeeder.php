<?php

namespace Database\Seeders;

use App\Models\Tagihan;
use App\Models\TransaksiPembayaran;
use App\Models\Beasiswa;
use App\Models\PenerimaBeasiswa;
use App\Models\Cicilan;
use App\Models\DetailCicilan;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KeuanganDashboardSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Keuangan Dashboard Data...');

        // Ambil data master yang diperlukan
        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();
        if (!$tahunAkademik) {
            $tahunAkademik = TahunAkademik::first();
        }

        if (!$tahunAkademik) {
            $this->command->error('Tahun Akademik tidak ditemukan! Jalankan TahunAkademikSeeder terlebih dahulu.');
            return;
        }

        $mahasiswas = Mahasiswa::limit(50)->get();
        if ($mahasiswas->isEmpty()) {
            $this->command->error('Mahasiswa tidak ditemukan! Jalankan MahasiswaSeeder terlebih dahulu.');
            return;
        }

        $admin = User::where('role', 'admin')->first();

        // === SEED TAGIHAN ===
        $this->seedTagihan($mahasiswas, $tahunAkademik);

        // === SEED TRANSAKSI PEMBAYARAN ===
        $this->seedTransaksiPembayaran($admin);

        // === SEED BEASISWA ===
        $this->seedBeasiswa($mahasiswas, $tahunAkademik);

        // === SEED CICILAN ===
        $this->seedCicilan();

        $this->command->info('Keuangan Dashboard Data seeded successfully!');
    }

    private function seedTagihan($mahasiswas, $tahunAkademik)
    {
        $this->command->info('Creating Tagihan...');

        // Hapus data lama untuk fresh start
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DetailCicilan::truncate();
        Cicilan::truncate();
        TransaksiPembayaran::truncate();
        Tagihan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $jenisTagihan = ['SPP', 'UKT', 'Praktikum', 'SKS', 'Wisuda', 'Registrasi'];
        $statuses = ['Belum Bayar', 'Cicilan', 'Lunas'];

        $counter = 1;
        $timestamp = now()->format('His');
        foreach ($mahasiswas as $mahasiswa) {
            // Setiap mahasiswa punya 2-3 tagihan
            $jumlahTagihan = rand(2, 3);
            
            for ($i = 0; $i < $jumlahTagihan; $i++) {
                $jenis = $jenisTagihan[array_rand($jenisTagihan)];
                $nominal = $this->getNominalByJenis($jenis);
                $status = $statuses[array_rand($statuses)];
                
                $jumlahDibayar = 0;
                $sisaTagihan = $nominal;
                
                if ($status == 'Lunas') {
                    $jumlahDibayar = $nominal;
                    $sisaTagihan = 0;
                } elseif ($status == 'Cicilan') {
                    $jumlahDibayar = rand(1, 3) * ($nominal / 4);
                    $sisaTagihan = $nominal - $jumlahDibayar;
                }

                // Tanggal jatuh tempo acak dalam 6 bulan terakhir hingga 2 bulan kedepan
                $jatuhTempo = Carbon::now()->subMonths(rand(0, 6))->addDays(rand(-60, 60));

                Tagihan::create([
                    'no_tagihan' => 'TAG' . $timestamp . str_pad($counter++, 4, '0', STR_PAD_LEFT),
                    'mahasiswa_id' => $mahasiswa->id,
                    'tahun_akademik_id' => $tahunAkademik->id,
                    'jenis_tagihan' => $jenis,
                    'keterangan_tagihan' => "Tagihan {$jenis} untuk {$mahasiswa->nama}",
                    'nominal' => $nominal,
                    'diskon' => 0,
                    'denda' => $status == 'Belum Bayar' && $jatuhTempo < now() ? rand(50000, 200000) : 0,
                    'total_bayar' => $nominal,
                    'jumlah_dibayar' => $jumlahDibayar,
                    'sisa_tagihan' => $sisaTagihan,
                    'status' => $status,
                    'tanggal_jatuh_tempo' => $jatuhTempo,
                ]);
            }
        }

        $this->command->info('  - ' . Tagihan::count() . ' tagihan created');
    }

    private function getNominalByJenis($jenis)
    {
        return match($jenis) {
            'SPP' => rand(3, 5) * 1000000,
            'UKT' => rand(5, 10) * 1000000,
            'Praktikum' => rand(500, 1500) * 1000,
            'SKS' => rand(100, 200) * 1000 * rand(15, 24),
            'Wisuda' => rand(1, 3) * 1000000,
            'Registrasi' => rand(500, 1000) * 1000,
            default => rand(1, 5) * 1000000,
        };
    }

    private function seedTransaksiPembayaran($admin)
    {
        $this->command->info('Creating Transaksi Pembayaran...');

        $tagihans = Tagihan::whereIn('status', ['Cicilan', 'Lunas'])->get();
        $metodePembayaran = ['Tunai', 'Transfer Bank', 'Virtual Account', 'QRIS'];
        $statuses = ['Verified', 'Verified', 'Verified', 'Pending', 'Rejected']; // Lebih banyak verified

        $counter = 1;
        $timestamp = now()->format('His');
        foreach ($tagihans as $tagihan) {
            // Buat 1-3 transaksi per tagihan yang sudah dibayar
            $jumlahTransaksi = $tagihan->status == 'Lunas' ? rand(1, 2) : rand(1, 3);
            $totalDibayar = $tagihan->jumlah_dibayar;
            $sisaBayar = $totalDibayar;

            for ($i = 0; $i < $jumlahTransaksi && $sisaBayar > 0; $i++) {
                $status = $statuses[array_rand($statuses)];
                $jumlahBayar = $i == $jumlahTransaksi - 1 ? $sisaBayar : rand(1, (int)($sisaBayar / 2)) + 100000;
                $jumlahBayar = min($jumlahBayar, $sisaBayar);
                
                // Tanggal bayar dalam 12 bulan terakhir
                $tanggalBayar = Carbon::now()->subDays(rand(1, 365));

                TransaksiPembayaran::create([
                    'no_transaksi' => 'TRX' . $timestamp . str_pad($counter++, 5, '0', STR_PAD_LEFT),
                    'tagihan_id' => $tagihan->id,
                    'mahasiswa_id' => $tagihan->mahasiswa_id,
                    'jumlah' => $jumlahBayar,
                    'metode_pembayaran' => $metodePembayaran[array_rand($metodePembayaran)],
                    'bank' => ['BCA', 'BNI', 'BRI', 'Mandiri', 'BSI'][array_rand(['BCA', 'BNI', 'BRI', 'Mandiri', 'BSI'])],
                    'no_referensi' => 'REF' . rand(100000, 999999),
                    'bukti_bayar' => 'bukti/payment_' . $counter . '.jpg',
                    'tanggal_bayar' => $tanggalBayar,
                    'status' => $status,
                    'verified_by' => $status == 'Verified' ? $admin?->id : null,
                    'verified_at' => $status == 'Verified' ? $tanggalBayar->copy()->addHours(rand(1, 48)) : null,
                    'catatan' => $status == 'Rejected' ? 'Bukti pembayaran tidak valid' : null,
                ]);

                $sisaBayar -= $jumlahBayar;
            }
        }

        $this->command->info('  - ' . TransaksiPembayaran::count() . ' transaksi created');
    }

    private function seedBeasiswa($mahasiswas, $tahunAkademik)
    {
        $this->command->info('Creating Beasiswa...');

        // Buat beberapa jenis beasiswa
        $beasiswaData = [
            ['nama' => 'Beasiswa Prestasi Akademik', 'tipe_potongan' => 'Nominal', 'nilai_potongan' => 5000000, 'jenis' => 'Beasiswa'],
            ['nama' => 'Beasiswa Kurang Mampu', 'tipe_potongan' => 'Persen', 'nilai_potongan' => 50, 'jenis' => 'Keringanan'],
            ['nama' => 'Beasiswa Hafidz Quran', 'tipe_potongan' => 'Nominal', 'nilai_potongan' => 3000000, 'jenis' => 'Beasiswa'],
            ['nama' => 'Beasiswa Atlet Berprestasi', 'tipe_potongan' => 'Nominal', 'nilai_potongan' => 4000000, 'jenis' => 'Beasiswa'],
            ['nama' => 'Beasiswa KIP Kuliah', 'tipe_potongan' => 'Persen', 'nilai_potongan' => 100, 'jenis' => 'Beasiswa'],
            ['nama' => 'Potongan Anak Guru/Dosen', 'tipe_potongan' => 'Persen', 'nilai_potongan' => 25, 'jenis' => 'Potongan'],
        ];

        $counter = 1;
        foreach ($beasiswaData as $data) {
            Beasiswa::firstOrCreate(
                ['nama' => $data['nama']],
                [
                    'kode' => 'BSW' . str_pad($counter++, 3, '0', STR_PAD_LEFT),
                    'jenis' => $data['jenis'],
                    'tipe_potongan' => $data['tipe_potongan'],
                    'nilai_potongan' => $data['nilai_potongan'],
                    'sumber_dana' => $data['jenis'] == 'Beasiswa' ? 'Kemendikbud' : 'Yayasan',
                    'kuota' => rand(20, 50),
                    'persyaratan' => 'IPK minimal 3.0, tidak sedang menerima beasiswa lain',
                    'is_active' => true,
                ]
            );
        }

        // Seed penerima beasiswa
        $beasiswas = Beasiswa::all();
        $statuses = ['Disetujui', 'Disetujui', 'Disetujui', 'Diajukan', 'Ditolak'];
        $mahasiswaIds = $mahasiswas->pluck('id')->toArray();
        shuffle($mahasiswaIds);

        $counter = 0;
        foreach ($beasiswas as $beasiswa) {
            // 5-15 penerima per beasiswa
            $jumlahPenerima = rand(5, 15);
            
            for ($i = 0; $i < $jumlahPenerima && $counter < count($mahasiswaIds); $i++) {
                $status = $statuses[array_rand($statuses)];
                PenerimaBeasiswa::firstOrCreate(
                    [
                        'mahasiswa_id' => $mahasiswaIds[$counter],
                        'beasiswa_id' => $beasiswa->id,
                        'tahun_akademik_id' => $tahunAkademik->id,
                    ],
                    [
                        'status' => $status,
                        'tanggal_mulai' => Carbon::now()->startOfYear(),
                        'tanggal_selesai' => Carbon::now()->endOfYear(),
                        'keterangan' => 'Pengajuan beasiswa ' . $beasiswa->nama,
                    ]
                );
                $counter++;
            }
        }

        $this->command->info('  - ' . Beasiswa::count() . ' beasiswa created');
        $this->command->info('  - ' . PenerimaBeasiswa::count() . ' penerima beasiswa created');
    }

    private function seedCicilan()
    {
        $this->command->info('Creating Cicilan...');

        // Ambil tagihan dengan status cicilan
        $tagihanCicilan = Tagihan::where('status', 'Cicilan')->limit(20)->get();
        
        // Ambil skema cicilan pertama
        $skemaCicilanId = DB::table('skema_cicilan')->first()?->id ?? 1;

        foreach ($tagihanCicilan as $tagihan) {
            $jumlahTerm = rand(3, 6);
            $nominalPerTerm = $tagihan->total_bayar / $jumlahTerm;
            $cicilanTerbayar = rand(1, $jumlahTerm - 1);

            $cicilan = Cicilan::create([
                'tagihan_id' => $tagihan->id,
                'skema_cicilan_id' => $skemaCicilanId,
                'total_tagihan_awal' => $tagihan->total_bayar,
                'biaya_admin' => 0,
                'total_bunga' => 0,
                'total_harus_dibayar' => $tagihan->total_bayar,
                'nominal_per_cicilan' => $nominalPerTerm,
                'jumlah_cicilan' => $jumlahTerm,
                'cicilan_terbayar' => $cicilanTerbayar,
                'status' => 'Aktif',
                'tanggal_mulai' => Carbon::now()->subMonths(rand(1, 3)),
            ]);

            // Buat detail cicilan
            for ($term = 1; $term <= $jumlahTerm; $term++) {
                $jatuhTempo = Carbon::parse($cicilan->tanggal_mulai)->addMonths($term);
                $statusDetail = $term <= $cicilanTerbayar ? 'Dibayar' : 'Belum Bayar';

                DetailCicilan::create([
                    'cicilan_id' => $cicilan->id,
                    'cicilan_ke' => $term,
                    'nominal' => $nominalPerTerm,
                    'jatuh_tempo' => $jatuhTempo,
                    'tanggal_bayar' => $statusDetail == 'Dibayar' ? $jatuhTempo->copy()->subDays(rand(1, 10)) : null,
                    'denda' => 0,
                    'status' => $statusDetail,
                ]);
            }
        }

        $this->command->info('  - ' . Cicilan::count() . ' cicilan created');
        $this->command->info('  - ' . DetailCicilan::count() . ' detail cicilan created');
    }
}
