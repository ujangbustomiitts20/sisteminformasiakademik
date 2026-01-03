<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Models\MataKuliah;
use App\Models\JadwalKuliah;
use App\Models\Ruangan;
use App\Models\Krs;
use App\Models\CutiAkademik;
use App\Models\TugasAkhir;
use App\Models\PengajuanKonversi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class KaprodiApprovalDummySeeder extends Seeder
{
    /**
     * Run the database seeds untuk data approval kaprodi
     */
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            // Pastikan ada prodi, tahun akademik, dosen
            $prodi = ProgramStudi::first();
            if (!$prodi) {
                $this->command->warn('Program Studi tidak ditemukan. Jalankan DashboardDummySeeder terlebih dahulu.');
                return;
            }

            $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();
            if (!$tahunAkademik) {
                $this->command->warn('Tahun Akademik aktif tidak ditemukan. Jalankan DashboardDummySeeder terlebih dahulu.');
                return;
            }

            // Pastikan ada dosen
            $dosens = Dosen::where('program_studi_id', $prodi->id)->get();
            if ($dosens->isEmpty()) {
                $this->command->warn('Dosen tidak ditemukan. Jalankan DashboardDummySeeder terlebih dahulu.');
                return;
            }

            // Pastikan ada mahasiswa
            $mahasiswas = Mahasiswa::where('program_studi_id', $prodi->id)->get();
            if ($mahasiswas->isEmpty()) {
                $this->command->warn('Mahasiswa tidak ditemukan. Jalankan DashboardDummySeeder terlebih dahulu.');
                return;
            }

            // Pastikan ada ruangan
            $ruangan = Ruangan::first();
            if (!$ruangan) {
                $ruangan = Ruangan::create([
                    'kode' => 'R101',
                    'nama' => 'Ruang Kuliah 101',
                    'kapasitas' => 40,
                    'gedung' => 'Gedung A',
                    'lantai' => 1,
                    'status' => 'Aktif',
                ]);
            }

            // Pastikan ada mata kuliah
            $mataKuliahs = MataKuliah::where('program_studi_id', $prodi->id)->get();
            if ($mataKuliahs->isEmpty()) {
                // Buat mata kuliah
                $mkData = [
                    ['kode' => 'MK001', 'nama' => 'Algoritma dan Pemrograman', 'sks_teori' => 2, 'sks_praktik' => 1, 'semester' => 1],
                    ['kode' => 'MK002', 'nama' => 'Basis Data', 'sks_teori' => 2, 'sks_praktik' => 1, 'semester' => 2],
                    ['kode' => 'MK003', 'nama' => 'Struktur Data', 'sks_teori' => 3, 'sks_praktik' => 0, 'semester' => 3],
                    ['kode' => 'MK004', 'nama' => 'Pemrograman Web', 'sks_teori' => 2, 'sks_praktik' => 1, 'semester' => 4],
                ];
                foreach ($mkData as $mk) {
                    MataKuliah::create([
                        'kode' => $mk['kode'],
                        'nama' => $mk['nama'],
                        'sks_teori' => $mk['sks_teori'],
                        'sks_praktik' => $mk['sks_praktik'],
                        'semester' => $mk['semester'],
                        'program_studi_id' => $prodi->id,
                        'status' => 'Aktif',
                    ]);
                }
                $mataKuliahs = MataKuliah::where('program_studi_id', $prodi->id)->get();
            }

            // Buat jadwal kuliah jika belum ada
            $jadwals = JadwalKuliah::where('tahun_akademik_id', $tahunAkademik->id)->get();
            if ($jadwals->isEmpty()) {
                $haris = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                foreach ($mataKuliahs as $i => $mk) {
                    JadwalKuliah::create([
                        'mata_kuliah_id' => $mk->id,
                        'dosen_id' => $dosens[$i % $dosens->count()]->id,
                        'ruangan_id' => $ruangan->id,
                        'tahun_akademik_id' => $tahunAkademik->id,
                        'kelas' => 'A',
                        'hari' => $haris[$i % 5],
                        'jam_mulai' => '08:00:00',
                        'jam_selesai' => '10:30:00',
                        'kuota' => 40,
                    ]);
                }
                $jadwals = JadwalKuliah::where('tahun_akademik_id', $tahunAkademik->id)->get();
            }

            $this->command->info('Creating KRS approval data...');
            
            // Buat KRS dengan status pending untuk diapprove
            foreach ($mahasiswas->take(3) as $mhs) {
                foreach ($jadwals->take(2) as $jadwal) {
                    Krs::firstOrCreate(
                        [
                            'mahasiswa_id' => $mhs->id,
                            'jadwal_kuliah_id' => $jadwal->id,
                            'tahun_akademik_id' => $tahunAkademik->id,
                        ],
                        [
                            'status' => 'pending',
                            'tanggal_pengajuan' => now(),
                        ]
                    );
                }
            }

            $this->command->info('Creating Cuti Akademik approval data...');
            
            // Buat Cuti Akademik dengan status Pending
            $counter = CutiAkademik::count();
            foreach ($mahasiswas->take(2) as $mhs) {
                $counter++;
                CutiAkademik::firstOrCreate(
                    [
                        'mahasiswa_id' => $mhs->id,
                        'tahun_akademik_id' => $tahunAkademik->id,
                    ],
                    [
                        'nomor_surat' => 'CUTI' . date('Ymd') . str_pad($counter, 4, '0', STR_PAD_LEFT),
                        'alasan' => 'Keuangan',
                        'keterangan' => 'Mengajukan cuti karena alasan keuangan',
                        'tanggal_mulai' => now(),
                        'tanggal_selesai' => now()->addMonths(6),
                        'jumlah_semester' => 1,
                        'status' => 'Pending',
                    ]
                );
            }

            $this->command->info('Creating Tugas Akhir approval data...');
            
            // Buat Tugas Akhir dengan status diajukan untuk diapprove
            $counter = TugasAkhir::count();
            foreach ($mahasiswas->take(2) as $mhs) {
                $counter++;
                TugasAkhir::firstOrCreate(
                    [
                        'mahasiswa_id' => $mhs->id,
                    ],
                    [
                        'judul' => 'Sistem Informasi Manajemen ' . $mhs->nama,
                        'abstrak' => 'Penelitian tentang pengembangan sistem informasi untuk meningkatkan efisiensi',
                        'bidang_kajian' => 'Sistem Informasi',
                        'pembimbing_1_id' => $dosens->first()->id,
                        'pembimbing_2_id' => $dosens->count() > 1 ? $dosens->skip(1)->first()->id : $dosens->first()->id,
                        'status' => 'diajukan',
                        'tanggal_pengajuan' => now(),
                        'tahun_akademik_id' => $tahunAkademik->id,
                    ]
                );
            }

            $this->command->info('Creating Pengajuan Konversi data...');
            
            // Buat Pengajuan Konversi Nilai dengan status diajukan
            $counter = PengajuanKonversi::count();
            foreach ($mahasiswas->take(1) as $mhs) {
                $counter++;
                PengajuanKonversi::firstOrCreate(
                    [
                        'mahasiswa_id' => $mhs->id,
                    ],
                    [
                        'universitas_asal' => 'Universitas Lain',
                        'program_studi_asal' => 'Teknik Informatika',
                        'nim_asal' => '2020' . str_pad($counter, 6, '0', STR_PAD_LEFT),
                        'tahun_masuk_asal' => 2020,
                        'catatan' => 'Pengajuan konversi nilai dari universitas sebelumnya',
                        'status' => 'diajukan',
                    ]
                );
            }

            DB::commit();

            $this->command->info('');
            $this->command->info('=== Data Approval Kaprodi Berhasil Dibuat ===');
            $this->command->info('KRS Pending: ' . Krs::where('status', 'pending')->count());
            $this->command->info('Cuti Pending: ' . CutiAkademik::where('status', 'Pending')->count());
            $this->command->info('Tugas Akhir Diajukan: ' . TugasAkhir::where('status', 'diajukan')->count());
            $this->command->info('Konversi Nilai Diajukan: ' . PengajuanKonversi::where('status', 'diajukan')->count());
            $this->command->info('');
            $this->command->info('Login sebagai Kaprodi untuk melihat dan melakukan approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
