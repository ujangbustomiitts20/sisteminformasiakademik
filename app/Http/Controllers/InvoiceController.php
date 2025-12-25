<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\TransaksiPembayaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Cetak Invoice Tagihan
     */
    public function invoice(Tagihan $tagihan)
    {
        $tagihan->load(['mahasiswa.programStudi', 'tahunAkademik', 'tarif', 'transaksi']);

        $pdf = Pdf::loadView('keuangan.invoice.invoice', compact('tagihan'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('invoice-' . $tagihan->no_tagihan . '.pdf');
    }

    /**
     * Download Invoice Tagihan
     */
    public function downloadInvoice(Tagihan $tagihan)
    {
        $tagihan->load(['mahasiswa.programStudi', 'tahunAkademik', 'tarif', 'transaksi']);

        $pdf = Pdf::loadView('keuangan.invoice.invoice', compact('tagihan'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('invoice-' . $tagihan->no_tagihan . '.pdf');
    }

    /**
     * Cetak Kwitansi Pembayaran
     */
    public function kwitansi(TransaksiPembayaran $transaksi)
    {
        $transaksi->load(['mahasiswa.programStudi', 'tagihan.tahunAkademik', 'tagihan.tarif', 'verifier']);

        $pdf = Pdf::loadView('keuangan.invoice.kwitansi', compact('transaksi'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('kwitansi-' . $transaksi->no_transaksi . '.pdf');
    }

    /**
     * Download Kwitansi Pembayaran
     */
    public function downloadKwitansi(TransaksiPembayaran $transaksi)
    {
        $transaksi->load(['mahasiswa.programStudi', 'tagihan.tahunAkademik', 'tagihan.tarif', 'verifier']);

        $pdf = Pdf::loadView('keuangan.invoice.kwitansi', compact('transaksi'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('kwitansi-' . $transaksi->no_transaksi . '.pdf');
    }

    /**
     * Cetak Kartu Tagihan Mahasiswa
     */
    public function kartuTagihan(Request $request, $mahasiswaId)
    {
        $mahasiswa = \App\Models\Mahasiswa::with('programStudi')->findOrFail($mahasiswaId);
        
        $query = Tagihan::with(['tahunAkademik', 'tarif', 'transaksi'])
            ->where('mahasiswa_id', $mahasiswaId);

        if ($request->tahun_akademik_id) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }

        $tagihan = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('keuangan.invoice.kartu-tagihan', compact('mahasiswa', 'tagihan'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('kartu-tagihan-' . $mahasiswa->nim . '.pdf');
    }
}
