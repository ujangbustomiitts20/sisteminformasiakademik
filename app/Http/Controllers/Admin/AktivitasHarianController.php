<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AktivitasHarian;
use App\Models\Dosen;
use App\Models\Pegawai;
use App\Models\UraianKegiatanSkp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AktivitasHarianController extends Controller
{
    /**
     * Display listing all aktivitas harian
     */
    public function index(Request $request)
    {
        $query = AktivitasHarian::with(['dosen.programStudi', 'pegawai', 'uraianKegiatanSkp', 'approvedBy']);

        // Filter dosen
        if ($request->filled('dosen_id')) {
            $query->where('dosen_id', $request->dosen_id);
        }

        // Filter tanggal
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        // Filter bulan & tahun (default)
        if (!$request->filled('tanggal_dari') && !$request->filled('tanggal_sampai')) {
            $bulan = $request->bulan ?? now()->month;
            $tahun = $request->tahun ?? now()->year;
            $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('uraian_kegiatan', 'like', "%{$search}%")
                  ->orWhereHas('dosen', fn($q) => $q->where('nama', 'like', "%{$search}%"))
                  ->orWhereHas('pegawai', fn($q) => $q->where('nama', 'like', "%{$search}%"));
            });
        }

        $aktivitas = $query->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Stats
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;
        
        $stats = [
            'total' => AktivitasHarian::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->count(),
            'diajukan' => AktivitasHarian::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                ->where('status', 'diajukan')->count(),
            'disetujui' => AktivitasHarian::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                ->where('status', 'disetujui')->count(),
            'ditolak' => AktivitasHarian::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                ->where('status', 'ditolak')->count(),
        ];

        // Dosen list for filter
        $dosenList = Dosen::where('status', 'aktif')->orderBy('nama')->get();

        return view('admin.aktivitas-harian.index', compact('aktivitas', 'stats', 'dosenList'));
    }

    /**
     * Show detail aktivitas
     */
    public function show(AktivitasHarian $aktivitasHarian)
    {
        $aktivitasHarian->load(['dosen.programStudi', 'pegawai', 'uraianKegiatanSkp', 'approvedBy']);
        
        return view('admin.aktivitas-harian.show', compact('aktivitasHarian'));
    }

    /**
     * Approve aktivitas
     */
    public function approve(AktivitasHarian $aktivitasHarian)
    {
        if ($aktivitasHarian->status !== 'diajukan') {
            return redirect()->back()->with('error', 'Hanya aktivitas dengan status "Diajukan" yang dapat disetujui!');
        }

        $aktivitasHarian->update([
            'status' => 'disetujui',
            'disetujui_oleh' => Auth::id(),
            'tanggal_disetujui' => now(),
        ]);

        return redirect()->back()->with('success', 'Aktivitas berhasil disetujui!');
    }

    /**
     * Reject aktivitas
     */
    public function reject(Request $request, AktivitasHarian $aktivitasHarian)
    {
        if ($aktivitasHarian->status !== 'diajukan') {
            return redirect()->back()->with('error', 'Hanya aktivitas dengan status "Diajukan" yang dapat ditolak!');
        }

        $validated = $request->validate([
            'catatan_atasan' => 'required|string|max:500',
        ], [
            'catatan_atasan.required' => 'Catatan/alasan penolakan wajib diisi',
        ]);

        $aktivitasHarian->update([
            'status' => 'ditolak',
            'catatan_atasan' => $validated['catatan_atasan'],
        ]);

        return redirect()->back()->with('success', 'Aktivitas ditolak dengan catatan!');
    }

    /**
     * Bulk approve
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'aktivitas_ids' => 'required|array|min:1',
            'aktivitas_ids.*' => 'exists:aktivitas_harian,id',
        ]);

        $count = AktivitasHarian::whereIn('id', $validated['aktivitas_ids'])
            ->where('status', 'diajukan')
            ->update([
                'status' => 'disetujui',
                'disetujui_oleh' => Auth::id(),
                'tanggal_disetujui' => now(),
            ]);

        return redirect()->back()->with('success', "{$count} aktivitas berhasil disetujui!");
    }

    /**
     * Rekap per pegawai
     */
    public function rekap(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        // Get rekap per dosen
        $rekapDosen = Dosen::where('status', 'aktif')
            ->withCount([
                'aktivitasHarian as total_aktivitas' => function ($q) use ($bulan, $tahun) {
                    $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
                },
                'aktivitasHarian as aktivitas_disetujui' => function ($q) use ($bulan, $tahun) {
                    $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                      ->where('status', 'disetujui');
                },
                'aktivitasHarian as aktivitas_diajukan' => function ($q) use ($bulan, $tahun) {
                    $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                      ->where('status', 'diajukan');
                },
            ])
            ->having('total_aktivitas', '>', 0)
            ->orderBy('nama')
            ->paginate(20)
            ->withQueryString();

        // Overall stats
        $stats = [
            'total_pegawai' => $rekapDosen->total(),
            'total_aktivitas' => AktivitasHarian::whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)->count(),
            'total_disetujui' => AktivitasHarian::whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)->where('status', 'disetujui')->count(),
            'menunggu_approval' => AktivitasHarian::whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)->where('status', 'diajukan')->count(),
        ];

        return view('admin.aktivitas-harian.rekap', compact('rekapDosen', 'stats', 'bulan', 'tahun'));
    }

    /**
     * Detail rekap per dosen
     */
    public function rekapDetail(Request $request, Dosen $dosen)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $aktivitasPerTanggal = AktivitasHarian::where('dosen_id', $dosen->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->with('uraianKegiatanSkp')
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy(function ($item) {
                return $item->tanggal->format('Y-m-d');
            });

        $stats = AktivitasHarian::getRekapBulanan($dosen->id, $bulan, $tahun);

        return view('admin.aktivitas-harian.rekap-detail', compact(
            'dosen',
            'aktivitasPerTanggal',
            'stats',
            'bulan',
            'tahun'
        ));
    }

    /**
     * Delete aktivitas (admin only)
     */
    public function destroy(AktivitasHarian $aktivitasHarian)
    {
        $aktivitasHarian->delete();

        return redirect()->back()->with('success', 'Aktivitas harian berhasil dihapus!');
    }
}
