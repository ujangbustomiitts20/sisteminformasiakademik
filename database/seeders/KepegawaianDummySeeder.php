<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dosen;
use App\Models\Pegawai;
use App\Models\IzinKeluar;
use App\Models\PengajuanLembur;
use App\Models\TarifLembur;
use App\Models\KontrakKerja;
use App\Models\SertifikasiDosen;
use App\Models\TunjanganPegawai;
use App\Models\JenisTunjangan;
use App\Models\PelanggaranPegawai;
use App\Models\JenisPelanggaran;
use App\Models\EvaluasiKinerja;
use App\Models\EvaluasiKinerjaDetail;
use App\Models\KriteriaEvaluasi;
use App\Models\PeriodeEvaluasi;
use Carbon\Carbon;

class KepegawaianDummySeeder extends Seeder
{
    /**
     * Seed dummy data untuk testing modul kepegawaian extended
     */
    public function run(): void
    {
        $this->command->info('Seeding Kepegawaian Dummy Data...');
        
        $dosens = Dosen::take(10)->get();
        $pegawais = Pegawai::take(5)->get();
        
        if ($dosens->isEmpty()) {
            $this->command->warn('Tidak ada data dosen. Jalankan DosenSeeder terlebih dahulu.');
            return;
        }

        $this->seedIzinKeluar($dosens, $pegawais);
        $this->seedPengajuanLembur($dosens, $pegawais);
        $this->seedKontrakKerja($dosens, $pegawais);
        $this->seedSertifikasiDosen($dosens);
        $this->seedTunjanganPegawai($dosens, $pegawais);
        $this->seedPelanggaranPegawai($dosens, $pegawais);
        $this->seedEvaluasiKinerja($dosens, $pegawais);
        
        $this->command->info('Kepegawaian Dummy Data seeding completed!');
    }

