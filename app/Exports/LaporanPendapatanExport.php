<?php

namespace App\Exports;

use App\Models\TransaksiPembayaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class LaporanPendapatanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = TransaksiPembayaran::with(['mahasiswa.programStudi', 'tagihan.tahunAkademik'])
            ->where('status', 'Verified');

        if ($this->request->tahun_akademik_id) {
            $query->whereHas('tagihan', fn($q) => $q->where('tahun_akademik_id', $this->request->tahun_akademik_id));
        }

        if ($this->request->tanggal_mulai) {
            $query->whereDate('tanggal_bayar', '>=', $this->request->tanggal_mulai);
        }

        if ($this->request->tanggal_selesai) {
            $query->whereDate('tanggal_bayar', '<=', $this->request->tanggal_selesai);
        }

        if ($this->request->metode_pembayaran) {
            $query->where('metode_pembayaran', $this->request->metode_pembayaran);
        }

        return $query->orderBy('tanggal_bayar', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No. Transaksi',
            'Tanggal Bayar',
            'NIM',
            'Nama Mahasiswa',
            'Program Studi',
            'No. Tagihan',
            'Metode Pembayaran',
            'Jumlah (Rp)',
            'Status',
        ];
    }

    public function map($transaksi): array
    {
        return [
            $transaksi->no_transaksi,
            $transaksi->tanggal_bayar->format('d/m/Y'),
            $transaksi->mahasiswa->nim ?? '-',
            $transaksi->mahasiswa->nama ?? '-',
            $transaksi->mahasiswa->programStudi->nama ?? '-',
            $transaksi->tagihan->no_tagihan ?? '-',
            $transaksi->metode_pembayaran,
            $transaksi->jumlah,
            $transaksi->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4CAF50'],
            ]],
        ];
    }
}
