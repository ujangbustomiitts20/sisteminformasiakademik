<?php

namespace Database\Seeders;

use App\Models\PejabatPenandatangan;
use App\Models\TemplateDokumen;
use App\Models\TemplateDokumenField;
use Illuminate\Database\Seeder;

class TemplateDokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get pejabat for reference
        $kabaak = PejabatPenandatangan::where('kode', 'kepala_baak')->first();
        $dekan = PejabatPenandatangan::where('kode', 'dekan')->first();
        $rektor = PejabatPenandatangan::where('kode', 'rektor')->first();
        $kepalaKeuangan = PejabatPenandatangan::where('kode', 'kepala_keuangan')->first();
        $kaprodi = PejabatPenandatangan::where('kode', 'kaprodi')->first();

        // 1. Surat Keterangan Aktif Kuliah
        $suratAktif = TemplateDokumen::firstOrCreate(
            ['kode' => 'surat_aktif_kuliah'],
            [
                'nama' => 'Surat Keterangan Aktif Kuliah',
                'kategori' => 'akademik',
                'deskripsi' => 'Surat keterangan bahwa mahasiswa masih aktif kuliah',
                'template_judul' => 'SURAT KETERANGAN AKTIF KULIAH',
                'template_nomor' => '{no_surat}/UN.XX/AK/{bulan_romawi}/{tahun}',
                'template_isi' => 'Yang bertanda tangan di bawah ini, {setting.nama_institusi}, menerangkan bahwa:

Nama                : {nama}
NIM                 : {nim}
Tempat/Tanggal Lahir: {tempat_lahir}, {tanggal_lahir}
Alamat              : {alamat}
Program Studi       : {program_studi}
Fakultas            : {fakultas}
Angkatan            : {angkatan}
Semester            : {semester}

Adalah benar mahasiswa yang masih aktif pada {setting.nama_institusi} untuk Tahun Akademik {tahun_akademik} Semester {semester_aktif}.',
                'template_penutup' => 'Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.',
                'tampilkan_kop' => true,
                'tampilkan_logo' => true,
                'pejabat_1_id' => $kabaak?->id,
                'pejabat_1_label' => 'Kepala BAAK',
                'tampilkan_ttd_digital' => true,
                'tampilkan_stempel' => true,
                'aktif' => true,
            ]
        );

        // Fields for Surat Aktif Kuliah
        $this->createFields($suratAktif, [
            ['kode_field' => 'no_surat', 'label' => 'Nomor Surat', 'tipe' => 'text', 'wajib' => true, 'urutan' => 1],
            ['kode_field' => 'nama', 'label' => 'Nama Lengkap', 'tipe' => 'text', 'sumber_data' => 'mahasiswa.nama', 'wajib' => true, 'urutan' => 2],
            ['kode_field' => 'nim', 'label' => 'NIM', 'tipe' => 'text', 'sumber_data' => 'mahasiswa.nim', 'wajib' => true, 'urutan' => 3],
            ['kode_field' => 'tempat_lahir', 'label' => 'Tempat Lahir', 'tipe' => 'text', 'sumber_data' => 'mahasiswa.tempat_lahir', 'wajib' => true, 'urutan' => 4],
            ['kode_field' => 'tanggal_lahir', 'label' => 'Tanggal Lahir', 'tipe' => 'date', 'sumber_data' => 'mahasiswa.tanggal_lahir', 'wajib' => true, 'urutan' => 5],
            ['kode_field' => 'alamat', 'label' => 'Alamat', 'tipe' => 'textarea', 'sumber_data' => 'mahasiswa.alamat', 'wajib' => true, 'urutan' => 6],
            ['kode_field' => 'program_studi', 'label' => 'Program Studi', 'tipe' => 'text', 'sumber_data' => 'mahasiswa.program_studi', 'wajib' => true, 'urutan' => 7],
            ['kode_field' => 'fakultas', 'label' => 'Fakultas', 'tipe' => 'text', 'sumber_data' => 'mahasiswa.fakultas', 'wajib' => true, 'urutan' => 8],
            ['kode_field' => 'angkatan', 'label' => 'Angkatan', 'tipe' => 'text', 'sumber_data' => 'mahasiswa.angkatan', 'wajib' => true, 'urutan' => 9],
            ['kode_field' => 'semester', 'label' => 'Semester', 'tipe' => 'number', 'wajib' => true, 'urutan' => 10],
            ['kode_field' => 'tahun_akademik', 'label' => 'Tahun Akademik', 'tipe' => 'text', 'wajib' => true, 'urutan' => 11],
            ['kode_field' => 'semester_aktif', 'label' => 'Semester Aktif', 'tipe' => 'select', 'opsi' => ['Ganjil' => 'Ganjil', 'Genap' => 'Genap'], 'wajib' => true, 'urutan' => 12],
        ]);

        // 2. Surat Keterangan Kelakuan Baik
        $suratKelakuanBaik = TemplateDokumen::firstOrCreate(
            ['kode' => 'surat_kelakuan_baik'],
            [
                'nama' => 'Surat Keterangan Kelakuan Baik',
                'kategori' => 'akademik',
                'deskripsi' => 'Surat keterangan kelakuan baik mahasiswa',
                'template_judul' => 'SURAT KETERANGAN KELAKUAN BAIK',
                'template_nomor' => '{no_surat}/UN.XX/KM/{bulan_romawi}/{tahun}',
                'template_isi' => 'Yang bertanda tangan di bawah ini, {setting.nama_institusi}, menerangkan bahwa:

Nama                : {nama}
NIM                 : {nim}
Program Studi       : {program_studi}
Fakultas            : {fakultas}

Selama menjadi mahasiswa di {setting.nama_institusi}, yang bersangkutan:
1. Tidak pernah terlibat tindak pidana
2. Tidak pernah mendapat sanksi akademik berat
3. Tidak pernah terlibat kegiatan yang mengganggu keamanan kampus
4. Berkelakuan baik',
                'template_penutup' => 'Demikian surat keterangan ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.',
                'tampilkan_kop' => true,
                'tampilkan_logo' => true,
                'pejabat_1_id' => $dekan?->id,
                'pejabat_1_label' => 'Dekan',
                'tampilkan_ttd_digital' => true,
                'tampilkan_stempel' => true,
                'aktif' => true,
            ]
        );

        $this->createFields($suratKelakuanBaik, [
            ['kode_field' => 'no_surat', 'label' => 'Nomor Surat', 'tipe' => 'text', 'wajib' => true, 'urutan' => 1],
            ['kode_field' => 'nama', 'label' => 'Nama Lengkap', 'tipe' => 'text', 'sumber_data' => 'mahasiswa.nama', 'wajib' => true, 'urutan' => 2],
            ['kode_field' => 'nim', 'label' => 'NIM', 'tipe' => 'text', 'sumber_data' => 'mahasiswa.nim', 'wajib' => true, 'urutan' => 3],
            ['kode_field' => 'program_studi', 'label' => 'Program Studi', 'tipe' => 'text', 'sumber_data' => 'mahasiswa.program_studi', 'wajib' => true, 'urutan' => 4],
            ['kode_field' => 'fakultas', 'label' => 'Fakultas', 'tipe' => 'text', 'sumber_data' => 'mahasiswa.fakultas', 'wajib' => true, 'urutan' => 5],
        ]);

        // 3. Surat Keterangan Lulus
        $suratLulus = TemplateDokumen::firstOrCreate(
            ['kode' => 'surat_keterangan_lulus'],
            [
                'nama' => 'Surat Keterangan Lulus',
                'kategori' => 'akademik',
                'deskripsi' => 'Surat keterangan kelulusan sementara',
                'template_judul' => 'SURAT KETERANGAN LULUS',
                'template_nomor' => '{no_surat}/UN.XX/DT/{bulan_romawi}/{tahun}',
                'template_isi' => 'Yang bertanda tangan di bawah ini, menerangkan bahwa:

Nama                : {nama}
NIM                 : {nim}
Tempat/Tanggal Lahir: {tempat_lahir}, {tanggal_lahir}
Program Studi       : {program_studi}
Fakultas            : {fakultas}
Tanggal Yudisium    : {tanggal_yudisium}
IPK                 : {ipk}
Predikat            : {predikat}

Telah dinyatakan LULUS dalam Ujian Akhir Program dan berhak menyandang gelar {gelar} ({singkatan_gelar}).

Ijazah dan Transkrip Akademik sedang dalam proses penyelesaian.',
                'template_penutup' => 'Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.',
                'tampilkan_kop' => true,
                'tampilkan_logo' => true,
                'pejabat_1_id' => $dekan?->id,
                'pejabat_1_label' => 'Dekan',
                'tampilkan_ttd_digital' => true,
                'tampilkan_stempel' => true,
                'aktif' => true,
            ]
        );

        $this->createFields($suratLulus, [
            ['kode_field' => 'no_surat', 'label' => 'Nomor Surat', 'tipe' => 'text', 'wajib' => true, 'urutan' => 1],
            ['kode_field' => 'nama', 'label' => 'Nama Lengkap', 'tipe' => 'text', 'sumber_data' => 'mahasiswa.nama', 'wajib' => true, 'urutan' => 2],
            ['kode_field' => 'nim', 'label' => 'NIM', 'tipe' => 'text', 'sumber_data' => 'mahasiswa.nim', 'wajib' => true, 'urutan' => 3],
            ['kode_field' => 'tempat_lahir', 'label' => 'Tempat Lahir', 'tipe' => 'text', 'sumber_data' => 'mahasiswa.tempat_lahir', 'wajib' => true, 'urutan' => 4],
            ['kode_field' => 'tanggal_lahir', 'label' => 'Tanggal Lahir', 'tipe' => 'date', 'sumber_data' => 'mahasiswa.tanggal_lahir', 'wajib' => true, 'urutan' => 5],
            ['kode_field' => 'program_studi', 'label' => 'Program Studi', 'tipe' => 'text', 'sumber_data' => 'mahasiswa.program_studi', 'wajib' => true, 'urutan' => 6],
            ['kode_field' => 'fakultas', 'label' => 'Fakultas', 'tipe' => 'text', 'sumber_data' => 'mahasiswa.fakultas', 'wajib' => true, 'urutan' => 7],
            ['kode_field' => 'tanggal_yudisium', 'label' => 'Tanggal Yudisium', 'tipe' => 'date', 'wajib' => true, 'urutan' => 8],
            ['kode_field' => 'ipk', 'label' => 'IPK', 'tipe' => 'text', 'sumber_data' => 'mahasiswa.ipk', 'wajib' => true, 'urutan' => 9],
            ['kode_field' => 'predikat', 'label' => 'Predikat Kelulusan', 'tipe' => 'select', 'opsi' => ['Memuaskan' => 'Memuaskan', 'Sangat Memuaskan' => 'Sangat Memuaskan', 'Dengan Pujian' => 'Dengan Pujian (Cum Laude)'], 'wajib' => true, 'urutan' => 10],
            ['kode_field' => 'gelar', 'label' => 'Gelar', 'tipe' => 'text', 'wajib' => true, 'urutan' => 11, 'nilai_default' => 'Sarjana Teknik'],
            ['kode_field' => 'singkatan_gelar', 'label' => 'Singkatan Gelar', 'tipe' => 'text', 'wajib' => true, 'urutan' => 12, 'nilai_default' => 'S.T.'],
        ]);

        // 4. Slip Gaji Pegawai
        $slipGaji = TemplateDokumen::firstOrCreate(
            ['kode' => 'slip_gaji'],
            [
                'nama' => 'Slip Gaji Pegawai',
                'kategori' => 'keuangan',
                'deskripsi' => 'Slip gaji bulanan untuk pegawai',
                'template_judul' => 'SLIP GAJI',
                'template_nomor' => 'SG/{no_urut}/{bulan_romawi}/{tahun}',
                'template_isi' => 'Periode: {periode}

Data Pegawai:
Nama          : {nama}
NIP/NIK       : {nip}
Jabatan       : {jabatan}
Unit Kerja    : {unit_kerja}

Komponen Penghasilan:
- Gaji Pokok         : Rp {gaji_pokok}
- Tunjangan Jabatan  : Rp {tunjangan_jabatan}
- Tunjangan Transport: Rp {tunjangan_transport}
- Tunjangan Makan    : Rp {tunjangan_makan}
- Lainnya            : Rp {tunjangan_lain}
--------------------------------------
Total Penghasilan    : Rp {total_penghasilan}

Komponen Potongan:
- PPh 21             : Rp {pph21}
- BPJS Kesehatan     : Rp {bpjs_kes}
- BPJS Ketenagakerjaan: Rp {bpjs_tk}
- Potongan Lain      : Rp {potongan_lain}
--------------------------------------
Total Potongan       : Rp {total_potongan}

======================================
GAJI BERSIH          : Rp {gaji_bersih}
======================================',
                'template_penutup' => '',
                'tampilkan_kop' => true,
                'tampilkan_logo' => true,
                'pejabat_1_id' => $kepalaKeuangan?->id,
                'pejabat_1_label' => 'Kepala Bagian Keuangan',
                'tampilkan_ttd_digital' => false,
                'tampilkan_stempel' => false,
                'aktif' => true,
            ]
        );

        $this->createFields($slipGaji, [
            ['kode_field' => 'no_urut', 'label' => 'Nomor Urut', 'tipe' => 'text', 'wajib' => true, 'urutan' => 1],
            ['kode_field' => 'periode', 'label' => 'Periode', 'tipe' => 'text', 'wajib' => true, 'urutan' => 2],
            ['kode_field' => 'nama', 'label' => 'Nama Pegawai', 'tipe' => 'text', 'sumber_data' => 'pegawai.nama', 'wajib' => true, 'urutan' => 3],
            ['kode_field' => 'nip', 'label' => 'NIP/NIK', 'tipe' => 'text', 'sumber_data' => 'pegawai.nip', 'wajib' => true, 'urutan' => 4],
            ['kode_field' => 'jabatan', 'label' => 'Jabatan', 'tipe' => 'text', 'sumber_data' => 'pegawai.jabatan', 'wajib' => true, 'urutan' => 5],
            ['kode_field' => 'unit_kerja', 'label' => 'Unit Kerja', 'tipe' => 'text', 'sumber_data' => 'pegawai.unit_kerja', 'wajib' => true, 'urutan' => 6],
            ['kode_field' => 'gaji_pokok', 'label' => 'Gaji Pokok', 'tipe' => 'number', 'wajib' => true, 'urutan' => 7],
            ['kode_field' => 'tunjangan_jabatan', 'label' => 'Tunjangan Jabatan', 'tipe' => 'number', 'urutan' => 8, 'nilai_default' => '0'],
            ['kode_field' => 'tunjangan_transport', 'label' => 'Tunjangan Transport', 'tipe' => 'number', 'urutan' => 9, 'nilai_default' => '0'],
            ['kode_field' => 'tunjangan_makan', 'label' => 'Tunjangan Makan', 'tipe' => 'number', 'urutan' => 10, 'nilai_default' => '0'],
            ['kode_field' => 'tunjangan_lain', 'label' => 'Tunjangan Lain', 'tipe' => 'number', 'urutan' => 11, 'nilai_default' => '0'],
            ['kode_field' => 'total_penghasilan', 'label' => 'Total Penghasilan', 'tipe' => 'number', 'wajib' => true, 'urutan' => 12],
            ['kode_field' => 'pph21', 'label' => 'PPh 21', 'tipe' => 'number', 'urutan' => 13, 'nilai_default' => '0'],
            ['kode_field' => 'bpjs_kes', 'label' => 'BPJS Kesehatan', 'tipe' => 'number', 'urutan' => 14, 'nilai_default' => '0'],
            ['kode_field' => 'bpjs_tk', 'label' => 'BPJS Ketenagakerjaan', 'tipe' => 'number', 'urutan' => 15, 'nilai_default' => '0'],
            ['kode_field' => 'potongan_lain', 'label' => 'Potongan Lain', 'tipe' => 'number', 'urutan' => 16, 'nilai_default' => '0'],
            ['kode_field' => 'total_potongan', 'label' => 'Total Potongan', 'tipe' => 'number', 'wajib' => true, 'urutan' => 17],
            ['kode_field' => 'gaji_bersih', 'label' => 'Gaji Bersih', 'tipe' => 'number', 'wajib' => true, 'urutan' => 18],
        ]);

        // 5. Surat Tugas Dosen
        $suratTugas = TemplateDokumen::firstOrCreate(
            ['kode' => 'surat_tugas_dosen'],
            [
                'nama' => 'Surat Tugas Dosen',
                'kategori' => 'kepegawaian',
                'deskripsi' => 'Surat tugas untuk dosen mengajar atau kegiatan akademik',
                'template_judul' => 'SURAT TUGAS',
                'template_nomor' => '{no_surat}/UN.XX/SDM/{bulan_romawi}/{tahun}',
                'template_isi' => 'Yang bertanda tangan di bawah ini, {setting.nama_institusi}, dengan ini memberikan tugas kepada:

Nama          : {nama}
NIDN          : {nidn}
Jabatan       : {jabatan}
Unit Kerja    : {unit_kerja}

Untuk melaksanakan tugas sebagai berikut:
{deskripsi_tugas}

Tempat        : {tempat}
Tanggal       : {tanggal_mulai} s.d. {tanggal_selesai}',
                'template_penutup' => 'Demikian surat tugas ini dibuat untuk dilaksanakan dengan penuh tanggung jawab.',
                'tampilkan_kop' => true,
                'tampilkan_logo' => true,
                'pejabat_1_id' => $dekan?->id,
                'pejabat_1_label' => 'Dekan',
                'tampilkan_ttd_digital' => true,
                'tampilkan_stempel' => true,
                'aktif' => true,
            ]
        );

        $this->createFields($suratTugas, [
            ['kode_field' => 'no_surat', 'label' => 'Nomor Surat', 'tipe' => 'text', 'wajib' => true, 'urutan' => 1],
            ['kode_field' => 'nama', 'label' => 'Nama Dosen', 'tipe' => 'text', 'sumber_data' => 'dosen.nama', 'wajib' => true, 'urutan' => 2],
            ['kode_field' => 'nidn', 'label' => 'NIDN', 'tipe' => 'text', 'sumber_data' => 'dosen.nidn', 'wajib' => true, 'urutan' => 3],
            ['kode_field' => 'jabatan', 'label' => 'Jabatan Fungsional', 'tipe' => 'text', 'sumber_data' => 'dosen.jabatan_fungsional', 'urutan' => 4],
            ['kode_field' => 'unit_kerja', 'label' => 'Unit Kerja', 'tipe' => 'text', 'sumber_data' => 'dosen.program_studi', 'wajib' => true, 'urutan' => 5],
            ['kode_field' => 'deskripsi_tugas', 'label' => 'Deskripsi Tugas', 'tipe' => 'textarea', 'wajib' => true, 'urutan' => 6],
            ['kode_field' => 'tempat', 'label' => 'Tempat Pelaksanaan', 'tipe' => 'text', 'wajib' => true, 'urutan' => 7],
            ['kode_field' => 'tanggal_mulai', 'label' => 'Tanggal Mulai', 'tipe' => 'date', 'wajib' => true, 'urutan' => 8],
            ['kode_field' => 'tanggal_selesai', 'label' => 'Tanggal Selesai', 'tipe' => 'date', 'wajib' => true, 'urutan' => 9],
        ]);

        // 6. Surat Rekomendasi
        $suratRekomendasi = TemplateDokumen::firstOrCreate(
            ['kode' => 'surat_rekomendasi'],
            [
                'nama' => 'Surat Rekomendasi',
                'kategori' => 'akademik',
                'deskripsi' => 'Surat rekomendasi untuk mahasiswa atau alumni',
                'template_judul' => 'SURAT REKOMENDASI',
                'template_nomor' => '{no_surat}/UN.XX/AK/{bulan_romawi}/{tahun}',
                'template_isi' => 'Yang bertanda tangan di bawah ini:

Nama          : {nama_pemberi}
Jabatan       : {jabatan_pemberi}

Dengan ini memberikan rekomendasi kepada:

Nama          : {nama}
NIM           : {nim}
Program Studi : {program_studi}

{isi_rekomendasi}

Saya mengenal yang bersangkutan selama {lama_kenal} dan dapat memberikan penilaian bahwa yang bersangkutan memiliki:
1. Kemampuan akademik: {penilaian_akademik}
2. Karakter/kepribadian: {penilaian_karakter}
3. Kemampuan komunikasi: {penilaian_komunikasi}',
                'template_penutup' => 'Demikian surat rekomendasi ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.',
                'tampilkan_kop' => true,
                'tampilkan_logo' => true,
                'pejabat_1_id' => $kaprodi?->id,
                'pejabat_1_label' => 'Ketua Program Studi',
                'tampilkan_ttd_digital' => true,
                'tampilkan_stempel' => false,
                'aktif' => true,
            ]
        );

        $this->createFields($suratRekomendasi, [
            ['kode_field' => 'no_surat', 'label' => 'Nomor Surat', 'tipe' => 'text', 'wajib' => true, 'urutan' => 1],
            ['kode_field' => 'nama_pemberi', 'label' => 'Nama Pemberi Rekomendasi', 'tipe' => 'text', 'wajib' => true, 'urutan' => 2],
            ['kode_field' => 'jabatan_pemberi', 'label' => 'Jabatan Pemberi', 'tipe' => 'text', 'wajib' => true, 'urutan' => 3],
            ['kode_field' => 'nama', 'label' => 'Nama Mahasiswa', 'tipe' => 'text', 'sumber_data' => 'mahasiswa.nama', 'wajib' => true, 'urutan' => 4],
            ['kode_field' => 'nim', 'label' => 'NIM', 'tipe' => 'text', 'sumber_data' => 'mahasiswa.nim', 'wajib' => true, 'urutan' => 5],
            ['kode_field' => 'program_studi', 'label' => 'Program Studi', 'tipe' => 'text', 'sumber_data' => 'mahasiswa.program_studi', 'wajib' => true, 'urutan' => 6],
            ['kode_field' => 'isi_rekomendasi', 'label' => 'Isi Rekomendasi', 'tipe' => 'textarea', 'wajib' => true, 'urutan' => 7],
            ['kode_field' => 'lama_kenal', 'label' => 'Lama Mengenal', 'tipe' => 'text', 'wajib' => true, 'urutan' => 8, 'nilai_default' => '4 tahun'],
            ['kode_field' => 'penilaian_akademik', 'label' => 'Penilaian Akademik', 'tipe' => 'select', 'opsi' => ['Sangat Baik' => 'Sangat Baik', 'Baik' => 'Baik', 'Cukup' => 'Cukup'], 'wajib' => true, 'urutan' => 9],
            ['kode_field' => 'penilaian_karakter', 'label' => 'Penilaian Karakter', 'tipe' => 'select', 'opsi' => ['Sangat Baik' => 'Sangat Baik', 'Baik' => 'Baik', 'Cukup' => 'Cukup'], 'wajib' => true, 'urutan' => 10],
            ['kode_field' => 'penilaian_komunikasi', 'label' => 'Penilaian Komunikasi', 'tipe' => 'select', 'opsi' => ['Sangat Baik' => 'Sangat Baik', 'Baik' => 'Baik', 'Cukup' => 'Cukup'], 'wajib' => true, 'urutan' => 11],
        ]);

        $this->command->info('Template Dokumen seeded: ' . TemplateDokumen::count() . ' templates with fields');
    }

    /**
     * Helper to create fields for a template
     */
    private function createFields(TemplateDokumen $template, array $fields): void
    {
        foreach ($fields as $field) {
            TemplateDokumenField::firstOrCreate(
                [
                    'template_dokumen_id' => $template->id,
                    'kode_field' => $field['kode_field'],
                ],
                array_merge($field, ['template_dokumen_id' => $template->id])
            );
        }
    }
}
