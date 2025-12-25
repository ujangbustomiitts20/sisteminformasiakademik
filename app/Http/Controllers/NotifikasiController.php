<?php

namespace App\Http\Controllers;

use App\Models\NotifikasiKeuangan;
use App\Models\Tagihan;
use App\Models\Cicilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Display a listing of notifications for admin
     */
    public function index(Request $request)
    {
        $query = NotifikasiKeuangan::with(['mahasiswa'])
            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('created_at', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('created_at', '<=', $request->sampai_tanggal);
        }

        $notifikasi = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total' => NotifikasiKeuangan::count(),
            'belum_dibaca' => NotifikasiKeuangan::whereNull('read_at')->count(),
            'reminder' => NotifikasiKeuangan::where('jenis', NotifikasiKeuangan::JENIS_REMINDER)->count(),
            'tagihan_jatuh_tempo' => NotifikasiKeuangan::where('jenis', NotifikasiKeuangan::JENIS_TAGIHAN_JATUH_TEMPO)->count(),
            'cicilan_jatuh_tempo' => NotifikasiKeuangan::where('jenis', NotifikasiKeuangan::JENIS_CICILAN_JATUH_TEMPO)->count(),
            'denda' => NotifikasiKeuangan::where('jenis', NotifikasiKeuangan::JENIS_DENDA)->count(),
            'pembayaran' => NotifikasiKeuangan::where('jenis', NotifikasiKeuangan::JENIS_PEMBAYARAN_BERHASIL)->count(),
        ];

        return view('keuangan.notifikasi.index', compact('notifikasi', 'stats'));
    }

    /**
     * Display notifications for mahasiswa
     */
    public function mahasiswa()
    {
        $user = Auth::user();
        
        // Get mahasiswa_id from user
        $mahasiswaId = $user->mahasiswa_id ?? $user->mahasiswa->id ?? null;
        
        if (!$mahasiswaId) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan');
        }

        $notifikasi = NotifikasiKeuangan::where('mahasiswa_id', $mahasiswaId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Mark as read
        NotifikasiKeuangan::where('mahasiswa_id', $mahasiswaId)
            ->whereNull('read_at')
            ->update(['read_at' => now(), 'status' => 'read']);

        return view('keuangan.notifikasi.mahasiswa', compact('notifikasi'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notifikasi = NotifikasiKeuangan::findOrFail($id);
        $notifikasi->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $query = NotifikasiKeuangan::whereNull('read_at');

        // If mahasiswa, only mark their notifications
        if ($request->has('mahasiswa_id')) {
            $query->where('mahasiswa_id', $request->mahasiswa_id);
        }

        $query->update(['read_at' => now(), 'status' => 'read']);

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai dibaca');
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notifikasi = NotifikasiKeuangan::findOrFail($id);
        $notifikasi->delete();

        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus');
    }

    /**
     * Get unread count for API
     */
    public function unreadCount(Request $request)
    {
        $query = NotifikasiKeuangan::whereNull('read_at');

        if ($request->has('mahasiswa_id')) {
            $query->where('mahasiswa_id', $request->mahasiswa_id);
        }

        return response()->json([
            'count' => $query->count()
        ]);
    }

    /**
     * Send manual notification
     */
    public function sendManual(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'jenis' => 'required|string',
            'judul' => 'required|string|max:255',
            'pesan' => 'required|string',
            'channel' => 'nullable|in:database,email,whatsapp',
        ]);

        NotifikasiKeuangan::create([
            'mahasiswa_id' => $request->mahasiswa_id,
            'jenis' => $request->jenis,
            'judul' => $request->judul,
            'pesan' => $request->pesan,
            'channel' => $request->channel ?? 'database',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Notifikasi berhasil dikirim');
    }

    /**
     * Bulk delete notifications
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:notifikasi_keuangan,id',
        ]);

        NotifikasiKeuangan::whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', count($request->ids) . ' notifikasi berhasil dihapus');
    }
}
