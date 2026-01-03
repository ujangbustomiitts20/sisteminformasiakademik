<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CutiPegawai;
use App\Models\SaldoCuti;
use App\Models\PresensiPegawai;
use App\Models\RekapPresensi;
use App\Models\SettingJamKerja;
use App\Models\PenugasanMutasi;
use App\Models\KenaikanGajiBerkala;
use App\Models\KenaikanPangkat;
use App\Models\Pensiun;
use App\Models\Dosen;
use App\Models\Pegawai;
use App\Models\User;
use Carbon\Carbon;

class SdmDummySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding SDM dummy data...');

        // Ambil data dosen dan pegawai
        $dosens = Dosen::where('status', 'Aktif')->get();
        $pegawais = Pegawai::where('status', 'Aktif')->get();
        $admin = User::where('role', 'admin')->first();

        if ($dosens->isEmpty()) {
            $this->command->warn('Tidak ada data dosen aktif. Skip SDM seeding.');
            return;
        }

        $tahun = date('Y');

        // =====================
        // 0. SETTING JAM KERJA
        // =====================
        $this->command->info('Creating setting jam kerja...');
        
        SettingJamKerja::firstOrCreate(
            ['nama_setting' => 'Jam Kerja Reguler'],
            [
                'jam_masuk' => '08:00',
                'jam_keluar' => '16:00',
                'toleransi_terlambat' => 15,
                'is_active' => true,
            ]
        );

        SettingJamKerja::firstOrCreate(
            ['nama_setting' => 'Jam Kerja Dosen'],
            [
                'jam_masuk' => '07:30',
                'jam_keluar' => '15:30',
                'toleransi_terlambat' => 30,
                'is_active' => false,
            ]
        );

        // =====================
        // 1. SALDO CUTI
        // =====================
        $this->command->info('Creating saldo cuti...');
        
        foreach ($dosens as $dosen) {
            SaldoCuti::firstOrCreate(
                ['dosen_id' => $dosen->id, 'tahun' => $tahun],
                [
                    'pegawai_id' => null,
                    'jatah_cuti' => 12,
                    'cuti_digunakan' => rand(0, 5),
                ]
            );
        }

        foreach ($pegawais as $pegawai) {
            SaldoCuti::firstOrCreate(
                ['pegawai_id' => $pegawai->id, 'tahun' => $tahun],
                [
                    'dosen_id' => null,
                    'jatah_cuti' => 12,
                    'cuti_digunakan' => rand(0, 5),
                ]
            );
        }

        // =====================
        // 2. CUTI PEGAWAI
        // =====================
        $this->command->info('Creating cuti pegawai...');
        
        $jenisCuti = array_keys(CutiPegawai::JENIS_CUTI);
        $statusCuti = ['diajukan', 'disetujui', 'ditolak', 'disetujui_atasan'];
        $counter = CutiPegawai::count();

        // Cuti untuk dosen
        foreach ($dosens->take(8) as $dosen) {
            $counter++;
            $tanggalMulai = Carbon::now()->subDays(rand(1, 60));
            $jumlahHari = rand(1, 7);
            
            CutiPegawai::create([
                'dosen_id' => $dosen->id,
                'pegawai_id' => null,
                'no_pengajuan' => 'CUTI' . date('Ymd') . str_pad($counter, 4, '0', STR_PAD_LEFT),
                'jenis_cuti' => $jenisCuti[array_rand($jenisCuti)],
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalMulai->copy()->addDays($jumlahHari - 1),
                'jumlah_hari' => $jumlahHari,
                'alasan' => 'Keperluan keluarga - ' . fake()->sentence(),
                'alamat_selama_cuti' => fake()->address(),
                'no_telepon_selama_cuti' => fake()->phoneNumber(),
                'status' => $statusCuti[array_rand($statusCuti)],
                'sisa_cuti_sebelum' => 12,
            ]);
        }

        // Cuti untuk pegawai
        foreach ($pegawais->take(5) as $pegawai) {
            $counter++;
            $tanggalMulai = Carbon::now()->subDays(rand(1, 60));
            $jumlahHari = rand(1, 5);
            
            CutiPegawai::create([
                'dosen_id' => null,
                'pegawai_id' => $pegawai->id,
                'no_pengajuan' => 'CUTI' . date('Ymd') . str_pad($counter, 4, '0', STR_PAD_LEFT),
                'jenis_cuti' => $jenisCuti[array_rand($jenisCuti)],
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalMulai->copy()->addDays($jumlahHari - 1),
                'jumlah_hari' => $jumlahHari,
                'alasan' => 'Urusan pribadi - ' . fake()->sentence(),
                'alamat_selama_cuti' => fake()->address(),
                'no_telepon_selama_cuti' => fake()->phoneNumber(),
                'status' => $statusCuti[array_rand($statusCuti)],
                'sisa_cuti_sebelum' => 12,
            ]);
        }

        // =====================
        // 3. PRESENSI PEGAWAI
        // =====================
        $this->command->info('Creating presensi pegawai...');
        
        $statusPresensi = ['hadir', 'terlambat', 'hadir', 'hadir', 'izin', 'sakit']; // More weight to hadir

        // Presensi 30 hari terakhir untuk beberapa dosen
        foreach ($dosens->take(10) as $dosen) {
            for ($i = 1; $i <= 20; $i++) {
                $tanggal = Carbon::now()->subDays($i);
                
                // Skip weekend
                if ($tanggal->isWeekend()) continue;
                
                $status = $statusPresensi[array_rand($statusPresensi)];
                $jamMasuk = $status == 'terlambat' 
                    ? sprintf('%02d:%02d:00', rand(8, 9), rand(1, 59)) 
                    : sprintf('%02d:%02d:00', rand(6, 7), rand(0, 59));
                
                PresensiPegawai::firstOrCreate(
                    [
                        'dosen_id' => $dosen->id,
                        'tanggal' => $tanggal->format('Y-m-d'),
                    ],
                    [
                        'pegawai_id' => null,
                        'jam_masuk' => in_array($status, ['hadir', 'terlambat']) ? $jamMasuk : null,
                        'jam_keluar' => in_array($status, ['hadir', 'terlambat']) ? sprintf('%02d:%02d:00', rand(16, 17), rand(0, 59)) : null,
                        'status' => $status,
                        'keterangan' => $status != 'hadir' ? fake()->sentence() : null,
                        'is_manual' => rand(0, 1),
                        'diinput_oleh' => $admin?->id,
                    ]
                );
            }
        }

        // Presensi pegawai
        foreach ($pegawais->take(5) as $pegawai) {
            for ($i = 1; $i <= 15; $i++) {
                $tanggal = Carbon::now()->subDays($i);
                if ($tanggal->isWeekend()) continue;
                
                $status = $statusPresensi[array_rand($statusPresensi)];
                
                PresensiPegawai::firstOrCreate(
                    [
                        'pegawai_id' => $pegawai->id,
                        'tanggal' => $tanggal->format('Y-m-d'),
                    ],
                    [
                        'dosen_id' => null,
                        'jam_masuk' => in_array($status, ['hadir', 'terlambat']) ? sprintf('%02d:%02d:00', rand(7, 8), rand(0, 59)) : null,
                        'jam_keluar' => in_array($status, ['hadir', 'terlambat']) ? sprintf('%02d:%02d:00', rand(16, 17), rand(0, 59)) : null,
                        'status' => $status,
                        'keterangan' => $status != 'hadir' ? fake()->sentence() : null,
                        'is_manual' => true,
                        'diinput_oleh' => $admin?->id,
                    ]
                );
            }
        }

        // =====================
        // 4. PENUGASAN & MUTASI
        // =====================
        $this->command->info('Creating penugasan & mutasi...');
        
        $jenisPenugasan = array_keys(PenugasanMutasi::JENIS);
        $statusPenugasan = array_keys(PenugasanMutasi::STATUS);

        foreach ($dosens->take(6) as $index => $dosen) {
            $tanggalSk = Carbon::now()->subMonths(rand(1, 12));
            $tmt = $tanggalSk->copy()->addDays(rand(7, 30));
            
            PenugasanMutasi::create([
                'dosen_id' => $dosen->id,
                'pegawai_id' => null,
                'jenis' => $jenisPenugasan[array_rand($jenisPenugasan)],
                'no_sk' => sprintf('SK/%d/PM/%04d', date('Y'), $index + 1),
                'tanggal_sk' => $tanggalSk,
                'tmt' => $tmt,
                'tanggal_selesai' => $tmt->copy()->addMonths(rand(6, 24)),
                'jabatan_asal' => fake()->jobTitle(),
                'jabatan_tujuan' => fake()->jobTitle(),
                'nama_tugas' => 'Penugasan ' . fake()->words(3, true),
                'deskripsi_tugas' => fake()->paragraph(),
                'lokasi_penugasan' => fake()->city(),
                'alasan' => fake()->sentence(),
                'status' => $statusPenugasan[array_rand($statusPenugasan)],
                'created_by' => $admin?->id,
            ]);
        }

        // =====================
        // 5. KENAIKAN GAJI BERKALA (KGB)
        // =====================
        $this->command->info('Creating KGB data...');
        
        $statusKgb = array_keys(KenaikanGajiBerkala::STATUS);
        $golongan = ['III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b'];

        foreach ($dosens->take(8) as $index => $dosen) {
            $tmtKgb = Carbon::now()->subMonths(rand(1, 24));
            $gajiLama = rand(3, 7) * 1000000;
            
            KenaikanGajiBerkala::create([
                'dosen_id' => $dosen->id,
                'pegawai_id' => null,
                'no_sk' => sprintf('SK/%d/KGB/%04d', date('Y'), $index + 1),
                'tanggal_sk' => $tmtKgb->copy()->subDays(rand(7, 30)),
                'tmt_kgb' => $tmtKgb,
                'tmt_kgb_berikutnya' => $tmtKgb->copy()->addYears(2),
                'golongan_ruang' => $golongan[array_rand($golongan)],
                'masa_kerja_golongan_tahun' => rand(2, 10),
                'masa_kerja_golongan_bulan' => rand(0, 11),
                'gaji_pokok_lama' => $gajiLama,
                'gaji_pokok_baru' => $gajiLama + rand(100000, 300000),
                'masa_kerja_total_tahun' => rand(5, 25),
                'masa_kerja_total_bulan' => rand(0, 11),
                'status' => $statusKgb[array_rand($statusKgb)],
                'is_otomatis' => rand(0, 1),
                'diproses_oleh' => $admin?->id,
                'tanggal_diproses' => now(),
            ]);
        }

        // =====================
        // 6. KENAIKAN PANGKAT
        // =====================
        $this->command->info('Creating kenaikan pangkat data...');
        
        $statusKp = array_keys(KenaikanPangkat::STATUS);
        $jenisKp = array_keys(KenaikanPangkat::JENIS);
        $periodeKp = array_keys(KenaikanPangkat::PERIODE);
        $pangkat = array_keys(KenaikanPangkat::PANGKAT);

        foreach ($dosens->take(6) as $index => $dosen) {
            $pangkatLamaIdx = array_rand($pangkat);
            $pangkatBaruIdx = min($pangkatLamaIdx + 1, count($pangkat) - 1);
            $tmtLama = Carbon::now()->subYears(rand(2, 6));
            
            KenaikanPangkat::create([
                'dosen_id' => $dosen->id,
                'pegawai_id' => null,
                'periode' => $periodeKp[array_rand($periodeKp)],
                'tahun' => date('Y'),
                'pangkat_lama' => KenaikanPangkat::PANGKAT[$pangkat[$pangkatLamaIdx]],
                'golongan_lama' => $pangkat[$pangkatLamaIdx],
                'tmt_pangkat_lama' => $tmtLama,
                'pangkat_baru' => KenaikanPangkat::PANGKAT[$pangkat[$pangkatBaruIdx]],
                'golongan_baru' => $pangkat[$pangkatBaruIdx],
                'tmt_pangkat_baru' => Carbon::now()->subMonths(rand(1, 6)),
                'jenis' => $jenisKp[array_rand($jenisKp)],
                'no_sk' => sprintf('SK/%d/KP/%04d', date('Y'), $index + 1),
                'tanggal_sk' => Carbon::now()->subMonths(rand(1, 12)),
                'pejabat_penandatangan' => 'Rektor ' . fake()->name(),
                'masa_kerja_tahun' => rand(5, 25),
                'masa_kerja_bulan' => rand(0, 11),
                'pendidikan_terakhir' => ['S1', 'S2', 'S3'][array_rand(['S1', 'S2', 'S3'])],
                'angka_kredit' => rand(100, 500) + rand(0, 99) / 100,
                'penilaian_kinerja' => ['Baik', 'Sangat Baik', 'Amat Baik'][array_rand(['Baik', 'Sangat Baik', 'Amat Baik'])],
                'status' => $statusKp[array_rand($statusKp)],
            ]);
        }

        // =====================
        // 7. PENSIUN
        // =====================
        $this->command->info('Creating pensiun data...');
        
        $statusPensiun = array_keys(Pensiun::STATUS);
        $jenisPensiun = array_keys(Pensiun::JENIS_PENSIUN);

        foreach ($dosens->take(4) as $index => $dosen) {
            $tanggalLahir = Carbon::now()->subYears(rand(55, 65))->subMonths(rand(0, 11));
            $usiaBup = [58, 60, 65, 70][array_rand([58, 60, 65, 70])];
            $tanggalBup = $tanggalLahir->copy()->addYears($usiaBup);
            $gajiPokok = rand(5, 10) * 1000000;
            
            Pensiun::create([
                'dosen_id' => $dosen->id,
                'pegawai_id' => null,
                'jenis_pensiun' => $jenisPensiun[array_rand($jenisPensiun)],
                'usia_bup' => $usiaBup,
                'tanggal_lahir' => $tanggalLahir,
                'tanggal_pensiun' => $tanggalBup,
                'tanggal_bup' => $tanggalBup,
                'pangkat_terakhir' => KenaikanPangkat::PANGKAT[$pangkat[array_rand($pangkat)]],
                'golongan_terakhir' => $pangkat[array_rand($pangkat)],
                'jabatan_terakhir' => ['Lektor', 'Lektor Kepala', 'Guru Besar'][array_rand(['Lektor', 'Lektor Kepala', 'Guru Besar'])],
                'masa_kerja_tahun' => rand(20, 35),
                'masa_kerja_bulan' => rand(0, 11),
                'no_sk' => sprintf('SK/%d/PSN/%04d', date('Y'), $index + 1),
                'tanggal_sk' => Carbon::now()->subMonths(rand(1, 6)),
                'pejabat_penandatangan' => 'Rektor ' . fake()->name(),
                'gaji_pokok_terakhir' => $gajiPokok,
                'dana_pensiun' => $gajiPokok * 0.75,
                'no_taspen' => 'TSP' . str_pad(rand(1, 999999), 10, '0', STR_PAD_LEFT),
                'alamat_pensiun' => fake()->address(),
                'no_telepon_pensiun' => fake()->phoneNumber(),
                'no_rekening_pensiun' => str_pad(rand(1, 9999999999), 15, '0', STR_PAD_LEFT),
                'nama_bank' => ['BRI', 'BNI', 'Mandiri', 'BTN'][array_rand(['BRI', 'BNI', 'Mandiri', 'BTN'])],
                'status' => $statusPensiun[array_rand($statusPensiun)],
                'sudah_serah_terima' => rand(0, 1),
                'diproses_oleh' => $admin?->id,
            ]);
        }

        // =====================
        // 8. REKAP PRESENSI BULANAN
        // =====================
        $this->command->info('Creating rekap presensi...');
        
        $bulan = date('n');
        
        foreach ($dosens->take(15) as $dosen) {
            RekapPresensi::firstOrCreate(
                ['dosen_id' => $dosen->id, 'bulan' => $bulan, 'tahun' => $tahun],
                [
                    'pegawai_id' => null,
                    'total_hari_kerja' => 22,
                    'hadir' => rand(15, 20),
                    'terlambat' => rand(0, 5),
                    'sakit' => rand(0, 2),
                    'izin' => rand(0, 2),
                    'cuti' => rand(0, 1),
                    'alpha' => rand(0, 2),
                    'dinas_luar' => rand(0, 3),
                ]
            );
        }

        foreach ($pegawais->take(5) as $pegawai) {
            RekapPresensi::firstOrCreate(
                ['pegawai_id' => $pegawai->id, 'bulan' => $bulan, 'tahun' => $tahun],
                [
                    'dosen_id' => null,
                    'total_hari_kerja' => 22,
                    'hadir' => rand(16, 20),
                    'terlambat' => rand(0, 4),
                    'sakit' => rand(0, 2),
                    'izin' => rand(0, 1),
                    'cuti' => rand(0, 1),
                    'alpha' => rand(0, 1),
                    'dinas_luar' => rand(0, 2),
                ]
            );
        }

        $this->command->info('SDM dummy data seeded successfully!');
        $this->command->info('- Setting Jam Kerja: ' . SettingJamKerja::count() . ' records');
        $this->command->info('- Saldo Cuti: ' . SaldoCuti::count() . ' records');
        $this->command->info('- Cuti Pegawai: ' . CutiPegawai::count() . ' records');
        $this->command->info('- Presensi: ' . PresensiPegawai::count() . ' records');
        $this->command->info('- Rekap Presensi: ' . RekapPresensi::count() . ' records');
        $this->command->info('- Penugasan/Mutasi: ' . PenugasanMutasi::count() . ' records');
        $this->command->info('- KGB: ' . KenaikanGajiBerkala::count() . ' records');
        $this->command->info('- Kenaikan Pangkat: ' . KenaikanPangkat::count() . ' records');
        $this->command->info('- Pensiun: ' . Pensiun::count() . ' records');
    }
}
