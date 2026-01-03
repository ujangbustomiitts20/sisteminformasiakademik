<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnitKerja;
use App\Models\Pegawai;
use App\Models\RiwayatPendidikan;
use App\Models\RiwayatJabatan;
use App\Models\RiwayatPangkat;
use App\Models\RiwayatPelatihan;
use App\Models\DokumenKepegawaian;
use Carbon\Carbon;

class KepegawaianSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Unit Kerja...');
        $this->seedUnitKerja();
        
        $this->command->info('Seeding Pegawai (Tendik)...');
        $this->seedPegawai();
        
        $this->command->info('Seeding Riwayat Kepegawaian...');
        $this->seedRiwayatKepegawaian();
        
        $this->command->info('Kepegawaian seeding completed!');
    }

    private function seedUnitKerja(): void
    {
        // Unit Kerja Utama (Level 1)
        $unitUtama = [
            ['kode' => 'REKTORAT', 'nama' => 'Rektorat', 'deskripsi' => 'Kantor Pusat Rektorat'],
            ['kode' => 'BAAK', 'nama' => 'Biro Administrasi Akademik dan Kemahasiswaan', 'deskripsi' => 'Biro yang menangani administrasi akademik'],
            ['kode' => 'BAU', 'nama' => 'Biro Administrasi Umum', 'deskripsi' => 'Biro yang menangani administrasi umum dan kepegawaian'],
            ['kode' => 'BKEU', 'nama' => 'Biro Keuangan', 'deskripsi' => 'Biro yang menangani keuangan'],
            ['kode' => 'LPPM', 'nama' => 'Lembaga Penelitian dan Pengabdian Masyarakat', 'deskripsi' => 'Lembaga penelitian'],
            ['kode' => 'LPM', 'nama' => 'Lembaga Penjaminan Mutu', 'deskripsi' => 'Lembaga penjaminan mutu internal'],
            ['kode' => 'UPT-TIK', 'nama' => 'UPT Teknologi Informasi dan Komunikasi', 'deskripsi' => 'Unit pengelola TIK'],
            ['kode' => 'UPT-PERPUS', 'nama' => 'UPT Perpustakaan', 'deskripsi' => 'Unit pengelola perpustakaan'],
            ['kode' => 'UPT-BAHASA', 'nama' => 'UPT Pengembangan Bahasa', 'deskripsi' => 'Unit pengembangan bahasa'],
        ];

        $createdUnits = [];
        foreach ($unitUtama as $unit) {
            $createdUnits[$unit['kode']] = UnitKerja::create([
                'kode' => $unit['kode'],
                'nama' => $unit['nama'],
                'deskripsi' => $unit['deskripsi'],
                'is_active' => true,
            ]);
        }

        // Sub Unit (Level 2)
        $subUnits = [
            // Sub unit BAAK
            ['kode' => 'BAAK-AKD', 'nama' => 'Bagian Akademik', 'parent' => 'BAAK', 'deskripsi' => 'Bagian pengelolaan akademik'],
            ['kode' => 'BAAK-KMH', 'nama' => 'Bagian Kemahasiswaan', 'parent' => 'BAAK', 'deskripsi' => 'Bagian pengelolaan kemahasiswaan'],
            ['kode' => 'BAAK-REG', 'nama' => 'Bagian Registrasi', 'parent' => 'BAAK', 'deskripsi' => 'Bagian pendaftaran dan registrasi'],
            
            // Sub unit BAU
            ['kode' => 'BAU-SDM', 'nama' => 'Bagian SDM dan Kepegawaian', 'parent' => 'BAU', 'deskripsi' => 'Bagian pengelolaan SDM'],
            ['kode' => 'BAU-SARPRAS', 'nama' => 'Bagian Sarana dan Prasarana', 'parent' => 'BAU', 'deskripsi' => 'Bagian pengelolaan sarana prasarana'],
            ['kode' => 'BAU-HUMAS', 'nama' => 'Bagian Humas dan Protokoler', 'parent' => 'BAU', 'deskripsi' => 'Bagian hubungan masyarakat'],
            ['kode' => 'BAU-RT', 'nama' => 'Bagian Rumah Tangga', 'parent' => 'BAU', 'deskripsi' => 'Bagian rumah tangga'],
            
            // Sub unit BKEU
            ['kode' => 'BKEU-AKT', 'nama' => 'Bagian Akuntansi', 'parent' => 'BKEU', 'deskripsi' => 'Bagian akuntansi dan pelaporan'],
            ['kode' => 'BKEU-PMB', 'nama' => 'Bagian Pembayaran', 'parent' => 'BKEU', 'deskripsi' => 'Bagian pembayaran mahasiswa'],
            ['kode' => 'BKEU-ANGGARAN', 'nama' => 'Bagian Anggaran', 'parent' => 'BKEU', 'deskripsi' => 'Bagian perencanaan anggaran'],
        ];

        foreach ($subUnits as $unit) {
            UnitKerja::create([
                'kode' => $unit['kode'],
                'nama' => $unit['nama'],
                'parent_id' => $createdUnits[$unit['parent']]->id,
                'deskripsi' => $unit['deskripsi'],
                'is_active' => true,
            ]);
        }
    }

    private function seedPegawai(): void
    {
        $unitKerja = UnitKerja::all()->keyBy('kode');
        
        $pegawaiData = [
            // Staff BAAK
            [
                'nip' => '198501152010011001',
                'nik' => '3201150115850001',
                'nama' => 'Ahmad Fauzi, S.Kom.',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1985-01-15',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'status_pernikahan' => 'Menikah',
                'email' => 'ahmad.fauzi@university.ac.id',
                'no_hp' => '081234567801',
                'alamat' => 'Jl. Merdeka No. 10, Bandung',
                'unit_kerja_id' => $unitKerja['BAAK']->id ?? null,
                'jabatan' => 'Kepala BAAK',
                'jenis_pegawai' => 'PNS',
                'golongan' => 'III/d',
                'pangkat' => 'Penata Tk.I',
                'status' => 'Aktif',
                'tmt_pegawai' => '2010-01-01',
            ],
            [
                'nip' => '199003202015012002',
                'nik' => '3201200320900002',
                'nama' => 'Siti Nurhaliza, A.Md.',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1990-03-20',
                'jenis_kelamin' => 'P',
                'agama' => 'Islam',
                'status_pernikahan' => 'Belum Menikah',
                'email' => 'siti.nurhaliza@university.ac.id',
                'no_hp' => '081234567802',
                'alamat' => 'Jl. Sudirman No. 25, Jakarta',
                'unit_kerja_id' => $unitKerja['BAAK-AKD']->id ?? $unitKerja['BAAK']->id ?? null,
                'jabatan' => 'Staff Akademik',
                'jenis_pegawai' => 'PNS',
                'golongan' => 'II/c',
                'pangkat' => 'Pengatur',
                'status' => 'Aktif',
                'tmt_pegawai' => '2015-01-01',
            ],
            [
                'nip' => '199205102018011003',
                'nik' => '3201100592920003',
                'nama' => 'Budi Santoso',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1992-05-10',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'status_pernikahan' => 'Menikah',
                'email' => 'budi.santoso@university.ac.id',
                'no_hp' => '081234567803',
                'alamat' => 'Jl. Diponegoro No. 15, Surabaya',
                'unit_kerja_id' => $unitKerja['BAAK-REG']->id ?? $unitKerja['BAAK']->id ?? null,
                'jabatan' => 'Staff Registrasi',
                'jenis_pegawai' => 'PPPK',
                'golongan' => 'II/b',
                'pangkat' => 'Pengatur Muda Tk.I',
                'status' => 'Aktif',
                'tmt_pegawai' => '2018-01-01',
            ],
            
            // Staff BAU
            [
                'nip' => '198008082008011004',
                'nik' => '3201080880800004',
                'nama' => 'Hendra Wijaya, S.E., M.M.',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1980-08-08',
                'jenis_kelamin' => 'L',
                'agama' => 'Kristen',
                'status_pernikahan' => 'Menikah',
                'email' => 'hendra.wijaya@university.ac.id',
                'no_hp' => '081234567804',
                'alamat' => 'Jl. Pemuda No. 30, Semarang',
                'unit_kerja_id' => $unitKerja['BAU']->id ?? null,
                'jabatan' => 'Kepala BAU',
                'jenis_pegawai' => 'PNS',
                'golongan' => 'IV/a',
                'pangkat' => 'Pembina',
                'status' => 'Aktif',
                'tmt_pegawai' => '2008-01-01',
            ],
            [
                'nip' => null,
                'nik' => '3201151088850005',
                'nama' => 'Dewi Anggraini',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1988-10-15',
                'jenis_kelamin' => 'P',
                'agama' => 'Islam',
                'status_pernikahan' => 'Menikah',
                'email' => 'dewi.anggraini@university.ac.id',
                'no_hp' => '081234567805',
                'alamat' => 'Jl. Malioboro No. 50, Yogyakarta',
                'unit_kerja_id' => $unitKerja['BAU-SDM']->id ?? $unitKerja['BAU']->id ?? null,
                'jabatan' => 'Staff SDM',
                'jenis_pegawai' => 'Honorer',
                'status' => 'Aktif',
                'tmt_pegawai' => '2016-06-01',
            ],
            [
                'nip' => null,
                'nik' => '3201200595950006',
                'nama' => 'Rizki Ramadhan',
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '1995-05-20',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'status_pernikahan' => 'Belum Menikah',
                'email' => 'rizki.ramadhan@university.ac.id',
                'no_hp' => '081234567806',
                'alamat' => 'Jl. Asia No. 100, Medan',
                'unit_kerja_id' => $unitKerja['BAU-SARPRAS']->id ?? $unitKerja['BAU']->id ?? null,
                'jabatan' => 'Staff Sarana Prasarana',
                'jenis_pegawai' => 'Kontrak',
                'status' => 'Aktif',
                'tmt_pegawai' => '2020-01-01',
            ],
            
            // Staff BKEU
            [
                'nip' => '197512122005011007',
                'nik' => '3201121275750007',
                'nama' => 'Ir. Susanto, M.Ak.',
                'tempat_lahir' => 'Makassar',
                'tanggal_lahir' => '1975-12-12',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'status_pernikahan' => 'Menikah',
                'email' => 'susanto@university.ac.id',
                'no_hp' => '081234567807',
                'alamat' => 'Jl. Hasanuddin No. 20, Makassar',
                'unit_kerja_id' => $unitKerja['BKEU']->id ?? null,
                'jabatan' => 'Kepala Biro Keuangan',
                'jenis_pegawai' => 'PNS',
                'golongan' => 'IV/b',
                'pangkat' => 'Pembina Tk.I',
                'status' => 'Aktif',
                'tmt_pegawai' => '2005-01-01',
            ],
            [
                'nip' => '199107072019012008',
                'nik' => '3201070791910008',
                'nama' => 'Rina Marlina, S.E.',
                'tempat_lahir' => 'Bogor',
                'tanggal_lahir' => '1991-07-07',
                'jenis_kelamin' => 'P',
                'agama' => 'Islam',
                'status_pernikahan' => 'Menikah',
                'email' => 'rina.marlina@university.ac.id',
                'no_hp' => '081234567808',
                'alamat' => 'Jl. Pajajaran No. 35, Bogor',
                'unit_kerja_id' => $unitKerja['BKEU-AKT']->id ?? $unitKerja['BKEU']->id ?? null,
                'jabatan' => 'Staff Akuntansi',
                'jenis_pegawai' => 'PPPK',
                'golongan' => 'III/a',
                'pangkat' => 'Penata Muda',
                'status' => 'Aktif',
                'tmt_pegawai' => '2019-01-01',
            ],
            [
                'nip' => null,
                'nik' => '3201251093930009',
                'nama' => 'Eko Prasetyo',
                'tempat_lahir' => 'Tangerang',
                'tanggal_lahir' => '1993-10-25',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'status_pernikahan' => 'Belum Menikah',
                'email' => 'eko.prasetyo@university.ac.id',
                'no_hp' => '081234567809',
                'alamat' => 'Jl. Raya Serpong No. 45, Tangerang',
                'unit_kerja_id' => $unitKerja['BKEU-PMB']->id ?? $unitKerja['BKEU']->id ?? null,
                'jabatan' => 'Staff Pembayaran',
                'jenis_pegawai' => 'Kontrak',
                'status' => 'Aktif',
                'tmt_pegawai' => '2021-03-01',
            ],
            
            // Staff UPT TIK
            [
                'nip' => '198706152012011010',
                'nik' => '3201150687870010',
                'nama' => 'Agus Setiawan, S.T., M.Kom.',
                'tempat_lahir' => 'Depok',
                'tanggal_lahir' => '1987-06-15',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'status_pernikahan' => 'Menikah',
                'email' => 'agus.setiawan@university.ac.id',
                'no_hp' => '081234567810',
                'alamat' => 'Jl. Margonda No. 100, Depok',
                'unit_kerja_id' => $unitKerja['UPT-TIK']->id ?? null,
                'jabatan' => 'Kepala UPT TIK',
                'jenis_pegawai' => 'PNS',
                'golongan' => 'III/c',
                'pangkat' => 'Penata',
                'status' => 'Aktif',
                'tmt_pegawai' => '2012-01-01',
            ],
            [
                'nip' => null,
                'nik' => '3201200896960011',
                'nama' => 'Dimas Prayoga',
                'tempat_lahir' => 'Bekasi',
                'tanggal_lahir' => '1996-08-20',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'status_pernikahan' => 'Belum Menikah',
                'email' => 'dimas.prayoga@university.ac.id',
                'no_hp' => '081234567811',
                'alamat' => 'Jl. Ahmad Yani No. 80, Bekasi',
                'unit_kerja_id' => $unitKerja['UPT-TIK']->id ?? null,
                'jabatan' => 'Programmer',
                'jenis_pegawai' => 'Kontrak',
                'status' => 'Aktif',
                'tmt_pegawai' => '2022-01-01',
            ],
            [
                'nip' => null,
                'nik' => '3201100797970012',
                'nama' => 'Putri Handayani',
                'tempat_lahir' => 'Cirebon',
                'tanggal_lahir' => '1997-07-10',
                'jenis_kelamin' => 'P',
                'agama' => 'Islam',
                'status_pernikahan' => 'Belum Menikah',
                'email' => 'putri.handayani@university.ac.id',
                'no_hp' => '081234567812',
                'alamat' => 'Jl. Siliwangi No. 55, Cirebon',
                'unit_kerja_id' => $unitKerja['UPT-TIK']->id ?? null,
                'jabatan' => 'Staff IT Support',
                'jenis_pegawai' => 'Kontrak',
                'status' => 'Aktif',
                'tmt_pegawai' => '2022-06-01',
            ],
            
            // Staff UPT Perpustakaan
            [
                'nip' => '198304042010012013',
                'nik' => '3201040483830013',
                'nama' => 'Lestari Wulandari, S.IP.',
                'tempat_lahir' => 'Malang',
                'tanggal_lahir' => '1983-04-04',
                'jenis_kelamin' => 'P',
                'agama' => 'Islam',
                'status_pernikahan' => 'Menikah',
                'email' => 'lestari.wulandari@university.ac.id',
                'no_hp' => '081234567813',
                'alamat' => 'Jl. Ijen No. 25, Malang',
                'unit_kerja_id' => $unitKerja['UPT-PERPUS']->id ?? null,
                'jabatan' => 'Kepala UPT Perpustakaan',
                'jenis_pegawai' => 'PNS',
                'golongan' => 'III/c',
                'pangkat' => 'Penata',
                'status' => 'Aktif',
                'tmt_pegawai' => '2010-01-01',
            ],
            [
                'nip' => null,
                'nik' => '3201181194940014',
                'nama' => 'Yusuf Hidayat',
                'tempat_lahir' => 'Surakarta',
                'tanggal_lahir' => '1994-11-18',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'status_pernikahan' => 'Menikah',
                'email' => 'yusuf.hidayat@university.ac.id',
                'no_hp' => '081234567814',
                'alamat' => 'Jl. Slamet Riyadi No. 60, Surakarta',
                'unit_kerja_id' => $unitKerja['UPT-PERPUS']->id ?? null,
                'jabatan' => 'Pustakawan',
                'jenis_pegawai' => 'Honorer',
                'status' => 'Aktif',
                'tmt_pegawai' => '2018-07-01',
            ],
            
            // Staff LPPM
            [
                'nip' => '197909092007011015',
                'nik' => '3201090979790015',
                'nama' => 'Dr. Bambang Supriyadi, M.T.',
                'tempat_lahir' => 'Palembang',
                'tanggal_lahir' => '1979-09-09',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'status_pernikahan' => 'Menikah',
                'email' => 'bambang.supriyadi@university.ac.id',
                'no_hp' => '081234567815',
                'alamat' => 'Jl. Sudirman No. 100, Palembang',
                'unit_kerja_id' => $unitKerja['LPPM']->id ?? null,
                'jabatan' => 'Sekretaris LPPM',
                'jenis_pegawai' => 'PNS',
                'golongan' => 'IV/a',
                'pangkat' => 'Pembina',
                'status' => 'Aktif',
                'tmt_pegawai' => '2007-01-01',
            ],
        ];

        foreach ($pegawaiData as $data) {
            Pegawai::create($data);
        }
        
        // Update kepala unit kerja
        $this->updateKepalaUnitKerja();
    }

    private function updateKepalaUnitKerja(): void
    {
        $pegawai = Pegawai::all()->keyBy('email');
        
        $assignments = [
            'BAAK' => 'ahmad.fauzi@university.ac.id',
            'BAU' => 'hendra.wijaya@university.ac.id',
            'BKEU' => 'susanto@university.ac.id',
            'UPT-TIK' => 'agus.setiawan@university.ac.id',
            'UPT-PERPUS' => 'lestari.wulandari@university.ac.id',
        ];

        foreach ($assignments as $kode => $email) {
            $unit = UnitKerja::where('kode', $kode)->first();
            $kepala = $pegawai[$email] ?? null;
            
            if ($unit && $kepala) {
                $unit->update(['kepala_id' => $kepala->id]);
            }
        }
    }

    private function seedRiwayatKepegawaian(): void
    {
        $pegawaiList = Pegawai::all();
        
        foreach ($pegawaiList as $pegawai) {
            // Seed riwayat pendidikan
            $this->seedRiwayatPendidikan($pegawai);
            
            // Seed riwayat jabatan
            $this->seedRiwayatJabatan($pegawai);
            
            // Seed riwayat pangkat untuk PNS/PPPK
            if (in_array($pegawai->jenis_pegawai, ['PNS', 'PPPK'])) {
                $this->seedRiwayatPangkat($pegawai);
            }
            
            // Seed beberapa pelatihan
            $this->seedRiwayatPelatihan($pegawai);
        }
    }

    private function seedRiwayatPendidikan(Pegawai $pegawai): void
    {
        $jenjangList = ['SMA', 'D3', 'S1', 'S2', 'S3'];
        $institusiList = [
            'SMA' => ['SMA Negeri 1', 'SMA Negeri 2', 'SMA Negeri 3', 'SMK Negeri 1'],
            'D3' => ['Politeknik Negeri', 'Akademi', 'Politeknik Swasta'],
            'S1' => ['Universitas Indonesia', 'Institut Teknologi Bandung', 'Universitas Gadjah Mada', 'Universitas Airlangga', 'Universitas Padjadjaran'],
            'S2' => ['Universitas Indonesia', 'Institut Teknologi Bandung', 'Universitas Gadjah Mada'],
            'S3' => ['Universitas Indonesia', 'Institut Teknologi Bandung'],
        ];
        $jurusanList = [
            'SMA' => ['IPA', 'IPS'],
            'D3' => ['Manajemen Informatika', 'Akuntansi', 'Administrasi'],
            'S1' => ['Teknik Informatika', 'Sistem Informasi', 'Akuntansi', 'Manajemen', 'Administrasi Publik', 'Ilmu Perpustakaan'],
            'S2' => ['Magister Komputer', 'Magister Manajemen', 'Magister Akuntansi'],
            'S3' => ['Doktor Ilmu Komputer', 'Doktor Manajemen'],
        ];

        // Determine education level based on nama
        $pendidikanTertinggi = 'S1';
        if (str_contains($pegawai->nama, 'Dr.')) {
            $pendidikanTertinggi = 'S3';
        } elseif (str_contains($pegawai->nama, 'M.') || str_contains($pegawai->nama, 'Ir.')) {
            $pendidikanTertinggi = 'S2';
        } elseif (str_contains($pegawai->nama, 'A.Md.')) {
            $pendidikanTertinggi = 'D3';
        } elseif (str_contains($pegawai->nama, 'S.')) {
            $pendidikanTertinggi = 'S1';
        }

        $tahunLahir = $pegawai->tanggal_lahir ? Carbon::parse($pegawai->tanggal_lahir)->year : 1990;
        
        // SMA
        RiwayatPendidikan::create([
            'pegawai_id' => $pegawai->id,
            'jenjang' => 'SMA',
            'nama_institusi' => $institusiList['SMA'][array_rand($institusiList['SMA'])] . ' ' . $pegawai->tempat_lahir,
            'jurusan' => $jurusanList['SMA'][array_rand($jurusanList['SMA'])],
            'tahun_masuk' => $tahunLahir + 15,
            'tahun_lulus' => $tahunLahir + 18,
        ]);

        // D3 jika ada
        if (in_array($pendidikanTertinggi, ['D3'])) {
            RiwayatPendidikan::create([
                'pegawai_id' => $pegawai->id,
                'jenjang' => 'D3',
                'nama_institusi' => $institusiList['D3'][array_rand($institusiList['D3'])],
                'jurusan' => $jurusanList['D3'][array_rand($jurusanList['D3'])],
                'tahun_masuk' => $tahunLahir + 18,
                'tahun_lulus' => $tahunLahir + 21,
                'ipk' => rand(300, 390) / 100,
            ]);
        }

        // S1
        if (in_array($pendidikanTertinggi, ['S1', 'S2', 'S3'])) {
            RiwayatPendidikan::create([
                'pegawai_id' => $pegawai->id,
                'jenjang' => 'S1',
                'nama_institusi' => $institusiList['S1'][array_rand($institusiList['S1'])],
                'jurusan' => $jurusanList['S1'][array_rand($jurusanList['S1'])],
                'tahun_masuk' => $tahunLahir + 18,
                'tahun_lulus' => $tahunLahir + 22,
                'ipk' => rand(300, 390) / 100,
            ]);
        }

        // S2
        if (in_array($pendidikanTertinggi, ['S2', 'S3'])) {
            RiwayatPendidikan::create([
                'pegawai_id' => $pegawai->id,
                'jenjang' => 'S2',
                'nama_institusi' => $institusiList['S2'][array_rand($institusiList['S2'])],
                'jurusan' => $jurusanList['S2'][array_rand($jurusanList['S2'])],
                'tahun_masuk' => $tahunLahir + 24,
                'tahun_lulus' => $tahunLahir + 26,
                'ipk' => rand(340, 400) / 100,
            ]);
        }

        // S3
        if ($pendidikanTertinggi === 'S3') {
            RiwayatPendidikan::create([
                'pegawai_id' => $pegawai->id,
                'jenjang' => 'S3',
                'nama_institusi' => $institusiList['S3'][array_rand($institusiList['S3'])],
                'jurusan' => $jurusanList['S3'][array_rand($jurusanList['S3'])],
                'tahun_masuk' => $tahunLahir + 28,
                'tahun_lulus' => $tahunLahir + 32,
                'ipk' => rand(350, 400) / 100,
            ]);
        }
    }

    private function seedRiwayatJabatan(Pegawai $pegawai): void
    {
        $tmtPegawai = $pegawai->tmt_pegawai ? Carbon::parse($pegawai->tmt_pegawai) : Carbon::now()->subYears(5);
        
        RiwayatJabatan::create([
            'pegawai_id' => $pegawai->id,
            'nama_jabatan' => $pegawai->jabatan,
            'jenis_jabatan' => 'Struktural',
            'unit_kerja_jabatan' => $pegawai->unitKerja?->nama ?? 'Universitas',
            'tmt_jabatan' => $tmtPegawai,
            'no_sk' => 'SK/' . $tmtPegawai->format('Y') . '/' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'tanggal_sk' => $tmtPegawai->copy()->subDays(rand(7, 30)),
            'pejabat_sk' => 'Rektor',
        ]);
    }

    private function seedRiwayatPangkat(Pegawai $pegawai): void
    {
        $golongan = $pegawai->golongan;
        $pangkat = $pegawai->pangkat;
        $tmtPegawai = $pegawai->tmt_pegawai ? Carbon::parse($pegawai->tmt_pegawai) : Carbon::now()->subYears(5);
        
        if ($golongan && $pangkat) {
            RiwayatPangkat::create([
                'pegawai_id' => $pegawai->id,
                'pangkat' => $pangkat,
                'golongan' => $golongan,
                'tmt_pangkat' => $tmtPegawai,
                'no_sk' => 'SK-PANGKAT/' . $tmtPegawai->format('Y') . '/' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'tanggal_sk' => $tmtPegawai->copy()->subDays(rand(7, 30)),
                'pejabat_penetap' => 'Presiden RI',
            ]);
        }
    }

    private function seedRiwayatPelatihan(Pegawai $pegawai): void
    {
        $pelatihanList = [
            ['nama' => 'Pelatihan Manajemen Kepegawaian', 'jenis' => 'Diklat', 'penyelenggara' => 'BKN'],
            ['nama' => 'Workshop Microsoft Office', 'jenis' => 'Workshop', 'penyelenggara' => 'Microsoft Indonesia'],
            ['nama' => 'Seminar Nasional Pendidikan', 'jenis' => 'Seminar', 'penyelenggara' => 'Kemendikbud'],
            ['nama' => 'Pelatihan Pelayanan Prima', 'jenis' => 'Diklat', 'penyelenggara' => 'LAN'],
            ['nama' => 'Sertifikasi ISO 9001', 'jenis' => 'Sertifikasi', 'penyelenggara' => 'SGS Indonesia'],
            ['nama' => 'Pelatihan Leadership', 'jenis' => 'Diklat', 'penyelenggara' => 'Universitas'],
            ['nama' => 'Workshop Digital Marketing', 'jenis' => 'Workshop', 'penyelenggara' => 'Google Indonesia'],
            ['nama' => 'Kursus Bahasa Inggris', 'jenis' => 'Kursus', 'penyelenggara' => 'British Council'],
        ];

        // Random 1-3 pelatihan per pegawai
        $jumlahPelatihan = rand(1, 3);
        $selectedPelatihan = array_rand($pelatihanList, $jumlahPelatihan);
        
        if (!is_array($selectedPelatihan)) {
            $selectedPelatihan = [$selectedPelatihan];
        }

        foreach ($selectedPelatihan as $index) {
            $pelatihan = $pelatihanList[$index];
            $tahun = rand(2018, 2024);
            $bulan = rand(1, 12);
            $tanggalMulai = Carbon::create($tahun, $bulan, rand(1, 20));
            
            RiwayatPelatihan::create([
                'pegawai_id' => $pegawai->id,
                'jenis' => $pelatihan['jenis'],
                'nama_pelatihan' => $pelatihan['nama'],
                'penyelenggara' => $pelatihan['penyelenggara'],
                'tempat' => 'Jakarta',
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalMulai->copy()->addDays(rand(1, 5)),
                'jumlah_jam' => rand(8, 40),
                'tahun' => $tahun,
                'no_sertifikat' => 'SERT/' . $tahun . '/' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            ]);
        }
    }
}
