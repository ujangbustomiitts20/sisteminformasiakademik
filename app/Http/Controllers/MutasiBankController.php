<?php

namespace App\Http\Controllers;

use App\Models\MutasiBank;
use App\Models\AkunBank;
use App\Models\TransaksiPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MutasiBankController extends Controller
{
    public function index(Request $request)
    {
        $query = MutasiBank::with(['akunBank', 'transaksiPembayaran.tagihan.mahasiswa'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        // Filter by akun bank
        if ($request->filled('akun_bank_id')) {
            $query->where('akun_bank_id', $request->akun_bank_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by tipe
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        // Filter by date range
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal', '<=', $request->sampai_tanggal);
        }

        $mutasi = $query->paginate(20)->withQueryString();
        $akunBank = AkunBank::active()->orderBy('nama_bank')->get();

        $stats = [
            'total' => MutasiBank::count(),
            'pending' => MutasiBank::pending()->count(),
            'matched' => MutasiBank::matched()->count(),
            'unmatched' => MutasiBank::where('status', 'unmatched')->count(),
            'total_kredit' => MutasiBank::kredit()->sum('nominal'),
            'total_debit' => MutasiBank::debit()->sum('nominal'),
        ];

        return view('keuangan.rekonsiliasi.mutasi.index', compact('mutasi', 'akunBank', 'stats'));
    }

    public function create()
    {
        $akunBank = AkunBank::active()->orderBy('nama_bank')->get();
        return view('keuangan.rekonsiliasi.mutasi.create', compact('akunBank'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'akun_bank_id' => 'required|exists:akun_bank,id',
            'tanggal' => 'required|date',
            'tipe' => 'required|in:kredit,debit',
            'nominal' => 'required|numeric|min:0',
            'nomor_referensi' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
            'nama_pengirim' => 'nullable|string|max:150',
        ]);

        MutasiBank::create([
            'akun_bank_id' => $request->akun_bank_id,
            'tanggal' => $request->tanggal,
            'tipe' => $request->tipe,
            'nominal' => $request->nominal,
            'nomor_referensi' => $request->nomor_referensi,
            'keterangan' => $request->keterangan,
            'nama_pengirim' => $request->nama_pengirim,
            'status' => 'pending',
        ]);

        // Update saldo sistem akun bank
        $akunBank = AkunBank::find($request->akun_bank_id);
        $akunBank->updateSaldoSistem();

        return redirect()->route('mutasi-bank.index')
            ->with('success', 'Mutasi bank berhasil ditambahkan');
    }

    public function show(MutasiBank $mutasiBank)
    {
        $mutasiBank->load(['akunBank', 'transaksiPembayaran.tagihan.mahasiswa', 'matchedBy']);

        // Cari transaksi yang potensial match
        $potentialMatches = [];
        if ($mutasiBank->isPending() && $mutasiBank->tipe === 'kredit') {
            $potentialMatches = TransaksiPembayaran::with('tagihan.mahasiswa')
                ->where('status', 'verified')
                ->whereBetween('jumlah', [$mutasiBank->nominal * 0.99, $mutasiBank->nominal * 1.01])
                ->whereBetween('tanggal_bayar', [
                    $mutasiBank->tanggal->subDays(3),
                    $mutasiBank->tanggal->addDays(3)
                ])
                ->whereDoesntHave('mutasiBank')
                ->limit(10)
                ->get();
        }

        return view('keuangan.rekonsiliasi.mutasi.show', compact('mutasiBank', 'potentialMatches'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'akun_bank_id' => 'required|exists:akun_bank,id',
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        $akunBank = AkunBank::findOrFail($request->akun_bank_id);
        $file = $request->file('file');
        
        $imported = 0;
        $skipped = 0;
        $errors = [];

        try {
            // Simple CSV parsing
            $handle = fopen($file->getRealPath(), 'r');
            $header = fgetcsv($handle); // Skip header
            
            while (($row = fgetcsv($handle)) !== false) {
                try {
                    // Expected format: tanggal, tipe, nominal, keterangan, nomor_referensi, nama_pengirim
                    if (count($row) < 3) continue;
                    
                    $tanggal = Carbon::parse($row[0]);
                    $tipe = strtolower(trim($row[1])) === 'kredit' ? 'kredit' : 'debit';
                    $nominal = abs((float) str_replace([',', '.'], ['', '.'], $row[2]));
                    $keterangan = $row[3] ?? null;
                    $nomorRef = $row[4] ?? null;
                    $namaPengirim = $row[5] ?? null;

                    // Check duplicate
                    $exists = MutasiBank::where('akun_bank_id', $akunBank->id)
                        ->where('tanggal', $tanggal->toDateString())
                        ->where('nominal', $nominal)
                        ->where('tipe', $tipe)
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    MutasiBank::create([
                        'akun_bank_id' => $akunBank->id,
                        'tanggal' => $tanggal,
                        'tipe' => $tipe,
                        'nominal' => $nominal,
                        'keterangan' => $keterangan,
                        'nomor_referensi' => $nomorRef,
                        'nama_pengirim' => $namaPengirim,
                        'status' => 'pending',
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris error: " . implode(',', $row);
                }
            }

            fclose($handle);

            // Update saldo
            $akunBank->updateSaldoSistem();

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal import file: ' . $e->getMessage());
        }

        $message = "{$imported} mutasi berhasil diimport";
        if ($skipped > 0) {
            $message .= ", {$skipped} data duplikat dilewati";
        }

        return redirect()->route('mutasi-bank.index')
            ->with('success', $message);
    }

    public function match(Request $request, MutasiBank $mutasiBank)
    {
        $request->validate([
            'transaksi_pembayaran_id' => 'required|exists:transaksi_pembayaran,id',
        ]);

        $transaksi = TransaksiPembayaran::findOrFail($request->transaksi_pembayaran_id);
        $mutasiBank->match($transaksi);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Mutasi berhasil dicocokkan dengan transaksi']);
        }

        return redirect()->back()
            ->with('success', 'Mutasi berhasil dicocokkan dengan transaksi');
    }

    public function unmatch(Request $request, MutasiBank $mutasiBank)
    {
        $mutasiBank->unmatch();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Pencocokan mutasi dibatalkan']);
        }

        return redirect()->back()
            ->with('success', 'Pencocokan mutasi dibatalkan');
    }

    public function markManual(Request $request, MutasiBank $mutasiBank)
    {
        $catatan = $request->catatan ?? 'Diproses manual';
        $mutasiBank->markAsManual($catatan);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Mutasi ditandai sebagai pencocokan manual']);
        }

        return redirect()->back()
            ->with('success', 'Mutasi ditandai sebagai pencocokan manual');
    }

    public function autoMatch(Request $request)
    {
        $request->validate([
            'akun_bank_id' => 'nullable|exists:akun_bank,id',
        ]);

        $query = MutasiBank::pending()->kredit();
        
        if ($request->filled('akun_bank_id')) {
            $query->where('akun_bank_id', $request->akun_bank_id);
        }

        $mutasiPending = $query->get();
        $matched = 0;

        foreach ($mutasiPending as $mutasi) {
            // Cari transaksi dengan nominal exact match
            $transaksi = TransaksiPembayaran::where('status', 'Verified')
                ->where('jumlah', $mutasi->nominal)
                ->whereDate('tanggal_bayar', '>=', $mutasi->tanggal->subDays(2))
                ->whereDate('tanggal_bayar', '<=', $mutasi->tanggal->addDays(2))
                ->whereDoesntHave('mutasiBank')
                ->first();

            if ($transaksi) {
                $mutasi->match($transaksi);
                $matched++;
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'matched' => $matched,
                'message' => "{$matched} mutasi berhasil dicocokkan secara otomatis"
            ]);
        }

        return redirect()->back()
            ->with('success', "{$matched} mutasi berhasil dicocokkan secara otomatis");
    }

    public function destroy(MutasiBank $mutasiBank)
    {
        if ($mutasiBank->isMatched()) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus mutasi yang sudah dicocokkan');
        }

        $akunBankId = $mutasiBank->akun_bank_id;
        $mutasiBank->delete();

        // Update saldo
        AkunBank::find($akunBankId)->updateSaldoSistem();

        return redirect()->back()
            ->with('success', 'Mutasi berhasil dihapus');
    }
}
