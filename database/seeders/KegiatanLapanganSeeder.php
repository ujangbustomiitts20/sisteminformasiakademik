<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisKegiatanLapangan;
use App\Models\PeriodeKegiatanLapangan;
use App\Models\MitraKegiatan;
use App\Models\PendaftaranKegiatanLapangan;
use App\Models\LogKegiatanLapangan;
use App\Models\PenilaianKegiatanLapangan;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\TahunAkademik;
use Carbon\Carbon;

class KegiatanLapanganSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();
        if (!$tahunAkademik) return;

        // 1. Jenis Kegiatan Lapangan (sesuai migration)
        $jenisKegiatan = [
            [
                'kode' => 'PKL',
                'nama' => 'Praktik Kerja Lapangan',
                'deskripsi' => 'Praktik kerja di perusahaan/industri untuk mendapatkan pengalaman kerja nyata.',
                'sks' => 4,
                'durasi_minggu' => 8,
                'semester_minimal' => 6,
                'sks_minimal' => 100,
                'is_active' => true,
            ],
            [
                'kode' => 'MG',
                'nama' => 'Magang',
                'deskripsi' => 'Program magang di perusahaan mitra selama satu semester.',
                'sks' => 20,
                'durasi_minggu' => 16,
                'semester_minimal' => 7,
                'sks_minimal' => 120,
                'is_active' => true,
            ],
            [
                'kode' => 'KKN',
                'nama' => 'Kuliah Kerja Nyata',
                'deskripsi' => 'Pengabdian masyarakat di desa/kelurahan binaan.',
                'sks' => 4,
                'durasi_minggu' => 6,
                'semester_minimal' => 6,
                'sks_minimal' => 100,
                'is_active' => true,
            ],
        ];

        foreach ($jenisKegiatan as $jenis) {
            JenisKegiatanLapangan::create($jenis);
        }

        // 2. Mitra Kegiatan (sesuai migration)
        $mitras = [
            [
                'kode' => 'M001',
                'nama' => 'PT Teknologi Nusantara',
                'alamat' => 'Jl. Sudirman No. 123',
                'kota' => 'Jakarta',
                'provinsi' => 'DKI Jakarta',
                'telepon' => '021-5551234',
                'email' => 'hr@teknologinusantara.co.id',
                'nama_kontak' => 'Ahmad Firmansyah',
                'jabatan_kontak' => 'HR Manager',
                'bidang_usaha' => 'Teknologi Informasi',
                'kuota_mahasiswa' => 10,
                'is_active' => true,
            ],
            [
                'kode' => 'M002',
                'nama' => 'PT Solusi Digital Indonesia',
                'alamat' => 'Jl. Gatot Subroto No. 45',
                'kota' => 'Jakarta',
                'provinsi' => 'DKI Jakarta',
                'telepon' => '021-5554567',
                'email' => 'recruitment@soludigital.com',
                'nama_kontak' => 'Dewi Sartika',
                'jabatan_kontak' => 'Talent Acquisition',
                'bidang_usaha' => 'Software Development',
                'kuota_mahasiswa' => 8,
                'is_active' => true,
            ],
            [
                'kode' => 'M003',
                'nama' => 'Desa Sukamaju',
                'alamat' => 'Kec. Sukamaju',
                'kota' => 'Bandung',
                'provinsi' => 'Jawa Barat',
                'telepon' => '022-1234567',
                'email' => 'desa.sukamaju@example.com',
                'nama_kontak' => 'H. Suparman',
                'jabatan_kontak' => 'Kepala Desa',
                'bidang_usaha' => 'Pemerintahan Desa',
                'kuota_mahasiswa' => 20,
                'is_active' => true,
            ],
            [
                'kode' => 'M004',
                'nama' => 'Kelurahan Mekar Sari',
                'alamat' => 'Kec. Mekar Sari',
                'kota' => 'Bekasi',
                'provinsi' => 'Jawa Barat',
                'telepon' => '021-8887766',
                'email' => 'lurah.mekarsari@example.com',
                'nama_kontak' => 'Ir. Bambang',
                'jabatan_kontak' => 'Lurah',
                'bidang_usaha' => 'Pemerintahan Kelurahan',
                'kuota_mahasiswa' => 15,
                'is_active' => true,
            ],
            [
                'kode' => 'M005',
                'nama' => 'PT Bank Mandiri',
                'alamat' => 'Jl. Jenderal Gatot Subroto Kav. 36-38',
                'kota' => 'Jakarta',
                'provinsi' => 'DKI Jakarta',
                'telepon' => '021-5263600',
                'email' => 'recruitment@bankmandiri.co.id',
                'nama_kontak' => 'Rina Novita',
                'jabatan_kontak' => 'HR Development',
                'bidang_usaha' => 'Perbankan',
                'kuota_mahasiswa' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($mitras as $mitra) {
            MitraKegiatan::create($mitra);
        }

        // 3. Periode Kegiatan (sesuai migration)
        $jenisKegiatans = JenisKegiatanLapangan::all();
        $mitrasAll = MitraKegiatan::all();

        foreach ($jenisKegiatans as $jenis) {
            PeriodeKegiatanLapangan::create([
                'jenis_kegiatan_id' => $jenis->id,
                'tahun_akademik_id' => $tahunAkademik->id,
                'nama' => 'Periode ' . $jenis->nama . ' ' . $tahunAkademik->nama,
                'tanggal_mulai_daftar' => Carbon::now()->subMonth(),
                'tanggal_selesai_daftar' => Carbon::now()->addMonth(),
                'tanggal_mulai_kegiatan' => Carbon::now()->addMonths(2),
                'tanggal_selesai_kegiatan' => Carbon::now()->addMonths(4),
                'kuota' => 30,
                'status' => 'dibuka',
            ]);
        }

        // 4. Pendaftaran Kegiatan (sesuai migration)
        $mahasiswas = Mahasiswa::take(10)->get();
        $dosens = Dosen::take(5)->get();
        $periodes = PeriodeKegiatanLapangan::all();
        $statusList = ['draft', 'diajukan', 'disetujui', 'berlangsung', 'selesai'];

        if ($mahasiswas->isEmpty() || $dosens->isEmpty()) return;

        $counter = 1;
        foreach ($mahasiswas as $index => $mahasiswa) {
            $periode = $periodes->random();
            $mitra = $mitrasAll->random();
            $status = $statusList[$index % 5];

            $pendaftaran = PendaftaranKegiatanLapangan::create([
                'nomor_pendaftaran' => 'PKL-' . date('Y') . '-' . str_pad($counter++, 4, '0', STR_PAD_LEFT),
                'periode_id' => $periode->id,
                'mahasiswa_id' => $mahasiswa->id,
                'mitra_pilihan_1' => $mitra->id,
                'mitra_pilihan_2' => $mitrasAll->random()->id,
                'mitra_pilihan_3' => $mitrasAll->random()->id,
                'mitra_diterima' => in_array($status, ['disetujui', 'berlangsung', 'selesai']) ? $mitra->id : null,
                'dosen_pembimbing_id' => $dosens->random()->id,
                'pembimbing_lapangan' => 'Budi Santoso',
                'jabatan_pembimbing_lapangan' => 'Senior Manager',
                'rencana_kegiatan' => 'Saya sangat tertarik untuk mendapatkan pengalaman praktis di bidang ini.',
                'status' => $status,
                'catatan' => null,
            ]);

            // Log kegiatan untuk pendaftaran yang sudah berlangsung/selesai
            if (in_array($status, ['berlangsung', 'selesai'])) {
                for ($i = 1; $i <= 5; $i++) {
                    LogKegiatanLapangan::create([
                        'pendaftaran_id' => $pendaftaran->id,
                        'tanggal' => Carbon::now()->subDays(30 - ($i * 5)),
                        'jam_mulai' => '08:00',
                        'jam_selesai' => '17:00',
                        'kegiatan' => 'Kegiatan harian ' . $i . ': Mengerjakan tugas yang diberikan supervisor.',
                        'hasil' => 'Tugas selesai dengan baik dan diterima oleh supervisor.',
                        'kendala' => $i % 2 == 0 ? 'Tidak ada kendala yang berarti.' : null,
                        'dokumentasi' => null,
                        'status' => 'disetujui',
                        'komentar_pembimbing' => 'Bagus, lanjutkan.',
                    ]);
                }
            }

            // Penilaian untuk pendaftaran yang sudah selesai
            if ($status == 'selesai') {
                $nilaiKed = rand(70, 100);
                $nilaiKer = rand(70, 100);
                $nilaiIni = rand(70, 100);
                $nilaiKet = rand(70, 100);
                $nilaiHas = rand(70, 100);
                $nilaiLap = rand(70, 100);
                $nilaiPre = rand(70, 100);
                $nilaiAkhir = ($nilaiKed + $nilaiKer + $nilaiIni + $nilaiKet + $nilaiHas + $nilaiLap + $nilaiPre) / 7;
                
                $grade = $nilaiAkhir >= 85 ? 'A' : ($nilaiAkhir >= 70 ? 'B' : ($nilaiAkhir >= 55 ? 'C' : ($nilaiAkhir >= 40 ? 'D' : 'E')));

                PenilaianKegiatanLapangan::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'jenis_penilai' => 'dosen',
                    'nilai_kedisiplinan' => $nilaiKed,
                    'nilai_kerjasama' => $nilaiKer,
                    'nilai_inisiatif' => $nilaiIni,
                    'nilai_keterampilan' => $nilaiKet,
                    'nilai_hasil_kerja' => $nilaiHas,
                    'nilai_laporan' => $nilaiLap,
                    'nilai_presentasi' => $nilaiPre,
                    'nilai_akhir' => $nilaiAkhir,
                    'grade' => $grade,
                    'catatan' => 'Mahasiswa menunjukkan kinerja yang baik selama kegiatan.',
                    'tanggal_penilaian' => Carbon::now(),
                ]);

                PenilaianKegiatanLapangan::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'jenis_penilai' => 'lapangan',
                    'nilai_kedisiplinan' => rand(70, 100),
                    'nilai_kerjasama' => rand(70, 100),
                    'nilai_inisiatif' => rand(70, 100),
                    'nilai_keterampilan' => rand(70, 100),
                    'nilai_hasil_kerja' => rand(70, 100),
                    'catatan' => 'Mahasiswa aktif dan mampu bekerja sesuai standar perusahaan.',
                    'tanggal_penilaian' => Carbon::now(),
                ]);
            }
        }
    }
}
