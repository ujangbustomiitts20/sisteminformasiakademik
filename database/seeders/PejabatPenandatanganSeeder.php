<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\NamaJabatan;
use App\Models\Pegawai;
use App\Models\PejabatPenandatangan;
use Illuminate\Database\Seeder;

class PejabatPenandatanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Pejabat Penandatangan...');

        // Build jabatan lookup by kode
        $jabatanMap = NamaJabatan::pluck('id', 'kode')->toArray();

        if (empty($jabatanMap)) {
            $this->command->warn('NamaJabatan belum di-seed! Jalankan NamaJabatanSeeder terlebih dahulu.');
            return;
        }

        // Get sample pegawai/dosen for linking (optional)
        $dosens = Dosen::where('status', 'aktif')->take(10)->get();
        $pegawais = Pegawai::where('status', 'aktif')->take(10)->get();

        $pejabats = [
            // Pimpinan
            [
                'kode' => 'rektor',
                'nama_jabatan_kode' => 'rektor',
                'nama' => 'Nama Rektor',
                'gelar_depan' => 'Prof. Dr.',
                'gelar_belakang' => 'M.T.',
                'nip' => '1960XXXXXXXXXXXX',
                'pangkat_golongan' => 'Pembina Utama (IV/e)',
                'kategori' => 'pimpinan',
                'dokumen_terkait' => ['ijazah', 'skpi', 'sk_pengangkatan', 'sk_kenaikan_pangkat', 'sk_pensiun', 'mou', 'ktm', 'undangan_wisuda'],
                'urutan' => 1,
                'use_dosen' => true, // Will link to dosen if available
            ],
            [
                'kode' => 'wakil_rektor_1',
                'nama_jabatan_kode' => 'wakil_rektor_1',
                'nama' => 'Nama Wakil Rektor 1',
                'gelar_depan' => 'Dr.',
                'gelar_belakang' => 'M.Sc.',
                'kategori' => 'pimpinan',
                'dokumen_terkait' => ['surat_aktif_kuliah', 'surat_cuti_akademik'],
                'urutan' => 2,
                'use_dosen' => true,
            ],
            [
                'kode' => 'wakil_rektor_2',
                'nama_jabatan_kode' => 'wakil_rektor_2',
                'nama' => 'Nama Wakil Rektor 2',
                'gelar_depan' => 'Dr.',
                'gelar_belakang' => 'M.M.',
                'kategori' => 'pimpinan',
                'dokumen_terkait' => ['slip_gaji', 'surat_tagihan', 'surat_cuti_pegawai'],
                'urutan' => 3,
                'use_dosen' => true,
            ],

            // Akademik - Dekan
            [
                'kode' => 'dekan_fti',
                'nama_jabatan_kode' => 'dekan_fti',
                'nama' => 'Nama Dekan FTI',
                'gelar_depan' => 'Dr.',
                'gelar_belakang' => 'S.T., M.T.',
                'kategori' => 'akademik',
                'dokumen_terkait' => ['transkrip', 'khs', 'skl', 'surat_yudisium', 'sk_pembimbing', 'sk_penguji', 'sk_wali', 'berita_acara_sidang', 'surat_aktif_kuliah', 'surat_izin_penelitian', 'surat_pengantar_magang', 'surat_rekomendasi'],
                'urutan' => 10,
                'use_dosen' => true,
            ],

            // Keuangan
            [
                'kode' => 'kepala_keuangan',
                'nama_jabatan_kode' => 'kepala_keuangan',
                'nama' => 'Nama Kepala Keuangan',
                'gelar_belakang' => 'S.E., M.M.',
                'kategori' => 'keuangan',
                'dokumen_terkait' => ['slip_gaji', 'kwitansi', 'surat_tagihan', 'surat_bebas_keuangan'],
                'urutan' => 50,
                'use_pegawai' => true, // Will link to pegawai if available
            ],
            [
                'kode' => 'bendahara',
                'nama_jabatan_kode' => 'bendahara',
                'nama' => 'Nama Bendahara',
                'gelar_belakang' => 'S.E.',
                'kategori' => 'keuangan',
                'dokumen_terkait' => ['kwitansi', 'surat_bebas_keuangan'],
                'urutan' => 51,
                'use_pegawai' => true,
            ],

            // SDM
            [
                'kode' => 'kepala_sdm',
                'nama_jabatan_kode' => 'kepala_sdm',
                'nama' => 'Nama Kepala SDM',
                'gelar_belakang' => 'S.Psi., M.M.',
                'kategori' => 'sdm',
                'dokumen_terkait' => ['surat_cuti_pegawai', 'surat_tugas', 'sppd', 'sk_mutasi'],
                'urutan' => 60,
                'use_pegawai' => true,
            ],

            // Kemahasiswaan
            [
                'kode' => 'kepala_kemahasiswaan',
                'nama_jabatan_kode' => 'kepala_kemahasiswaan',
                'nama' => 'Nama Kepala Kemahasiswaan',
                'gelar_belakang' => 'S.Sos., M.Si.',
                'kategori' => 'kemahasiswaan',
                'dokumen_terkait' => ['sk_beasiswa', 'surat_rekomendasi'],
                'urutan' => 70,
                'use_pegawai' => true,
            ],

            // Umum
            [
                'kode' => 'kepala_perpustakaan',
                'nama_jabatan_kode' => 'kepala_perpustakaan',
                'nama' => 'Nama Kepala Perpustakaan',
                'gelar_belakang' => 'S.I.Pust., M.Hum.',
                'kategori' => 'umum',
                'dokumen_terkait' => ['surat_bebas_perpustakaan'],
                'urutan' => 80,
                'use_pegawai' => true,
            ],
            [
                'kode' => 'kepala_laboratorium',
                'nama_jabatan_kode' => 'kepala_laboratorium',
                'nama' => 'Nama Kepala Lab',
                'gelar_belakang' => 'S.T., M.T.',
                'kategori' => 'umum',
                'dokumen_terkait' => ['surat_bebas_lab'],
                'urutan' => 81,
                'use_dosen' => true,
            ],
        ];

        $dosenIndex = 0;
        $pegawaiIndex = 0;

        foreach ($pejabats as $data) {
            // Get nama_jabatan_id from lookup
            $namaJabatanId = $jabatanMap[$data['nama_jabatan_kode']] ?? null;

            // Get jabatan name from NamaJabatan if linked
            $namaJabatan = $namaJabatanId ? NamaJabatan::find($namaJabatanId) : null;
            $jabatan = $namaJabatan?->nama ?? $data['nama_jabatan_kode'];

            // Prepare base data
            $createData = [
                'nama_jabatan_id' => $namaJabatanId,
                'jabatan' => $jabatan,
                'nama' => $data['nama'],
                'gelar_depan' => $data['gelar_depan'] ?? null,
                'gelar_belakang' => $data['gelar_belakang'] ?? null,
                'nip' => $data['nip'] ?? null,
                'pangkat_golongan' => $data['pangkat_golongan'] ?? null,
                'kategori' => $data['kategori'],
                'dokumen_terkait' => $data['dokumen_terkait'],
                'urutan' => $data['urutan'],
                'aktif' => true,
            ];

            // Link to Dosen if available and marked
            if (!empty($data['use_dosen']) && $dosens->isNotEmpty() && $dosenIndex < $dosens->count()) {
                $dosen = $dosens[$dosenIndex];
                $createData['dosen_id'] = $dosen->id;
                $createData['nama'] = $dosen->nama;
                $createData['nip'] = $dosen->nidn ?? $dosen->nip;
                $createData['gelar_depan'] = $dosen->gelar_depan ?? $createData['gelar_depan'];
                $createData['gelar_belakang'] = $dosen->gelar_belakang ?? $createData['gelar_belakang'];
                $createData['pangkat_golongan'] = $dosen->pangkat_golongan ?? $createData['pangkat_golongan'];
                $dosenIndex++;
            }

            // Link to Pegawai if available and marked
            if (!empty($data['use_pegawai']) && $pegawais->isNotEmpty() && $pegawaiIndex < $pegawais->count()) {
                $pegawai = $pegawais[$pegawaiIndex];
                $createData['pegawai_id'] = $pegawai->id;
                $createData['nama'] = $pegawai->nama;
                $createData['nip'] = $pegawai->nip ?? $pegawai->nik;
                $createData['gelar_depan'] = $pegawai->gelar_depan ?? $createData['gelar_depan'];
                $createData['gelar_belakang'] = $pegawai->gelar_belakang ?? $createData['gelar_belakang'];
                $createData['pangkat_golongan'] = $pegawai->pangkat_golongan ?? $createData['pangkat_golongan'];
                $pegawaiIndex++;
            }

            // Remove helper keys
            unset($data['nama_jabatan_kode'], $data['use_dosen'], $data['use_pegawai']);

            PejabatPenandatangan::firstOrCreate(
                ['kode' => $data['kode']],
                $createData
            );
        }

        $this->command->info('Seeded ' . count($pejabats) . ' pejabat penandatangan');
    }
}
