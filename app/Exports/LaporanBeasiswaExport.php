<?php

namespace App\Exports;

use App\Models\PenerimaBeasiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class LaporanBeasiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = PenerimaBeasiswa::with(['beasiswa', 'mahasiswa.programStudi', 'tahunAkademik']);

        if ($this->request->beasiswa_id) {
            $query->where('beasiswa_id', $this->request->beasiswa_id);
        }

        if ($this->request->tahun_akademik_id) {
            $query->where('tahun_akademik_id', $this->request->tahun_akademik_id);
        }

        if ($this->request->status) {
            $query->where('status', $this->request->status);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'NIM',
            'Nama Mahasiswa',
            'Program Studi',
            'Nama Beasiswa',
            'Jenis',
            'Tipe Potongan',
            'Nilai Potongan',
            'Tahun Akademik',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Status',
        ];
    }

    public function map($penerima): array
    {
        return [
            $penerima->mahasiswa->nim ?? '-',
            $penerima->mahasiswa->nama ?? '-',
            $penerima->mahasiswa->programStudi->nama ?? '-',
            $penerima->beasiswa->nama ?? '-',
            $penerima->beasiswa->jenis ?? '-',
            $penerima->beasiswa->tipe_potongan ?? '-',
            $penerima->beasiswa->tipe_potongan === 'Persen' 
                ? $penerima->beasiswa->nilai_potongan . '%' 
                : 'Rp ' . number_format($penerima->beasiswa->nilai_potongan, 0, ',', '.'),
            $penerima->tahunAkademik->nama ?? '-',
            $penerima->tanggal_mulai ? $penerima->tanggal_mulai->format('d/m/Y') : '-',
            $penerima->tanggal_selesai ? $penerima->tanggal_selesai->format('d/m/Y') : '-',
            $penerima->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF2196F3'],
            ]],
        ];
    }
}
