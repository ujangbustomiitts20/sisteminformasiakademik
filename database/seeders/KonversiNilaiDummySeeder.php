<?php

namespace Database\Seeders;

use App\Models\DetailKonversi;
use App\Models\MataKuliah;
use App\Models\PengajuanKonversi;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Database\Seeder;

class KonversiNilaiDummySeeder extends Seeder
{
    /**
     * Data dummy untuk konversi nilai mahasiswa pindahan
     */
    public function run(): void
    {
        $programStudis = ProgramStudi::all();
        
        if ($programStudis->isEmpty()) {
            $this->command->warn('Tidak ada program studi! Jalankan seeder master data terlebih dahulu.');
            return;
        }

        // Cari user kaprodi untuk referensi
        $kaprodi = User::where('role', 'kaprodi')->first();

        // Daftar universitas asal dummy
        $universitasAsal = [
            ['universitas' => 'Universitas Negeri Jakarta', 'prodi' => 'Teknik Informatika'],
            ['universitas' => 'Universitas Gadjah Mada', 'prodi' => 'Ilmu Komputer'],
            ['universitas' => 'Institut Teknologi Bandung', 'prodi' => 'Teknik Elektro'],
            ['universitas' => 'Universitas Brawijaya', 'prodi' => 'Sistem Informasi'],
            ['universitas' => 'Universitas Diponegoro', 'prodi' => 'Teknik Informatika'],
            ['universitas' => 'Universitas Padjadjaran', 'prodi' => 'Manajemen Informatika'],
            ['universitas' => 'Politeknik Negeri Bandung', 'prodi' => 'Teknik Komputer'],
            ['universitas' => 'Universitas Sebelas Maret', 'prodi' => 'Informatika'],
        ];

        // Daftar mata kuliah asal dummy
        $mataKuliahAsal = [
            ['kode' => 'TI101', 'nama' => 'Algoritma dan Pemrograman', 'sks' => 3, 'nilai' => 'A'],
            ['kode' => 'TI102', 'nama' => 'Matematika Diskrit', 'sks' => 3, 'nilai' => 'B+'],
            ['kode' => 'TI103', 'nama' => 'Basis Data', 'sks' => 3, 'nilai' => 'A-'],
            ['kode' => 'TI104', 'nama' => 'Pemrograman Web', 'sks' => 3, 'nilai' => 'B'],
            ['kode' => 'TI105', 'nama' => 'Jaringan Komputer', 'sks' => 3, 'nilai' => 'A'],
            ['kode' => 'TI106', 'nama' => 'Struktur Data', 'sks' => 3, 'nilai' => 'B+'],
            ['kode' => 'TI107', 'nama' => 'Sistem Operasi', 'sks' => 3, 'nilai' => 'A-'],
            ['kode' => 'TI108', 'nama' => 'Pemrograman Berorientasi Objek', 'sks' => 3, 'nilai' => 'B+'],
            ['kode' => 'TI109', 'nama' => 'Kecerdasan Buatan', 'sks' => 3, 'nilai' => 'A'],
            ['kode' => 'TI110', 'nama' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'nilai' => 'B'],
            ['kode' => 'TI111', 'nama' => 'Manajemen Proyek TI', 'sks' => 2, 'nilai' => 'A-'],
            ['kode' => 'TI112', 'nama' => 'Statistika', 'sks' => 2, 'nilai' => 'B+'],
        ];

        // Nama dummy calon mahasiswa
        $calonMahasiswa = [
            ['nama' => 'Ahmad Fauzi', 'email' => 'ahmad.fauzi@mail.com', 'hp' => '081234567890'],
            ['nama' => 'Siti Nurhaliza', 'email' => 'siti.nurhaliza@mail.com', 'hp' => '081234567891'],
            ['nama' => 'Budi Santoso', 'email' => 'budi.santoso@mail.com', 'hp' => '081234567892'],
            ['nama' => 'Dewi Anggraini', 'email' => 'dewi.anggraini@mail.com', 'hp' => '081234567893'],
            ['nama' => 'Eko Prasetyo', 'email' => 'eko.prasetyo@mail.com', 'hp' => '081234567894'],
            ['nama' => 'Fitri Handayani', 'email' => 'fitri.handayani@mail.com', 'hp' => '081234567895'],
            ['nama' => 'Gunawan Wibisono', 'email' => 'gunawan.wibisono@mail.com', 'hp' => '081234567896'],
            ['nama' => 'Hesti Marlina', 'email' => 'hesti.marlina@mail.com', 'hp' => '081234567897'],
        ];

        // Status yang akan dibuat
        $statusList = [
            'draft',
            'menunggu_kaprodi',
            'menunggu_kaprodi',
            'diproses_kaprodi',
            'disetujui_kaprodi',
            'disetujui',
        ];

        $counter = PengajuanKonversi::count();

        foreach ($programStudis->take(3) as $prodi) { // Buat untuk 3 prodi saja
            $mataKuliahProdi = MataKuliah::where('program_studi_id', $prodi->id)->get();

            // Buat beberapa pengajuan untuk setiap prodi
            foreach ($statusList as $index => $status) {
                $counter++;
                $univ = $universitasAsal[array_rand($universitasAsal)];
                $calon = $calonMahasiswa[$index % count($calonMahasiswa)];

                $pengajuan = PengajuanKonversi::create([
                    'nomor_pengajuan' => 'KNV' . date('Ymd') . str_pad($counter, 4, '0', STR_PAD_LEFT),
                    'nama_calon_mahasiswa' => $calon['nama'],
                    'email_calon' => $calon['email'],
                    'no_hp_calon' => $calon['hp'],
                    'program_studi_tujuan_id' => $prodi->id,
                    'universitas_asal' => $univ['universitas'],
                    'program_studi_asal' => $univ['prodi'],
                    'nim_asal' => '20' . rand(18, 22) . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'tahun_masuk_asal' => rand(2019, 2022),
                    'status' => $status,
                    'catatan' => $status === 'ditolak' ? 'Dokumen tidak lengkap' : null,
                    'diproses_kaprodi_oleh' => in_array($status, ['diproses_kaprodi', 'disetujui_kaprodi', 'ditolak_kaprodi', 'disetujui']) && $kaprodi ? $kaprodi->id : null,
                    'tanggal_diproses_kaprodi' => in_array($status, ['diproses_kaprodi', 'disetujui_kaprodi', 'ditolak_kaprodi', 'disetujui']) ? now()->subDays(rand(1, 10)) : null,
                    'catatan_kaprodi' => in_array($status, ['disetujui_kaprodi', 'disetujui']) ? 'Mata kuliah sudah dipetakan sesuai kurikulum.' : null,
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);

                // Tambah detail mata kuliah (3-6 mata kuliah per pengajuan)
                $jumlahMK = rand(3, 6);
                $selectedMK = collect($mataKuliahAsal)->random($jumlahMK);

                foreach ($selectedMK as $mkIdx => $mk) {
                    $isApproved = in_array($status, ['disetujui_kaprodi', 'disetujui']);
                    $mkTujuan = $isApproved && $mataKuliahProdi->isNotEmpty() ? $mataKuliahProdi->random() : null;

                    DetailKonversi::create([
                        'pengajuan_konversi_id' => $pengajuan->id,
                        'kode_mk_asal' => $mk['kode'],
                        'nama_mk_asal' => $mk['nama'],
                        'sks_asal' => $mk['sks'],
                        'nilai_asal' => $mk['nilai'],
                        'bobot_asal' => $this->nilaiKeBobot($mk['nilai']),
                        'mata_kuliah_id' => $mkTujuan?->id,
                        'nilai_konversi' => $isApproved ? $mk['nilai'] : null,
                        'bobot_konversi' => $isApproved ? $this->nilaiKeBobot($mk['nilai']) : null,
                        'status' => $isApproved ? 'disetujui' : 'pending',
                        'alasan' => $isApproved ? 'Mata kuliah setara dengan kurikulum' : null,
                    ]);
                }
            }
        }

        $this->command->info('Data dummy konversi nilai berhasil dibuat!');
        $this->command->info('Total pengajuan: ' . PengajuanKonversi::count());
    }

    /**
     * Konversi nilai huruf ke bobot
     */
    private function nilaiKeBobot(string $nilai): float
    {
        return match ($nilai) {
            'A' => 4.00,
            'A-' => 3.75,
            'B+' => 3.50,
            'B' => 3.00,
            'B-' => 2.75,
            'C+' => 2.50,
            'C' => 2.00,
            'C-' => 1.75,
            'D' => 1.00,
            'E' => 0.00,
            default => 0.00,
        };
    }
}