    private function seedIzinKeluar($dosens, $pegawais): void
    {
        $this->command->info('  - Seeding Izin Keluar...');
        
        // Menggunakan nilai yang sesuai dengan ENUM di database
        $keperluanList = ['dinas', 'pribadi', 'kesehatan', 'keluarga', 'lainnya'];

        $tujuan = [
            'Rumah Sakit',
            'Bank BRI Cabang Kota',
            'Kantor Dinas Pendidikan',
            'Hotel Grand Mercure',
            'Gedung Serbaguna',
            'Sekolah SD/SMP',
            'Kantor Kelurahan',
        ];

        $keterangan = [
            'Urusan keluarga darurat',
            'Berobat ke dokter',
            'Mengambil dokumen penting',
            'Rapat di luar kantor',
            'Antar jemput anak sekolah',
            'Keperluan bank',
            'Pertemuan dengan klien',
            'Training di luar kantor',
        ];

        $statusList = ['diajukan', 'disetujui', 'ditolak', 'selesai'];
        $count = 0;

        // Izin keluar untuk dosen
        foreach ($dosens->take(6) as $dosen) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                $tanggal = now()->subDays(rand(1, 60));
                $jamKeluar = sprintf('%02d:%02d', rand(8, 14), rand(0, 5) * 10);
                $jamKembali = sprintf('%02d:%02d', rand(14, 17), rand(0, 5) * 10);
                $status = $statusList[array_rand($statusList)];
                
                IzinKeluar::firstOrCreate(
                    [
                        'dosen_id' => $dosen->id,
                        'tanggal' => $tanggal->toDateString(),
                        'jam_keluar' => $jamKeluar,
                    ],
                    [
                        'jam_kembali' => $jamKembali,
                        'keperluan' => $keperluanList[array_rand($keperluanList)],
                        'tujuan' => $tujuan[array_rand($tujuan)],
                        'keterangan' => $keterangan[array_rand($keterangan)],
                        'status' => $status,
                        'disetujui_oleh' => in_array($status, ['disetujui', 'selesai']) ? 1 : null,
                        'tanggal_disetujui' => in_array($status, ['disetujui', 'selesai']) ? $tanggal->copy()->addHours(rand(1, 4)) : null,
                        'catatan_approval' => $status == 'ditolak' ? 'Izin tidak dapat diberikan' : null,
                    ]
                );
                $count++;
            }
        }

        // Izin keluar untuk pegawai
        foreach ($pegawais->take(4) as $pegawai) {
            for ($i = 0; $i < rand(1, 2); $i++) {
                $tanggal = now()->subDays(rand(1, 60));
                $jamKeluar = sprintf('%02d:%02d', rand(8, 14), rand(0, 5) * 10);
                $jamKembali = sprintf('%02d:%02d', rand(14, 17), rand(0, 5) * 10);
                $status = $statusList[array_rand($statusList)];
                
                IzinKeluar::firstOrCreate(
                    [
                        'pegawai_id' => $pegawai->id,
                        'tanggal' => $tanggal->toDateString(),
                        'jam_keluar' => $jamKeluar,
                    ],
                    [
                        'jam_kembali' => $jamKembali,
                        'keperluan' => $keperluanList[array_rand($keperluanList)],
                        'tujuan' => $tujuan[array_rand($tujuan)],
                        'keterangan' => $keterangan[array_rand($keterangan)],
                        'status' => $status,
                        'disetujui_oleh' => in_array($status, ['disetujui', 'selesai']) ? 1 : null,
                        'tanggal_disetujui' => in_array($status, ['disetujui', 'selesai']) ? $tanggal->copy()->addHours(rand(1, 4)) : null,
                        'catatan_approval' => $status == 'ditolak' ? 'Izin tidak dapat diberikan karena alasan tertentu' : null,
                    ]
                );
                $count++;
            }
        }

        $this->command->info("    Izin Keluar: $count records created");
    }

    private function seedPengajuanLembur($dosens, $pegawais): void
    {
        $this->command->info('  - Seeding Pengajuan Lembur...');
        
        $tarifList = TarifLembur::where('is_active', true)->get();
        if ($tarifList->isEmpty()) {
            $this->command->warn('    Tidak ada data tarif lembur aktif.');
            return;
        }

        $alasan = [
            'Deadline proyek mendekati',
            'Persiapan akreditasi program studi',
            'Input nilai akhir semester',
            'Penyusunan jadwal kuliah semester baru',
            'Maintenance sistem informasi',
            'Pengolahan data mahasiswa baru',
            'Persiapan wisuda',
            'Penyusunan laporan tahunan',
            'Rapat koordinasi pimpinan',
            'Persiapan audit internal',
        ];

        $pekerjaan = [
            'Menyelesaikan dokumen akreditasi',
            'Input data nilai mahasiswa',
            'Perbaikan sistem informasi',
            'Rekap data kepegawaian',
            'Penyusunan jadwal semester',
            'Pengolahan data PMB',
        ];

        $statusList = ['diajukan', 'disetujui', 'ditolak', 'selesai'];
        $count = 0;

        // Lembur untuk dosen
        foreach ($dosens->take(5) as $dosen) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                $tanggal = now()->subDays(rand(1, 45));
                $durasi = rand(1, 5);
                $tarif = $tarifList->random();
                $status = $statusList[array_rand($statusList)];
                $tarifPerJam = $tarif->nominal_tetap > 0 ? $tarif->nominal_tetap : 50000;
                
                PengajuanLembur::firstOrCreate(
                    [
                        'dosen_id' => $dosen->id,
                        'tanggal' => $tanggal->toDateString(),
                    ],
                    [
                        'tarif_lembur_id' => $tarif->id,
                        'jam_mulai' => '17:00',
                        'jam_selesai' => sprintf('%02d:00', 17 + $durasi),
                        'durasi_jam' => $durasi,
                        'alasan' => $alasan[array_rand($alasan)],
                        'pekerjaan_yang_dilakukan' => $pekerjaan[array_rand($pekerjaan)],
                        'status' => $status,
                        'tarif_per_jam' => $tarifPerJam,
                        'total_bayar' => $tarifPerJam * $durasi,
                        'disetujui_oleh' => in_array($status, ['disetujui', 'selesai']) ? 1 : null,
                        'tanggal_disetujui' => in_array($status, ['disetujui', 'selesai']) ? $tanggal->copy()->addDays(1) : null,
                        'catatan_approval' => $status == 'ditolak' ? 'Tidak memenuhi syarat lembur' : null,
                    ]
                );
                $count++;
            }
        }

        // Lembur untuk pegawai
        foreach ($pegawais->take(4) as $pegawai) {
            for ($i = 0; $i < rand(1, 2); $i++) {
                $tanggal = now()->subDays(rand(1, 45));
                $durasi = rand(1, 4);
                $tarif = $tarifList->random();
                $status = $statusList[array_rand($statusList)];
                $tarifPerJam = $tarif->nominal_tetap > 0 ? $tarif->nominal_tetap : 40000;
                
                PengajuanLembur::firstOrCreate(
                    [
                        'pegawai_id' => $pegawai->id,
                        'tanggal' => $tanggal->toDateString(),
                    ],
                    [
                        'tarif_lembur_id' => $tarif->id,
                        'jam_mulai' => '17:00',
                        'jam_selesai' => sprintf('%02d:00', 17 + $durasi),
                        'durasi_jam' => $durasi,
                        'alasan' => $alasan[array_rand($alasan)],
                        'pekerjaan_yang_dilakukan' => $pekerjaan[array_rand($pekerjaan)],
                        'status' => $status,
                        'tarif_per_jam' => $tarifPerJam,
                        'total_bayar' => $tarifPerJam * $durasi,
                        'disetujui_oleh' => in_array($status, ['disetujui', 'selesai']) ? 1 : null,
                        'tanggal_disetujui' => in_array($status, ['disetujui', 'selesai']) ? $tanggal->copy()->addDays(1) : null,
                        'catatan_approval' => $status == 'ditolak' ? 'Lembur tidak disetujui' : null,
                    ]
                );
                $count++;
            }
        }

        $this->command->info("    Pengajuan Lembur: $count records created");
    }

    private function seedKontrakKerja($dosens, $pegawais): void
    {
        $this->command->info('  - Seeding Kontrak Kerja...');
        
        $count = 0;
        // Menggunakan nilai enum yang sesuai dengan database
        $jenisKontrak = ['tetap', 'kontrak', 'honorer', 'paruh_waktu'];

        // Kontrak untuk dosen
        foreach ($dosens->take(6) as $i => $dosen) {
            $startDate = now()->subMonths(rand(6, 24));
            $jenis = $jenisKontrak[array_rand($jenisKontrak)];
            
            KontrakKerja::firstOrCreate(
                ['dosen_id' => $dosen->id, 'nomor_kontrak' => 'KTR/DSN/' . date('Y') . '/' . str_pad($i + 1, 4, '0', STR_PAD_LEFT)],
                [
                    'tanggal_mulai' => $startDate->toDateString(),
                    'tanggal_berakhir' => $startDate->copy()->addYear()->toDateString(),
                    'jenis_kontrak' => $jenis,
                    'gaji_pokok' => rand(5, 15) * 1000000,
                    'status' => 'aktif',
                    'keterangan' => 'Kontrak kerja dosen',
                    'created_by' => 1,
                ]
            );
            $count++;
        }

        // Kontrak untuk pegawai
        foreach ($pegawais->take(4) as $i => $pegawai) {
            $startDate = now()->subMonths(rand(6, 24));
            $jenis = $jenisKontrak[array_rand($jenisKontrak)];
            
            KontrakKerja::firstOrCreate(
                ['pegawai_id' => $pegawai->id, 'nomor_kontrak' => 'KTR/PGW/' . date('Y') . '/' . str_pad($i + 1, 4, '0', STR_PAD_LEFT)],
                [
                    'tanggal_mulai' => $startDate->toDateString(),
                    'tanggal_berakhir' => $startDate->copy()->addYear()->toDateString(),
                    'jenis_kontrak' => $jenis,
                    'gaji_pokok' => rand(4, 10) * 1000000,
                    'status' => 'aktif',
                    'keterangan' => 'Kontrak kerja pegawai',
                    'created_by' => 1,
                ]
            );
            $count++;
        }

        $this->command->info("    Kontrak Kerja: $count records created");
    }

    private function seedSertifikasiDosen($dosens): void
    {
        $this->command->info('  - Seeding Sertifikasi Dosen...');
        
        // Menggunakan nilai enum yang sesuai dengan database
        $jenisSertifikasi = ['serdos', 'kompetensi', 'profesi', 'keahlian', 'lainnya'];
        $lembaga = ['Kemendikbud', 'LLDIKTI', 'BAN-PT', 'Lembaga Sertifikasi Profesi', 'BNSP'];
        $count = 0;

        foreach ($dosens->take(7) as $i => $dosen) {
            $jenis = $jenisSertifikasi[$i % count($jenisSertifikasi)];
            
            SertifikasiDosen::firstOrCreate(
                ['dosen_id' => $dosen->id, 'nomor_sertifikat' => 'SERT/' . strtoupper($jenis) . '/' . str_pad($i + 1, 5, '0', STR_PAD_LEFT)],
                [
                    'nama_sertifikasi' => 'Sertifikasi ' . ucfirst($jenis),
                    'jenis_sertifikasi' => $jenis,
                    'bidang_studi' => $dosen->programStudi?->nama ?? 'Umum',
                    'penerbit' => $lembaga[array_rand($lembaga)],
                    'tanggal_terbit' => now()->subYears(rand(1, 4))->toDateString(),
                    'tanggal_berlaku' => now()->subYears(rand(1, 4))->toDateString(),
                    'tanggal_expired' => now()->addYears(rand(1, 4))->toDateString(),
                    'status' => 'aktif',
                    'keterangan' => 'Sertifikasi ' . $jenis . ' ' . $dosen->nama,
                ]
            );
            $count++;
        }

        $this->command->info("    Sertifikasi Dosen: $count records created");
    }

    private function seedTunjanganPegawai($dosens, $pegawais): void
    {
        $this->command->info('  - Seeding Tunjangan Pegawai...');
        
        $jenisTunjanganList = JenisTunjangan::where('is_active', true)->get();
        if ($jenisTunjanganList->isEmpty()) {
            $this->command->warn('    Tidak ada data jenis tunjangan aktif.');
            return;
        }

        $count = 0;

        // Tunjangan untuk dosen
        foreach ($dosens->take(6) as $dosen) {
            $jenisTunjangan = $jenisTunjanganList->random();
            TunjanganPegawai::firstOrCreate(
                ['dosen_id' => $dosen->id, 'jenis_tunjangan_id' => $jenisTunjangan->id],
                [
                    'nominal' => $jenisTunjangan->nilai_default ?? rand(5, 20) * 100000,
                    'tanggal_mulai' => now()->subMonths(rand(1, 12))->toDateString(),
                    'tanggal_berakhir' => now()->addMonths(rand(6, 12))->toDateString(),
                    'no_sk' => 'SK/TJ/' . date('Y') . '/' . str_pad(rand(1, 100), 4, '0', STR_PAD_LEFT),
                    'status' => 'aktif',
                    'keterangan' => 'Tunjangan ' . $jenisTunjangan->nama . ' untuk ' . $dosen->nama,
                ]
            );
            $count++;
        }

        // Tunjangan untuk pegawai
        foreach ($pegawais->take(4) as $pegawai) {
            $jenisTunjangan = $jenisTunjanganList->random();
            TunjanganPegawai::firstOrCreate(
                ['pegawai_id' => $pegawai->id, 'jenis_tunjangan_id' => $jenisTunjangan->id],
                [
                    'nominal' => $jenisTunjangan->nilai_default ?? rand(3, 15) * 100000,
                    'tanggal_mulai' => now()->subMonths(rand(1, 12))->toDateString(),
                    'tanggal_berakhir' => now()->addMonths(rand(6, 12))->toDateString(),
                    'no_sk' => 'SK/TJ/' . date('Y') . '/' . str_pad(rand(100, 200), 4, '0', STR_PAD_LEFT),
                    'status' => 'aktif',
                    'keterangan' => 'Tunjangan ' . $jenisTunjangan->nama . ' untuk ' . $pegawai->nama,
                ]
            );
            $count++;
        }

        $this->command->info("    Tunjangan Pegawai: $count records created");
    }

    private function seedPelanggaranPegawai($dosens, $pegawais): void
    {
        $this->command->info('  - Seeding Pelanggaran Pegawai...');
        
        $jenisPelanggaranList = JenisPelanggaran::where('is_active', true)->get();
        if ($jenisPelanggaranList->isEmpty()) {
            $this->command->warn('    Tidak ada data jenis pelanggaran aktif.');
            return;
        }

        $count = 0;

        // Pelanggaran untuk beberapa dosen
        foreach ($dosens->take(3) as $dosen) {
            $jenisPelanggaran = $jenisPelanggaranList->random();
            PelanggaranPegawai::firstOrCreate(
                ['dosen_id' => $dosen->id, 'jenis_pelanggaran_id' => $jenisPelanggaran->id, 'tanggal_pelanggaran' => now()->subDays(rand(10, 90))->toDateString()],
                [
                    'deskripsi' => 'Pelanggaran: ' . $jenisPelanggaran->nama,
                    'bukti' => 'Laporan dari atasan langsung',
                    'status' => collect(['dilaporkan', 'ditindaklanjuti', 'selesai'])->random(),
                    'dilaporkan_oleh' => 1,
                ]
            );
            $count++;
        }

        // Pelanggaran untuk pegawai
        foreach ($pegawais->take(2) as $pegawai) {
            $jenisPelanggaran = $jenisPelanggaranList->random();
            PelanggaranPegawai::firstOrCreate(
                ['pegawai_id' => $pegawai->id, 'jenis_pelanggaran_id' => $jenisPelanggaran->id, 'tanggal_pelanggaran' => now()->subDays(rand(10, 90))->toDateString()],
                [
                    'deskripsi' => 'Pelanggaran: ' . $jenisPelanggaran->nama,
                    'bukti' => 'Catatan dari supervisor',
                    'status' => collect(['dilaporkan', 'ditindaklanjuti', 'selesai'])->random(),
                    'dilaporkan_oleh' => 1,
                ]
            );
            $count++;
        }

        $this->command->info("    Pelanggaran Pegawai: $count records created");
    }

    private function seedEvaluasiKinerja($dosens, $pegawais): void
    {
        $this->command->info('  - Seeding Evaluasi Kinerja...');
        
        $periode = PeriodeEvaluasi::where('status', 'aktif')->first();
        $kriteriaList = KriteriaEvaluasi::where('is_active', true)->get();
        
        if (!$periode) {
            $this->command->warn('    Tidak ada periode evaluasi aktif.');
            return;
        }

        if ($kriteriaList->isEmpty()) {
            $this->command->warn('    Tidak ada kriteria evaluasi aktif.');
            return;
        }

        $count = 0;

        // Evaluasi untuk beberapa dosen
        foreach ($dosens->take(5) as $dosen) {
            $nilaiTotal = rand(65, 95);
            $predikat = $nilaiTotal >= 85 ? 'Sangat Baik' : ($nilaiTotal >= 70 ? 'Baik' : ($nilaiTotal >= 55 ? 'Cukup' : 'Kurang'));
            
            $evaluasi = EvaluasiKinerja::firstOrCreate(
                ['periode_evaluasi_id' => $periode->id, 'dosen_id' => $dosen->id],
                [
                    'penilai_id' => 1,
                    'tanggal_penilaian' => now()->subDays(rand(1, 20))->toDateString(),
                    'nilai_total' => $nilaiTotal,
                    'predikat' => $predikat,
                    'catatan' => 'Evaluasi kinerja dosen ' . $dosen->nama,
                    'rekomendasi' => $nilaiTotal >= 80 ? 'Layak untuk promosi' : 'Perlu pembinaan',
                    'status' => collect(['draft', 'diajukan', 'disetujui'])->random(),
                ]
            );

            // Detail evaluasi per kriteria
            foreach ($kriteriaList as $kriteria) {
                $nilai = rand(60, 100);
                
                EvaluasiKinerjaDetail::firstOrCreate(
                    ['evaluasi_kinerja_id' => $evaluasi->id, 'kriteria_evaluasi_id' => $kriteria->id],
                    [
                        'nilai' => $nilai,
                        'keterangan' => null,
                    ]
                );
            }

            $count++;
        }

        $this->command->info("    Evaluasi Kinerja: $count records created");
    }
}
