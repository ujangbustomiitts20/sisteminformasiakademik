<?php

namespace Database\Seeders;

use App\Models\UraianKegiatanSkp;
use Illuminate\Database\Seeder;

class UraianKegiatanSkpSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Uraian Kegiatan SKP...');

        // Data format: [tipe_pegawai, kategori, sub_kategori, uraian_kegiatan, satuan, target_default, bobot]
        $data = [
            // ============================================
            // === DOSEN - TRI DHARMA - PENDIDIKAN ===
            // ============================================
            ['dosen', 'tri_dharma', 'pendidikan', 'Melaksanakan perkuliahan sesuai jadwal', 'SKS', 12, 10],
            ['dosen', 'tri_dharma', 'pendidikan', 'Membimbing tugas akhir/skripsi mahasiswa S1', 'mahasiswa', 5, 8],
            ['dosen', 'tri_dharma', 'pendidikan', 'Membimbing tesis mahasiswa S2', 'mahasiswa', 3, 10],
            ['dosen', 'tri_dharma', 'pendidikan', 'Membimbing disertasi mahasiswa S3', 'mahasiswa', 2, 15],
            ['dosen', 'tri_dharma', 'pendidikan', 'Membimbing kerja praktik mahasiswa', 'mahasiswa', 5, 5],
            ['dosen', 'tri_dharma', 'pendidikan', 'Menguji sidang tugas akhir/skripsi sebagai ketua', 'mahasiswa', 10, 7],
            ['dosen', 'tri_dharma', 'pendidikan', 'Menguji sidang tugas akhir/skripsi sebagai anggota', 'mahasiswa', 15, 5],
            ['dosen', 'tri_dharma', 'pendidikan', 'Menguji sidang tesis sebagai ketua', 'mahasiswa', 5, 8],
            ['dosen', 'tri_dharma', 'pendidikan', 'Menguji sidang tesis sebagai anggota', 'mahasiswa', 8, 6],
            ['dosen', 'tri_dharma', 'pendidikan', 'Menguji sidang disertasi sebagai ketua', 'mahasiswa', 3, 10],
            ['dosen', 'tri_dharma', 'pendidikan', 'Menguji sidang disertasi sebagai anggota', 'mahasiswa', 5, 8],
            ['dosen', 'tri_dharma', 'pendidikan', 'Menguji sidang komprehensif/proposal', 'mahasiswa', 10, 4],
            ['dosen', 'tri_dharma', 'pendidikan', 'Menyusun dan mengembangkan bahan ajar/modul', 'modul', 2, 8],
            ['dosen', 'tri_dharma', 'pendidikan', 'Membimbing mahasiswa dalam PKM/PIMNAS', 'tim', 2, 6],
            ['dosen', 'tri_dharma', 'pendidikan', 'Melaksanakan pembimbingan akademik sebagai dosen wali', 'mahasiswa', 25, 5],
            ['dosen', 'tri_dharma', 'pendidikan', 'Menyusun RPS/RPKPS mata kuliah', 'dokumen', 4, 4],
            ['dosen', 'tri_dharma', 'pendidikan', 'Menyusun soal ujian dan rubrik penilaian', 'dokumen', 8, 3],
            ['dosen', 'tri_dharma', 'pendidikan', 'Melaksanakan praktikum/laboratorium', 'kegiatan', 12, 6],
            ['dosen', 'tri_dharma', 'pendidikan', 'Menyusun diktat kuliah', 'diktat', 1, 6],
            ['dosen', 'tri_dharma', 'pendidikan', 'Menyusun petunjuk praktikum', 'buku', 1, 5],
            ['dosen', 'tri_dharma', 'pendidikan', 'Mengembangkan media pembelajaran berbasis teknologi', 'media', 2, 5],
            ['dosen', 'tri_dharma', 'pendidikan', 'Mengembangkan metode pembelajaran inovatif', 'metode', 1, 5],
            ['dosen', 'tri_dharma', 'pendidikan', 'Melakukan asesmen/evaluasi pembelajaran', 'kegiatan', 4, 3],
            ['dosen', 'tri_dharma', 'pendidikan', 'Membimbing mahasiswa dalam kompetisi nasional', 'tim', 2, 6],
            ['dosen', 'tri_dharma', 'pendidikan', 'Membimbing mahasiswa dalam kompetisi internasional', 'tim', 1, 8],
            ['dosen', 'tri_dharma', 'pendidikan', 'Menjadi koordinator mata kuliah', 'mata kuliah', 2, 4],
            ['dosen', 'tri_dharma', 'pendidikan', 'Menjadi koordinator praktikum', 'praktikum', 2, 4],
            ['dosen', 'tri_dharma', 'pendidikan', 'Membuat video pembelajaran/tutorial', 'video', 4, 4],
            ['dosen', 'tri_dharma', 'pendidikan', 'Mengembangkan e-learning/blended learning', 'modul', 2, 5],
            ['dosen', 'tri_dharma', 'pendidikan', 'Melaksanakan kuliah tamu/guest lecture', 'kegiatan', 2, 3],
            ['dosen', 'tri_dharma', 'pendidikan', 'Melaksanakan team teaching', 'SKS', 6, 5],
            ['dosen', 'tri_dharma', 'pendidikan', 'Mengoreksi dan menilai tugas/ujian mahasiswa', 'kegiatan', 8, 3],

            // ============================================
            // === DOSEN - TRI DHARMA - PENELITIAN ===
            // ============================================
            ['dosen', 'tri_dharma', 'penelitian', 'Melaksanakan penelitian (Hibah Internal) sebagai ketua', 'penelitian', 1, 15],
            ['dosen', 'tri_dharma', 'penelitian', 'Melaksanakan penelitian (Hibah Internal) sebagai anggota', 'penelitian', 1, 10],
            ['dosen', 'tri_dharma', 'penelitian', 'Melaksanakan penelitian (Hibah Eksternal Kemendikbud) sebagai ketua', 'penelitian', 1, 25],
            ['dosen', 'tri_dharma', 'penelitian', 'Melaksanakan penelitian (Hibah Eksternal Kemendikbud) sebagai anggota', 'penelitian', 1, 15],
            ['dosen', 'tri_dharma', 'penelitian', 'Melaksanakan penelitian (Hibah Non-Kemendikbud) sebagai ketua', 'penelitian', 1, 20],
            ['dosen', 'tri_dharma', 'penelitian', 'Melaksanakan penelitian (Hibah Non-Kemendikbud) sebagai anggota', 'penelitian', 1, 12],
            ['dosen', 'tri_dharma', 'penelitian', 'Melaksanakan penelitian mandiri', 'penelitian', 1, 10],
            ['dosen', 'tri_dharma', 'penelitian', 'Membuat publikasi ilmiah pada jurnal nasional tidak terakreditasi', 'artikel', 1, 8],
            ['dosen', 'tri_dharma', 'penelitian', 'Membuat publikasi ilmiah pada jurnal nasional terakreditasi Sinta 5-6', 'artikel', 1, 12],
            ['dosen', 'tri_dharma', 'penelitian', 'Membuat publikasi ilmiah pada jurnal nasional terakreditasi Sinta 3-4', 'artikel', 1, 15],
            ['dosen', 'tri_dharma', 'penelitian', 'Membuat publikasi ilmiah pada jurnal nasional terakreditasi Sinta 1-2', 'artikel', 1, 20],
            ['dosen', 'tri_dharma', 'penelitian', 'Membuat publikasi ilmiah pada jurnal internasional', 'artikel', 1, 20],
            ['dosen', 'tri_dharma', 'penelitian', 'Membuat publikasi ilmiah pada jurnal internasional bereputasi (Q3-Q4)', 'artikel', 1, 25],
            ['dosen', 'tri_dharma', 'penelitian', 'Membuat publikasi ilmiah pada jurnal internasional bereputasi (Q1-Q2)', 'artikel', 1, 30],
            ['dosen', 'tri_dharma', 'penelitian', 'Mempresentasikan makalah pada seminar nasional', 'makalah', 2, 5],
            ['dosen', 'tri_dharma', 'penelitian', 'Mempresentasikan makalah pada seminar internasional', 'makalah', 1, 8],
            ['dosen', 'tri_dharma', 'penelitian', 'Menulis prosiding seminar nasional', 'artikel', 2, 6],
            ['dosen', 'tri_dharma', 'penelitian', 'Menulis prosiding seminar internasional terindeks Scopus', 'artikel', 1, 12],
            ['dosen', 'tri_dharma', 'penelitian', 'Menulis prosiding seminar internasional tidak terindeks', 'artikel', 1, 8],
            ['dosen', 'tri_dharma', 'penelitian', 'Menulis buku referensi/monograf', 'buku', 1, 15],
            ['dosen', 'tri_dharma', 'penelitian', 'Menulis buku ajar ber-ISBN', 'buku', 1, 12],
            ['dosen', 'tri_dharma', 'penelitian', 'Menulis buku chapter/bagian buku', 'chapter', 1, 8],
            ['dosen', 'tri_dharma', 'penelitian', 'Menerjemahkan/menyadur buku', 'buku', 1, 8],
            ['dosen', 'tri_dharma', 'penelitian', 'Menyunting/mengedit buku', 'buku', 1, 6],
            ['dosen', 'tri_dharma', 'penelitian', 'Mendaftarkan HKI Cipta', 'HKI', 1, 8],
            ['dosen', 'tri_dharma', 'penelitian', 'Mendaftarkan HKI Paten Sederhana', 'paten', 1, 12],
            ['dosen', 'tri_dharma', 'penelitian', 'Mendaftarkan HKI Paten', 'paten', 1, 15],
            ['dosen', 'tri_dharma', 'penelitian', 'Mendaftarkan HKI Desain Industri', 'HKI', 1, 10],
            ['dosen', 'tri_dharma', 'penelitian', 'Membuat karya inovatif/produk TTG', 'produk', 1, 10],
            ['dosen', 'tri_dharma', 'penelitian', 'Membuat karya seni/desain yang dipamerkan', 'karya', 1, 8],
            ['dosen', 'tri_dharma', 'penelitian', 'Menulis artikel di media massa/koran', 'artikel', 2, 4],
            ['dosen', 'tri_dharma', 'penelitian', 'Mendapatkan sitasi dari artikel ilmiah', 'sitasi', 20, 3],
            ['dosen', 'tri_dharma', 'penelitian', 'Menjadi editor jurnal nasional terakreditasi', 'jurnal', 1, 6],
            ['dosen', 'tri_dharma', 'penelitian', 'Menjadi editor jurnal internasional bereputasi', 'jurnal', 1, 10],

            // ============================================
            // === DOSEN - TRI DHARMA - PENGABDIAN ===
            // ============================================
            ['dosen', 'tri_dharma', 'pengabdian', 'Melaksanakan pengabdian kepada masyarakat (Hibah Internal) sebagai ketua', 'kegiatan', 1, 10],
            ['dosen', 'tri_dharma', 'pengabdian', 'Melaksanakan pengabdian kepada masyarakat (Hibah Internal) sebagai anggota', 'kegiatan', 1, 6],
            ['dosen', 'tri_dharma', 'pengabdian', 'Melaksanakan pengabdian kepada masyarakat (Hibah Eksternal) sebagai ketua', 'kegiatan', 1, 15],
            ['dosen', 'tri_dharma', 'pengabdian', 'Melaksanakan pengabdian kepada masyarakat (Hibah Eksternal) sebagai anggota', 'kegiatan', 1, 10],
            ['dosen', 'tri_dharma', 'pengabdian', 'Melaksanakan pengabdian mandiri', 'kegiatan', 2, 8],
            ['dosen', 'tri_dharma', 'pengabdian', 'Melaksanakan pelatihan/workshop untuk masyarakat', 'kegiatan', 3, 5],
            ['dosen', 'tri_dharma', 'pengabdian', 'Menjadi narasumber/pemateri dalam kegiatan masyarakat', 'kegiatan', 4, 4],
            ['dosen', 'tri_dharma', 'pengabdian', 'Menjadi narasumber/pemateri tingkat nasional', 'kegiatan', 2, 6],
            ['dosen', 'tri_dharma', 'pengabdian', 'Menjadi narasumber/pemateri tingkat internasional', 'kegiatan', 1, 8],
            ['dosen', 'tri_dharma', 'pengabdian', 'Memberikan konsultasi kepada industri/instansi pemerintah', 'kegiatan', 3, 5],
            ['dosen', 'tri_dharma', 'pengabdian', 'Memberikan layanan kepakaran/expert judgment', 'kegiatan', 2, 5],
            ['dosen', 'tri_dharma', 'pengabdian', 'Melaksanakan KKN sebagai DPL (Dosen Pembimbing Lapangan)', 'kelompok', 2, 6],
            ['dosen', 'tri_dharma', 'pengabdian', 'Memberikan penyuluhan kepada masyarakat', 'kegiatan', 4, 4],
            ['dosen', 'tri_dharma', 'pengabdian', 'Memberikan pelatihan/pendampingan UMKM', 'kegiatan', 2, 5],
            ['dosen', 'tri_dharma', 'pengabdian', 'Memberikan layanan kesehatan gratis', 'kegiatan', 2, 5],
            ['dosen', 'tri_dharma', 'pengabdian', 'Memberikan bantuan teknis kepada masyarakat', 'kegiatan', 3, 4],
            ['dosen', 'tri_dharma', 'pengabdian', 'Membina desa binaan', 'desa', 1, 8],
            ['dosen', 'tri_dharma', 'pengabdian', 'Membina sekolah binaan', 'sekolah', 1, 6],
            ['dosen', 'tri_dharma', 'pengabdian', 'Menjadi juri/penilai dalam kompetisi tingkat daerah', 'kegiatan', 2, 3],
            ['dosen', 'tri_dharma', 'pengabdian', 'Menjadi juri/penilai dalam kompetisi tingkat nasional', 'kegiatan', 1, 5],
            ['dosen', 'tri_dharma', 'pengabdian', 'Menjadi juri/penilai dalam kompetisi tingkat internasional', 'kegiatan', 1, 7],
            ['dosen', 'tri_dharma', 'pengabdian', 'Menulis artikel pengabdian di jurnal pengabdian', 'artikel', 1, 6],
            ['dosen', 'tri_dharma', 'pengabdian', 'Menghasilkan produk yang diadopsi masyarakat', 'produk', 1, 8],
            ['dosen', 'tri_dharma', 'pengabdian', 'Menjadi saksi ahli', 'kegiatan', 2, 5],
            ['dosen', 'tri_dharma', 'pengabdian', 'Memberikan ceramah keagamaan', 'kegiatan', 4, 3],

            // ============================================
            // === DOSEN - PENUNJANG - AKADEMIK ===
            // ============================================
            ['dosen', 'penunjang', 'akademik', 'Menjadi panitia ujian tengah semester', 'kegiatan', 2, 2],
            ['dosen', 'penunjang', 'akademik', 'Menjadi panitia ujian akhir semester', 'kegiatan', 2, 2],
            ['dosen', 'penunjang', 'akademik', 'Menjadi panitia wisuda', 'kegiatan', 2, 3],
            ['dosen', 'penunjang', 'akademik', 'Menjadi panitia PKKMB/orientasi mahasiswa baru', 'kegiatan', 1, 3],
            ['dosen', 'penunjang', 'akademik', 'Menjadi panitia yudisium', 'kegiatan', 2, 2],
            ['dosen', 'penunjang', 'akademik', 'Menjadi panitia akreditasi program studi (APS)', 'kegiatan', 1, 8],
            ['dosen', 'penunjang', 'akademik', 'Menjadi panitia akreditasi institusi (AIPT)', 'kegiatan', 1, 10],
            ['dosen', 'penunjang', 'akademik', 'Menjadi panitia sertifikasi internasional', 'kegiatan', 1, 10],
            ['dosen', 'penunjang', 'akademik', 'Menyusun borang akreditasi program studi', 'dokumen', 1, 10],
            ['dosen', 'penunjang', 'akademik', 'Menyusun borang akreditasi institusi', 'dokumen', 1, 12],
            ['dosen', 'penunjang', 'akademik', 'Menyusun LKPS (Laporan Kinerja Program Studi)', 'dokumen', 1, 6],
            ['dosen', 'penunjang', 'akademik', 'Menyusun LED (Laporan Evaluasi Diri)', 'dokumen', 1, 8],
            ['dosen', 'penunjang', 'akademik', 'Menjadi pengawas ujian', 'kegiatan', 10, 2],
            ['dosen', 'penunjang', 'akademik', 'Menjadi panitia penerimaan mahasiswa baru', 'kegiatan', 1, 4],
            ['dosen', 'penunjang', 'akademik', 'Menjadi panitia dies natalis', 'kegiatan', 1, 2],
            ['dosen', 'penunjang', 'akademik', 'Menjadi panitia seminar/konferensi tingkat nasional', 'kegiatan', 1, 4],
            ['dosen', 'penunjang', 'akademik', 'Menjadi panitia seminar/konferensi tingkat internasional', 'kegiatan', 1, 6],
            ['dosen', 'penunjang', 'akademik', 'Menyusun kurikulum program studi', 'dokumen', 1, 8],
            ['dosen', 'penunjang', 'akademik', 'Melakukan evaluasi kurikulum', 'dokumen', 1, 5],
            ['dosen', 'penunjang', 'akademik', 'Menjadi tim GKM (Gugus Kendali Mutu)', 'bulan', 12, 5],
            ['dosen', 'penunjang', 'akademik', 'Menjadi tim audit mutu internal', 'kegiatan', 2, 4],
            ['dosen', 'penunjang', 'akademik', 'Menjadi assessor BKD', 'kegiatan', 2, 4],

            // ============================================
            // === DOSEN - PENUNJANG - STRUKTURAL ===
            // ============================================
            ['dosen', 'penunjang', 'struktural', 'Melaksanakan tugas sebagai Rektor', 'bulan', 12, 25],
            ['dosen', 'penunjang', 'struktural', 'Melaksanakan tugas sebagai Wakil Rektor', 'bulan', 12, 20],
            ['dosen', 'penunjang', 'struktural', 'Melaksanakan tugas sebagai Dekan', 'bulan', 12, 18],
            ['dosen', 'penunjang', 'struktural', 'Melaksanakan tugas sebagai Wakil Dekan', 'bulan', 12, 15],
            ['dosen', 'penunjang', 'struktural', 'Melaksanakan tugas sebagai Ketua Program Studi', 'bulan', 12, 12],
            ['dosen', 'penunjang', 'struktural', 'Melaksanakan tugas sebagai Sekretaris Program Studi', 'bulan', 12, 8],
            ['dosen', 'penunjang', 'struktural', 'Melaksanakan tugas sebagai Direktur Pascasarjana', 'bulan', 12, 15],
            ['dosen', 'penunjang', 'struktural', 'Melaksanakan tugas sebagai Kepala Lembaga Penelitian', 'bulan', 12, 12],
            ['dosen', 'penunjang', 'struktural', 'Melaksanakan tugas sebagai Kepala Lembaga Pengabdian', 'bulan', 12, 12],
            ['dosen', 'penunjang', 'struktural', 'Melaksanakan tugas sebagai Kepala Biro/Bagian', 'bulan', 12, 10],
            ['dosen', 'penunjang', 'struktural', 'Melaksanakan tugas sebagai Kepala UPT', 'bulan', 12, 8],
            ['dosen', 'penunjang', 'struktural', 'Melaksanakan tugas sebagai Kepala Laboratorium', 'bulan', 12, 6],
            ['dosen', 'penunjang', 'struktural', 'Melaksanakan tugas sebagai Kepala Studio', 'bulan', 12, 6],
            ['dosen', 'penunjang', 'struktural', 'Melaksanakan tugas sebagai Kepala Pusat Studi', 'bulan', 12, 6],
            ['dosen', 'penunjang', 'struktural', 'Melaksanakan tugas sebagai anggota senat universitas', 'rapat', 12, 4],
            ['dosen', 'penunjang', 'struktural', 'Melaksanakan tugas sebagai anggota senat fakultas', 'rapat', 12, 3],
            ['dosen', 'penunjang', 'struktural', 'Melaksanakan tugas sebagai sekretaris senat', 'bulan', 12, 5],
            ['dosen', 'penunjang', 'struktural', 'Melaksanakan tugas sebagai pembimbing UKM', 'bulan', 12, 4],
            ['dosen', 'penunjang', 'struktural', 'Melaksanakan tugas sebagai pembina Himpunan Mahasiswa', 'bulan', 12, 4],
            ['dosen', 'penunjang', 'struktural', 'Menjadi Ketua Jurusan/Departemen', 'bulan', 12, 12],
            ['dosen', 'penunjang', 'struktural', 'Menjadi Sekretaris Jurusan/Departemen', 'bulan', 12, 8],

            // ============================================
            // === DOSEN - PENUNJANG - PROFESI ===
            // ============================================
            ['dosen', 'penunjang', 'profesi', 'Mengikuti pelatihan/workshop dalam negeri', 'kegiatan', 4, 3],
            ['dosen', 'penunjang', 'profesi', 'Mengikuti pelatihan/workshop luar negeri', 'kegiatan', 1, 5],
            ['dosen', 'penunjang', 'profesi', 'Mengikuti seminar/webinar nasional', 'kegiatan', 6, 2],
            ['dosen', 'penunjang', 'profesi', 'Mengikuti seminar/webinar internasional', 'kegiatan', 3, 3],
            ['dosen', 'penunjang', 'profesi', 'Mendapatkan sertifikasi kompetensi/profesi nasional', 'sertifikat', 1, 5],
            ['dosen', 'penunjang', 'profesi', 'Mendapatkan sertifikasi kompetensi/profesi internasional', 'sertifikat', 1, 8],
            ['dosen', 'penunjang', 'profesi', 'Lulus sertifikasi dosen (Serdos)', 'sertifikat', 1, 10],
            ['dosen', 'penunjang', 'profesi', 'Menjadi reviewer jurnal ilmiah nasional', 'artikel', 6, 3],
            ['dosen', 'penunjang', 'profesi', 'Menjadi reviewer jurnal ilmiah internasional', 'artikel', 4, 5],
            ['dosen', 'penunjang', 'profesi', 'Menjadi reviewer proposal penelitian', 'proposal', 5, 3],
            ['dosen', 'penunjang', 'profesi', 'Menjadi reviewer proposal pengabdian', 'proposal', 5, 3],
            ['dosen', 'penunjang', 'profesi', 'Menjadi anggota asosiasi profesi tingkat nasional', 'organisasi', 1, 2],
            ['dosen', 'penunjang', 'profesi', 'Menjadi anggota asosiasi profesi tingkat internasional', 'organisasi', 1, 3],
            ['dosen', 'penunjang', 'profesi', 'Menjadi pengurus asosiasi profesi tingkat nasional', 'organisasi', 1, 4],
            ['dosen', 'penunjang', 'profesi', 'Menjadi pengurus asosiasi profesi tingkat internasional', 'organisasi', 1, 6],
            ['dosen', 'penunjang', 'profesi', 'Mengikuti program sabbatical leave', 'bulan', 6, 10],
            ['dosen', 'penunjang', 'profesi', 'Mengikuti program visiting professor', 'bulan', 3, 8],
            ['dosen', 'penunjang', 'profesi', 'Mengikuti program post-doctoral', 'tahun', 1, 15],
            ['dosen', 'penunjang', 'profesi', 'Melaksanakan studi lanjut S2', 'semester', 4, 20],
            ['dosen', 'penunjang', 'profesi', 'Melaksanakan studi lanjut S3', 'semester', 8, 25],
            ['dosen', 'penunjang', 'profesi', 'Menjadi assesor kompetensi', 'kegiatan', 3, 4],
            ['dosen', 'penunjang', 'profesi', 'Menjadi asesor BAN-PT', 'kegiatan', 2, 6],
            ['dosen', 'penunjang', 'profesi', 'Menjadi asesor LAM', 'kegiatan', 2, 6],

            // ============================================
            // === DOSEN - PENUNJANG - ORGANISASI ===
            // ============================================
            ['dosen', 'penunjang', 'organisasi', 'Menjadi pengurus organisasi profesi tingkat cabang', 'jabatan', 1, 3],
            ['dosen', 'penunjang', 'organisasi', 'Menjadi pengurus organisasi profesi tingkat wilayah', 'jabatan', 1, 4],
            ['dosen', 'penunjang', 'organisasi', 'Menjadi pengurus organisasi profesi tingkat pusat', 'jabatan', 1, 6],
            ['dosen', 'penunjang', 'organisasi', 'Menjadi anggota organisasi kemasyarakatan', 'organisasi', 1, 2],
            ['dosen', 'penunjang', 'organisasi', 'Menjadi pengurus organisasi kemasyarakatan', 'organisasi', 1, 3],
            ['dosen', 'penunjang', 'organisasi', 'Menjadi pengurus yayasan pendidikan', 'jabatan', 1, 4],
            ['dosen', 'penunjang', 'organisasi', 'Menjadi anggota dewan pendidikan', 'jabatan', 1, 4],
            ['dosen', 'penunjang', 'organisasi', 'Aktif dalam kegiatan kerohanian di kampus', 'kegiatan', 12, 2],

            // ============================================
            // === DOSEN - TAMBAHAN ===
            // ============================================
            ['dosen', 'tambahan', null, 'Mengikuti kegiatan kemahasiswaan sebagai pendamping', 'kegiatan', 4, 2],
            ['dosen', 'tambahan', null, 'Menjadi fasilitator kegiatan orientasi mahasiswa baru', 'kegiatan', 1, 2],
            ['dosen', 'tambahan', null, 'Melaksanakan tugas khusus dari pimpinan', 'kegiatan', 4, 3],
            ['dosen', 'tambahan', null, 'Menjadi Tim dalam kegiatan Institusi', 'kegiatan', 4, 3],
            ['dosen', 'tambahan', null, 'Menghadiri rapat koordinasi program studi', 'rapat', 12, 2],
            ['dosen', 'tambahan', null, 'Menghadiri rapat koordinasi fakultas', 'rapat', 6, 2],
            ['dosen', 'tambahan', null, 'Menghadiri rapat koordinasi universitas', 'rapat', 4, 2],
            ['dosen', 'tambahan', null, 'Piket/jaga di program studi', 'kegiatan', 12, 2],
            ['dosen', 'tambahan', null, 'Menjadi pembina kegiatan mahasiswa', 'kegiatan', 6, 3],
            ['dosen', 'tambahan', null, 'Mendampingi kunjungan industri', 'kegiatan', 2, 2],
            ['dosen', 'tambahan', null, 'Mendampingi studi ekskursi mahasiswa', 'kegiatan', 1, 3],
            ['dosen', 'tambahan', null, 'Menerima kunjungan tamu/delegasi', 'kegiatan', 4, 2],
            ['dosen', 'tambahan', null, 'Melakukan benchmarking ke institusi lain', 'kegiatan', 2, 3],
            ['dosen', 'tambahan', null, 'Mengikuti upacara/kegiatan seremonial', 'kegiatan', 12, 1],
            ['dosen', 'tambahan', null, 'Mengelola media sosial program studi', 'bulan', 12, 3],
            ['dosen', 'tambahan', null, 'Menjadi narahubung kerjasama', 'kegiatan', 4, 3],
            ['dosen', 'tambahan', null, 'Mengikuti kegiatan olahraga/seni institusi', 'kegiatan', 4, 1],

            // ============================================
            // === TENDIK - LAYANAN AKADEMIK ===
            // ============================================
            ['tendik', 'tendik', 'akademik', 'Melakukan registrasi mahasiswa baru', 'mahasiswa', 300, 10],
            ['tendik', 'tendik', 'akademik', 'Memproses heregistrasi mahasiswa', 'mahasiswa', 1000, 8],
            ['tendik', 'tendik', 'akademik', 'Memproses pengajuan KRS mahasiswa', 'dokumen', 500, 8],
            ['tendik', 'tendik', 'akademik', 'Membuat jadwal perkuliahan', 'jadwal', 2, 8],
            ['tendik', 'tendik', 'akademik', 'Memproses transkrip nilai mahasiswa', 'dokumen', 200, 7],
            ['tendik', 'tendik', 'akademik', 'Memproses pengajuan cuti akademik', 'dokumen', 30, 5],
            ['tendik', 'tendik', 'akademik', 'Melayani legalisir dokumen akademik', 'dokumen', 200, 5],
            ['tendik', 'tendik', 'akademik', 'Memproses pengajuan wisuda', 'dokumen', 100, 7],
            ['tendik', 'tendik', 'akademik', 'Memproses ijazah dan transkrip kelulusan', 'dokumen', 100, 8],
            ['tendik', 'tendik', 'akademik', 'Mengelola data akademik di sistem informasi', 'data', 12, 10],
            ['tendik', 'tendik', 'akademik', 'Memproses perubahan data mahasiswa', 'dokumen', 100, 5],
            ['tendik', 'tendik', 'akademik', 'Memproses pengajuan pindah program studi', 'dokumen', 20, 5],
            ['tendik', 'tendik', 'akademik', 'Memproses pengajuan alih kredit/transfer', 'dokumen', 30, 6],
            ['tendik', 'tendik', 'akademik', 'Memproses pengaktifan kembali mahasiswa', 'dokumen', 30, 5],
            ['tendik', 'tendik', 'akademik', 'Memproses pengunduran diri mahasiswa', 'dokumen', 20, 4],
            ['tendik', 'tendik', 'akademik', 'Memproses drop out mahasiswa', 'dokumen', 10, 4],
            ['tendik', 'tendik', 'akademik', 'Mengelola data nilai di SIAKAD', 'data', 2, 8],
            ['tendik', 'tendik', 'akademik', 'Memproses surat keterangan aktif kuliah', 'surat', 300, 5],
            ['tendik', 'tendik', 'akademik', 'Memproses surat pengantar beasiswa', 'surat', 100, 5],
            ['tendik', 'tendik', 'akademik', 'Memverifikasi data PDDIKTI', 'data', 4, 7],
            ['tendik', 'tendik', 'akademik', 'Melakukan pelaporan PDDIKTI', 'laporan', 2, 8],
            ['tendik', 'tendik', 'akademik', 'Menyusun jadwal ujian', 'jadwal', 2, 6],
            ['tendik', 'tendik', 'akademik', 'Memproses surat rekomendasi akademik', 'surat', 50, 4],

            // ============================================
            // === TENDIK - ADMINISTRASI & TATA USAHA ===
            // ============================================
            ['tendik', 'tendik', 'administrasi', 'Mengelola surat masuk', 'surat', 500, 6],
            ['tendik', 'tendik', 'administrasi', 'Mengelola surat keluar', 'surat', 500, 6],
            ['tendik', 'tendik', 'administrasi', 'Membuat surat keterangan mahasiswa', 'surat', 200, 5],
            ['tendik', 'tendik', 'administrasi', 'Membuat surat keterangan pegawai', 'surat', 100, 5],
            ['tendik', 'tendik', 'administrasi', 'Membuat surat tugas', 'surat', 200, 5],
            ['tendik', 'tendik', 'administrasi', 'Membuat surat undangan', 'surat', 100, 4],
            ['tendik', 'tendik', 'administrasi', 'Membuat surat perjanjian kerjasama', 'surat', 30, 6],
            ['tendik', 'tendik', 'administrasi', 'Mengarsipkan dokumen institusi', 'dokumen', 500, 6],
            ['tendik', 'tendik', 'administrasi', 'Mengelola arsip digital', 'dokumen', 300, 5],
            ['tendik', 'tendik', 'administrasi', 'Melaksanakan administrasi rapat', 'rapat', 48, 5],
            ['tendik', 'tendik', 'administrasi', 'Menyusun laporan bulanan unit', 'laporan', 12, 6],
            ['tendik', 'tendik', 'administrasi', 'Menyusun laporan tahunan unit', 'laporan', 1, 8],
            ['tendik', 'tendik', 'administrasi', 'Mengelola notulensi rapat', 'dokumen', 48, 4],
            ['tendik', 'tendik', 'administrasi', 'Mengelola agenda pimpinan', 'kegiatan', 12, 5],
            ['tendik', 'tendik', 'administrasi', 'Melayani tamu/pengunjung', 'tamu', 500, 4],
            ['tendik', 'tendik', 'administrasi', 'Menerima telepon masuk', 'panggilan', 1000, 4],
            ['tendik', 'tendik', 'administrasi', 'Mengelola ekspedisi dokumen', 'dokumen', 200, 4],
            ['tendik', 'tendik', 'administrasi', 'Memproses legalisir dokumen', 'dokumen', 200, 4],
            ['tendik', 'tendik', 'administrasi', 'Menyusun SOP unit kerja', 'dokumen', 5, 6],
            ['tendik', 'tendik', 'administrasi', 'Mengelola sistem penomoran surat', 'sistem', 12, 4],

            // ============================================
            // === TENDIK - KEUANGAN ===
            // ============================================
            ['tendik', 'tendik', 'keuangan', 'Memproses pembayaran SPP mahasiswa', 'transaksi', 2000, 10],
            ['tendik', 'tendik', 'keuangan', 'Membuat laporan keuangan bulanan', 'laporan', 12, 8],
            ['tendik', 'tendik', 'keuangan', 'Membuat laporan keuangan tahunan', 'laporan', 1, 10],
            ['tendik', 'tendik', 'keuangan', 'Memproses penggajian pegawai', 'transaksi', 12, 10],
            ['tendik', 'tendik', 'keuangan', 'Mengelola kas kecil', 'kegiatan', 12, 6],
            ['tendik', 'tendik', 'keuangan', 'Menyusun anggaran tahunan', 'dokumen', 1, 8],
            ['tendik', 'tendik', 'keuangan', 'Menyusun RKA (Rencana Kerja Anggaran)', 'dokumen', 1, 8],
            ['tendik', 'tendik', 'keuangan', 'Melakukan rekonsiliasi bank', 'transaksi', 12, 7],
            ['tendik', 'tendik', 'keuangan', 'Memproses reimbursement/penggantian biaya', 'transaksi', 200, 5],
            ['tendik', 'tendik', 'keuangan', 'Memproses pembayaran honor dosen', 'transaksi', 24, 7],
            ['tendik', 'tendik', 'keuangan', 'Memproses pembayaran vendor/supplier', 'transaksi', 100, 6],
            ['tendik', 'tendik', 'keuangan', 'Mengelola piutang mahasiswa', 'data', 12, 6],
            ['tendik', 'tendik', 'keuangan', 'Mengelola utang institusi', 'data', 12, 6],
            ['tendik', 'tendik', 'keuangan', 'Memproses pajak (PPh 21, 23, PPN)', 'laporan', 12, 8],
            ['tendik', 'tendik', 'keuangan', 'Menyusun laporan pajak', 'laporan', 12, 7],
            ['tendik', 'tendik', 'keuangan', 'Melakukan audit internal keuangan', 'kegiatan', 2, 8],
            ['tendik', 'tendik', 'keuangan', 'Mengelola pembayaran beasiswa', 'transaksi', 24, 6],
            ['tendik', 'tendik', 'keuangan', 'Membuat laporan pertanggungjawaban keuangan', 'laporan', 12, 7],

            // ============================================
            // === TENDIK - SDM/KEPEGAWAIAN ===
            // ============================================
            ['tendik', 'tendik', 'kepegawaian', 'Mengelola data kepegawaian', 'data', 12, 8],
            ['tendik', 'tendik', 'kepegawaian', 'Memproses pengajuan cuti pegawai', 'dokumen', 100, 5],
            ['tendik', 'tendik', 'kepegawaian', 'Mengelola presensi pegawai', 'data', 12, 7],
            ['tendik', 'tendik', 'kepegawaian', 'Memproses SK kenaikan pangkat/jabatan', 'dokumen', 30, 6],
            ['tendik', 'tendik', 'kepegawaian', 'Mengelola arsip kepegawaian', 'dokumen', 200, 5],
            ['tendik', 'tendik', 'kepegawaian', 'Memproses rekrutmen pegawai baru', 'kegiatan', 4, 8],
            ['tendik', 'tendik', 'kepegawaian', 'Menyelenggarakan orientasi pegawai baru', 'kegiatan', 4, 5],
            ['tendik', 'tendik', 'kepegawaian', 'Memproses kontrak kerja pegawai', 'dokumen', 50, 6],
            ['tendik', 'tendik', 'kepegawaian', 'Memproses perpanjangan kontrak pegawai', 'dokumen', 50, 5],
            ['tendik', 'tendik', 'kepegawaian', 'Memproses pengunduran diri pegawai', 'dokumen', 20, 5],
            ['tendik', 'tendik', 'kepegawaian', 'Mengelola data BPJS Kesehatan', 'data', 12, 6],
            ['tendik', 'tendik', 'kepegawaian', 'Mengelola data BPJS Ketenagakerjaan', 'data', 12, 6],
            ['tendik', 'tendik', 'kepegawaian', 'Memproses mutasi pegawai', 'dokumen', 20, 5],
            ['tendik', 'tendik', 'kepegawaian', 'Memproses pemberhentian pegawai', 'dokumen', 10, 6],
            ['tendik', 'tendik', 'kepegawaian', 'Mengelola penilaian kinerja pegawai', 'data', 2, 8],
            ['tendik', 'tendik', 'kepegawaian', 'Mengelola data pelatihan pegawai', 'data', 12, 5],
            ['tendik', 'tendik', 'kepegawaian', 'Menyusun analisis jabatan', 'dokumen', 1, 7],
            ['tendik', 'tendik', 'kepegawaian', 'Menyusun analisis beban kerja', 'dokumen', 1, 7],
            ['tendik', 'tendik', 'kepegawaian', 'Mengelola pensiun pegawai', 'dokumen', 10, 6],
            ['tendik', 'tendik', 'kepegawaian', 'Membuat laporan kepegawaian', 'laporan', 12, 6],

            // ============================================
            // === TENDIK - SARANA PRASARANA ===
            // ============================================
            ['tendik', 'tendik', 'sarana', 'Melakukan pemeliharaan gedung dan fasilitas', 'kegiatan', 12, 8],
            ['tendik', 'tendik', 'sarana', 'Mengelola inventaris barang', 'unit', 500, 7],
            ['tendik', 'tendik', 'sarana', 'Melaksanakan pengadaan barang/jasa', 'transaksi', 50, 8],
            ['tendik', 'tendik', 'sarana', 'Mengelola kebersihan lingkungan kampus', 'area', 12, 6],
            ['tendik', 'tendik', 'sarana', 'Mengelola keamanan kampus', 'shift', 365, 8],
            ['tendik', 'tendik', 'sarana', 'Mengelola parkir dan transportasi', 'kegiatan', 12, 5],
            ['tendik', 'tendik', 'sarana', 'Melakukan perbaikan sarana rusak', 'unit', 100, 6],
            ['tendik', 'tendik', 'sarana', 'Mengelola gudang dan logistik', 'kegiatan', 12, 5],
            ['tendik', 'tendik', 'sarana', 'Memproses peminjaman ruangan', 'dokumen', 200, 4],
            ['tendik', 'tendik', 'sarana', 'Memproses peminjaman kendaraan dinas', 'dokumen', 100, 4],
            ['tendik', 'tendik', 'sarana', 'Melakukan stock opname', 'kegiatan', 2, 6],
            ['tendik', 'tendik', 'sarana', 'Mengelola penghapusan barang', 'dokumen', 1, 5],
            ['tendik', 'tendik', 'sarana', 'Memelihara taman dan landscape', 'kegiatan', 12, 5],
            ['tendik', 'tendik', 'sarana', 'Mengelola listrik dan utilitas', 'kegiatan', 12, 6],
            ['tendik', 'tendik', 'sarana', 'Mengelola AC dan pendingin ruangan', 'unit', 50, 5],
            ['tendik', 'tendik', 'sarana', 'Mengelola lift dan eskalator', 'unit', 10, 5],
            ['tendik', 'tendik', 'sarana', 'Mengelola CCTV dan sistem keamanan', 'sistem', 12, 6],
            ['tendik', 'tendik', 'sarana', 'Mengelola genset dan backup power', 'kegiatan', 12, 5],
            ['tendik', 'tendik', 'sarana', 'Membuat laporan aset', 'laporan', 4, 6],

            // ============================================
            // === TENDIK - TEKNOLOGI INFORMASI ===
            // ============================================
            ['tendik', 'tendik', 'it', 'Mengelola sistem informasi akademik', 'sistem', 12, 10],
            ['tendik', 'tendik', 'it', 'Melakukan maintenance server', 'kegiatan', 12, 8],
            ['tendik', 'tendik', 'it', 'Melakukan maintenance jaringan', 'kegiatan', 12, 8],
            ['tendik', 'tendik', 'it', 'Memberikan dukungan teknis kepada pengguna', 'tiket', 500, 7],
            ['tendik', 'tendik', 'it', 'Melakukan backup data sistem', 'kegiatan', 52, 6],
            ['tendik', 'tendik', 'it', 'Mengembangkan aplikasi/sistem baru', 'modul', 6, 10],
            ['tendik', 'tendik', 'it', 'Mengelola website institusi', 'kegiatan', 12, 6],
            ['tendik', 'tendik', 'it', 'Mengelola keamanan sistem informasi', 'kegiatan', 12, 8],
            ['tendik', 'tendik', 'it', 'Mengelola email institusi', 'kegiatan', 12, 5],
            ['tendik', 'tendik', 'it', 'Mengelola domain dan hosting', 'kegiatan', 12, 5],
            ['tendik', 'tendik', 'it', 'Melakukan troubleshooting hardware', 'tiket', 200, 6],
            ['tendik', 'tendik', 'it', 'Melakukan troubleshooting software', 'tiket', 300, 6],
            ['tendik', 'tendik', 'it', 'Mengelola e-learning/LMS', 'sistem', 12, 7],
            ['tendik', 'tendik', 'it', 'Mengelola sistem ujian online', 'sistem', 4, 6],
            ['tendik', 'tendik', 'it', 'Mengelola wifi/hotspot kampus', 'kegiatan', 12, 6],
            ['tendik', 'tendik', 'it', 'Melakukan instalasi software', 'unit', 200, 5],
            ['tendik', 'tendik', 'it', 'Melakukan instalasi hardware', 'unit', 100, 5],
            ['tendik', 'tendik', 'it', 'Mengelola database', 'kegiatan', 12, 8],
            ['tendik', 'tendik', 'it', 'Mengelola sistem antivirus', 'kegiatan', 12, 5],
            ['tendik', 'tendik', 'it', 'Membuat dokumentasi sistem', 'dokumen', 12, 5],
            ['tendik', 'tendik', 'it', 'Melakukan pelatihan IT untuk pengguna', 'kegiatan', 6, 5],
            ['tendik', 'tendik', 'it', 'Mengelola sistem video conference', 'kegiatan', 12, 5],
            ['tendik', 'tendik', 'it', 'Mengelola sistem presensi digital', 'sistem', 12, 6],
            ['tendik', 'tendik', 'it', 'Mengelola mobile app institusi', 'sistem', 12, 6],

            // ============================================
            // === TENDIK - PERPUSTAKAAN ===
            // ============================================
            ['tendik', 'tendik', 'perpustakaan', 'Melayani peminjaman buku', 'transaksi', 2000, 8],
            ['tendik', 'tendik', 'perpustakaan', 'Melayani pengembalian buku', 'transaksi', 2000, 6],
            ['tendik', 'tendik', 'perpustakaan', 'Mengelola katalog perpustakaan', 'item', 1000, 7],
            ['tendik', 'tendik', 'perpustakaan', 'Mengelola koleksi perpustakaan', 'item', 500, 7],
            ['tendik', 'tendik', 'perpustakaan', 'Melakukan pengadaan buku', 'item', 200, 6],
            ['tendik', 'tendik', 'perpustakaan', 'Melakukan pengadaan jurnal', 'item', 50, 6],
            ['tendik', 'tendik', 'perpustakaan', 'Mengelola repositori digital', 'dokumen', 500, 7],
            ['tendik', 'tendik', 'perpustakaan', 'Memberikan layanan referensi', 'pengunjung', 500, 5],
            ['tendik', 'tendik', 'perpustakaan', 'Memberikan layanan literasi informasi', 'kegiatan', 12, 5],
            ['tendik', 'tendik', 'perpustakaan', 'Mengelola e-journal/e-book', 'kegiatan', 12, 6],
            ['tendik', 'tendik', 'perpustakaan', 'Melakukan shelving/penataan buku', 'kegiatan', 52, 5],
            ['tendik', 'tendik', 'perpustakaan', 'Melakukan stocktaking koleksi', 'kegiatan', 1, 6],
            ['tendik', 'tendik', 'perpustakaan', 'Mengelola keanggotaan perpustakaan', 'anggota', 500, 5],
            ['tendik', 'tendik', 'perpustakaan', 'Melakukan preservasi koleksi', 'kegiatan', 12, 5],
            ['tendik', 'tendik', 'perpustakaan', 'Mengelola ruang baca', 'kegiatan', 12, 5],
            ['tendik', 'tendik', 'perpustakaan', 'Membuat laporan statistik perpustakaan', 'laporan', 12, 5],
            ['tendik', 'tendik', 'perpustakaan', 'Mengelola denda keterlambatan', 'transaksi', 200, 4],
            ['tendik', 'tendik', 'perpustakaan', 'Melakukan bimbingan pemakai perpustakaan', 'kegiatan', 12, 5],

            // ============================================
            // === TENDIK - LABORATORIUM ===
            // ============================================
            ['tendik', 'tendik', 'laboratorium', 'Menyiapkan alat dan bahan praktikum', 'kegiatan', 200, 8],
            ['tendik', 'tendik', 'laboratorium', 'Melakukan perawatan alat laboratorium', 'unit', 100, 7],
            ['tendik', 'tendik', 'laboratorium', 'Mengelola inventaris laboratorium', 'item', 300, 6],
            ['tendik', 'tendik', 'laboratorium', 'Mendampingi kegiatan praktikum', 'kegiatan', 200, 7],
            ['tendik', 'tendik', 'laboratorium', 'Menyusun laporan penggunaan laboratorium', 'laporan', 12, 5],
            ['tendik', 'tendik', 'laboratorium', 'Mengelola jadwal penggunaan laboratorium', 'jadwal', 2, 5],
            ['tendik', 'tendik', 'laboratorium', 'Mengelola limbah laboratorium', 'kegiatan', 12, 6],
            ['tendik', 'tendik', 'laboratorium', 'Melakukan kalibrasi alat ukur', 'unit', 50, 6],
            ['tendik', 'tendik', 'laboratorium', 'Menyusun SOP penggunaan laboratorium', 'dokumen', 5, 5],
            ['tendik', 'tendik', 'laboratorium', 'Mengelola K3 laboratorium', 'kegiatan', 12, 7],
            ['tendik', 'tendik', 'laboratorium', 'Melakukan pengadaan bahan habis pakai', 'transaksi', 24, 6],
            ['tendik', 'tendik', 'laboratorium', 'Mengelola penyimpanan bahan kimia', 'kegiatan', 12, 6],
            ['tendik', 'tendik', 'laboratorium', 'Membuat laporan inventaris laboratorium', 'laporan', 4, 5],

            // ============================================
            // === TENDIK - PENERIMAAN MAHASISWA BARU (PMB) ===
            // ============================================
            ['tendik', 'tendik', 'pmb', 'Mengelola pendaftaran online', 'data', 4, 8],
            ['tendik', 'tendik', 'pmb', 'Memverifikasi berkas pendaftaran', 'berkas', 500, 7],
            ['tendik', 'tendik', 'pmb', 'Melakukan seleksi administrasi', 'berkas', 500, 6],
            ['tendik', 'tendik', 'pmb', 'Menyelenggarakan tes masuk', 'kegiatan', 6, 8],
            ['tendik', 'tendik', 'pmb', 'Melakukan wawancara calon mahasiswa', 'mahasiswa', 200, 6],
            ['tendik', 'tendik', 'pmb', 'Mengumumkan hasil seleksi', 'kegiatan', 6, 5],
            ['tendik', 'tendik', 'pmb', 'Memproses daftar ulang mahasiswa baru', 'mahasiswa', 300, 8],
            ['tendik', 'tendik', 'pmb', 'Melakukan promosi dan sosialisasi', 'kegiatan', 12, 6],
            ['tendik', 'tendik', 'pmb', 'Mengunjungi sekolah untuk promosi', 'sekolah', 30, 5],
            ['tendik', 'tendik', 'pmb', 'Mengelola pameran pendidikan', 'kegiatan', 4, 5],
            ['tendik', 'tendik', 'pmb', 'Membuat materi promosi', 'materi', 10, 5],
            ['tendik', 'tendik', 'pmb', 'Menjawab inquiry calon mahasiswa', 'inquiry', 500, 5],
            ['tendik', 'tendik', 'pmb', 'Membuat laporan PMB', 'laporan', 4, 6],

            // ============================================
            // === TENDIK - KEMAHASISWAAN ===
            // ============================================
            ['tendik', 'tendik', 'kemahasiswaan', 'Mengelola beasiswa mahasiswa', 'data', 12, 8],
            ['tendik', 'tendik', 'kemahasiswaan', 'Memproses pengajuan beasiswa', 'dokumen', 200, 7],
            ['tendik', 'tendik', 'kemahasiswaan', 'Mengelola UKM/organisasi mahasiswa', 'organisasi', 20, 6],
            ['tendik', 'tendik', 'kemahasiswaan', 'Memproses proposal kegiatan mahasiswa', 'dokumen', 100, 6],
            ['tendik', 'tendik', 'kemahasiswaan', 'Memfasilitasi kegiatan kemahasiswaan', 'kegiatan', 24, 6],
            ['tendik', 'tendik', 'kemahasiswaan', 'Mengelola konseling mahasiswa', 'kegiatan', 100, 7],
            ['tendik', 'tendik', 'kemahasiswaan', 'Mengelola layanan kesehatan mahasiswa', 'kegiatan', 12, 6],
            ['tendik', 'tendik', 'kemahasiswaan', 'Memproses surat izin kegiatan mahasiswa', 'surat', 100, 5],
            ['tendik', 'tendik', 'kemahasiswaan', 'Mengelola asuransi mahasiswa', 'data', 12, 5],
            ['tendik', 'tendik', 'kemahasiswaan', 'Mengkoordinir kegiatan PKKMB', 'kegiatan', 1, 7],
            ['tendik', 'tendik', 'kemahasiswaan', 'Mengelola wisma/asrama mahasiswa', 'kegiatan', 12, 6],
            ['tendik', 'tendik', 'kemahasiswaan', 'Memfasilitasi lomba tingkat nasional', 'kegiatan', 6, 6],
            ['tendik', 'tendik', 'kemahasiswaan', 'Membuat laporan kemahasiswaan', 'laporan', 12, 5],

            // ============================================
            // === TENDIK - KERJASAMA & HUMAS ===
            // ============================================
            ['tendik', 'tendik', 'kerjasama', 'Mengelola MoU/perjanjian kerjasama', 'dokumen', 30, 7],
            ['tendik', 'tendik', 'kerjasama', 'Memfasilitasi kunjungan tamu', 'kegiatan', 50, 5],
            ['tendik', 'tendik', 'kerjasama', 'Mengelola hubungan dengan industri', 'kegiatan', 12, 6],
            ['tendik', 'tendik', 'kerjasama', 'Mengelola hubungan dengan alumni', 'kegiatan', 12, 6],
            ['tendik', 'tendik', 'kerjasama', 'Mengelola hubungan dengan media', 'kegiatan', 12, 5],
            ['tendik', 'tendik', 'kerjasama', 'Membuat press release', 'dokumen', 24, 5],
            ['tendik', 'tendik', 'kerjasama', 'Mengelola media sosial institusi', 'kegiatan', 12, 6],
            ['tendik', 'tendik', 'kerjasama', 'Membuat konten publikasi', 'konten', 50, 5],
            ['tendik', 'tendik', 'kerjasama', 'Mengelola tracer study alumni', 'data', 2, 6],
            ['tendik', 'tendik', 'kerjasama', 'Memfasilitasi career fair', 'kegiatan', 2, 6],
            ['tendik', 'tendik', 'kerjasama', 'Mengelola database alumni', 'data', 12, 5],
            ['tendik', 'tendik', 'kerjasama', 'Mengelola website berita institusi', 'kegiatan', 12, 5],
            ['tendik', 'tendik', 'kerjasama', 'Mendokumentasikan kegiatan institusi', 'kegiatan', 100, 5],
            ['tendik', 'tendik', 'kerjasama', 'Mengelola majalah/buletin institusi', 'edisi', 4, 6],

            // ============================================
            // === SEMUA PEGAWAI - PENGEMBANGAN DIRI ===
            // ============================================
            ['semua', 'penunjang', 'pengembangan', 'Mengikuti pelatihan/workshop internal', 'kegiatan', 6, 3],
            ['semua', 'penunjang', 'pengembangan', 'Mengikuti pelatihan/workshop eksternal', 'kegiatan', 3, 4],
            ['semua', 'penunjang', 'pengembangan', 'Mendapatkan sertifikasi kompetensi', 'sertifikat', 1, 5],
            ['semua', 'penunjang', 'pengembangan', 'Mengikuti seminar/webinar', 'kegiatan', 8, 2],
            ['semua', 'penunjang', 'pengembangan', 'Mengikuti kursus bahasa asing', 'kegiatan', 2, 3],
            ['semua', 'penunjang', 'pengembangan', 'Mengikuti pelatihan kepemimpinan', 'kegiatan', 1, 4],
            ['semua', 'penunjang', 'pengembangan', 'Mengikuti bimbingan teknis', 'kegiatan', 4, 3],
            ['semua', 'penunjang', 'pengembangan', 'Mengikuti coaching/mentoring', 'kegiatan', 6, 3],
            ['semua', 'penunjang', 'pengembangan', 'Membuat karya tulis ilmiah populer', 'artikel', 2, 3],

            // ============================================
            // === SEMUA PEGAWAI - TAMBAHAN ===
            // ============================================
            ['semua', 'tambahan', null, 'Menjadi panitia kegiatan institusi', 'kegiatan', 6, 3],
            ['semua', 'tambahan', null, 'Melaksanakan tugas khusus dari pimpinan', 'kegiatan', 6, 3],
            ['semua', 'tambahan', null, 'Berpartisipasi dalam kegiatan sosial institusi', 'kegiatan', 4, 2],
            ['semua', 'tambahan', null, 'Mengikuti upacara/kegiatan seremonial', 'kegiatan', 12, 1],
            ['semua', 'tambahan', null, 'Mengikuti kegiatan olahraga institusi', 'kegiatan', 6, 1],
            ['semua', 'tambahan', null, 'Mengikuti kegiatan keagamaan institusi', 'kegiatan', 6, 1],
            ['semua', 'tambahan', null, 'Mengikuti kegiatan gotong royong', 'kegiatan', 4, 1],
            ['semua', 'tambahan', null, 'Menjadi petugas upacara', 'kegiatan', 6, 2],
            ['semua', 'tambahan', null, 'Menjadi penghubung antar unit kerja', 'kegiatan', 12, 2],
            ['semua', 'tambahan', null, 'Mengikuti rapat koordinasi rutin', 'rapat', 24, 2],
            ['semua', 'tambahan', null, 'Menyusun laporan kinerja individu', 'laporan', 2, 3],
            ['semua', 'tambahan', null, 'Piket di unit kerja', 'kegiatan', 24, 2],
            ['semua', 'tambahan', null, 'Menjadi anggota tim khusus', 'kegiatan', 4, 3],
            ['semua', 'tambahan', null, 'Mengelola kebersihan ruang kerja', 'kegiatan', 12, 1],
            ['semua', 'tambahan', null, 'Mengikuti kegiatan peningkatan budaya kerja', 'kegiatan', 6, 2],
        ];

        $urutan = 0;
        foreach ($data as $item) {
            $urutan++;
            UraianKegiatanSkp::firstOrCreate(
                [
                    'tipe_pegawai' => $item[0],
                    'kategori' => $item[1],
                    'sub_kategori' => $item[2],
                    'uraian_kegiatan' => $item[3],
                ],
                [
                    'kode' => UraianKegiatanSkp::generateKode($item[1]),
                    'satuan' => $item[4],
                    'target_default' => $item[5],
                    'bobot' => $item[6],
                    'urutan' => $urutan,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Seeded ' . count($data) . ' uraian kegiatan SKP');
    }
}
