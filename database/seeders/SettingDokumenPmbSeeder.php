<?php

namespace Database\Seeders;

use App\Models\SettingDokumenPmb;
use Illuminate\Database\Seeder;

class SettingDokumenPmbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dokumen = [
            [
                'kode' => 'ktp',
                'nama_dokumen' => 'KTP',
                'deskripsi' => 'Kartu Tanda Penduduk (jika sudah 17 tahun)',
                'format_file' => '.pdf,.jpg,.jpeg,.png',
                'max_size_kb' => 2048,
                'is_wajib' => false,
                'is_active' => true,
                'urutan' => 1,
            ],
            [
                'kode' => 'kk',
                'nama_dokumen' => 'Kartu Keluarga',
                'deskripsi' => 'Kartu Keluarga yang masih berlaku',
                'format_file' => '.pdf,.jpg,.jpeg,.png',
                'max_size_kb' => 2048,
                'is_wajib' => true,
                'is_active' => true,
                'urutan' => 2,
            ],
            [
                'kode' => 'akta',
                'nama_dokumen' => 'Akta Kelahiran',
                'deskripsi' => 'Akta Kelahiran asli',
                'format_file' => '.pdf,.jpg,.jpeg,.png',
                'max_size_kb' => 2048,
                'is_wajib' => true,
                'is_active' => true,
                'urutan' => 3,
            ],
            [
                'kode' => 'ijazah',
                'nama_dokumen' => 'Ijazah',
                'deskripsi' => 'Ijazah SMA/SMK/MA sederajat atau Surat Keterangan Lulus',
                'format_file' => '.pdf,.jpg,.jpeg,.png',
                'max_size_kb' => 2048,
                'is_wajib' => true,
                'is_active' => true,
                'urutan' => 4,
            ],
            [
                'kode' => 'skhun',
                'nama_dokumen' => 'SKHUN / Transkrip Nilai',
                'deskripsi' => 'SKHUN atau Transkrip Nilai SMA/SMK/MA',
                'format_file' => '.pdf,.jpg,.jpeg,.png',
                'max_size_kb' => 2048,
                'is_wajib' => false,
                'is_active' => true,
                'urutan' => 5,
            ],
            [
                'kode' => 'rapor',
                'nama_dokumen' => 'Rapor',
                'deskripsi' => 'Rapor semester 1-5 SMA/SMK/MA (untuk jalur prestasi)',
                'format_file' => '.pdf,.jpg,.jpeg,.png',
                'max_size_kb' => 5120,
                'is_wajib' => false,
                'is_active' => true,
                'urutan' => 6,
            ],
            [
                'kode' => 'foto',
                'nama_dokumen' => 'Pas Foto',
                'deskripsi' => 'Pas foto 3x4 berwarna latar belakang merah',
                'format_file' => '.jpg,.jpeg,.png',
                'max_size_kb' => 1024,
                'is_wajib' => true,
                'is_active' => true,
                'urutan' => 7,
            ],
            [
                'kode' => 'surat_sehat',
                'nama_dokumen' => 'Surat Keterangan Sehat',
                'deskripsi' => 'Surat keterangan sehat dari dokter/puskesmas',
                'format_file' => '.pdf,.jpg,.jpeg,.png',
                'max_size_kb' => 2048,
                'is_wajib' => false,
                'is_active' => true,
                'urutan' => 8,
            ],
            [
                'kode' => 'surat_kelakuan_baik',
                'nama_dokumen' => 'SKCK / Surat Kelakuan Baik',
                'deskripsi' => 'SKCK dari kepolisian atau surat kelakuan baik dari sekolah',
                'format_file' => '.pdf,.jpg,.jpeg,.png',
                'max_size_kb' => 2048,
                'is_wajib' => false,
                'is_active' => true,
                'urutan' => 9,
            ],
            [
                'kode' => 'sertifikat',
                'nama_dokumen' => 'Sertifikat Prestasi',
                'deskripsi' => 'Sertifikat prestasi akademik/non-akademik (opsional)',
                'format_file' => '.pdf,.jpg,.jpeg,.png',
                'max_size_kb' => 5120,
                'is_wajib' => false,
                'is_active' => true,
                'urutan' => 10,
            ],
        ];

        foreach ($dokumen as $dok) {
            SettingDokumenPmb::updateOrCreate(
                ['kode' => $dok['kode']],
                $dok
            );
        }

        $this->command->info('Setting dokumen PMB berhasil di-seed: ' . count($dokumen) . ' dokumen');
    }
}
