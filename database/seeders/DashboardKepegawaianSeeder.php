<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\CutiPegawai;
use App\Models\PresensiPegawai;
use App\Models\PenugasanMutasi;
use App\Models\KenaikanGajiBerkala;
use App\Models\KenaikanPangkat;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DashboardKepegawaianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Dashboard Kepegawaian data...');

        // Get existing data
        $dosens = Dosen::all();
        $pegawais = Pegawai::all();
        $unitKerjas = UnitKerja::all();

        if ($dosens->isEmpty()) {
            $this->command->warn('No Dosen data found. Creating sample dosen...');
            $this->createSampleDosen();
            $dosens = Dosen::all();
        }

        if ($pegawais->isEmpty()) {
            $this->command->warn('No Pegawai data found. Creating sample pegawai...');
            $this->createSamplePegawai($unitKerjas);
            $pegawais = Pegawai::all();
        }

        // Seed Cuti Pegawai
        $this->seedCutiPegawai($dosens, $pegawais);

        // Seed Presensi Pegawai
        $this->seedPresensiPegawai($dosens, $pegawais);

        // Seed Penugasan Mutasi
        $this->seedPenugasanMutasi($dosens, $pegawais);

        // Seed Kenaikan Gaji Berkala
        $this->seedKenaikanGajiBerkala($dosens, $pegawais);

        // Seed Kenaikan Pangkat
        $this->seedKenaikanPangkat($dosens, $pegawais);

        $this->command->info('Dashboard Kepegawaian data seeded successfully!');
    }

    private function createSampleDosen()
    {
        $programStudis = \App\Models\ProgramStudi::all();
        $prodiId = $programStudis->isNotEmpty() ? $programStudis->random()->id : null;

        $dosenData = [
            ['nama' => 'Dr. Ahmad Fauzi, M.Kom', 'nidn' => '0101018001', 'pendidikan_terakhir' => 'S3', 'tanggal_lahir' => '1980-01-15'],
            ['nama' => 'Prof. Dr. Budi Santoso, M.Sc', 'nidn' => '0102017502', 'pendidikan_terakhir' => 'S3', 'tanggal_lahir' => '1975-02-20'],
            ['nama' => 'Dr. Citra Dewi, M.T', 'nidn' => '0103018203', 'pendidikan_terakhir' => 'S3', 'tanggal_lahir' => '1982-03-10'],
            ['nama' => 'Dian Permata, M.Kom', 'nidn' => '0104019004', 'pendidikan_terakhir' => 'S2', 'tanggal_lahir' => '1990-04-25'],
            ['nama' => 'Eko Prasetyo, M.T', 'nidn' => '0105018505', 'pendidikan_terakhir' => 'S2', 'tanggal_lahir' => '1985-05-30'],
            ['nama' => 'Dr. Fitri Handayani, M.Pd', 'nidn' => '0106017806', 'pendidikan_terakhir' => 'S3', 'tanggal_lahir' => '1978-06-05'],
            ['nama' => 'Gunawan Wibowo, M.Kom', 'nidn' => '0107019207', 'pendidikan_terakhir' => 'S2', 'tanggal_lahir' => '1992-07-12'],
            ['nama' => 'Hendra Wijaya, M.Sc', 'nidn' => '0108018808', 'pendidikan_terakhir' => 'S2', 'tanggal_lahir' => '1988-08-18'],
            ['nama' => 'Dr. Indah Lestari, M.M', 'nidn' => '0109017709', 'pendidikan_terakhir' => 'S3', 'tanggal_lahir' => '1977-09-22'],
            ['nama' => 'Joko Susanto, M.T', 'nidn' => '0110019110', 'pendidikan_terakhir' => 'S2', 'tanggal_lahir' => '1991-10-28'],
            // Add dosen yang akan ulang tahun bulan ini (Januari)
            ['nama' => 'Dr. Kartika Sari, M.Kom', 'nidn' => '0111018011', 'pendidikan_terakhir' => 'S3', 'tanggal_lahir' => '1980-01-05'],
            ['nama' => 'Lukman Hakim, M.T', 'nidn' => '0112019012', 'pendidikan_terakhir' => 'S2', 'tanggal_lahir' => '1990-01-20'],
            // Add dosen yang akan pensiun (lahir sekitar 1966 = 60 tahun di 2026)
            ['nama' => 'Prof. Dr. Maman Sulaiman, M.Sc', 'nidn' => '0113016613', 'pendidikan_terakhir' => 'S3', 'tanggal_lahir' => '1966-03-15'],
            ['nama' => 'Dr. Nurhayati, M.Pd', 'nidn' => '0114016614', 'pendidikan_terakhir' => 'S3', 'tanggal_lahir' => '1966-06-20'],
        ];

        foreach ($dosenData as $data) {
            Dosen::firstOrCreate(
                ['nidn' => $data['nidn']],
                array_merge($data, [
                    'program_studi_id' => $prodiId,
                    'status' => 'Aktif',
                    'email' => strtolower(str_replace([' ', '.', ','], '', explode(',', $data['nama'])[0])) . '@univ.ac.id',
                    'jenis_kelamin' => rand(0, 1) ? 'Laki-laki' : 'Perempuan',
                    'jabatan_fungsional' => collect(['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar'])->random(),
                    'golongan' => collect(['III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b', 'IV/c'])->random(),
                ])
            );
        }
    }

    private function createSamplePegawai($unitKerjas)
    {
        if ($unitKerjas->isEmpty()) {
            $this->command->warn('No UnitKerja found. Creating sample unit kerja...');
            $unitKerjaData = [
                ['nama' => 'Bagian Akademik', 'kode' => 'BAK', 'is_active' => true],
                ['nama' => 'Bagian Keuangan', 'kode' => 'BKU', 'is_active' => true],
                ['nama' => 'Bagian Kepegawaian', 'kode' => 'BKP', 'is_active' => true],
                ['nama' => 'Bagian Umum', 'kode' => 'BUM', 'is_active' => true],
                ['nama' => 'Perpustakaan', 'kode' => 'LIB', 'is_active' => true],
                ['nama' => 'UPT TIK', 'kode' => 'TIK', 'is_active' => true],
            ];
            foreach ($unitKerjaData as $uk) {
                UnitKerja::firstOrCreate(['kode' => $uk['kode']], $uk);
            }
            $unitKerjas = UnitKerja::all();
        }

        $pegawaiData = [
            ['nama' => 'Agus Setiawan', 'nip' => '198501012010011001', 'tanggal_lahir' => '1985-01-01'],
            ['nama' => 'Bambang Hermawan', 'nip' => '198602022011012002', 'tanggal_lahir' => '1986-02-02'],
            ['nama' => 'Cahya Pratama', 'nip' => '198703032012013003', 'tanggal_lahir' => '1987-03-03'],
            ['nama' => 'Dewi Anggraini', 'nip' => '198804042013014004', 'tanggal_lahir' => '1988-04-04'],
            ['nama' => 'Eka Putra', 'nip' => '198905052014015005', 'tanggal_lahir' => '1989-05-05'],
            ['nama' => 'Fajar Nugroho', 'nip' => '199006062015016006', 'tanggal_lahir' => '1990-06-06'],
            ['nama' => 'Gita Ayu Lestari', 'nip' => '199107072016017007', 'tanggal_lahir' => '1991-07-07'],
            ['nama' => 'Hadi Wijaya', 'nip' => '199208082017018008', 'tanggal_lahir' => '1992-08-08'],
            // Pegawai ulang tahun bulan ini (Januari)
            ['nama' => 'Irma Susanti', 'nip' => '199001152018019009', 'tanggal_lahir' => '1990-01-15'],
            ['nama' => 'Jaya Kusuma', 'nip' => '198801252019011010', 'tanggal_lahir' => '1988-01-25'],
            // Pegawai akan pensiun
            ['nama' => 'Karman Suparman', 'nip' => '196605052000011011', 'tanggal_lahir' => '1966-05-05'],
        ];

        foreach ($pegawaiData as $data) {
            Pegawai::firstOrCreate(
                ['nip' => $data['nip']],
                array_merge($data, [
                    'unit_kerja_id' => $unitKerjas->random()->id,
                    'status' => 'Aktif',
                    'email' => strtolower(str_replace(' ', '.', $data['nama'])) . '@univ.ac.id',
                    'jenis_kelamin' => rand(0, 1) ? 'Laki-laki' : 'Perempuan',
                    'jabatan' => collect(['Staff', 'Kepala Bagian', 'Sekretaris', 'Admin', 'Operator'])->random(),
                    'golongan' => collect(['II/a', 'II/b', 'II/c', 'II/d', 'III/a', 'III/b', 'III/c'])->random(),
                ])
            );
        }
    }

    private function seedCutiPegawai($dosens, $pegawais)
    {
        $this->command->info('Seeding Cuti Pegawai...');

        $jenisCuti = ['tahunan', 'sakit', 'melahirkan', 'besar', 'alasan_penting'];
        $statuses = ['diajukan', 'disetujui', 'ditolak'];

        // Buat beberapa pengajuan cuti
        foreach ($dosens->take(5) as $dosen) {
            CutiPegawai::firstOrCreate(
                ['dosen_id' => $dosen->id, 'tanggal_mulai' => now()->subDays(rand(1, 30))->toDateString()],
                [
                    'dosen_id' => $dosen->id,
                    'jenis_cuti' => collect($jenisCuti)->random(),
                    'tanggal_mulai' => now()->subDays(rand(1, 30)),
                    'tanggal_selesai' => now()->addDays(rand(1, 14)),
                    'jumlah_hari' => rand(1, 14),
                    'alasan' => 'Keperluan keluarga',
                    'status' => collect($statuses)->random(),
                ]
            );
        }

        // Buat cuti yang sedang berlangsung
        $dosenCutiAktif = $dosens->skip(5)->first();
        if ($dosenCutiAktif) {
            CutiPegawai::firstOrCreate(
                ['dosen_id' => $dosenCutiAktif->id, 'status' => 'disetujui', 'tanggal_mulai' => now()->subDays(2)->toDateString()],
                [
                    'dosen_id' => $dosenCutiAktif->id,
                    'jenis_cuti' => 'tahunan',
                    'tanggal_mulai' => now()->subDays(2),
                    'tanggal_selesai' => now()->addDays(5),
                    'jumlah_hari' => 7,
                    'alasan' => 'Cuti tahunan',
                    'status' => 'disetujui',
                ]
            );
        }

        // Buat pengajuan cuti pending
        foreach ($dosens->skip(6)->take(3) as $dosen) {
            CutiPegawai::firstOrCreate(
                ['dosen_id' => $dosen->id, 'status' => 'diajukan'],
                [
                    'dosen_id' => $dosen->id,
                    'jenis_cuti' => 'tahunan',
                    'tanggal_mulai' => now()->addDays(rand(7, 30)),
                    'tanggal_selesai' => now()->addDays(rand(31, 45)),
                    'jumlah_hari' => rand(3, 7),
                    'alasan' => 'Pengajuan cuti tahunan',
                    'status' => 'diajukan',
                ]
            );
        }

        // Cuti untuk pegawai
        foreach ($pegawais->take(3) as $pegawai) {
            CutiPegawai::firstOrCreate(
                ['pegawai_id' => $pegawai->id],
                [
                    'pegawai_id' => $pegawai->id,
                    'jenis_cuti' => collect($jenisCuti)->random(),
                    'tanggal_mulai' => now()->addDays(rand(5, 20)),
                    'tanggal_selesai' => now()->addDays(rand(21, 35)),
                    'jumlah_hari' => rand(2, 5),
                    'alasan' => 'Keperluan pribadi',
                    'status' => 'diajukan',
                ]
            );
        }
    }

    private function seedPresensiPegawai($dosens, $pegawais)
    {
        $this->command->info('Seeding Presensi Pegawai...');

        $today = now()->toDateString();
        $statuses = ['Hadir', 'Terlambat', 'Hadir'];

        // Presensi dosen hari ini
        foreach ($dosens->take(8) as $dosen) {
            $status = collect($statuses)->random();
            $jamMasuk = $status == 'Terlambat' ? '08:' . rand(15, 59) . ':00' : '07:' . rand(30, 59) . ':00';

            PresensiPegawai::firstOrCreate(
                ['dosen_id' => $dosen->id, 'tanggal' => $today],
                [
                    'dosen_id' => $dosen->id,
                    'tanggal' => $today,
                    'jam_masuk' => $jamMasuk,
                    'jam_keluar' => rand(0, 1) ? '16:' . rand(0, 59) . ':00' : null,
                    'status' => $status,
                    'keterangan' => $status == 'Terlambat' ? 'Terlambat masuk' : null,
                ]
            );
        }

        // Presensi pegawai hari ini
        foreach ($pegawais->take(6) as $pegawai) {
            $status = collect($statuses)->random();
            $jamMasuk = $status == 'Terlambat' ? '08:' . rand(15, 59) . ':00' : '07:' . rand(30, 59) . ':00';

            PresensiPegawai::firstOrCreate(
                ['pegawai_id' => $pegawai->id, 'tanggal' => $today],
                [
                    'pegawai_id' => $pegawai->id,
                    'tanggal' => $today,
                    'jam_masuk' => $jamMasuk,
                    'jam_keluar' => rand(0, 1) ? '16:' . rand(0, 59) . ':00' : null,
                    'status' => $status,
                    'keterangan' => null,
                ]
            );
        }
    }

    private function seedPenugasanMutasi($dosens, $pegawais)
    {
        $this->command->info('Seeding Penugasan Mutasi...');

        $jenisPenugasan = ['mutasi', 'rotasi', 'promosi', 'penugasan'];
        $unitKerjas = UnitKerja::all();
        
        if ($unitKerjas->count() < 2) {
            $this->command->warn('Not enough unit kerja for mutasi seeding');
            return;
        }

        // Mutasi pending untuk dosen
        foreach ($dosens->take(2) as $index => $dosen) {
            $unitAsal = $unitKerjas->random();
            $unitTujuan = $unitKerjas->where('id', '!=', $unitAsal->id)->random();
            
            PenugasanMutasi::firstOrCreate(
                ['dosen_id' => $dosen->id, 'status' => 'draft'],
                [
                    'dosen_id' => $dosen->id,
                    'jenis' => collect($jenisPenugasan)->random(),
                    'no_sk' => 'SK-MUTASI-' . now()->year . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'tanggal_sk' => now(),
                    'unit_kerja_asal_id' => $unitAsal->id,
                    'unit_kerja_tujuan_id' => $unitTujuan->id,
                    'tmt' => now()->addMonths(1),
                    'alasan' => 'Kebutuhan organisasi',
                    'status' => 'draft',
                ]
            );
        }

        // Mutasi pending untuk pegawai
        foreach ($pegawais->take(2) as $index => $pegawai) {
            $unitAsal = $unitKerjas->random();
            $unitTujuan = $unitKerjas->where('id', '!=', $unitAsal->id)->random();
            
            PenugasanMutasi::firstOrCreate(
                ['pegawai_id' => $pegawai->id, 'status' => 'draft'],
                [
                    'pegawai_id' => $pegawai->id,
                    'jenis' => collect($jenisPenugasan)->random(),
                    'no_sk' => 'SK-MUTASI-PGW-' . now()->year . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'tanggal_sk' => now(),
                    'unit_kerja_asal_id' => $unitAsal->id,
                    'unit_kerja_tujuan_id' => $unitTujuan->id,
                    'tmt' => now()->addMonths(1),
                    'alasan' => 'Pengembangan karir',
                    'status' => 'draft',
                ]
            );
        }
    }

    private function seedKenaikanGajiBerkala($dosens, $pegawais)
    {
        $this->command->info('Seeding Kenaikan Gaji Berkala...');

        $golonganList = ['III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b'];

        // KGB pending
        foreach ($dosens->take(3) as $dosen) {
            $golongan = collect($golonganList)->random();
            KenaikanGajiBerkala::firstOrCreate(
                ['dosen_id' => $dosen->id, 'status' => 'pending'],
                [
                    'dosen_id' => $dosen->id,
                    'no_sk' => 'SK-KGB-' . now()->year . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'tanggal_sk' => now(),
                    'tmt_kgb' => now()->startOfMonth(),
                    'tmt_kgb_berikutnya' => now()->addYears(2)->startOfMonth(),
                    'golongan_ruang' => $golongan,
                    'gaji_pokok_lama' => rand(3000000, 5000000),
                    'gaji_pokok_baru' => rand(3200000, 5500000),
                    'masa_kerja_golongan_tahun' => rand(2, 10),
                    'masa_kerja_golongan_bulan' => rand(0, 11),
                    'status' => 'pending',
                ]
            );
        }

        // KGB bulan ini (sudah disetujui)
        foreach ($dosens->skip(3)->take(2) as $dosen) {
            $golongan = collect($golonganList)->random();
            KenaikanGajiBerkala::firstOrCreate(
                ['dosen_id' => $dosen->id, 'tmt_kgb' => now()->startOfMonth()],
                [
                    'dosen_id' => $dosen->id,
                    'no_sk' => 'SK-KGB-' . now()->year . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'tanggal_sk' => now()->subDays(10),
                    'tmt_kgb' => now()->startOfMonth(),
                    'tmt_kgb_berikutnya' => now()->addYears(2)->startOfMonth(),
                    'golongan_ruang' => $golongan,
                    'gaji_pokok_lama' => rand(3000000, 5000000),
                    'gaji_pokok_baru' => rand(3200000, 5500000),
                    'masa_kerja_golongan_tahun' => rand(2, 10),
                    'masa_kerja_golongan_bulan' => rand(0, 11),
                    'status' => 'disetujui',
                ]
            );
        }

        // KGB untuk pegawai
        foreach ($pegawais->take(2) as $pegawai) {
            $golongan = collect(['II/a', 'II/b', 'II/c', 'II/d', 'III/a'])->random();
            KenaikanGajiBerkala::firstOrCreate(
                ['pegawai_id' => $pegawai->id, 'status' => 'pending'],
                [
                    'pegawai_id' => $pegawai->id,
                    'no_sk' => 'SK-KGB-' . now()->year . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'tanggal_sk' => now(),
                    'tmt_kgb' => now()->startOfMonth(),
                    'tmt_kgb_berikutnya' => now()->addYears(2)->startOfMonth(),
                    'golongan_ruang' => $golongan,
                    'gaji_pokok_lama' => rand(2500000, 4000000),
                    'gaji_pokok_baru' => rand(2700000, 4300000),
                    'masa_kerja_golongan_tahun' => rand(2, 8),
                    'masa_kerja_golongan_bulan' => rand(0, 11),
                    'status' => 'pending',
                ]
            );
        }
    }

    private function seedKenaikanPangkat($dosens, $pegawais)
    {
        $this->command->info('Seeding Kenaikan Pangkat...');

        $pangkatDosen = [
            ['pangkat' => 'Penata Muda', 'golongan' => 'III/a'],
            ['pangkat' => 'Penata Muda Tk. I', 'golongan' => 'III/b'],
            ['pangkat' => 'Penata', 'golongan' => 'III/c'],
            ['pangkat' => 'Penata Tk. I', 'golongan' => 'III/d'],
            ['pangkat' => 'Pembina', 'golongan' => 'IV/a'],
            ['pangkat' => 'Pembina Tk. I', 'golongan' => 'IV/b'],
        ];

        $periodes = ['april', 'oktober'];
        $jenis = ['reguler', 'pilihan', 'fungsional'];

        // Kenaikan pangkat pending
        foreach ($dosens->take(3) as $index => $dosen) {
            $pangkatLama = $pangkatDosen[$index % count($pangkatDosen)];
            $pangkatBaru = $pangkatDosen[min($index + 1, count($pangkatDosen) - 1)];

            KenaikanPangkat::firstOrCreate(
                ['dosen_id' => $dosen->id, 'status' => 'diusulkan'],
                [
                    'dosen_id' => $dosen->id,
                    'periode' => collect($periodes)->random(),
                    'tahun' => now()->year,
                    'pangkat_lama' => $pangkatLama['pangkat'],
                    'golongan_lama' => $pangkatLama['golongan'],
                    'tmt_pangkat_lama' => now()->subYears(4),
                    'pangkat_baru' => $pangkatBaru['pangkat'],
                    'golongan_baru' => $pangkatBaru['golongan'],
                    'tmt_pangkat_baru' => now()->addMonths(3)->startOfMonth(),
                    'jenis' => collect($jenis)->random(),
                    'no_sk' => null,
                    'tanggal_sk' => null,
                    'masa_kerja_tahun' => rand(4, 15),
                    'masa_kerja_bulan' => rand(0, 11),
                    'status' => 'diusulkan',
                ]
            );
        }

        // Kenaikan pangkat bulan ini (sudah disetujui)
        foreach ($dosens->skip(3)->take(2) as $index => $dosen) {
            $pangkatLama = $pangkatDosen[$index % count($pangkatDosen)];
            $pangkatBaru = $pangkatDosen[min($index + 1, count($pangkatDosen) - 1)];

            KenaikanPangkat::firstOrCreate(
                ['dosen_id' => $dosen->id, 'tmt_pangkat_baru' => now()->startOfMonth()],
                [
                    'dosen_id' => $dosen->id,
                    'periode' => 'april',
                    'tahun' => now()->year,
                    'pangkat_lama' => $pangkatLama['pangkat'],
                    'golongan_lama' => $pangkatLama['golongan'],
                    'tmt_pangkat_lama' => now()->subYears(4),
                    'pangkat_baru' => $pangkatBaru['pangkat'],
                    'golongan_baru' => $pangkatBaru['golongan'],
                    'tmt_pangkat_baru' => now()->startOfMonth(),
                    'jenis' => 'reguler',
                    'no_sk' => 'SK-PANGKAT-' . now()->year . '-' . str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT),
                    'tanggal_sk' => now()->subDays(5),
                    'masa_kerja_tahun' => rand(4, 15),
                    'masa_kerja_bulan' => rand(0, 11),
                    'status' => 'disetujui',
                ]
            );
        }

        // Kenaikan pangkat untuk pegawai
        $pangkatPegawai = [
            ['pangkat' => 'Pengatur Muda', 'golongan' => 'II/a'],
            ['pangkat' => 'Pengatur Muda Tk. I', 'golongan' => 'II/b'],
            ['pangkat' => 'Pengatur', 'golongan' => 'II/c'],
            ['pangkat' => 'Pengatur Tk. I', 'golongan' => 'II/d'],
            ['pangkat' => 'Penata Muda', 'golongan' => 'III/a'],
        ];

        foreach ($pegawais->take(2) as $index => $pegawai) {
            $pangkatLama = $pangkatPegawai[$index % count($pangkatPegawai)];
            $pangkatBaru = $pangkatPegawai[min($index + 1, count($pangkatPegawai) - 1)];

            KenaikanPangkat::firstOrCreate(
                ['pegawai_id' => $pegawai->id, 'status' => 'diusulkan'],
                [
                    'pegawai_id' => $pegawai->id,
                    'periode' => collect($periodes)->random(),
                    'tahun' => now()->year,
                    'pangkat_lama' => $pangkatLama['pangkat'],
                    'golongan_lama' => $pangkatLama['golongan'],
                    'tmt_pangkat_lama' => now()->subYears(4),
                    'pangkat_baru' => $pangkatBaru['pangkat'],
                    'golongan_baru' => $pangkatBaru['golongan'],
                    'tmt_pangkat_baru' => now()->addMonths(3)->startOfMonth(),
                    'jenis' => 'reguler',
                    'masa_kerja_tahun' => rand(4, 10),
                    'masa_kerja_bulan' => rand(0, 11),
                    'status' => 'diusulkan',
                ]
            );
        }
    }
}
