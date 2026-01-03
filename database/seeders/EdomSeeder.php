<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PertanyaanEdom;
use App\Models\PeriodeEdom;
use App\Models\TahunAkademik;

class EdomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat pertanyaan EDOM
        $pertanyaans = [
            // Kompetensi Pedagogik (5 pertanyaan)
            [
                'kode' => 'PED01',
                'pertanyaan' => 'Dosen menyampaikan tujuan pembelajaran dengan jelas di awal perkuliahan',
                'kategori' => 'kompetensi_pedagogik',
                'urutan' => 1,
            ],
            [
                'kode' => 'PED02',
                'pertanyaan' => 'Dosen menggunakan metode pembelajaran yang bervariasi dan menarik',
                'kategori' => 'kompetensi_pedagogik',
                'urutan' => 2,
            ],
            [
                'kode' => 'PED03',
                'pertanyaan' => 'Dosen memberikan kesempatan kepada mahasiswa untuk bertanya dan berdiskusi',
                'kategori' => 'kompetensi_pedagogik',
                'urutan' => 3,
            ],
            [
                'kode' => 'PED04',
                'pertanyaan' => 'Dosen memberikan feedback yang konstruktif terhadap tugas dan ujian',
                'kategori' => 'kompetensi_pedagogik',
                'urutan' => 4,
            ],
            [
                'kode' => 'PED05',
                'pertanyaan' => 'Dosen menggunakan media pembelajaran yang mendukung pemahaman materi',
                'kategori' => 'kompetensi_pedagogik',
                'urutan' => 5,
            ],
            
            // Kompetensi Profesional (5 pertanyaan)
            [
                'kode' => 'PRO01',
                'pertanyaan' => 'Dosen menguasai materi perkuliahan dengan baik',
                'kategori' => 'kompetensi_profesional',
                'urutan' => 1,
            ],
            [
                'kode' => 'PRO02',
                'pertanyaan' => 'Dosen menyampaikan materi secara sistematis dan mudah dipahami',
                'kategori' => 'kompetensi_profesional',
                'urutan' => 2,
            ],
            [
                'kode' => 'PRO03',
                'pertanyaan' => 'Dosen memberikan contoh dan aplikasi nyata dari materi yang diajarkan',
                'kategori' => 'kompetensi_profesional',
                'urutan' => 3,
            ],
            [
                'kode' => 'PRO04',
                'pertanyaan' => 'Dosen mengikuti perkembangan ilmu pengetahuan terkini dalam bidangnya',
                'kategori' => 'kompetensi_profesional',
                'urutan' => 4,
            ],
            [
                'kode' => 'PRO05',
                'pertanyaan' => 'Dosen mampu menjawab pertanyaan mahasiswa dengan tepat dan jelas',
                'kategori' => 'kompetensi_profesional',
                'urutan' => 5,
            ],
            
            // Kompetensi Kepribadian (5 pertanyaan)
            [
                'kode' => 'KEP01',
                'pertanyaan' => 'Dosen datang tepat waktu sesuai jadwal perkuliahan',
                'kategori' => 'kompetensi_kepribadian',
                'urutan' => 1,
            ],
            [
                'kode' => 'KEP02',
                'pertanyaan' => 'Dosen bersikap adil dan tidak diskriminatif terhadap mahasiswa',
                'kategori' => 'kompetensi_kepribadian',
                'urutan' => 2,
            ],
            [
                'kode' => 'KEP03',
                'pertanyaan' => 'Dosen menunjukkan sikap profesional dan bertanggung jawab',
                'kategori' => 'kompetensi_kepribadian',
                'urutan' => 3,
            ],
            [
                'kode' => 'KEP04',
                'pertanyaan' => 'Dosen dapat menjadi teladan yang baik bagi mahasiswa',
                'kategori' => 'kompetensi_kepribadian',
                'urutan' => 4,
            ],
            [
                'kode' => 'KEP05',
                'pertanyaan' => 'Dosen menghargai pendapat dan ide-ide mahasiswa',
                'kategori' => 'kompetensi_kepribadian',
                'urutan' => 5,
            ],
            
            // Kompetensi Sosial (5 pertanyaan)
            [
                'kode' => 'SOS01',
                'pertanyaan' => 'Dosen berkomunikasi dengan baik dan santun kepada mahasiswa',
                'kategori' => 'kompetensi_sosial',
                'urutan' => 1,
            ],
            [
                'kode' => 'SOS02',
                'pertanyaan' => 'Dosen mudah dihubungi untuk konsultasi di luar jam kuliah',
                'kategori' => 'kompetensi_sosial',
                'urutan' => 2,
            ],
            [
                'kode' => 'SOS03',
                'pertanyaan' => 'Dosen menciptakan suasana kelas yang kondusif dan menyenangkan',
                'kategori' => 'kompetensi_sosial',
                'urutan' => 3,
            ],
            [
                'kode' => 'SOS04',
                'pertanyaan' => 'Dosen mampu mengelola kelas dengan baik',
                'kategori' => 'kompetensi_sosial',
                'urutan' => 4,
            ],
            [
                'kode' => 'SOS05',
                'pertanyaan' => 'Dosen membangun hubungan yang baik dengan mahasiswa',
                'kategori' => 'kompetensi_sosial',
                'urutan' => 5,
            ],
        ];

        foreach ($pertanyaans as $p) {
            PertanyaanEdom::firstOrCreate(
                ['kode' => $p['kode']],
                array_merge($p, ['is_active' => true])
            );
        }

        $this->command->info('20 pertanyaan EDOM berhasil dibuat!');

        // Buat periode EDOM contoh
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->first();
        
        if ($tahunAkademik) {
            PeriodeEdom::firstOrCreate(
                [
                    'tahun_akademik_id' => $tahunAkademik->id,
                    'nama' => 'EDOM ' . $tahunAkademik->nama,
                ],
                [
                    'tanggal_mulai' => now()->startOfMonth(),
                    'tanggal_selesai' => now()->endOfMonth()->addWeek(),
                    'status' => 'aktif',
                    'deskripsi' => 'Periode evaluasi dosen untuk ' . $tahunAkademik->nama . '. Silakan isi evaluasi untuk setiap mata kuliah yang Anda ikuti.',
                ]
            );
            
            $this->command->info('Periode EDOM berhasil dibuat!');
        }
    }
}
