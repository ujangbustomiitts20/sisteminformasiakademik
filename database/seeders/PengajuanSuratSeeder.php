<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengajuanSurat;
use App\Models\Mahasiswa;

class PengajuanSuratSeeder extends Seeder
{
    public function run(): void
    {
        $mahasiswa = Mahasiswa::where('nim', '2024004001')->first();
        
        if (!$mahasiswa) {
            $this->command->error('Mahasiswa dengan NIM 2024004001 tidak ditemukan!');
            return;
        }
        
        $this->command->info('Membuat data pengajuan surat untuk: ' . $mahasiswa->nama);
        
        // Hapus data lama
        PengajuanSurat::where('mahasiswa_id', $mahasiswa->id)->delete();
        
        $pengajuanData = [
            [
                'jenis_surat' => 'Surat Keterangan Aktif Kuliah',
                'keperluan' => 'Untuk persyaratan beasiswa PPA tahun 2025',
                'ditujukan_kepada' => 'Dinas Pendidikan Provinsi DKI Jakarta',
                'status' => 'disetujui',
                'nomor_surat' => '001/SK/AKD/I/2025',
                'catatan_admin' => 'Surat telah disetujui dan dapat diambil',
            ],
            [
                'jenis_surat' => 'Surat Pengantar Magang/PKL',
                'keperluan' => 'Magang di PT. Teknologi Nusantara selama 3 bulan',
                'ditujukan_kepada' => 'HRD PT. Teknologi Nusantara',
                'status' => 'diproses',
                'nomor_surat' => null,
                'catatan_admin' => 'Sedang dalam proses verifikasi data',
            ],
            [
                'jenis_surat' => 'Surat Pengantar Penelitian',
                'keperluan' => 'Penelitian skripsi tentang sistem informasi manajemen di BUMN',
                'ditujukan_kepada' => 'PT. PLN (Persero)',
                'keterangan_tambahan' => 'Penelitian akan dilaksanakan pada bulan Februari-April 2025',
                'status' => 'pending',
                'nomor_surat' => null,
                'catatan_admin' => null,
            ],
            [
                'jenis_surat' => 'Surat Keterangan Berkelakuan Baik',
                'keperluan' => 'Persyaratan melamar pekerjaan di instansi pemerintah',
                'ditujukan_kepada' => 'Badan Kepegawaian Negara',
                'status' => 'disetujui',
                'nomor_surat' => '002/SK/AKD/I/2025',
                'catatan_admin' => 'Surat telah disetujui',
            ],
            [
                'jenis_surat' => 'Surat Rekomendasi',
                'keperluan' => 'Mendaftar program pertukaran pelajar ke Jepang',
                'ditujukan_kepada' => 'Japan Student Services Organization (JASSO)',
                'keterangan_tambahan' => 'Mohon dapat diproses segera karena deadline pendaftaran 31 Januari 2025',
                'status' => 'ditolak',
                'nomor_surat' => null,
                'catatan_admin' => 'Mohon maaf, IPK belum memenuhi syarat minimum 3.00 untuk program pertukaran pelajar',
            ],
        ];
        
        foreach ($pengajuanData as $data) {
            PengajuanSurat::create([
                'mahasiswa_id' => $mahasiswa->id,
                'jenis_surat' => $data['jenis_surat'],
                'keperluan' => $data['keperluan'],
                'ditujukan_kepada' => $data['ditujukan_kepada'],
                'keterangan_tambahan' => $data['keterangan_tambahan'] ?? null,
                'status' => $data['status'],
                'nomor_surat' => $data['nomor_surat'],
                'catatan_admin' => $data['catatan_admin'],
                'diproses_oleh' => $data['status'] != 'pending' ? 1 : null,
                'tanggal_diproses' => $data['status'] != 'pending' ? now()->subDays(rand(1, 7)) : null,
            ]);
            
            $this->command->info('  ✓ ' . $data['jenis_surat'] . ' - ' . $data['status']);
        }
        
        $this->command->info('');
        $this->command->info('Berhasil membuat ' . count($pengajuanData) . ' pengajuan surat!');
    }
}
