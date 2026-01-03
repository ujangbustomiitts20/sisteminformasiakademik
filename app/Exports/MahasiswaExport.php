<?php

namespace App\Exports;

use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class MahasiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Mahasiswa::with(['programStudi.fakultas', 'dosenWali']);

        if ($this->request->program_studi_id) {
            $query->where('program_studi_id', $this->request->program_studi_id);
        }
        if ($this->request->status) {
            $query->where('status', $this->request->status);
        }
        if ($this->request->angkatan) {
            $query->where('angkatan', $this->request->angkatan);
        }
        if ($this->request->search) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('nim')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NIM',
            'Nama Lengkap',
            'Email',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'NIK',
            'No. KK',
            'Agama',
            'Kewarganegaraan',
            'Gol. Darah',
            'Kode Prodi',
            'Program Studi',
            'Fakultas',
            'Angkatan',
            'Jalur Masuk',
            'Semester Aktif',
            'Status',
            'Asal Sekolah',
            'Jurusan Asal',
            'Tahun Lulus Sekolah',
            'Nilai UN',
            'No. Ijazah SMA',
            'Dosen Wali',
            'Telepon',
            'No. HP',
            'Alamat',
            'Nama Ayah',
            'NIK Ayah',
            'Pekerjaan Ayah',
            'Pendidikan Ayah',
            'Nama Ibu',
            'NIK Ibu',
            'Pekerjaan Ibu',
            'Pendidikan Ibu',
            'No. HP Ortu',
            'Email Ortu',
            'Penghasilan Ortu',
            'Alamat Ortu',
            'Nama Wali',
            'Hubungan Wali',
            'Pekerjaan Wali',
            'No. HP Wali',
            'Alamat Wali',
            'No. Rekening',
            'Nama Bank',
            'Atas Nama Rekening',
            'Penerima KIP',
            'No. KIP',
        ];
    }

    public function map($mahasiswa): array
    {
        static $no = 0;
        $no++;
        
        return [
            $no,
            $mahasiswa->nim,
            $mahasiswa->nama,
            $mahasiswa->email,
            $mahasiswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
            $mahasiswa->tempat_lahir ?? '-',
            $mahasiswa->tanggal_lahir ? date('d-m-Y', strtotime($mahasiswa->tanggal_lahir)) : '-',
            $mahasiswa->nik ?? '-',
            $mahasiswa->no_kk ?? '-',
            $mahasiswa->agama ?? '-',
            $mahasiswa->kewarganegaraan ?? 'WNI',
            $mahasiswa->golongan_darah ?? '-',
            $mahasiswa->programStudi->kode ?? '-',
            $mahasiswa->programStudi->nama ?? '-',
            $mahasiswa->programStudi->fakultas->nama ?? '-',
            $mahasiswa->angkatan,
            $mahasiswa->jalur_masuk ?? '-',
            $mahasiswa->semester_aktif ?? '-',
            $mahasiswa->status,
            $mahasiswa->asal_sekolah ?? '-',
            $mahasiswa->jurusan_asal ?? '-',
            $mahasiswa->tahun_lulus_sekolah ?? '-',
            $mahasiswa->nilai_un ?? '-',
            $mahasiswa->no_ijazah_sma ?? '-',
            $mahasiswa->dosenWali->nama ?? '-',
            $mahasiswa->telepon ?? '-',
            $mahasiswa->no_hp ?? '-',
            $mahasiswa->alamat ?? '-',
            $mahasiswa->nama_ayah ?? '-',
            $mahasiswa->nik_ayah ?? '-',
            $mahasiswa->pekerjaan_ayah ?? '-',
            $mahasiswa->pendidikan_ayah ?? '-',
            $mahasiswa->nama_ibu ?? '-',
            $mahasiswa->nik_ibu ?? '-',
            $mahasiswa->pekerjaan_ibu ?? '-',
            $mahasiswa->pendidikan_ibu ?? '-',
            $mahasiswa->no_hp_ortu ?? '-',
            $mahasiswa->email_ortu ?? '-',
            $mahasiswa->penghasilan_ortu ?? '-',
            $mahasiswa->alamat_ortu ?? '-',
            $mahasiswa->nama_wali ?? '-',
            $mahasiswa->hubungan_wali ?? '-',
            $mahasiswa->pekerjaan_wali ?? '-',
            $mahasiswa->no_hp_wali ?? '-',
            $mahasiswa->alamat_wali ?? '-',
            $mahasiswa->no_rekening ?? '-',
            $mahasiswa->nama_bank ?? '-',
            $mahasiswa->atas_nama_rekening ?? '-',
            $mahasiswa->penerima_kip ? 'Ya' : 'Tidak',
            $mahasiswa->no_kip ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Bold header row
            1 => ['font' => ['bold' => true]],
        ];
    }
}
