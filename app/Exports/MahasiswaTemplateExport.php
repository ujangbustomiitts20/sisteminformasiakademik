<?php

namespace App\Exports;

use App\Models\ProgramStudi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class MahasiswaTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new MahasiswaTemplateSheet(),
            new ProgramStudiReferenceSheet(),
        ];
    }
}

class MahasiswaTemplateSheet implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    public function title(): string
    {
        return 'Template Import';
    }

    public function collection()
    {
        return new Collection([
            [
                'AUTO',           // nim
                'Ahmad Fauzi',    // nama
                'AUTO',           // email
                'Laki-laki',      // jenis_kelamin
                'Jakarta',        // tempat_lahir
                '2000-01-15',     // tanggal_lahir
                '1234567890123456', // nik
                '1234567890123456', // no_kk
                'Islam',          // agama
                'WNI',            // kewarganegaraan
                'A',              // golongan_darah
                'TI',             // kode_prodi
                '2025',           // angkatan
                'SNMPTN',         // jalur_masuk
                'SMAN 1 Jakarta', // asal_sekolah
                'IPA',            // jurusan_asal
                '2024',           // tahun_lulus_sekolah
                '85.50',          // nilai_un
                'DN-01/12345',    // no_ijazah_sma
                'Jl. Contoh No. 1', // alamat
                '08123456789',    // telepon
                '081234567890',   // no_hp
                'Hasan Fauzi',    // nama_ayah
                '1234567890123456', // nik_ayah
                'PNS',            // pekerjaan_ayah
                'S1',             // pendidikan_ayah
                'Siti Aminah',    // nama_ibu
                '1234567890123456', // nik_ibu
                'Ibu Rumah Tangga', // pekerjaan_ibu
                'SMA',            // pendidikan_ibu
                '081234567890',   // no_hp_ortu
                'ortu@email.com', // email_ortu
                '3 - 5 Juta',     // penghasilan_ortu
                'Jl. Ortu No. 1', // alamat_ortu
                '',               // nama_wali
                '',               // hubungan_wali
                '',               // pekerjaan_wali
                '',               // no_hp_wali
                '',               // alamat_wali
                '1234567890',     // no_rekening
                'BRI',            // nama_bank
                'Ahmad Fauzi',    // atas_nama_rekening
                'Tidak',          // penerima_kip
                '',               // no_kip
            ],
            [
                'AUTO',           // nim
                'Siti Rahma',     // nama
                'AUTO',           // email
                'Perempuan',      // jenis_kelamin
                'Bandung',        // tempat_lahir
                '2000-05-20',     // tanggal_lahir
                '',               // nik
                '',               // no_kk
                'Islam',          // agama
                'WNI',            // kewarganegaraan
                'B',              // golongan_darah
                'SI',             // kode_prodi
                '2025',           // angkatan
                'SBMPTN',         // jalur_masuk
                'SMAN 2 Bandung', // asal_sekolah
                'IPS',            // jurusan_asal
                '2024',           // tahun_lulus_sekolah
                '80.25',          // nilai_un
                '',               // no_ijazah_sma
                'Jl. Contoh No. 2', // alamat
                '08987654321',    // telepon
                '',               // no_hp
                '',               // nama_ayah
                '',               // nik_ayah
                '',               // pekerjaan_ayah
                '',               // pendidikan_ayah
                '',               // nama_ibu
                '',               // nik_ibu
                '',               // pekerjaan_ibu
                '',               // pendidikan_ibu
                '',               // no_hp_ortu
                '',               // email_ortu
                '',               // penghasilan_ortu
                '',               // alamat_ortu
                'Budi Wali',      // nama_wali
                'Paman',          // hubungan_wali
                'Wiraswasta',     // pekerjaan_wali
                '081111222333',   // no_hp_wali
                'Jl. Wali No. 1', // alamat_wali
                '',               // no_rekening
                '',               // nama_bank
                '',               // atas_nama_rekening
                'Ya',             // penerima_kip
                'KIP123456789',   // no_kip
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'nim',
            'nama',
            'email',
            'jenis_kelamin',
            'tempat_lahir',
            'tanggal_lahir',
            'nik',
            'no_kk',
            'agama',
            'kewarganegaraan',
            'golongan_darah',
            'kode_prodi',
            'angkatan',
            'jalur_masuk',
            'asal_sekolah',
            'jurusan_asal',
            'tahun_lulus_sekolah',
            'nilai_un',
            'no_ijazah_sma',
            'alamat',
            'telepon',
            'no_hp',
            'nama_ayah',
            'nik_ayah',
            'pekerjaan_ayah',
            'pendidikan_ayah',
            'nama_ibu',
            'nik_ibu',
            'pekerjaan_ibu',
            'pendidikan_ibu',
            'no_hp_ortu',
            'email_ortu',
            'penghasilan_ortu',
            'alamat_ortu',
            'nama_wali',
            'hubungan_wali',
            'pekerjaan_wali',
            'no_hp_wali',
            'alamat_wali',
            'no_rekening',
            'nama_bank',
            'atas_nama_rekening',
            'penerima_kip',
            'no_kip',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = 'AS'; // Column for no_kip
        
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4CAF50'],
            ],
        ]);

        // Add comments/instructions
        $sheet->getComment('A1')->getText()->createTextRun("NIM Mahasiswa\n- Isi 'AUTO' untuk generate otomatis\n- Atau isi manual");
        $sheet->getComment('B1')->getText()->createTextRun('Nama lengkap mahasiswa (wajib)');
        $sheet->getComment('C1')->getText()->createTextRun("Email mahasiswa\n- Isi 'AUTO' untuk generate otomatis dari NIM");
        $sheet->getComment('D1')->getText()->createTextRun('Laki-laki atau Perempuan (wajib)');
        $sheet->getComment('E1')->getText()->createTextRun('Tempat lahir (opsional)');
        $sheet->getComment('F1')->getText()->createTextRun('Format: YYYY-MM-DD (opsional)');
        $sheet->getComment('G1')->getText()->createTextRun('NIK 16 digit (opsional)');
        $sheet->getComment('L1')->getText()->createTextRun('Kode Program Studi (wajib, lihat sheet Referensi Prodi)');
        $sheet->getComment('N1')->getText()->createTextRun('SNMPTN/SBMPTN/Mandiri/Pindahan/Beasiswa (opsional)');
        $sheet->getComment('AR1')->getText()->createTextRun("Penerima KIP?\n- Isi 'Ya' atau 'Tidak'");

        return [];
    }
}

class ProgramStudiReferenceSheet implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    public function title(): string
    {
        return 'Referensi Prodi';
    }

    public function collection()
    {
        return ProgramStudi::with('fakultas')
            ->orderBy('kode')
            ->get()
            ->map(function ($prodi) {
                return [
                    $prodi->kode,
                    $prodi->nama,
                    $prodi->fakultas->kode ?? '-',
                    $prodi->fakultas->nama ?? '-',
                    $prodi->jenjang ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Kode Prodi',
            'Nama Program Studi',
            'Kode Fakultas',
            'Nama Fakultas',
            'Jenjang',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2196F3'],
                ],
            ],
        ];
    }
}
