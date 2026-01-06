<?php

namespace Database\Seeders;

use App\Models\JenisTunjangan;
use App\Models\JenisPelanggaran;
use App\Models\TarifLembur;
use App\Models\KriteriaEvaluasi;
use App\Models\PeriodeEvaluasi;
use Illuminate\Database\Seeder;

class KepegawaianExtendedSeeder extends Seeder
{
    /**
     * Seed data default untuk modul kepegawaian extended
     */
    public function run(): void
    {
        $this->seedJenisTunjangan();
        $this->seedJenisPelanggaran();
        $this->seedTarifLembur();
        $this->seedPeriodeEvaluasi();
        $this->seedKriteriaEvaluasi();

        $this->command->info('Kepegawaian extended data seeded successfully!');
    }

    private function seedJenisTunjangan(): void
    {
        $jenisTunjangan = [
            ['kode' => 'T001', 'nama' => 'Tunjangan Jabatan', 'deskripsi' => 'Tunjangan untuk pemegang jabatan struktural', 'tipe_perhitungan' => 'tetap', 'nilai_default' => 2000000, 'is_active' => true],
            ['kode' => 'T002', 'nama' => 'Tunjangan Kinerja', 'deskripsi' => 'Tunjangan berdasarkan kinerja pegawai', 'tipe_perhitungan' => 'variabel', 'nilai_default' => 1500000, 'is_active' => true],
            ['kode' => 'T003', 'nama' => 'Tunjangan Keluarga', 'deskripsi' => 'Tunjangan untuk pegawai yang sudah berkeluarga', 'tipe_perhitungan' => 'tetap', 'nilai_default' => 500000, 'is_active' => true],
            ['kode' => 'T004', 'nama' => 'Tunjangan Pendidikan', 'deskripsi' => 'Tunjangan untuk pegawai yang melanjutkan pendidikan', 'tipe_perhitungan' => 'tetap', 'nilai_default' => 1000000, 'is_active' => true],
            ['kode' => 'T005', 'nama' => 'Tunjangan Transportasi', 'deskripsi' => 'Tunjangan transportasi bulanan', 'tipe_perhitungan' => 'tetap', 'nilai_default' => 500000, 'is_active' => true],
            ['kode' => 'T006', 'nama' => 'Tunjangan Makan', 'deskripsi' => 'Tunjangan makan harian', 'tipe_perhitungan' => 'tetap', 'nilai_default' => 750000, 'is_active' => true],
            ['kode' => 'T007', 'nama' => 'Tunjangan Kesehatan', 'deskripsi' => 'Tunjangan kesehatan pegawai dan keluarga', 'tipe_perhitungan' => 'tetap', 'nilai_default' => 500000, 'is_active' => true],
            ['kode' => 'T008', 'nama' => 'Tunjangan Sertifikasi', 'deskripsi' => 'Tunjangan untuk dosen tersertifikasi', 'tipe_perhitungan' => 'tetap', 'nilai_default' => 3000000, 'is_active' => true],
        ];

        foreach ($jenisTunjangan as $data) {
            JenisTunjangan::firstOrCreate(['kode' => $data['kode']], $data);
        }

        $this->command->info('- Jenis Tunjangan: ' . count($jenisTunjangan) . ' records');
    }

    private function seedJenisPelanggaran(): void
    {
        $jenisPelanggaran = [
            ['kode' => 'P001', 'nama' => 'Terlambat Masuk Kerja', 'tingkat' => 'ringan', 'deskripsi' => 'Terlambat masuk kerja tanpa alasan yang sah', 'is_active' => true],
            ['kode' => 'P002', 'nama' => 'Tidak Hadir Tanpa Keterangan', 'tingkat' => 'sedang', 'deskripsi' => 'Alpha/tidak hadir tanpa izin atau keterangan', 'is_active' => true],
            ['kode' => 'P003', 'nama' => 'Meninggalkan Tugas', 'tingkat' => 'sedang', 'deskripsi' => 'Meninggalkan tugas saat jam kerja tanpa izin', 'is_active' => true],
            ['kode' => 'P004', 'nama' => 'Tidak Melaksanakan Tugas', 'tingkat' => 'sedang', 'deskripsi' => 'Tidak melaksanakan tugas yang diberikan', 'is_active' => true],
            ['kode' => 'P005', 'nama' => 'Pelanggaran Kode Etik', 'tingkat' => 'berat', 'deskripsi' => 'Melanggar kode etik pegawai/dosen', 'is_active' => true],
            ['kode' => 'P006', 'nama' => 'Penyalahgunaan Wewenang', 'tingkat' => 'berat', 'deskripsi' => 'Menyalahgunakan wewenang jabatan', 'is_active' => true],
            ['kode' => 'P007', 'nama' => 'Indisipliner Berulang', 'tingkat' => 'sedang', 'deskripsi' => 'Melakukan pelanggaran ringan secara berulang', 'is_active' => true],
            ['kode' => 'P008', 'nama' => 'Tidak Menjalankan Tri Dharma', 'tingkat' => 'sedang', 'deskripsi' => 'Tidak melaksanakan tugas Tri Dharma Perguruan Tinggi', 'is_active' => true],
        ];

        foreach ($jenisPelanggaran as $data) {
            JenisPelanggaran::firstOrCreate(['kode' => $data['kode']], $data);
        }

        $this->command->info('- Jenis Pelanggaran: ' . count($jenisPelanggaran) . ' records');
    }

