<?php

namespace Database\Seeders;

use App\Models\KomponenGaji;
use Illuminate\Database\Seeder;

class KomponenGajiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $komponens = [
            // Pendapatan/Tunjangan
            [
                'kode' => 'TJ001',
                'nama' => 'Tunjangan Jabatan',
                'jenis' => 'pendapatan',
                'tipe_nilai' => 'tetap',
                'nilai_default' => 500000,
                'wajib' => false,
                'urutan' => 1,
            ],
            [
                'kode' => 'TJ002',
                'nama' => 'Tunjangan Keluarga',
                'jenis' => 'pendapatan',
                'tipe_nilai' => 'tetap',
                'nilai_default' => 300000,
                'wajib' => false,
                'urutan' => 2,
            ],
            [
                'kode' => 'TJ003',
                'nama' => 'Tunjangan Transportasi',
                'jenis' => 'pendapatan',
                'tipe_nilai' => 'tetap',
                'nilai_default' => 400000,
                'wajib' => true,
                'urutan' => 3,
            ],
            [
                'kode' => 'TJ004',
                'nama' => 'Tunjangan Makan',
                'jenis' => 'pendapatan',
                'tipe_nilai' => 'tetap',
                'nilai_default' => 500000,
                'wajib' => true,
                'urutan' => 4,
            ],
            [
                'kode' => 'TJ005',
                'nama' => 'Tunjangan Kesehatan',
                'jenis' => 'pendapatan',
                'tipe_nilai' => 'tetap',
                'nilai_default' => 300000,
                'wajib' => false,
                'urutan' => 5,
            ],
            [
                'kode' => 'TJ006',
                'nama' => 'Tunjangan Kinerja',
                'jenis' => 'pendapatan',
                'tipe_nilai' => 'tetap',
                'nilai_default' => 0,
                'wajib' => false,
                'urutan' => 6,
            ],
            [
                'kode' => 'TJ007',
                'nama' => 'Insentif Mengajar',
                'jenis' => 'pendapatan',
                'tipe_nilai' => 'tetap',
                'nilai_default' => 0,
                'wajib' => false,
                'urutan' => 7,
            ],
            [
                'kode' => 'TJ008',
                'nama' => 'Honorarium',
                'jenis' => 'pendapatan',
                'tipe_nilai' => 'tetap',
                'nilai_default' => 0,
                'wajib' => false,
                'urutan' => 8,
            ],
            
            // Potongan
            [
                'kode' => 'PT001',
                'nama' => 'BPJS Kesehatan',
                'jenis' => 'potongan',
                'tipe_nilai' => 'persentase',
                'nilai_default' => 0,
                'wajib' => true,
                'urutan' => 1,
                'keterangan' => '1% dari gaji pokok',
            ],
            [
                'kode' => 'PT002',
                'nama' => 'BPJS Ketenagakerjaan',
                'jenis' => 'potongan',
                'tipe_nilai' => 'persentase',
                'nilai_default' => 0,
                'wajib' => true,
                'urutan' => 2,
                'keterangan' => '2% dari gaji pokok',
            ],
            [
                'kode' => 'PT003',
                'nama' => 'PPh 21',
                'jenis' => 'potongan',
                'tipe_nilai' => 'tetap',
                'nilai_default' => 0,
                'wajib' => false,
                'urutan' => 3,
            ],
            [
                'kode' => 'PT004',
                'nama' => 'Potongan Ketidakhadiran',
                'jenis' => 'potongan',
                'tipe_nilai' => 'tetap',
                'nilai_default' => 0,
                'wajib' => false,
                'urutan' => 4,
            ],
            [
                'kode' => 'PT005',
                'nama' => 'Pinjaman/Kasbon',
                'jenis' => 'potongan',
                'tipe_nilai' => 'tetap',
                'nilai_default' => 0,
                'wajib' => false,
                'urutan' => 5,
            ],
            [
                'kode' => 'PT006',
                'nama' => 'Iuran Koperasi',
                'jenis' => 'potongan',
                'tipe_nilai' => 'tetap',
                'nilai_default' => 0,
                'wajib' => false,
                'urutan' => 6,
            ],
        ];

        foreach ($komponens as $komponen) {
            KomponenGaji::updateOrCreate(
                ['kode' => $komponen['kode']],
                $komponen
            );
        }

        $this->command->info('Komponen gaji seeded: ' . count($komponens) . ' komponen');
    }
}
