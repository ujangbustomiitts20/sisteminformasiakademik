<?php

namespace Database\Seeders;

use App\Models\NamaJabatan;
use Illuminate\Database\Seeder;

class NamaJabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jabatans = [
            // Pimpinan Institusi
            ['kode' => 'rektor', 'nama' => 'Rektor', 'nama_singkat' => 'Rektor', 'kategori' => 'pimpinan', 'level' => 0, 'urutan' => 1],
            ['kode' => 'wakil_rektor_1', 'nama' => 'Wakil Rektor I Bidang Akademik', 'nama_singkat' => 'Warek I', 'kategori' => 'pimpinan', 'level' => 1, 'urutan' => 2],
            ['kode' => 'wakil_rektor_2', 'nama' => 'Wakil Rektor II Bidang Keuangan dan Administrasi', 'nama_singkat' => 'Warek II', 'kategori' => 'pimpinan', 'level' => 1, 'urutan' => 3],
            ['kode' => 'wakil_rektor_3', 'nama' => 'Wakil Rektor III Bidang Kemahasiswaan', 'nama_singkat' => 'Warek III', 'kategori' => 'pimpinan', 'level' => 1, 'urutan' => 4],
            ['kode' => 'wakil_rektor_4', 'nama' => 'Wakil Rektor IV Bidang Kerjasama', 'nama_singkat' => 'Warek IV', 'kategori' => 'pimpinan', 'level' => 1, 'urutan' => 5],
            
            // Akademik
            ['kode' => 'dekan_fti', 'nama' => 'Dekan Fakultas Teknologi Industri', 'nama_singkat' => 'Dekan FTI', 'kategori' => 'akademik', 'level' => 2, 'urutan' => 10],
            ['kode' => 'dekan_feb', 'nama' => 'Dekan Fakultas Ekonomi dan Bisnis', 'nama_singkat' => 'Dekan FEB', 'kategori' => 'akademik', 'level' => 2, 'urutan' => 11],
            ['kode' => 'dekan_fkip', 'nama' => 'Dekan Fakultas Keguruan dan Ilmu Pendidikan', 'nama_singkat' => 'Dekan FKIP', 'kategori' => 'akademik', 'level' => 2, 'urutan' => 12],
            ['kode' => 'wadek_1', 'nama' => 'Wakil Dekan I Bidang Akademik', 'nama_singkat' => 'Wadek I', 'kategori' => 'akademik', 'level' => 3, 'urutan' => 15],
            ['kode' => 'wadek_2', 'nama' => 'Wakil Dekan II Bidang Keuangan dan SDM', 'nama_singkat' => 'Wadek II', 'kategori' => 'akademik', 'level' => 3, 'urutan' => 16],
            ['kode' => 'wadek_3', 'nama' => 'Wakil Dekan III Bidang Kemahasiswaan', 'nama_singkat' => 'Wadek III', 'kategori' => 'akademik', 'level' => 3, 'urutan' => 17],
            ['kode' => 'kaprodi_ti', 'nama' => 'Ketua Program Studi Teknik Informatika', 'nama_singkat' => 'Kaprodi TI', 'kategori' => 'akademik', 'level' => 4, 'urutan' => 20],
            ['kode' => 'kaprodi_si', 'nama' => 'Ketua Program Studi Sistem Informasi', 'nama_singkat' => 'Kaprodi SI', 'kategori' => 'akademik', 'level' => 4, 'urutan' => 21],
            ['kode' => 'kaprodi_tm', 'nama' => 'Ketua Program Studi Teknik Mesin', 'nama_singkat' => 'Kaprodi TM', 'kategori' => 'akademik', 'level' => 4, 'urutan' => 22],
            ['kode' => 'kaprodi_te', 'nama' => 'Ketua Program Studi Teknik Elektro', 'nama_singkat' => 'Kaprodi TE', 'kategori' => 'akademik', 'level' => 4, 'urutan' => 23],
            ['kode' => 'sekprodi', 'nama' => 'Sekretaris Program Studi', 'nama_singkat' => 'Sekprodi', 'kategori' => 'akademik', 'level' => 5, 'urutan' => 30],
            ['kode' => 'kepala_baak', 'nama' => 'Kepala Biro Administrasi Akademik', 'nama_singkat' => 'Ka. BAAK', 'kategori' => 'akademik', 'level' => 6, 'urutan' => 40],
            
            // Keuangan
            ['kode' => 'kepala_keuangan', 'nama' => 'Kepala Biro Keuangan', 'nama_singkat' => 'Ka. Keuangan', 'kategori' => 'keuangan', 'level' => 6, 'urutan' => 50],
            ['kode' => 'bendahara', 'nama' => 'Bendahara', 'nama_singkat' => 'Bendahara', 'kategori' => 'keuangan', 'level' => 7, 'urutan' => 51],
            
            // SDM/Kepegawaian
            ['kode' => 'kepala_sdm', 'nama' => 'Kepala Biro SDM dan Kepegawaian', 'nama_singkat' => 'Ka. SDM', 'kategori' => 'sdm', 'level' => 6, 'urutan' => 60],
            ['kode' => 'staff_sdm', 'nama' => 'Staff SDM dan Kepegawaian', 'nama_singkat' => 'Staff SDM', 'kategori' => 'sdm', 'level' => 7, 'urutan' => 61],
            
            // Kemahasiswaan
            ['kode' => 'kepala_kemahasiswaan', 'nama' => 'Kepala Biro Kemahasiswaan', 'nama_singkat' => 'Ka. Kemahasiswaan', 'kategori' => 'kemahasiswaan', 'level' => 6, 'urutan' => 70],
            ['kode' => 'staff_kemahasiswaan', 'nama' => 'Staff Kemahasiswaan', 'nama_singkat' => 'Staff Kemahasiswaan', 'kategori' => 'kemahasiswaan', 'level' => 7, 'urutan' => 71],
            
            // Umum
            ['kode' => 'kepala_umum', 'nama' => 'Kepala Biro Umum', 'nama_singkat' => 'Ka. Umum', 'kategori' => 'umum', 'level' => 6, 'urutan' => 80],
            ['kode' => 'kepala_perpustakaan', 'nama' => 'Kepala Perpustakaan', 'nama_singkat' => 'Ka. Perpustakaan', 'kategori' => 'umum', 'level' => 6, 'urutan' => 81],
            ['kode' => 'kepala_laboratorium', 'nama' => 'Kepala Laboratorium', 'nama_singkat' => 'Ka. Lab', 'kategori' => 'umum', 'level' => 6, 'urutan' => 82],
            ['kode' => 'kepala_lppm', 'nama' => 'Kepala Lembaga Penelitian dan Pengabdian Masyarakat', 'nama_singkat' => 'Ka. LPPM', 'kategori' => 'akademik', 'level' => 6, 'urutan' => 83],
            ['kode' => 'kepala_lpm', 'nama' => 'Kepala Lembaga Penjaminan Mutu', 'nama_singkat' => 'Ka. LPM', 'kategori' => 'akademik', 'level' => 6, 'urutan' => 84],
        ];

        foreach ($jabatans as $jabatan) {
            NamaJabatan::firstOrCreate(
                ['kode' => $jabatan['kode']],
                array_merge($jabatan, ['aktif' => true])
            );
        }

        $this->command->info('Nama jabatan seeded successfully!');
    }
}
