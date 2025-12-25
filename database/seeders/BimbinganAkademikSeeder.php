<?php

namespace Database\Seeders;

use App\Models\BimbinganAkademik;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use Illuminate\Database\Seeder;

class BimbinganAkademikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();
        
        if (!$tahunAkademik) {
            $this->command->warn('Tahun akademik aktif tidak ditemukan!');
            return;
        }

        $mahasiswas = Mahasiswa::whereNotNull('dosen_wali_id')->inRandomOrder()->take(10)->get();

        $topiks = [
            'KRS' => ['Konsultasi pengambilan mata kuliah semester ini', 'Membahas beban SKS yang diambil', 'Pemilihan mata kuliah pilihan'],
            'Akademik' => ['Konsultasi terkait nilai IPK', 'Diskusi strategi belajar', 'Pemantauan progress akademik'],
            'Karir' => ['Konsultasi persiapan magang', 'Diskusi rencana karir setelah lulus', 'Membahas sertifikasi yang perlu diambil'],
            'Pribadi' => ['Konsultasi motivasi belajar', 'Membahas kendala dalam perkuliahan', 'Diskusi manajemen waktu'],
        ];

        foreach ($mahasiswas as $mahasiswa) {
            $jenis = array_rand($topiks);
            $topik = $topiks[$jenis][array_rand($topiks[$jenis])];

            // Bimbingan yang sudah selesai
            BimbinganAkademik::firstOrCreate([
                'mahasiswa_id' => $mahasiswa->id,
                'dosen_id' => $mahasiswa->dosen_wali_id,
                'tahun_akademik_id' => $tahunAkademik->id,
                'tanggal_bimbingan' => now()->subDays(rand(7, 30)),
            ], [
                'jenis' => $jenis,
                'topik' => $topik,
                'catatan_mahasiswa' => 'Mohon bimbingan terkait ' . strtolower($topik),
                'catatan_dosen' => 'Sudah diberikan arahan dan saran. ' . ($jenis == 'KRS' ? 'KRS sudah sesuai dengan jadwal dan kemampuan mahasiswa.' : 'Mahasiswa disarankan untuk terus meningkatkan motivasi belajar.'),
                'rekomendasi' => 'Lanjutkan sesuai rencana yang sudah dibahas.',
                'status' => 'Selesai',
            ]);

            // Bimbingan yang dijadwalkan
            if (rand(0, 1)) {
                $jenis2 = array_rand($topiks);
                $topik2 = $topiks[$jenis2][array_rand($topiks[$jenis2])];

                BimbinganAkademik::firstOrCreate([
                    'mahasiswa_id' => $mahasiswa->id,
                    'dosen_id' => $mahasiswa->dosen_wali_id,
                    'tahun_akademik_id' => $tahunAkademik->id,
                    'tanggal_bimbingan' => now()->addDays(rand(1, 14)),
                ], [
                    'jenis' => $jenis2,
                    'topik' => $topik2,
                    'catatan_mahasiswa' => 'Mohon bimbingan terkait ' . strtolower($topik2),
                    'catatan_dosen' => null,
                    'rekomendasi' => null,
                    'status' => 'Dijadwalkan',
                ]);
            }
        }

        $this->command->info('Bimbingan Akademik seeder berhasil dijalankan!');
    }
}