    private function seedTarifLembur(): void
    {
        $tarifLembur = [
            ['kode' => 'TL0001', 'nama' => 'Jam Pertama Hari Kerja', 'deskripsi' => 'Tarif lembur untuk jam pertama pada hari kerja biasa', 'jenis_hari' => 'kerja', 'jenis_jam' => 'jam_pertama', 'persentase' => 150, 'nominal_tetap' => 25000, 'is_active' => true],
            ['kode' => 'TL0002', 'nama' => 'Jam Kedua dst Hari Kerja', 'deskripsi' => 'Tarif lembur untuk jam kedua dan seterusnya pada hari kerja biasa', 'jenis_hari' => 'kerja', 'jenis_jam' => 'jam_kedua_dst', 'persentase' => 200, 'nominal_tetap' => 30000, 'is_active' => true],
            ['kode' => 'TL0003', 'nama' => 'Jam Pertama Hari Libur', 'deskripsi' => 'Tarif lembur untuk jam pertama pada hari libur/weekend', 'jenis_hari' => 'libur', 'jenis_jam' => 'jam_pertama', 'persentase' => 200, 'nominal_tetap' => 40000, 'is_active' => true],
            ['kode' => 'TL0004', 'nama' => 'Jam Kedua dst Hari Libur', 'deskripsi' => 'Tarif lembur untuk jam kedua dan seterusnya pada hari libur/weekend', 'jenis_hari' => 'libur', 'jenis_jam' => 'jam_kedua_dst', 'persentase' => 300, 'nominal_tetap' => 50000, 'is_active' => true],
            ['kode' => 'TL0005', 'nama' => 'Semua Jam Hari Libur Nasional', 'deskripsi' => 'Tarif lembur untuk semua jam pada hari libur nasional', 'jenis_hari' => 'libur_nasional', 'jenis_jam' => 'semua', 'persentase' => 300, 'nominal_tetap' => 60000, 'is_active' => true],
        ];

        foreach ($tarifLembur as $data) {
            TarifLembur::firstOrCreate(
                ['kode' => $data['kode']], 
                $data
            );
        }

        $this->command->info('- Tarif Lembur: ' . count($tarifLembur) . ' records');
    }

    private function seedPeriodeEvaluasi(): void
    {
        $tahun = date('Y');
        
        $periodeEvaluasi = [
            ['nama' => "Evaluasi Semester Ganjil $tahun", 'tahun' => $tahun, 'semester' => 'Ganjil', 'tanggal_mulai' => "$tahun-01-15", 'tanggal_selesai' => "$tahun-02-15", 'status' => 'selesai'],
            ['nama' => "Evaluasi Semester Genap $tahun", 'tahun' => $tahun, 'semester' => 'Genap', 'tanggal_mulai' => "$tahun-07-15", 'tanggal_selesai' => "$tahun-08-15", 'status' => 'aktif'],
        ];

        foreach ($periodeEvaluasi as $data) {
            PeriodeEvaluasi::firstOrCreate(
                ['tahun' => $data['tahun'], 'semester' => $data['semester']], 
                $data
            );
        }

        $this->command->info('- Periode Evaluasi: ' . count($periodeEvaluasi) . ' records');
    }

    private function seedKriteriaEvaluasi(): void
    {
        $kriteria = [
            ['nama' => 'Kehadiran', 'bobot' => 15, 'deskripsi' => 'Tingkat kehadiran dan kedisiplinan waktu', 'kategori' => 'semua', 'urutan' => 1, 'is_active' => true],
            ['nama' => 'Kualitas Pengajaran', 'bobot' => 25, 'deskripsi' => 'Kualitas dalam melaksanakan tugas pengajaran', 'kategori' => 'dosen', 'urutan' => 2, 'is_active' => true],
            ['nama' => 'Penelitian', 'bobot' => 20, 'deskripsi' => 'Produktivitas dan kualitas penelitian', 'kategori' => 'dosen', 'urutan' => 3, 'is_active' => true],
            ['nama' => 'Pengabdian Masyarakat', 'bobot' => 15, 'deskripsi' => 'Kontribusi dalam pengabdian masyarakat', 'kategori' => 'dosen', 'urutan' => 4, 'is_active' => true],
            ['nama' => 'Kerjasama Tim', 'bobot' => 10, 'deskripsi' => 'Kemampuan bekerjasama dengan rekan kerja', 'kategori' => 'semua', 'urutan' => 5, 'is_active' => true],
            ['nama' => 'Inisiatif & Kreativitas', 'bobot' => 10, 'deskripsi' => 'Inisiatif dan kreativitas dalam bekerja', 'kategori' => 'semua', 'urutan' => 6, 'is_active' => true],
            ['nama' => 'Administrasi', 'bobot' => 5, 'deskripsi' => 'Kelengkapan dan ketepatan administrasi', 'kategori' => 'semua', 'urutan' => 7, 'is_active' => true],
        ];

        foreach ($kriteria as $data) {
            KriteriaEvaluasi::firstOrCreate(['nama' => $data['nama']], $data);
        }

        $this->command->info('- Kriteria Evaluasi: ' . count($kriteria) . ' records');
    }
}
