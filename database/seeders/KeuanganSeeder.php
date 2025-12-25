<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tarif;
use App\Models\Tagihan;
use App\Models\TransaksiPembayaran;
use App\Models\Beasiswa;
use App\Models\PenerimaBeasiswa;
use App\Models\PengaturanDenda;
use App\Models\VirtualAccount;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use Carbon\Carbon;

class KeuanganSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Keuangan data...');

        // Get existing data
        $programStudi = ProgramStudi::all();
        $mahasiswa = Mahasiswa::where('status', 'Aktif')->get();
        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();

        if (!$tahunAkademik) {
            $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->first();
        }

        // 1. Seed Tarif
        $this->command->info('Creating tarif...');
        $tarifData = [
            // SPP per Prodi
            ['jenis' => 'SPP', 'nama_tarif' => 'SPP Reguler', 'nominal' => 3500000, 'periode' => 'Semester', 'program_studi_id' => null],
            ['jenis' => 'Herregistrasi', 'nama_tarif' => 'Biaya Herregistrasi', 'nominal' => 500000, 'periode' => 'Semester', 'program_studi_id' => null],
            ['jenis' => 'Praktikum', 'nama_tarif' => 'Biaya Praktikum Lab Komputer', 'nominal' => 750000, 'periode' => 'Semester', 'program_studi_id' => $programStudi->where('nama', 'like', '%Teknik Informatika%')->first()?->id],
            ['jenis' => 'Praktikum', 'nama_tarif' => 'Biaya Praktikum Lab Bahasa', 'nominal' => 500000, 'periode' => 'Semester', 'program_studi_id' => $programStudi->where('nama', 'like', '%Sastra%')->first()?->id],
            ['jenis' => 'SKS', 'nama_tarif' => 'Biaya Per SKS', 'nominal' => 150000, 'periode' => 'Semester', 'program_studi_id' => null],
            ['jenis' => 'Wisuda', 'nama_tarif' => 'Biaya Wisuda', 'nominal' => 2500000, 'periode' => 'Sekali', 'program_studi_id' => null],
            ['jenis' => 'Almamater', 'nama_tarif' => 'Biaya Almamater & Atribut', 'nominal' => 350000, 'periode' => 'Sekali', 'program_studi_id' => null],
            ['jenis' => 'KKN', 'nama_tarif' => 'Biaya KKN', 'nominal' => 1500000, 'periode' => 'Sekali', 'program_studi_id' => null],
            ['jenis' => 'PKL', 'nama_tarif' => 'Biaya PKL/Magang', 'nominal' => 500000, 'periode' => 'Sekali', 'program_studi_id' => null],
            ['jenis' => 'Lainnya', 'nama_tarif' => 'Biaya Pengembangan Institusi', 'nominal' => 1000000, 'periode' => 'Tahunan', 'program_studi_id' => null],
        ];

        foreach ($tarifData as $data) {
            Tarif::firstOrCreate(
                ['nama_tarif' => $data['nama_tarif']],
                array_merge($data, ['is_active' => true])
            );
        }

        // 2. Seed Beasiswa
        $this->command->info('Creating beasiswa...');
        $beasiswaData = [
            [
                'kode' => 'BEA-001',
                'nama' => 'Beasiswa Prestasi Akademik',
                'jenis' => 'Beasiswa',
                'tipe_potongan' => 'Persen',
                'nilai_potongan' => 50,
                'sumber_dana' => 'Internal',
                'kuota' => 20,
                'persyaratan' => "- IPK minimal 3.50\n- Tidak pernah mengulang mata kuliah\n- Aktif dalam kegiatan kemahasiswaan",
            ],
            [
                'kode' => 'BEA-002',
                'nama' => 'Beasiswa Kurang Mampu',
                'jenis' => 'Beasiswa',
                'tipe_potongan' => 'Persen',
                'nilai_potongan' => 75,
                'sumber_dana' => 'Internal',
                'kuota' => 30,
                'persyaratan' => "- Melampirkan SKTM\n- Penghasilan orang tua < 3 juta/bulan\n- IPK minimal 2.75",
            ],
            [
                'kode' => 'BEA-003',
                'nama' => 'Beasiswa KIP Kuliah',
                'jenis' => 'Beasiswa',
                'tipe_potongan' => 'Persen',
                'nilai_potongan' => 100,
                'sumber_dana' => 'Pemerintah',
                'kuota' => 50,
                'persyaratan' => "- Terdaftar sebagai penerima KIP\n- Memenuhi syarat KIP Kuliah dari Kemendikbud",
            ],
            [
                'kode' => 'POT-001',
                'nama' => 'Potongan Anak Dosen/Karyawan',
                'jenis' => 'Potongan',
                'tipe_potongan' => 'Persen',
                'nilai_potongan' => 25,
                'sumber_dana' => 'Internal',
                'kuota' => null,
                'persyaratan' => "- Anak kandung dosen/karyawan aktif\n- Melampirkan SK Karyawan/Dosen orang tua",
            ],
            [
                'kode' => 'POT-002',
                'nama' => 'Potongan Kakak-Adik',
                'jenis' => 'Potongan',
                'tipe_potongan' => 'Nominal',
                'nilai_potongan' => 500000,
                'sumber_dana' => 'Internal',
                'kuota' => null,
                'persyaratan' => "- Memiliki kakak/adik yang kuliah di universitas yang sama\n- Melampirkan kartu keluarga",
            ],
            [
                'kode' => 'KER-001',
                'nama' => 'Keringanan Pandemi',
                'jenis' => 'Keringanan',
                'tipe_potongan' => 'Persen',
                'nilai_potongan' => 10,
                'sumber_dana' => 'Internal',
                'kuota' => 100,
                'persyaratan' => "- Orang tua terdampak PHK\n- Melampirkan surat keterangan PHK",
            ],
        ];

        foreach ($beasiswaData as $data) {
            Beasiswa::firstOrCreate(
                ['kode' => $data['kode']],
                array_merge($data, ['is_active' => true])
            );
        }

        // 3. Seed Pengaturan Denda
        $this->command->info('Creating pengaturan denda...');
        $dendaData = [
            [
                'nama' => 'Denda Keterlambatan SPP',
                'tipe' => 'Nominal',
                'nilai' => 5000,
                'periode' => 'Harian',
                'grace_period' => 7,
                'maksimal_denda' => 500000,
            ],
            [
                'nama' => 'Denda Keterlambatan Herregistrasi',
                'tipe' => 'Persen',
                'nilai' => 1,
                'periode' => 'Harian',
                'grace_period' => 3,
                'maksimal_denda' => 250000,
            ],
        ];

        foreach ($dendaData as $data) {
            PengaturanDenda::firstOrCreate(
                ['nama' => $data['nama']],
                array_merge($data, ['is_active' => true])
            );
        }

        // 4. Seed Tagihan untuk mahasiswa
        $this->command->info('Creating tagihan...');
        $tarifSPP = Tarif::where('jenis', 'SPP')->first();
        $tarifHereg = Tarif::where('jenis', 'Herregistrasi')->first();

        if ($tahunAkademik && $mahasiswa->count() > 0) {
            foreach ($mahasiswa->take(20) as $mhs) {
                // Tagihan SPP
                if ($tarifSPP) {
                    $existingTagihan = Tagihan::where('mahasiswa_id', $mhs->id)
                        ->where('tahun_akademik_id', $tahunAkademik->id)
                        ->where('jenis_tagihan', 'SPP')
                        ->first();

                    if (!$existingTagihan) {
                        $diskon = 0;
                        // Random beberapa mahasiswa dapat beasiswa
                        if (rand(1, 5) == 1) {
                            $beasiswa = Beasiswa::where('jenis', 'Beasiswa')->inRandomOrder()->first();
                            if ($beasiswa) {
                                $diskon = $beasiswa->hitungPotongan($tarifSPP->nominal);
                            }
                        }

                        $nominal = $tarifSPP->nominal;
                        $totalBayar = $nominal - $diskon;
                        $status = collect(['Belum Bayar', 'Belum Bayar', 'Cicilan', 'Lunas'])->random();
                        $jumlahDibayar = match($status) {
                            'Lunas' => $totalBayar,
                            'Cicilan' => rand(1, (int)($totalBayar / 1000000)) * 1000000,
                            default => 0
                        };

                        Tagihan::create([
                            'mahasiswa_id' => $mhs->id,
                            'tahun_akademik_id' => $tahunAkademik->id,
                            'tarif_id' => $tarifSPP->id,
                            'jenis_tagihan' => 'SPP',
                            'keterangan_tagihan' => 'SPP ' . $tahunAkademik->nama,
                            'nominal' => $nominal,
                            'diskon' => $diskon,
                            'denda' => 0,
                            'total_bayar' => $totalBayar,
                            'jumlah_dibayar' => $jumlahDibayar,
                            'sisa_tagihan' => $totalBayar - $jumlahDibayar,
                            'tanggal_jatuh_tempo' => Carbon::now()->addDays(rand(-10, 30)),
                            'status' => $status,
                        ]);
                    }
                }

                // Tagihan Herregistrasi
                if ($tarifHereg) {
                    $existingTagihan = Tagihan::where('mahasiswa_id', $mhs->id)
                        ->where('tahun_akademik_id', $tahunAkademik->id)
                        ->where('jenis_tagihan', 'Herregistrasi')
                        ->first();

                    if (!$existingTagihan) {
                        $nominal = $tarifHereg->nominal;
                        $status = collect(['Belum Bayar', 'Lunas', 'Lunas'])->random();
                        $jumlahDibayar = $status === 'Lunas' ? $nominal : 0;

                        Tagihan::create([
                            'mahasiswa_id' => $mhs->id,
                            'tahun_akademik_id' => $tahunAkademik->id,
                            'tarif_id' => $tarifHereg->id,
                            'jenis_tagihan' => 'Herregistrasi',
                            'keterangan_tagihan' => 'Herregistrasi ' . $tahunAkademik->nama,
                            'nominal' => $nominal,
                            'diskon' => 0,
                            'denda' => 0,
                            'total_bayar' => $nominal,
                            'jumlah_dibayar' => $jumlahDibayar,
                            'sisa_tagihan' => $nominal - $jumlahDibayar,
                            'tanggal_jatuh_tempo' => Carbon::now()->addDays(rand(-5, 20)),
                            'status' => $status,
                        ]);
                    }
                }
            }
        }

        // 5. Seed Transaksi Pembayaran untuk tagihan yang sudah bayar
        $this->command->info('Creating transaksi pembayaran...');
        $tagihanDibayar = Tagihan::whereIn('status', ['Cicilan', 'Lunas'])->get();
        $adminUser = \App\Models\User::where('role', 'admin')->first();

        foreach ($tagihanDibayar as $tagihan) {
            $existingTransaksi = TransaksiPembayaran::where('tagihan_id', $tagihan->id)->first();
            
            if (!$existingTransaksi && $tagihan->jumlah_dibayar > 0) {
                $metode = collect(array_keys(TransaksiPembayaran::METODE))->random();
                
                TransaksiPembayaran::create([
                    'tagihan_id' => $tagihan->id,
                    'mahasiswa_id' => $tagihan->mahasiswa_id,
                    'jumlah' => $tagihan->jumlah_dibayar,
                    'tanggal_bayar' => Carbon::now()->subDays(rand(1, 30)),
                    'metode_pembayaran' => $metode,
                    'bank' => in_array($metode, ['Transfer Bank', 'Virtual Account']) ? collect(['BCA', 'BNI', 'BRI', 'Mandiri'])->random() : null,
                    'no_referensi' => in_array($metode, ['Transfer Bank', 'Virtual Account']) ? 'REF' . rand(100000, 999999) : null,
                    'status' => 'Verified',
                    'verified_by' => $adminUser?->id,
                    'verified_at' => Carbon::now()->subDays(rand(0, 5)),
                    'catatan' => null,
                ]);
            }
        }

        // 6. Seed Virtual Account untuk mahasiswa
        $this->command->info('Creating virtual accounts...');
        foreach ($mahasiswa->take(20) as $mhs) {
            $existingVA = VirtualAccount::where('mahasiswa_id', $mhs->id)->first();
            
            if (!$existingVA) {
                $bank = collect(array_keys(VirtualAccount::BANKS))->random();
                VirtualAccount::create([
                    'mahasiswa_id' => $mhs->id,
                    'bank_code' => $bank,
                    'va_number' => VirtualAccount::generateVA($mhs->id, $bank),
                    'is_active' => true,
                ]);
            }
        }

        // 7. Seed Penerima Beasiswa
        $this->command->info('Creating penerima beasiswa...');
        $beasiswaList = Beasiswa::where('is_active', true)->get();
        
        foreach ($beasiswaList->take(3) as $beasiswa) {
            $randomMahasiswa = $mahasiswa->random(min(3, $mahasiswa->count()));
            
            foreach ($randomMahasiswa as $mhs) {
                $existing = PenerimaBeasiswa::where('beasiswa_id', $beasiswa->id)
                    ->where('mahasiswa_id', $mhs->id)
                    ->where('tahun_akademik_id', $tahunAkademik->id)
                    ->first();
                
                if (!$existing) {
                    $status = collect(['Diajukan', 'Disetujui', 'Disetujui'])->random();
                    
                    PenerimaBeasiswa::create([
                        'beasiswa_id' => $beasiswa->id,
                        'mahasiswa_id' => $mhs->id,
                        'tahun_akademik_id' => $tahunAkademik->id,
                        'status' => $status,
                        'tanggal_mulai' => $status === 'Disetujui' ? Carbon::now()->startOfMonth() : null,
                        'tanggal_selesai' => $status === 'Disetujui' ? Carbon::now()->addMonths(6) : null,
                        'keterangan' => $status === 'Disetujui' ? 'Disetujui berdasarkan evaluasi tim seleksi' : null,
                    ]);
                }
            }
        }

        $this->command->info('Keuangan seeding completed!');
        $this->command->info('- Tarif: ' . Tarif::count());
        $this->command->info('- Beasiswa: ' . Beasiswa::count());
        $this->command->info('- Pengaturan Denda: ' . PengaturanDenda::count());
        $this->command->info('- Tagihan: ' . Tagihan::count());
        $this->command->info('- Transaksi Pembayaran: ' . TransaksiPembayaran::count());
        $this->command->info('- Virtual Account: ' . VirtualAccount::count());
        $this->command->info('- Penerima Beasiswa: ' . PenerimaBeasiswa::count());
    }
}
