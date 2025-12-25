<?php

namespace App\Http\Controllers;

use App\Models\Rekonsiliasi;
use App\Models\DetailRekonsiliasi;
use App\Models\AkunBank;
use App\Models\MutasiBank;
use App\Models\TransaksiPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RekonsiliasiBankController extends Controller
{
    public function index(Request $request)
    {
        $query = Rekonsiliasi::with(['akunBank', 'createdBy', 'approvedBy'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('akun_bank_id')) {
            $query->where('akun_bank_id', $request->akun_bank_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rekonsiliasi = $query->paginate(15)->withQueryString();
        $akunBank = AkunBank::active()->orderBy('nama_bank')->get();

        $stats = [
            'total' => Rekonsiliasi::count(),
            'draft' => Rekonsiliasi::draft()->count(),
            'in_progress' => Rekonsiliasi::inProgress()->count(),
            'completed' => Rekonsiliasi::completed()->count(),
            'approved' => Rekonsiliasi::approved()->count(),
        ];

        return view('keuangan.rekonsiliasi.index', compact('rekonsiliasi', 'akunBank', 'stats'));
    }

    public function create()
    {
        $akunBank = AkunBank::active()->orderBy('nama_bank')->get();
        return view('keuangan.rekonsiliasi.create', compact('akunBank'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'akun_bank_id' => 'required|exists:akun_bank,id',
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
            'saldo_awal_bank' => 'required|numeric',
            'saldo_akhir_bank' => 'required|numeric',
        ]);

        $akunBank = AkunBank::findOrFail($request->akun_bank_id);

        // Hitung saldo sistem dari mutasi dalam periode
        $mutasiPeriode = MutasiBank::where('akun_bank_id', $akunBank->id)
            ->whereBetween('tanggal', [$request->periode_awal, $request->periode_akhir])
            ->get();

        $totalKredit = $mutasiPeriode->where('tipe', 'kredit')->sum('nominal');
        $totalDebit = $mutasiPeriode->where('tipe', 'debit')->sum('nominal');
        $saldoSistem = $request->saldo_awal_bank + $totalKredit - $totalDebit;

        DB::beginTransaction();
        try {
            $rekonsiliasi = Rekonsiliasi::create([
                'akun_bank_id' => $request->akun_bank_id,
                'nomor_rekonsiliasi' => Rekonsiliasi::generateNomorRekonsiliasi(),
                'periode_awal' => $request->periode_awal,
                'periode_akhir' => $request->periode_akhir,
                'saldo_awal_bank' => $request->saldo_awal_bank,
                'saldo_akhir_bank' => $request->saldo_akhir_bank,
                'saldo_sistem' => $saldoSistem,
                'selisih' => $request->saldo_akhir_bank - $saldoSistem,
                'total_mutasi' => $mutasiPeriode->count(),
                'status' => 'draft',
                'created_by' => auth()->id(),
                'catatan' => $request->catatan,
            ]);

            // Buat detail rekonsiliasi untuk setiap mutasi
            foreach ($mutasiPeriode as $mutasi) {
                DetailRekonsiliasi::create([
                    'rekonsiliasi_id' => $rekonsiliasi->id,
                    'mutasi_bank_id' => $mutasi->id,
                    'transaksi_pembayaran_id' => $mutasi->transaksi_pembayaran_id,
                    'status' => $mutasi->isMatched() ? 'matched' : 'unmatched',
                ]);
            }

            $rekonsiliasi->updateStatistik();

            DB::commit();

            return redirect()->route('rekonsiliasi.show', $rekonsiliasi)
                ->with('success', 'Rekonsiliasi berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal membuat rekonsiliasi: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Rekonsiliasi $rekonsiliasi)
    {
        $rekonsiliasi->load([
            'akunBank', 
            'createdBy', 
            'approvedBy',
            'detailRekonsiliasi.mutasiBank',
            'detailRekonsiliasi.transaksiPembayaran.tagihan.mahasiswa',
        ]);

        $stats = [
            'total_kredit' => $rekonsiliasi->detailRekonsiliasi
                ->where('mutasiBank.tipe', 'kredit')
                ->sum('mutasiBank.nominal'),
            'total_debit' => $rekonsiliasi->detailRekonsiliasi
                ->where('mutasiBank.tipe', 'debit')
                ->sum('mutasiBank.nominal'),
        ];

        // Transaksi yang belum di-match untuk periode ini
        $unmatchedTransaksi = [];
        if ($rekonsiliasi->isEditable()) {
            $unmatchedTransaksi = TransaksiPembayaran::with('tagihan.mahasiswa')
                ->where('status', 'verified')
                ->whereBetween('tanggal_bayar', [$rekonsiliasi->periode_awal, $rekonsiliasi->periode_akhir])
                ->whereDoesntHave('mutasiBank')
                ->get();
        }

        return view('keuangan.rekonsiliasi.show', compact('rekonsiliasi', 'stats', 'unmatchedTransaksi'));
    }

    public function process(Rekonsiliasi $rekonsiliasi)
    {
        if (!$rekonsiliasi->isEditable()) {
            return redirect()->back()
                ->with('error', 'Rekonsiliasi tidak dapat diproses');
        }

        // Update status to in_progress if still draft
        if ($rekonsiliasi->status === 'draft') {
            $rekonsiliasi->status = 'in_progress';
            $rekonsiliasi->save();
        }

        $rekonsiliasi->load([
            'akunBank', 
            'detailRekonsiliasi.mutasiBank',
            'detailRekonsiliasi.transaksiPembayaran.tagihan.mahasiswa',
        ]);

        // Get pending mutasi (unmatched)
        $pendingMutasi = $rekonsiliasi->detailRekonsiliasi
            ->where('status', 'unmatched')
            ->map(function($detail) {
                return $detail->mutasiBank;
            })
            ->filter();

        // Get unmatched transaksi
        $unmatchedTransaksi = TransaksiPembayaran::with('tagihan.mahasiswa')
            ->where('status', 'verified')
            ->whereBetween('tanggal_bayar', [$rekonsiliasi->periode_awal, $rekonsiliasi->periode_akhir])
            ->whereDoesntHave('mutasiBank')
            ->get();

        return view('keuangan.rekonsiliasi.process', compact('rekonsiliasi', 'pendingMutasi', 'unmatchedTransaksi'));
    }

    public function matchDetail(Request $request, Rekonsiliasi $rekonsiliasi)
    {
        $request->validate([
            'mutasi_bank_id' => 'required|exists:mutasi_bank,id',
            'transaksi_pembayaran_id' => 'required|exists:transaksi_pembayaran,id',
        ]);

        $detail = DetailRekonsiliasi::where('rekonsiliasi_id', $rekonsiliasi->id)
            ->where('mutasi_bank_id', $request->mutasi_bank_id)
            ->firstOrFail();

        $transaksi = TransaksiPembayaran::findOrFail($request->transaksi_pembayaran_id);
        $detail->match($transaksi);

        // Update statistik rekonsiliasi
        $rekonsiliasi->updateStatistik();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Berhasil mencocokkan mutasi dengan transaksi']);
        }

        return redirect()->back()
            ->with('success', 'Berhasil mencocokkan mutasi dengan transaksi');
    }

    public function unmatchDetail(Request $request, Rekonsiliasi $rekonsiliasi)
    {
        $request->validate([
            'mutasi_bank_id' => 'required|exists:mutasi_bank,id',
        ]);

        $detail = DetailRekonsiliasi::where('rekonsiliasi_id', $rekonsiliasi->id)
            ->where('mutasi_bank_id', $request->mutasi_bank_id)
            ->firstOrFail();

        $detail->unmatch();
        
        // Update statistik rekonsiliasi
        $rekonsiliasi->updateStatistik();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Pencocokan dibatalkan']);
        }

        return redirect()->back()
            ->with('success', 'Pencocokan dibatalkan');
    }

    public function ignoreDetail(Request $request, Rekonsiliasi $rekonsiliasi)
    {
        $request->validate([
            'mutasi_bank_id' => 'required|exists:mutasi_bank,id',
        ]);

        $detail = DetailRekonsiliasi::where('rekonsiliasi_id', $rekonsiliasi->id)
            ->where('mutasi_bank_id', $request->mutasi_bank_id)
            ->firstOrFail();

        $detail->ignore($request->keterangan);
        
        // Update statistik rekonsiliasi
        $rekonsiliasi->updateStatistik();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Mutasi ditandai diabaikan']);
        }

        return redirect()->back()
            ->with('success', 'Mutasi ditandai diabaikan');
    }

    public function markManual(Request $request, Rekonsiliasi $rekonsiliasi)
    {
        $request->validate([
            'mutasi_bank_id' => 'required|exists:mutasi_bank,id',
        ]);

        $detail = DetailRekonsiliasi::where('rekonsiliasi_id', $rekonsiliasi->id)
            ->where('mutasi_bank_id', $request->mutasi_bank_id)
            ->firstOrFail();

        $detail->status = DetailRekonsiliasi::STATUS_MANUAL;
        $detail->keterangan = $request->catatan ?? 'Diproses manual';
        $detail->save();
        
        // Update statistik rekonsiliasi
        $rekonsiliasi->updateStatistik();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Mutasi ditandai manual']);
        }

        return redirect()->back()
            ->with('success', 'Mutasi ditandai manual');
    }

    public function autoMatchAll(Rekonsiliasi $rekonsiliasi)
    {
        if (!$rekonsiliasi->isEditable()) {
            return redirect()->back()
                ->with('error', 'Rekonsiliasi tidak dapat diproses');
        }

        $matched = 0;

        foreach ($rekonsiliasi->detailRekonsiliasi()->where('status', 'unmatched')->get() as $detail) {
            $mutasi = $detail->mutasiBank;
            
            if ($mutasi->tipe !== 'kredit') continue;

            // Cari transaksi dengan nominal exact match
            $transaksi = TransaksiPembayaran::where('status', 'verified')
                ->where('jumlah', $mutasi->nominal)
                ->whereBetween('tanggal_bayar', [
                    $mutasi->tanggal->subDays(2),
                    $mutasi->tanggal->addDays(2)
                ])
                ->whereDoesntHave('mutasiBank')
                ->first();

            if ($transaksi) {
                $detail->match($transaksi);
                $matched++;
            }
        }

        $rekonsiliasi->updateStatistik();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'matched' => $matched,
                'message' => "{$matched} mutasi berhasil dicocokkan otomatis"
            ]);
        }

        return redirect()->back()
            ->with('success', "{$matched} mutasi berhasil dicocokkan otomatis");
    }

    public function complete(Rekonsiliasi $rekonsiliasi)
    {
        if (!$rekonsiliasi->isEditable()) {
            return redirect()->back()
                ->with('error', 'Rekonsiliasi tidak dapat diselesaikan');
        }

        $rekonsiliasi->complete();

        return redirect()->route('rekonsiliasi.show', $rekonsiliasi)
            ->with('success', 'Rekonsiliasi berhasil diselesaikan');
    }

    public function approve(Rekonsiliasi $rekonsiliasi)
    {
        if ($rekonsiliasi->status !== 'completed') {
            return redirect()->back()
                ->with('error', 'Hanya rekonsiliasi yang completed yang dapat diapprove');
        }

        $rekonsiliasi->approve();

        return redirect()->route('rekonsiliasi.show', $rekonsiliasi)
            ->with('success', 'Rekonsiliasi berhasil diapprove');
    }

    public function destroy(Rekonsiliasi $rekonsiliasi)
    {
        if ($rekonsiliasi->status === 'approved') {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus rekonsiliasi yang sudah diapprove');
        }

        // Unmatch all related mutasi
        foreach ($rekonsiliasi->detailRekonsiliasi as $detail) {
            if ($detail->status === 'matched') {
                $detail->mutasiBank->unmatch();
            }
        }

        $rekonsiliasi->delete();

        return redirect()->route('rekonsiliasi.index')
            ->with('success', 'Rekonsiliasi berhasil dihapus');
    }

    public function report(Rekonsiliasi $rekonsiliasi)
    {
        $rekonsiliasi->load([
            'akunBank',
            'createdBy',
            'approvedBy',
            'detailRekonsiliasi.mutasiBank',
            'detailRekonsiliasi.transaksiPembayaran.tagihan.mahasiswa',
        ]);

        return view('keuangan.rekonsiliasi.report', compact('rekonsiliasi'));
    }
}
