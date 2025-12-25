<?php

namespace Database\Seeders;

use App\Models\CutiAkademik;
use App\Models\HistoryStatusMahasiswa;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Database\Seeder;

class CutiAkademikSeeder extends Seeder
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

        $admin = User::where('role', 'admin')->first();
        $mahasiswas = Mahasiswa::inRandomOrder()->take(5)->get();

        $alasanList = ['Keuangan', 'Kesehatan', 'Keluarga', 'Pekerjaan', 'Lainnya'];
        $keteranganByAlasan = [
            'Keuangan' => 'Mengalami kesulitan keuangan untuk membayar biaya kuliah semester ini.',
            'Kesehatan' => 'Perlu waktu untuk pemulihan kesehatan setelah menjalani operasi.',
            'Keluarga' => 'Harus membantu orang tua yang sedang sakit di kampung halaman.',
            'Pekerjaan' => 'Mendapatkan kesempatan bekerja penuh waktu yang tidak dapat ditinggalkan.',
            'Lainnya' => 'Membutuhkan waktu untuk kegiatan sosial/keagamaan yang penting.',
        ];

        foreach ($mahasiswas as $index => $mahasiswa) {
            $alasan = $alasanList[$index % count($alasanList)];
            
            // Tentukan status berdasarkan index
            $status = match($index) {
                0 => 'Pending',
                1 => 'Disetujui Kaprodi',
                2 => 'Disetujui Dekan',
                3 => 'Ditolak',
                4 => 'Selesai',
                default => 'Pending',
            };

            $cutiData = [
                'mahasiswa_id' => $mahasiswa->id,
                'tahun_akademik_id' => $tahunAkademik->id,
                'alasan' => $alasan,
                'keterangan' => $keteranganByAlasan[$alasan],
                'tanggal_mulai' => $tahunAkademik->tanggal_mulai ?? now(),
                'tanggal_selesai' => $tahunAkademik->tanggal_selesai ?? now()->addMonths(6),
                'jumlah_semester' => rand(1, 2),
                'status' => $status,
            ];

            // Tambah data persetujuan berdasarkan status
            if (in_array($status, ['Disetujui Kaprodi', 'Disetujui Dekan', 'Selesai'])) {
                $cutiData['disetujui_kaprodi_oleh'] = $admin?->id;
                $cutiData['tanggal_persetujuan_kaprodi'] = now()->subDays(7);
                $cutiData['catatan_kaprodi'] = 'Disetujui karena alasan yang dapat diterima.';
            }

            if (in_array($status, ['Disetujui Dekan', 'Selesai'])) {
                $cutiData['nomor_surat'] = CutiAkademik::generateNomorSurat();
                $cutiData['disetujui_dekan_oleh'] = $admin?->id;
                $cutiData['tanggal_persetujuan_dekan'] = now()->subDays(3);
                $cutiData['catatan_dekan'] = 'Disetujui. Mahasiswa dapat mengaktifkan kembali status setelah masa cuti selesai.';
            }

            if ($status == 'Ditolak') {
                $cutiData['catatan_kaprodi'] = 'Ditolak karena dokumen pendukung tidak lengkap. Silakan ajukan ulang dengan dokumen yang lengkap.';
            }

            // Cek apakah sudah ada
            $existing = CutiAkademik::where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->first();

            if (!$existing) {
                $cuti = CutiAkademik::create($cutiData);

                // Jika disetujui dekan, update status mahasiswa dan catat history
                if ($status == 'Disetujui Dekan') {
                    $statusLama = $mahasiswa->status;
                    $mahasiswa->update(['status' => 'Cuti']);

                    HistoryStatusMahasiswa::catat(
                        $mahasiswa->id,
                        $tahunAkademik->id,
                        $statusLama,
                        'Cuti',
                        'Cuti akademik disetujui: ' . $alasan,
                        $admin?->id
                    );
                }

                // Jika selesai, status kembali aktif
                if ($status == 'Selesai') {
                    HistoryStatusMahasiswa::catat(
                        $mahasiswa->id,
                        $tahunAkademik->id,
                        'Cuti',
                        'Aktif',
                        'Masa cuti akademik selesai',
                        $admin?->id
                    );
                    $mahasiswa->update(['status' => 'Aktif']);
                }
            }
        }

        $this->command->info('Cuti Akademik seeder berhasil dijalankan!');
    }
}
