<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\RefundHistory;
use App\Models\TransaksiPembayaran;
use App\Models\Mahasiswa;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RefundController extends Controller
{
    /**
     * Display a listing of refunds.
     */
    public function index(Request $request)
    {
        $query = Refund::with(['transaksiPembayaran', 'mahasiswa', 'tagihan', 'disetujuiOleh', 'diprosesOleh', 'diselesaikanOleh']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Filter by date range
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        // Filter by search keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_refund', 'like', "%{$search}%")
                  ->orWhereHas('mahasiswa', function ($q2) use ($search) {
                      $q2->where('nama', 'like', "%{$search}%")
                         ->orWhere('nim', 'like', "%{$search}%");
                  });
            });
        }

        $refunds = $query->latest()->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => Refund::count(),
            'pending' => Refund::pending()->count(),
            'diproses' => Refund::diproses()->count(),
            'disetujui' => Refund::disetujui()->count(),
            'selesai' => Refund::selesai()->count(),
            'ditolak' => Refund::ditolak()->count(),
            'total_nominal' => Refund::whereIn('status', [Refund::STATUS_DISETUJUI, Refund::STATUS_SELESAI])->sum('jumlah_disetujui'),
        ];

        return view('keuangan.refund.index', compact('refunds', 'stats'));
    }

    /**
     * Show the form for creating a new refund.
     */
    public function create(Request $request)
    {
        $transaksiPembayaran = null;
        $mahasiswa = null;

        // If transaction ID is provided
        if ($request->filled('transaksi_id')) {
            $transaksiPembayaran = TransaksiPembayaran::with(['mahasiswa', 'tagihan'])->find($request->transaksi_id);
            $mahasiswa = $transaksiPembayaran?->mahasiswa;
        }

        // If mahasiswa ID is provided
        if ($request->filled('mahasiswa_id')) {
            $mahasiswa = Mahasiswa::find($request->mahasiswa_id);
        }

        $jenisList = Refund::JENIS_LIST;
        $metodeList = Refund::METODE_LIST;

        return view('keuangan.refund.create', compact('transaksiPembayaran', 'mahasiswa', 'jenisList', 'metodeList'));
    }

    /**
     * Store a newly created refund.
     */
    public function store(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'transaksi_pembayaran_id' => 'nullable|exists:transaksi_pembayaran,id',
            'tagihan_id' => 'nullable|exists:tagihan,id',
            'jenis' => 'required|in:' . implode(',', array_keys(Refund::JENIS_LIST)),
            'alasan' => 'required|string|max:1000',
            'jumlah_pengajuan' => 'required|numeric|min:0',
            'metode_refund' => 'required|in:' . implode(',', array_keys(Refund::METODE_LIST)),
            'nama_bank' => 'required_if:metode_refund,transfer',
            'nomor_rekening' => 'required_if:metode_refund,transfer',
            'nama_pemilik_rekening' => 'required_if:metode_refund,transfer',
            'dokumen_pendukung' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Check if refund already exists for this transaction
            if ($request->filled('transaksi_pembayaran_id')) {
                $existingRefund = Refund::where('transaksi_pembayaran_id', $request->transaksi_pembayaran_id)
                    ->whereNotIn('status', [Refund::STATUS_DITOLAK, Refund::STATUS_DIBATALKAN])
                    ->first();

                if ($existingRefund) {
                    return back()->with('error', 'Sudah ada pengajuan refund untuk transaksi ini.')->withInput();
                }
            }

            $dokumenPath = null;
            if ($request->hasFile('dokumen_pendukung')) {
                $dokumenPath = $request->file('dokumen_pendukung')->store('refund/dokumen', 'public');
            }

            $refund = Refund::create([
                'mahasiswa_id' => $request->mahasiswa_id,
                'transaksi_pembayaran_id' => $request->transaksi_pembayaran_id,
                'tagihan_id' => $request->tagihan_id,
                'jenis' => $request->jenis,
                'alasan' => $request->alasan,
                'jumlah_pengajuan' => $request->jumlah_pengajuan,
                'metode_refund' => $request->metode_refund,
                'nama_bank' => $request->nama_bank,
                'nomor_rekening' => $request->nomor_rekening,
                'nama_pemilik_rekening' => $request->nama_pemilik_rekening,
                'dokumen_pendukung' => $dokumenPath,
                'status' => Refund::STATUS_PENDING,
            ]);

            DB::commit();

            return redirect()->route('keuangan.refund.show', $refund)
                ->with('success', 'Pengajuan refund berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat pengajuan refund: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified refund.
     */
    public function show(Refund $refund)
    {
        $refund->load([
            'transaksiPembayaran',
            'mahasiswa',
            'tagihan',
            'disetujuiOleh',
            'diprosesOleh',
            'diselesaikanOleh',
            'histories.user'
        ]);

        return view('keuangan.refund.show', compact('refund'));
    }

    /**
     * Show the form for editing the specified refund.
     */
    public function edit(Refund $refund)
    {
        if ($refund->status !== Refund::STATUS_PENDING) {
            return back()->with('error', 'Hanya refund dengan status pending yang dapat diedit.');
        }

        $refund->load(['transaksiPembayaran', 'mahasiswa', 'tagihan']);
        
        $jenisList = Refund::JENIS_LIST;
        $metodeList = Refund::METODE_LIST;

        return view('keuangan.refund.edit', compact('refund', 'jenisList', 'metodeList'));
    }

    /**
     * Update the specified refund.
     */
    public function update(Request $request, Refund $refund)
    {
        if ($refund->status !== Refund::STATUS_PENDING) {
            return back()->with('error', 'Hanya refund dengan status pending yang dapat diedit.');
        }

        $request->validate([
            'jenis' => 'required|in:' . implode(',', array_keys(Refund::JENIS_LIST)),
            'alasan' => 'required|string|max:1000',
            'jumlah_pengajuan' => 'required|numeric|min:0',
            'metode_refund' => 'required|in:' . implode(',', array_keys(Refund::METODE_LIST)),
            'nama_bank' => 'required_if:metode_refund,transfer',
            'nomor_rekening' => 'required_if:metode_refund,transfer',
            'nama_pemilik_rekening' => 'required_if:metode_refund,transfer',
            'dokumen_pendukung' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $dokumenPath = $refund->dokumen_pendukung;
        if ($request->hasFile('dokumen_pendukung')) {
            // Delete old file
            if ($dokumenPath && Storage::disk('public')->exists($dokumenPath)) {
                Storage::disk('public')->delete($dokumenPath);
            }
            $dokumenPath = $request->file('dokumen_pendukung')->store('refund/dokumen', 'public');
        }

        $refund->update([
            'jenis' => $request->jenis,
            'alasan' => $request->alasan,
            'jumlah_pengajuan' => $request->jumlah_pengajuan,
            'metode_refund' => $request->metode_refund,
            'nama_bank' => $request->nama_bank,
            'nomor_rekening' => $request->nomor_rekening,
            'nama_pemilik_rekening' => $request->nama_pemilik_rekening,
            'dokumen_pendukung' => $dokumenPath,
        ]);

        return redirect()->route('keuangan.refund.show', $refund)
            ->with('success', 'Data refund berhasil diperbarui.');
    }

    /**
     * Remove the specified refund.
     */
    public function destroy(Refund $refund)
    {
        if (!in_array($refund->status, [Refund::STATUS_PENDING, Refund::STATUS_DITOLAK])) {
            return back()->with('error', 'Hanya refund dengan status pending atau ditolak yang dapat dihapus.');
        }

        $refund->delete();

        return redirect()->route('keuangan.refund.index')
            ->with('success', 'Refund berhasil dihapus.');
    }

    /**
     * Process the refund request.
     */
    public function process(Request $request, Refund $refund)
    {
        if (!$refund->canProcess()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Refund tidak dapat diproses.'], 400);
            }
            return back()->with('error', 'Refund tidak dapat diproses.');
        }

        $request->validate([
            'catatan' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $refund->process($request->catatan);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Refund sedang diproses.']);
            }
            return redirect()->route('keuangan.refund.show', $refund)
                ->with('success', 'Refund sedang diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal memproses refund: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal memproses refund: ' . $e->getMessage());
        }
    }

    /**
     * Approve the refund request.
     */
    public function approve(Request $request, Refund $refund)
    {
        if (!$refund->canApprove()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Refund tidak dapat disetujui.'], 400);
            }
            return back()->with('error', 'Refund tidak dapat disetujui.');
        }

        $request->validate([
            'jumlah_disetujui' => 'required|numeric|min:0|max:' . $refund->jumlah_pengajuan,
            'catatan' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $refund->approve($request->jumlah_disetujui, $request->catatan);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Refund berhasil disetujui.']);
            }
            return redirect()->route('keuangan.refund.show', $refund)
                ->with('success', 'Refund berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal menyetujui refund: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal menyetujui refund: ' . $e->getMessage());
        }
    }

    /**
     * Reject the refund request.
     */
    public function reject(Request $request, Refund $refund)
    {
        if (!$refund->canReject()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Refund tidak dapat ditolak.'], 400);
            }
            return back()->with('error', 'Refund tidak dapat ditolak.');
        }

        $request->validate([
            'alasan_penolakan' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $refund->reject($request->alasan_penolakan);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Refund berhasil ditolak.']);
            }
            return redirect()->route('keuangan.refund.show', $refund)
                ->with('success', 'Refund berhasil ditolak.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal menolak refund: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal menolak refund: ' . $e->getMessage());
        }
    }

    /**
     * Complete the refund.
     */
    public function complete(Request $request, Refund $refund)
    {
        if (!$refund->canComplete()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Refund tidak dapat diselesaikan.'], 400);
            }
            return back()->with('error', 'Refund tidak dapat diselesaikan.');
        }

        $request->validate([
            'bukti_refund' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'nomor_referensi_refund' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $buktiPath = null;
            if ($request->hasFile('bukti_refund')) {
                $buktiPath = $request->file('bukti_refund')->store('refund/bukti', 'public');
            }

            $refund->complete($buktiPath, $request->nomor_referensi_refund);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Refund berhasil diselesaikan.']);
            }
            return redirect()->route('keuangan.refund.show', $refund)
                ->with('success', 'Refund berhasil diselesaikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal menyelesaikan refund: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal menyelesaikan refund: ' . $e->getMessage());
        }
    }

    /**
     * Cancel the refund.
     */
    public function cancel(Request $request, Refund $refund)
    {
        if (!$refund->canCancel()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Refund tidak dapat dibatalkan.'], 400);
            }
            return back()->with('error', 'Refund tidak dapat dibatalkan.');
        }

        DB::beginTransaction();
        try {
            $refund->cancel();

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Refund berhasil dibatalkan.']);
            }
            return redirect()->route('keuangan.refund.show', $refund)
                ->with('success', 'Refund berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal membatalkan refund: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal membatalkan refund: ' . $e->getMessage());
        }
    }

    /**
     * Search transaksi pembayaran for refund.
     */
    public function searchTransaksi(Request $request)
    {
        $query = TransaksiPembayaran::with(['mahasiswa', 'tagihan']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%")
                  ->orWhereHas('mahasiswa', function ($q2) use ($search) {
                      $q2->where('nama', 'like', "%{$search}%")
                         ->orWhere('nim', 'like', "%{$search}%");
                  });
            });
        }

        // Only get verified transactions
        $query->where('status', 'Verified');

        // Exclude transactions that already have active refund
        $query->whereDoesntHave('refunds', function ($q) {
            $q->whereNotIn('status', [Refund::STATUS_DITOLAK, Refund::STATUS_DIBATALKAN]);
        });

        $transaksi = $query->latest()->limit(20)->get();

        return response()->json($transaksi->map(function ($t) {
            return [
                'id' => $t->id,
                'no_transaksi' => $t->no_transaksi,
                'tanggal' => $t->tanggal_bayar?->format('d/m/Y'),
                'jumlah' => $t->jumlah,
                'jumlah_formatted' => 'Rp ' . number_format($t->jumlah, 0, ',', '.'),
                'mahasiswa' => $t->mahasiswa ? [
                    'id' => $t->mahasiswa->id,
                    'nim' => $t->mahasiswa->nim,
                    'nama' => $t->mahasiswa->nama,
                ] : null,
                'tagihan' => $t->tagihan ? [
                    'id' => $t->tagihan->id,
                    'nama' => $t->tagihan->nama ?? $t->tagihan->jenis_tagihan,
                ] : null,
            ];
        }));
    }

    /**
     * Search mahasiswa for refund.
     */
    public function searchMahasiswa(Request $request)
    {
        $query = Mahasiswa::query();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $mahasiswa = $query->limit(20)->get();

        return response()->json($mahasiswa->map(function ($m) {
            return [
                'id' => $m->id,
                'nim' => $m->nim,
                'nama' => $m->nama,
                'prodi' => $m->prodi?->nama ?? '-',
            ];
        }));
    }

    /**
     * Export refunds to Excel/PDF.
     */
    public function export(Request $request)
    {
        $query = Refund::with(['transaksiPembayaran', 'mahasiswa', 'tagihan', 'disetujuiOleh', 'diprosesOleh', 'diselesaikanOleh']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        $refunds = $query->latest()->get();

        if ($request->format === 'pdf') {
            // PDF export logic
            return view('keuangan.refund.export-pdf', compact('refunds'));
        }

        // Excel export (CSV)
        $filename = 'refund_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($refunds) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No. Refund', 'NIM', 'Nama Mahasiswa', 'No. Transaksi', 'Jenis', 'Jumlah Pengajuan', 'Jumlah Disetujui', 'Status', 'Metode', 'Tanggal Pengajuan', 'Tanggal Selesai']);

            foreach ($refunds as $r) {
                fputcsv($file, [
                    $r->nomor_refund,
                    $r->mahasiswa?->nim,
                    $r->mahasiswa?->nama,
                    $r->transaksiPembayaran?->no_transaksi,
                    $r->jenis_label,
                    $r->jumlah_pengajuan,
                    $r->jumlah_disetujui,
                    $r->status_label,
                    $r->metode_label,
                    $r->created_at?->format('d/m/Y H:i'),
                    $r->diselesaikan_at?->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
