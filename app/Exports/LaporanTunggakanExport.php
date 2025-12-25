<?php

namespace App\Exports;

use App\Models\Tagihan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class LaporanTunggakanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Tagihan::with(['mahasiswa.programStudi', 'tahunAkademik', 'tarif'])
            ->where('sisa_tagihan', '>', 0);

        if ($this->request->program_studi_id) {
            $query->whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $this->request->program_studi_id));
        }

        if ($this->request->tahun_akademik_id) {
            $query->where('tahun_akademik_id', $this->request->tahun_akademik_id);
        }

        if ($this->request->angkatan) {
            $query->whereHas('mahasiswa', fn($q) => $q->where('angkatan', $this->request->angkatan));
        }

        return $query->orderBy('sisa_tagihan', 'DESC')->get();
    }

    public function headings(): array
    {
        return [
            'No. Tagihan',
            'NIM',
            'Nama Mahasiswa',
            'Angkatan',
            'Program Studi',
            'Tahun Akademik',
            'Jenis Tagihan',
            'Total Tagihan (Rp)',
            'Total Bayar (Rp)',
            'Sisa Tunggakan (Rp)',
            'Status',
        ];
    }

    public function map($tagihan): array
    {
        return [
            $tagihan->no_tagihan,
            $tagihan->mahasiswa->nim ?? '-',
            $tagihan->mahasiswa->nama ?? '-',
            $tagihan->mahasiswa->angkatan ?? '-',
            $tagihan->mahasiswa->programStudi->nama ?? '-',
            $tagihan->tahunAkademik->nama ?? '-',
            $tagihan->tarif->nama ?? '-',
            $tagihan->nominal,
            $tagihan->jumlah_dibayar,
            $tagihan->sisa_tagihan,
            $tagihan->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFF9800'],
            ]],
        ];
    }
}
