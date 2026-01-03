<?php

namespace Database\Seeders;

use App\Models\PeriodePmb;
use App\Models\GelombangPmb;
use App\Models\JalurSeleksi;
use App\Models\CalonMahasiswa;
use App\Models\DokumenCamaba;
use App\Models\NilaiSeleksi;
use App\Models\HasilSeleksi;
use App\Models\DaftarUlang;
use App\Models\PembayaranPmb;
use App\Models\KuotaPmb;
use App\Models\ProgramStudi;
use App\Models\KomponenNilai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PmbDummySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Membuat data dummy PMB...');

        // Cek program studi
        $prodis = ProgramStudi::all();
        if ($prodis->isEmpty()) {
            $this->command->warn('Program studi tidak ditemukan! Membuat program studi dummy...');
            $prodis = collect([
                ProgramStudi::create(['kode' => 'TI', 'nama' => 'Teknik Informatika', 'jenjang' => 'S1', 'status' => 'aktif']),
                ProgramStudi::create(['kode' => 'SI', 'nama' => 'Sistem Informasi', 'jenjang' => 'S1', 'status' => 'aktif']),
                ProgramStudi::create(['kode' => 'TK', 'nama' => 'Teknik Komputer', 'jenjang' => 'S1', 'status' => 'aktif']),
            ]);
        }

        // 1. Buat Jalur Seleksi
        $this->command->info('Membuat jalur seleksi...');
        $jalurRegular = JalurSeleksi::firstOrCreate(
            ['nama' => 'Reguler'],
            [
                'kode' => 'REG',
                'deskripsi' => 'Jalur seleksi reguler dengan ujian tertulis',
                'is_active' => true,
            ]
        );

        $jalurPrestasi = JalurSeleksi::firstOrCreate(
            ['nama' => 'Prestasi'],
            [
                'kode' => 'PRS',
                'deskripsi' => 'Jalur seleksi berdasarkan prestasi akademik',
                'is_active' => true,
            ]
        );

        $jalurRapor = JalurSeleksi::firstOrCreate(
            ['nama' => 'Nilai Rapor'],
            [
                'kode' => 'RPR',
                'deskripsi' => 'Jalur seleksi berdasarkan nilai rapor',
                'is_active' => true,
            ]
        );

        $jalurs = collect([$jalurRegular, $jalurPrestasi, $jalurRapor]);

        // 2. Buat Periode PMB
        $this->command->info('Membuat periode PMB...');
        $periode = PeriodePmb::firstOrCreate(
            ['tahun_akademik' => '2024/2025'],
            [
                'nama' => 'PMB Tahun Akademik 2024/2025',
                'tanggal_mulai' => '2024-01-01',
                'tanggal_selesai' => '2024-08-31',
                'is_active' => true,
            ]
        );

        // 3. Buat Gelombang PMB
        $this->command->info('Membuat gelombang PMB...');
        $gelombang1 = GelombangPmb::firstOrCreate(
            ['periode_pmb_id' => $periode->id, 'nama' => 'Gelombang 1'],
            [
                'nomor_gelombang' => 1,
                'tanggal_mulai_daftar' => '2024-01-15',
                'tanggal_selesai_daftar' => '2024-03-31',
                'tanggal_ujian' => '2024-04-10',
                'tanggal_pengumuman' => '2024-04-20',
                'is_active' => true,
            ]
        );

        $gelombang2 = GelombangPmb::firstOrCreate(
            ['periode_pmb_id' => $periode->id, 'nama' => 'Gelombang 2'],
            [
                'nomor_gelombang' => 2,
                'tanggal_mulai_daftar' => '2024-04-01',
                'tanggal_selesai_daftar' => '2024-05-31',
                'tanggal_ujian' => '2024-06-10',
                'tanggal_pengumuman' => '2024-06-20',
                'is_active' => false,
            ]
        );

        $gelombangs = collect([$gelombang1, $gelombang2]);

        // 4. Buat Kuota per Prodi per Gelombang per Jalur
        $this->command->info('Membuat kuota PMB per prodi...');
        foreach ($gelombangs as $gelombang) {
            foreach ($prodis as $prodi) {
                foreach ($jalurs as $jalur) {
                    KuotaPmb::firstOrCreate(
                        [
                            'gelombang_pmb_id' => $gelombang->id,
                            'program_studi_id' => $prodi->id,
                            'jalur_seleksi_id' => $jalur->id,
                        ],
                        [
                            'kuota' => rand(20, 40),
                            'terisi' => 0,
                        ]
                    );
                }
            }
        }

        // 5. Buat Komponen Nilai (jika ada)
        $this->command->info('Membuat komponen nilai...');
        $komponenNilai = [];
        if (class_exists(\App\Models\KomponenNilai::class)) {
            $komponenNilai = [
                KomponenNilai::firstOrCreate(['nama' => 'Tes Potensi Akademik'], ['bobot' => 30, 'is_active' => true]),
                KomponenNilai::firstOrCreate(['nama' => 'Tes Bahasa Inggris'], ['bobot' => 20, 'is_active' => true]),
                KomponenNilai::firstOrCreate(['nama' => 'Tes Matematika'], ['bobot' => 25, 'is_active' => true]),
                KomponenNilai::firstOrCreate(['nama' => 'Wawancara'], ['bobot' => 25, 'is_active' => true]),
            ];
        }

        // 6. Buat Calon Mahasiswa
        $this->command->info('Membuat calon mahasiswa...');
        $namaDepan = ['Andi', 'Budi', 'Citra', 'Dewi', 'Eko', 'Fitri', 'Galih', 'Hana', 'Irfan', 'Joko', 
                      'Kartika', 'Lina', 'Maya', 'Nanda', 'Okta', 'Putra', 'Qori', 'Rina', 'Santi', 'Tono',
                      'Udin', 'Vera', 'Wati', 'Xena', 'Yudi', 'Zara', 'Ahmad', 'Bella', 'Cindy', 'Dani'];
        $namaBelakang = ['Pratama', 'Wijaya', 'Sari', 'Putri', 'Kusuma', 'Hidayat', 'Nugraha', 'Permana', 
                        'Santoso', 'Wibowo', 'Susanto', 'Hartono', 'Setiawan', 'Firmansyah', 'Ramadhan'];
        
        $statusList = ['mendaftar', 'terdaftar', 'verifikasi_dokumen', 'lulus_administrasi', 'mengikuti_ujian', 'lulus', 'tidak_lulus', 'daftar_ulang'];
        $agamaList = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha'];
        
        $calonMahasiswas = [];
        
        foreach ($gelombangs as $gelombang) {
            $jumlahPendaftar = $gelombang->nama == 'Gelombang 1' ? 80 : 50;
            
            for ($i = 1; $i <= $jumlahPendaftar; $i++) {
                $nama = $namaDepan[array_rand($namaDepan)] . ' ' . $namaBelakang[array_rand($namaBelakang)];
                $jenisKelamin = rand(0, 1) ? 'L' : 'P';
                $prodi = $prodis->random();
                $jalur = $jalurs->random();
                $status = $statusList[array_rand($statusList)];
                
                // Generate NIK
                $nik = '32' . str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT) . 
                       str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . 
                       str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT) . 
                       str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
                
                $camaba = CalonMahasiswa::create([
                    'gelombang_pmb_id' => $gelombang->id,
                    'jalur_seleksi_id' => $jalur->id,
                    'program_studi_id' => $prodi->id,
                    'nama_lengkap' => $nama,
                    'nik' => $nik,
                    'tempat_lahir' => ['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang', 'Malang'][array_rand(['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang', 'Malang'])],
                    'tanggal_lahir' => date('Y-m-d', strtotime('-' . rand(17, 22) . ' years -' . rand(0, 365) . ' days')),
                    'jenis_kelamin' => $jenisKelamin,
                    'agama' => $agamaList[array_rand($agamaList)],
                    'alamat' => 'Jl. ' . $namaBelakang[array_rand($namaBelakang)] . ' No. ' . rand(1, 100) . ', RT ' . rand(1, 10) . '/RW ' . rand(1, 10),
                    'no_hp' => '08' . rand(1, 9) . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT),
                    'email' => strtolower(str_replace(' ', '.', $nama)) . '_' . time() . rand(100, 999) . '@gmail.com',
                    'asal_sekolah' => 'SMA ' . ['Negeri', 'Swasta'][rand(0, 1)] . ' ' . rand(1, 20) . ' ' . ['Jakarta', 'Bandung', 'Surabaya'][rand(0, 2)],
                    'tahun_lulus' => rand(2022, 2024),
                    'status' => $status,
                    'password' => bcrypt('password123'),
                    'is_bayar_pendaftaran' => in_array($status, ['terdaftar', 'verifikasi_dokumen', 'lulus_administrasi', 'mengikuti_ujian', 'lulus', 'daftar_ulang']),
                    'tanggal_bayar' => in_array($status, ['terdaftar', 'verifikasi_dokumen', 'lulus_administrasi', 'mengikuti_ujian', 'lulus', 'daftar_ulang']) ? now()->subDays(rand(1, 60)) : null,
                ]);
                
                $calonMahasiswas[] = $camaba;
            }
        }

        $this->command->info('Berhasil membuat ' . count($calonMahasiswas) . ' calon mahasiswa');

        // 7. Buat Pembayaran PMB
        $this->command->info('Membuat data pembayaran PMB...');
        foreach ($calonMahasiswas as $camaba) {
            if ($camaba->is_bayar_pendaftaran) {
                $statusPembayaran = ['terverifikasi', 'terverifikasi', 'terverifikasi', 'menunggu_verifikasi'][rand(0, 3)];
                
                PembayaranPmb::create([
                    'calon_mahasiswa_id' => $camaba->id,
                    'jenis_pembayaran' => 'pendaftaran',
                    'jumlah' => 250000,
                    'tanggal_bayar' => $camaba->tanggal_bayar,
                    'metode_pembayaran' => ['transfer', 'va', 'qris'][rand(0, 2)],
                    'bukti_bayar' => null,
                    'status' => $statusPembayaran,
                    'verified_at' => $statusPembayaran == 'terverifikasi' ? now()->subDays(rand(1, 30)) : null,
                ]);
            } else {
                // Beberapa yang belum bayar
                PembayaranPmb::create([
                    'calon_mahasiswa_id' => $camaba->id,
                    'jenis_pembayaran' => 'pendaftaran',
                    'jumlah' => 250000,
                    'tanggal_bayar' => null,
                    'metode_pembayaran' => null,
                    'bukti_bayar' => null,
                    'status' => 'pending',
                ]);
            }
        }

        // 8. Buat Nilai Seleksi untuk yang sudah mengikuti ujian
        $this->command->info('Membuat nilai seleksi...');
        $pesertaUjian = CalonMahasiswa::whereIn('status', ['mengikuti_ujian', 'lulus', 'tidak_lulus', 'daftar_ulang'])->get();
        
        foreach ($pesertaUjian as $camaba) {
            // Nilai random untuk tiap komponen
            $nilaiTPA = rand(40, 100);
            $nilaiBahasa = rand(40, 100);
            $nilaiMtk = rand(40, 100);
            $nilaiWawancara = rand(50, 100);
            $nilaiTotal = round(($nilaiTPA * 0.3) + ($nilaiBahasa * 0.2) + ($nilaiMtk * 0.25) + ($nilaiWawancara * 0.25), 2);
            
            NilaiSeleksi::create([
                'calon_mahasiswa_id' => $camaba->id,
                'gelombang_pmb_id' => $camaba->gelombang_pmb_id,
                'komponen_nilai' => [
                    'tpa' => $nilaiTPA,
                    'bahasa_inggris' => $nilaiBahasa,
                    'matematika' => $nilaiMtk,
                    'wawancara' => $nilaiWawancara,
                ],
                'nilai' => $nilaiTotal,
                'bobot' => 1,
                'nilai_tpa' => $nilaiTPA,
                'nilai_bahasa' => $nilaiBahasa,
                'nilai_matematika' => $nilaiMtk,
                'nilai_wawancara' => $nilaiWawancara,
                'nilai_total' => $nilaiTotal,
                'nilai_akhir' => $nilaiTotal,
            ]);
        }

        // 9. Buat Hasil Seleksi untuk yang lulus/tidak lulus
        $this->command->info('Membuat hasil seleksi...');
        $pesertaSelesai = CalonMahasiswa::whereIn('status', ['lulus', 'tidak_lulus', 'daftar_ulang'])->get();
        
        foreach ($pesertaSelesai as $index => $camaba) {
            $nilaiSeleksi = NilaiSeleksi::where('calon_mahasiswa_id', $camaba->id)->first();
            $statusHasil = in_array($camaba->status, ['lulus', 'daftar_ulang']) ? 'lulus' : 'tidak_lulus';
            
            HasilSeleksi::create([
                'calon_mahasiswa_id' => $camaba->id,
                'gelombang_pmb_id' => $camaba->gelombang_pmb_id,
                'nilai_total' => $nilaiSeleksi->nilai_total ?? rand(50, 95),
                'ranking' => $index + 1,
                'status' => $statusHasil,
                'program_studi_diterima_id' => $statusHasil == 'lulus' ? $camaba->program_studi_id : null,
                'catatan' => $statusHasil == 'lulus' ? 'Selamat! Anda dinyatakan lulus seleksi.' : 'Mohon maaf, Anda belum berhasil pada seleksi ini.',
                'tanggal_pengumuman' => now()->subDays(rand(5, 20)),
            ]);
        }

        // 10. Buat Daftar Ulang untuk yang lulus
        $this->command->info('Membuat data daftar ulang...');
        $pesertaLulus = CalonMahasiswa::where('status', 'daftar_ulang')->get();
        
        foreach ($pesertaLulus as $camaba) {
            $hasilSeleksi = HasilSeleksi::where('calon_mahasiswa_id', $camaba->id)->first();
            // Gunakan status yang valid untuk ENUM: pending, lunas, verifikasi_dokumen, selesai, batal
            $statusDU = ['pending', 'lunas', 'selesai'][rand(0, 2)];
            
            DaftarUlang::create([
                'calon_mahasiswa_id' => $camaba->id,
                'program_studi_id' => $hasilSeleksi->program_studi_diterima_id ?? $camaba->program_studi_id,
                'biaya' => 5000000,
                'biaya_daftar_ulang' => 5000000,
                'biaya_ukt' => rand(3, 8) * 1000000,
                'status' => $statusDU,
                'tanggal_expired' => now()->addDays(14),
                'tanggal_bayar' => $statusDU != 'pending' ? now()->subDays(rand(1, 10)) : null,
            ]);

            // Buat pembayaran daftar ulang
            if ($statusDU != 'pending') {
                PembayaranPmb::create([
                    'calon_mahasiswa_id' => $camaba->id,
                    'jenis_pembayaran' => 'daftar_ulang',
                    'jumlah' => 5000000,
                    'tanggal_bayar' => now()->subDays(rand(1, 10)),
                    'metode_pembayaran' => ['transfer', 'va'][rand(0, 1)],
                    'status' => 'terverifikasi',
                    'verified_at' => now()->subDays(rand(1, 5)),
                ]);
            }
        }

        $this->command->info('✅ Data dummy PMB berhasil dibuat!');
        $this->command->info('   - Periode PMB: 1');
        $this->command->info('   - Gelombang PMB: 2');
        $this->command->info('   - Jalur Seleksi: 3');
        $this->command->info('   - Calon Mahasiswa: ' . count($calonMahasiswas));
        $this->command->info('   - Pembayaran: ' . PembayaranPmb::count());
        $this->command->info('   - Nilai Seleksi: ' . NilaiSeleksi::count());
        $this->command->info('   - Hasil Seleksi: ' . HasilSeleksi::count());
        $this->command->info('   - Daftar Ulang: ' . DaftarUlang::count());
    }
}
