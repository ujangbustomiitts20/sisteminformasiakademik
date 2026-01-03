<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TugasAkhir;
use App\Models\BimbinganTA;
use App\Models\SeminarProposal;
use App\Models\SidangTA;
use App\Models\RevisiTA;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\TahunAkademik;
use Carbon\Carbon;

class TugasAkhirSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();
        $mahasiswas = Mahasiswa::take(10)->get();
        $dosens = Dosen::take(8)->get();

        if (!$tahunAkademik || $mahasiswas->isEmpty() || $dosens->count() < 3) {
            return;
        }

        $bidangKajian = [
            'Artificial Intelligence',
            'Machine Learning',
            'Data Science',
            'Web Development',
            'Mobile Development',
            'Computer Networks',
            'Information Security',
            'Software Engineering',
        ];

        $judulContoh = [
            'Implementasi Algoritma Machine Learning untuk Prediksi Harga Saham',
            'Pengembangan Sistem E-Commerce dengan Rekomendasi Produk Berbasis AI',
            'Analisis Sentimen Media Sosial Menggunakan Natural Language Processing',
            'Sistem Deteksi Wajah Real-Time dengan Deep Learning',
            'Aplikasi Mobile untuk Manajemen Kesehatan Berbasis IoT',
            'Perancangan Sistem Keamanan Jaringan Menggunakan Machine Learning',
            'Pengembangan Chatbot Cerdas untuk Layanan Pelanggan',
            'Sistem Informasi Geografis untuk Pemetaan Sebaran COVID-19',
            'Implementasi Blockchain untuk Sistem Voting Elektronik',
            'Optimalisasi Rute Distribusi dengan Algoritma Genetika',
        ];

        $statusList = ['draft', 'diajukan', 'judul_disetujui', 'proposal_diajukan', 'penelitian', 'sidang_diajukan', 'sidang_dijadwalkan', 'lulus'];

        $counter = 1;
        foreach ($mahasiswas as $index => $mahasiswa) {
            $status = $statusList[$index % count($statusList)];
            $pembimbing1 = $dosens->random();
            $pembimbing2 = $dosens->where('id', '!=', $pembimbing1->id)->random();

            $ta = TugasAkhir::create([
                'nomor_ta' => 'TA-' . date('Y') . '-' . str_pad($counter++, 4, '0', STR_PAD_LEFT),
                'mahasiswa_id' => $mahasiswa->id,
                'tahun_akademik_id' => $tahunAkademik->id,
                'judul' => $judulContoh[$index % count($judulContoh)],
                'abstrak' => 'Abstrak: Penelitian ini mengkaji tentang ' . $judulContoh[$index % count($judulContoh)] . '. Hasil penelitian menunjukkan bahwa sistem yang dikembangkan dapat berjalan dengan baik.',
                'bidang_kajian' => $bidangKajian[$index % count($bidangKajian)],
                'latar_belakang' => 'Latar belakang penelitian ini didasari oleh kebutuhan akan solusi teknologi.',
                'rumusan_masalah' => '1. Bagaimana merancang sistem yang efektif?',
                'metodologi' => '1. Analisis kebutuhan 2. Perancangan sistem 3. Implementasi 4. Pengujian',
                'pembimbing_1_id' => in_array($status, ['draft', 'diajukan']) ? null : $pembimbing1->id,
                'pembimbing_2_id' => in_array($status, ['draft', 'diajukan']) ? null : $pembimbing2->id,
                'status' => $status,
                'tanggal_pengajuan' => Carbon::now()->subMonths(6 - ($index % 6)),
                'tanggal_approval_judul' => !in_array($status, ['draft', 'diajukan']) ? Carbon::now()->subMonths(5 - ($index % 5)) : null,
                'tanggal_lulus' => $status == 'lulus' ? Carbon::now()->subWeek() : null,
                'catatan_pembimbing' => !in_array($status, ['draft', 'diajukan']) ? 'Judul disetujui. Silakan mulai penelitian.' : null,
            ]);

            // Bimbingan untuk TA yang sudah disetujui
            if (!in_array($status, ['draft', 'diajukan']) && $pembimbing1 && $pembimbing2) {
                $jumlahBimbingan = match($status) {
                    'judul_disetujui' => rand(1, 3),
                    'proposal_diajukan' => rand(3, 5),
                    default => rand(5, 8),
                };

                for ($i = 1; $i <= $jumlahBimbingan; $i++) {
                    BimbinganTA::create([
                        'tugas_akhir_id' => $ta->id,
                        'dosen_id' => $i % 2 == 0 ? $pembimbing2->id : $pembimbing1->id,
                        'tanggal' => Carbon::now()->subDays(($jumlahBimbingan - $i + 1) * 7),
                        'waktu_mulai' => '10:00',
                        'waktu_selesai' => '11:00',
                        'tempat' => 'Ruang Dosen',
                        'materi_bimbingan' => 'Bimbingan ke-' . $i . ': Pembahasan progress penelitian.',
                        'hasil_bimbingan' => 'Mahasiswa telah menyelesaikan milestone ke-' . $i,
                        'catatan_dosen' => 'Progress baik. Lanjutkan ke tahap berikutnya.',
                        'rencana_selanjutnya' => 'Melanjutkan ke bab ' . min($i + 1, 5),
                        'persentase_progress' => min(($i * 10), 100),
                        'status' => 'selesai',
                    ]);
                }
            }

            // Seminar Proposal untuk TA yang sudah melewati tahap proposal
            if (in_array($status, ['penelitian', 'sidang_diajukan', 'sidang_dijadwalkan', 'lulus'])) {
                $penguji1 = $dosens->whereNotIn('id', [$pembimbing1->id, $pembimbing2->id])->random();
                $penguji2 = $dosens->whereNotIn('id', [$pembimbing1->id, $pembimbing2->id, $penguji1->id])->random();

                SeminarProposal::create([
                    'nomor_seminar' => 'SEM-' . date('Y') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'tugas_akhir_id' => $ta->id,
                    'tanggal' => Carbon::now()->subMonths(3),
                    'waktu_mulai' => '09:00',
                    'waktu_selesai' => '11:00',
                    'ruangan' => 'Ruang Sidang A',
                    'penguji_1_id' => $penguji1->id,
                    'penguji_2_id' => $penguji2->id,
                    'nilai_pembimbing_1' => 80,
                    'nilai_pembimbing_2' => 78,
                    'nilai_penguji_1' => 75,
                    'nilai_penguji_2' => 77,
                    'nilai_akhir' => 77.5,
                    'hasil' => 'lulus',
                    'status' => 'selesai',
                ]);
            }

            // Sidang untuk TA yang sudah dijadwalkan sidang atau lulus
            if (in_array($status, ['sidang_dijadwalkan', 'lulus'])) {
                $ketuaPenguji = $dosens->whereNotIn('id', [$pembimbing1->id, $pembimbing2->id])->random();
                $penguji1 = $dosens->whereNotIn('id', [$pembimbing1->id, $pembimbing2->id, $ketuaPenguji->id])->random();
                $sisaDosen = $dosens->whereNotIn('id', [$pembimbing1->id, $pembimbing2->id, $ketuaPenguji->id, $penguji1->id]);
                $penguji2 = $sisaDosen->isNotEmpty() ? $sisaDosen->random() : $ketuaPenguji;

                $sidang = SidangTA::create([
                    'nomor_sidang' => 'SID-' . date('Y') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'tugas_akhir_id' => $ta->id,
                    'tanggal' => $status == 'lulus' ? Carbon::now()->subWeek() : Carbon::now()->addWeek(),
                    'waktu_mulai' => '08:00',
                    'waktu_selesai' => '10:00',
                    'ruangan' => 'Ruang Sidang B',
                    'ketua_penguji_id' => $ketuaPenguji->id,
                    'penguji_1_id' => $penguji1->id,
                    'penguji_2_id' => $penguji2->id,
                    'status' => $status == 'lulus' ? 'selesai' : 'dijadwalkan',
                    'nilai_presentasi' => $status == 'lulus' ? 80 : null,
                    'nilai_penguasaan_materi' => $status == 'lulus' ? 82 : null,
                    'nilai_tanya_jawab' => $status == 'lulus' ? 78 : null,
                    'nilai_dokumen' => $status == 'lulus' ? 85 : null,
                    'nilai_ketua' => $status == 'lulus' ? 82 : null,
                    'nilai_penguji_1' => $status == 'lulus' ? 80 : null,
                    'nilai_penguji_2' => $status == 'lulus' ? 78 : null,
                    'nilai_pembimbing_1' => $status == 'lulus' ? 85 : null,
                    'nilai_pembimbing_2' => $status == 'lulus' ? 83 : null,
                    'nilai_akhir' => $status == 'lulus' ? 81.6 : null,
                    'grade' => $status == 'lulus' ? 'A' : null,
                    'hasil' => $status == 'lulus' ? 'lulus' : null,
                    'revisi_selesai' => $status == 'lulus',
                ]);

                // Revisi untuk yang sudah selesai
                if ($status == 'lulus') {
                    foreach ([$ketuaPenguji, $penguji1, $penguji2] as $revisiDosen) {
                        RevisiTA::create([
                            'sidang_ta_id' => $sidang->id,
                            'dosen_id' => $revisiDosen->id,
                            'catatan_revisi' => 'Perbaiki penulisan dan format sesuai pedoman.',
                            'sudah_diperbaiki' => true,
                            'tanggal_perbaikan' => Carbon::now()->subDays(3),
                        ]);
                    }
                }
            }
        }
    }
}
