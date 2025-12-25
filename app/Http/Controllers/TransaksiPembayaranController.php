<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPembayaran;
use App\Models\Tagihan;
use App\Models\VirtualAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiPembayaranController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Mahasiswa view
        if ($user->isMahasiswa()) {
            $mahasiswa = $user->mahasiswa;
            $transaksi = TransaksiPembayaran::whereHas('tagihan', function($q) use ($mahasiswa) {
                $q->where('mahasiswa_id', $mahasiswa->id);
            })
            ->with(['tagihan.tahunAkademik', 'verifier'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

            return view('keuangan.transaksi.mahasiswa', compact('transaksi', 'mahasiswa'));
        }

        // Admin view
        $query = TransaksiPembayaran::with(['tagihan.mahasiswa', 'tagihan.tahunAkademik', 'verifier']);

        if ($request->search) {
            $query->where('no_transaksi', 'like', "%{$request->search}%")
                ->orWhereHas('tagihan.mahasiswa', function($q) use ($request) {
                    $q->where('nim', 'like', "%{$request->search}%")
                        ->orWhere('nama', 'like', "%{$request->search}%");
                });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->metode) {
            $query->where('metode_pembayaran', $request->metode);
        }

        if ($request->tanggal_dari) {
            $query->whereDate('tanggal_bayar', '>=', $request->tanggal_dari);
        }

        if ($request->tanggal_sampai) {
            $query->whereDate('tanggal_bayar', '<=', $request->tanggal_sampai);
        }

        $transaksi = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Statistics
        $stats = [
            'total_hari_ini' => TransaksiPembayaran::whereDate('tanggal_bayar', today())
                ->where('status', 'Verified')->sum('jumlah'),
            'total_bulan_ini' => TransaksiPembayaran::whereMonth('tanggal_bayar', now()->month)
                ->whereYear('tanggal_bayar', now()->year)
                ->where('status', 'Verified')->sum('jumlah'),
            'menunggu_verifikasi' => TransaksiPembayaran::where('status', 'Pending')->count(),
        ];

        return view('keuangan.transaksi.index', compact('transaksi', 'stats'));
    }

    public function create(Request $request)
    {
        $tagihan = null;
        if ($request->tagihan_id) {
            $tagihan = Tagihan::with('mahasiswa')->find($request->tagihan_id);
        }

        $tagihanList = Tagihan::belumLunas()->with('mahasiswa')->get();
        
        return view('keuangan.transaksi.create', compact('tagihanList', 'tagihan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tagihan_id' => 'required|exists:tagihan,id',
            'jumlah_bayar' => 'required|numeric|min:1000',
            'metode_pembayaran' => 'required|in:' . implode(',', array_keys(TransaksiPembayaran::METODE)),
            'tanggal_bayar' => 'required|date',
            'bukti_pembayaran' => 'nullable|image|max:2048',
        ]);

        $tagihan = Tagihan::find($request->tagihan_id);
        
        if ($request->jumlah_bayar > $tagihan->sisa_tagihan) {
            return back()->with('error', 'Jumlah bayar tidak boleh melebihi sisa tagihan (Rp ' . number_format($tagihan->sisa_tagihan, 0, ',', '.') . ')')->withInput();
        }

        DB::beginTransaction();
        try {
            $data = [
                'tagihan_id' => $request->tagihan_id,
                'mahasiswa_id' => $tagihan->mahasiswa_id,
                'jumlah' => $request->jumlah_bayar,
                'tanggal_bayar' => $request->tanggal_bayar,
                'metode_pembayaran' => $request->metode_pembayaran,
                'catatan' => $request->catatan,
            ];

            // Upload bukti pembayaran
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/bukti-pembayaran', $filename);
                $data['bukti_bayar'] = $filename;
            }

            // Set status based on metode
            if (in_array($request->metode_pembayaran, ['Transfer Bank', 'Virtual Account'])) {
                $data['status'] = 'Pending';
            } else {
                // Cash payment - langsung verified
                $data['status'] = 'Verified';
                $data['verified_by'] = auth()->id();
                $data['verified_at'] = now();
            }

            $transaksi = TransaksiPembayaran::create($data);

            // Update tagihan if verified
            if ($transaksi->status === 'Verified') {
                $transaksi->updateTagihanAmount();
            }

            DB::commit();
            return redirect()->route('transaksi-pembayaran.index')->with('success', 'Transaksi pembayaran berhasil dicatat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mencatat transaksi: ' . $e->getMessage());
        }
    }

    public function show(TransaksiPembayaran $transaksiPembayaran)
    {
        $transaksiPembayaran->load(['tagihan.mahasiswa.programStudi', 'tagihan.tahunAkademik', 'verifier']);
        return view('keuangan.transaksi.show', compact('transaksiPembayaran'));
    }

    /**
     * Verifikasi pembayaran
     */
    public function verify(TransaksiPembayaran $transaksiPembayaran)
    {
        if ($transaksiPembayaran->status !== 'Pending') {
            return back()->with('error', 'Transaksi ini tidak dalam status pending.');
        }

        DB::beginTransaction();
        try {
            $transaksiPembayaran->update([
                'status' => 'Verified',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            $transaksiPembayaran->updateTagihanAmount();

            DB::commit();
            return back()->with('success', 'Pembayaran berhasil diverifikasi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal verifikasi: ' . $e->getMessage());
        }
    }

    /**
     * Tolak pembayaran
     */
    public function reject(Request $request, TransaksiPembayaran $transaksiPembayaran)
    {
        if ($transaksiPembayaran->status !== 'Pending') {
            return back()->with('error', 'Transaksi ini tidak dalam status pending.');
        }

        $request->validate([
            'catatan_penolakan' => 'required|string|max:500',
        ]);

        $transaksiPembayaran->update([
            'status' => 'Rejected',
            'catatan' => $request->catatan_penolakan,
        ]);

        return back()->with('success', 'Pembayaran ditolak.');
    }

    /**
     * Pembayaran mahasiswa - halaman bayar
     */
    public function bayar(Tagihan $tagihan)
    {
        $user = auth()->user();
        
        if (!$user->isMahasiswa()) {
            return redirect()->route('transaksi-pembayaran.create', ['tagihan_id' => $tagihan->id]);
        }

        $mahasiswa = $user->mahasiswa;
        
        if ($tagihan->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        // Get or create VA
        $va = VirtualAccount::where('mahasiswa_id', $mahasiswa->id)->active()->first();
        
        if (!$va) {
            $va = VirtualAccount::create([
                'mahasiswa_id' => $mahasiswa->id,
                'bank_code' => 'BNI',
                'va_number' => VirtualAccount::generateVA($mahasiswa->id, 'BNI'),
            ]);
        }

        return view('keuangan.transaksi.bayar', compact('tagihan', 'mahasiswa', 'va'));
    }

    /**
     * Upload bukti bayar mahasiswa
     */
    public function uploadBukti(Request $request, Tagihan $tagihan)
    {
        $user = auth()->user();
        
        if (!$user->isMahasiswa() || $tagihan->mahasiswa_id != $user->mahasiswa->id) {
            abort(403);
        }

        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:10000',
            'bukti_pembayaran' => 'required|image|max:2048',
        ]);

        if ($request->jumlah_bayar > $tagihan->sisa_tagihan) {
            return back()->with('error', 'Jumlah bayar melebihi sisa tagihan!');
        }

        // Upload bukti
        $file = $request->file('bukti_pembayaran');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/bukti-pembayaran', $filename);

        TransaksiPembayaran::create([
            'tagihan_id' => $tagihan->id,
            'mahasiswa_id' => $tagihan->mahasiswa_id,
            'jumlah' => $request->jumlah_bayar,
            'metode_pembayaran' => 'Transfer Bank',
            'tanggal_bayar' => now(),
            'bukti_bayar' => $filename,
            'status' => 'Pending',
        ]);

        return redirect()->route('tagihan.index')->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi admin.');
    }

    /**
     * Laporan transaksi
     */
    public function laporan(Request $request)
    {
        $query = TransaksiPembayaran::where('status', 'Verified')
            ->with(['tagihan.mahasiswa.programStudi', 'tagihan.tahunAkademik']);

        if ($request->bulan && $request->tahun) {
            $query->whereMonth('tanggal_bayar', $request->bulan)
                ->whereYear('tanggal_bayar', $request->tahun);
        } elseif ($request->tahun) {
            $query->whereYear('tanggal_bayar', $request->tahun);
        } else {
            // Default: bulan ini
            $query->whereMonth('tanggal_bayar', now()->month)
                ->whereYear('tanggal_bayar', now()->year);
        }

        $transaksi = $query->orderBy('tanggal_bayar', 'desc')->get();

        // Summary by metode
        $summaryByMetode = $transaksi->groupBy('metode_pembayaran')
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('jumlah'),
                ];
            });

        $total = $transaksi->sum('jumlah');

        return view('keuangan.transaksi.laporan', compact('transaksi', 'summaryByMetode', 'total'));
    }

    /**
     * Get unmatched transactions for bank reconciliation
     */
    public function getUnmatched(Request $request)
    {
        $query = TransaksiPembayaran::with('tagihan.mahasiswa')
            ->where('status', 'Verified')
            ->whereDoesntHave('mutasiBank');

        // Filter by nominal (with 1% tolerance)
        if ($request->filled('nominal')) {
            $nominal = (float) $request->nominal;
            $query->whereBetween('jumlah', [$nominal * 0.99, $nominal * 1.01]);
        }

        // Filter by date range
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_bayar', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_bayar', '<=', $request->sampai_tanggal);
        }

        $transaksi = $query->orderBy('tanggal_bayar', 'desc')
            ->limit(50)
            ->get()
            ->map(function($trx) {
                return [
                    'id' => $trx->id,
                    'nomor_transaksi' => $trx->no_transaksi,
                    'mahasiswa_nama' => $trx->tagihan->mahasiswa->nama ?? '-',
                    'tanggal' => $trx->tanggal_bayar->format('d/m/Y'),
                    'jumlah' => $trx->jumlah,
                ];
            });

        return response()->json($transaksi);
    }
}
