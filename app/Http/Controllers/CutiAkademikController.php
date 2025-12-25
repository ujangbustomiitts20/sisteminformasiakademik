<?php

namespace App\Http\Controllers;

use App\Models\CutiAkademik;
use App\Models\HistoryStatusMahasiswa;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CutiAkademikController extends Controller
{
    /**
     * Display listing for admin
     */
    public function index(Request $request)
    {
        $query = CutiAkademik::with(['mahasiswa.programStudi', 'tahunAkademik']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('alasan')) {
            $query->where('alasan', $request->alasan);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $cutis = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'pending' => CutiAkademik::where('status', 'Pending')->count(),
            'disetujui_kaprodi' => CutiAkademik::where('status', 'Disetujui Kaprodi')->count(),
            'disetujui_dekan' => CutiAkademik::where('status', 'Disetujui Dekan')->count(),
            'ditolak' => CutiAkademik::where('status', 'Ditolak')->count(),
        ];

        return view('akademik.cuti.index', compact('cutis', 'stats'));
    }

    /**
     * Display for mahasiswa
     */
    public function mahasiswaIndex()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        $cutis = CutiAkademik::with('tahunAkademik')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();

        // Cek apakah bisa mengajukan cuti
        $canApply = !CutiAkademik::where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('status', ['Pending', 'Disetujui Kaprodi', 'Disetujui Dekan'])
            ->exists();

        return view('akademik.cuti.mahasiswa-index', compact('cutis', 'mahasiswa', 'tahunAkademik', 'canApply'));
    }

    /**
     * Show form for creating new cuti
     */
    public function create()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        // Cek apakah sudah ada pengajuan aktif
        $existingCuti = CutiAkademik::where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('status', ['Pending', 'Disetujui Kaprodi', 'Disetujui Dekan'])
            ->first();

        if ($existingCuti) {
            return redirect()->route('cuti.mahasiswa')
                ->with('error', 'Anda masih memiliki pengajuan cuti yang belum selesai!');
        }

        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();

        return view('akademik.cuti.create', compact('mahasiswa', 'tahunAkademik'));
    }

    /**
     * Store new cuti request
     */
    public function store(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        $validated = $request->validate([
            'alasan' => 'required|in:Keuangan,Kesehatan,Keluarga,Pekerjaan,Lainnya',
            'keterangan' => 'required|string|max:1000',
            'jumlah_semester' => 'required|integer|min:1|max:2',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();

        // Calculate dates
        $tanggalMulai = $tahunAkademik->tanggal_mulai;
        $tanggalSelesai = $validated['jumlah_semester'] == 1 
            ? $tahunAkademik->tanggal_selesai 
            : TahunAkademik::where('id', '>', $tahunAkademik->id)->first()?->tanggal_selesai ?? $tahunAkademik->tanggal_selesai->addMonths(6);

        // Upload dokumen
        $dokumenPath = null;
        if ($request->hasFile('dokumen_pendukung')) {
            $dokumenPath = $request->file('dokumen_pendukung')->store('cuti-dokumen', 'public');
        }

        CutiAkademik::create([
            'mahasiswa_id' => $mahasiswa->id,
            'tahun_akademik_id' => $tahunAkademik->id,
            'alasan' => $validated['alasan'],
            'keterangan' => $validated['keterangan'],
            'jumlah_semester' => $validated['jumlah_semester'],
            'dokumen_pendukung' => $dokumenPath,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'status' => 'Pending',
        ]);

        return redirect()->route('cuti.mahasiswa')
            ->with('success', 'Pengajuan cuti berhasil dikirim! Silakan tunggu persetujuan.');
    }

    /**
     * Show detail cuti
     */
    public function show(CutiAkademik $cuti)
    {
        $cuti->load(['mahasiswa.programStudi', 'tahunAkademik', 'disetujuiKaprodiOleh', 'disetujuiDekanOleh']);
        
        return view('akademik.cuti.show', compact('cuti'));
    }

    /**
     * Process approval (admin)
     */
    public function approve(Request $request, CutiAkademik $cuti)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve_kaprodi,approve_dekan,reject',
            'catatan' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        if ($validated['action'] === 'approve_kaprodi') {
            if ($cuti->status !== 'Pending') {
                return redirect()->back()->with('error', 'Status pengajuan tidak valid!');
            }

            $cuti->update([
                'status' => 'Disetujui Kaprodi',
                'disetujui_kaprodi_oleh' => $user->id,
                'tanggal_persetujuan_kaprodi' => now(),
                'catatan_kaprodi' => $validated['catatan'],
            ]);

            return redirect()->route('cuti.index')
                ->with('success', 'Pengajuan cuti disetujui oleh Kaprodi!');
        }

        if ($validated['action'] === 'approve_dekan') {
            if ($cuti->status !== 'Disetujui Kaprodi') {
                return redirect()->back()->with('error', 'Pengajuan harus disetujui Kaprodi terlebih dahulu!');
            }

            $cuti->update([
                'status' => 'Disetujui Dekan',
                'nomor_surat' => CutiAkademik::generateNomorSurat(),
                'disetujui_dekan_oleh' => $user->id,
                'tanggal_persetujuan_dekan' => now(),
                'catatan_dekan' => $validated['catatan'],
            ]);

            // Update status mahasiswa
            $mahasiswa = $cuti->mahasiswa;
            $statusLama = $mahasiswa->status;
            $mahasiswa->update(['status' => 'Cuti']);

            // Catat history
            HistoryStatusMahasiswa::catat(
                $mahasiswa->id,
                $cuti->tahun_akademik_id,
                $statusLama,
                'Cuti',
                'Cuti akademik disetujui: ' . $cuti->alasan,
                $user->id
            );

            return redirect()->route('cuti.index')
                ->with('success', 'Pengajuan cuti disetujui oleh Dekan! Status mahasiswa diubah menjadi Cuti.');
        }

        if ($validated['action'] === 'reject') {
            $cuti->update([
                'status' => 'Ditolak',
                'catatan_kaprodi' => $cuti->status === 'Pending' ? $validated['catatan'] : $cuti->catatan_kaprodi,
                'catatan_dekan' => $cuti->status === 'Disetujui Kaprodi' ? $validated['catatan'] : null,
            ]);

            return redirect()->route('cuti.index')
                ->with('success', 'Pengajuan cuti ditolak!');
        }

        return redirect()->back()->with('error', 'Aksi tidak valid!');
    }

    /**
     * Reject cuti (admin)
     */
    public function reject(Request $request, CutiAkademik $cuti)
    {
        $validated = $request->validate([
            'catatan' => 'required|string|max:500',
        ]);

        if (!in_array($cuti->status, ['Pending', 'Disetujui Kaprodi'])) {
            return redirect()->back()->with('error', 'Status pengajuan tidak valid untuk ditolak!');
        }

        $cuti->update([
            'status' => 'Ditolak',
            'catatan_kaprodi' => $cuti->status === 'Pending' ? $validated['catatan'] : $cuti->catatan_kaprodi,
            'catatan_dekan' => $cuti->status === 'Disetujui Kaprodi' ? $validated['catatan'] : null,
        ]);

        return redirect()->route('cuti.index')
            ->with('success', 'Pengajuan cuti berhasil ditolak!');
    }

    /**
     * Activate cuti (admin) - aktifkan mahasiswa kembali
     */
    public function activate(CutiAkademik $cuti)
    {
        return $this->endCuti($cuti);
    }

    /**
     * Cancel cuti (mahasiswa)
     */
    public function cancel(CutiAkademik $cuti)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if ($cuti->mahasiswa_id !== $mahasiswa->id) {
            return redirect()->back()->with('error', 'Akses ditolak!');
        }

        if ($cuti->status !== 'Pending') {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status pending yang dapat dibatalkan!');
        }

        // Delete dokumen if exists
        if ($cuti->dokumen_pendukung) {
            Storage::disk('public')->delete($cuti->dokumen_pendukung);
        }

        $cuti->delete();

        return redirect()->route('cuti.mahasiswa')
            ->with('success', 'Pengajuan cuti berhasil dibatalkan!');
    }

    /**
     * End cuti (admin) - mahasiswa kembali aktif
     */
    public function endCuti(CutiAkademik $cuti)
    {
        if ($cuti->status !== 'Disetujui Dekan') {
            return redirect()->back()->with('error', 'Hanya cuti yang disetujui yang dapat diakhiri!');
        }

        $user = Auth::user();
        $mahasiswa = $cuti->mahasiswa;
        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();

        // Update status mahasiswa
        $statusLama = $mahasiswa->status;
        $mahasiswa->update(['status' => 'Aktif']);

        // Update cuti
        $cuti->update(['status' => 'Selesai']);

        // Catat history
        HistoryStatusMahasiswa::catat(
            $mahasiswa->id,
            $tahunAkademik->id,
            $statusLama,
            'Aktif',
            'Masa cuti akademik selesai',
            $user->id
        );

        return redirect()->route('cuti.index')
            ->with('success', 'Cuti berhasil diakhiri! Status mahasiswa kembali Aktif.');
    }

    /**
     * Download dokumen pendukung
     */
    public function downloadDokumen(CutiAkademik $cuti)
    {
        if (!$cuti->dokumen_pendukung) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan!');
        }

        return Storage::disk('public')->download($cuti->dokumen_pendukung);
    }

    /**
     * Print surat cuti
     */
    public function printSurat(CutiAkademik $cuti)
    {
        if ($cuti->status !== 'Disetujui Dekan' && $cuti->status !== 'Selesai') {
            return redirect()->back()->with('error', 'Surat hanya dapat dicetak setelah disetujui Dekan!');
        }

        $cuti->load(['mahasiswa.programStudi.fakultas', 'tahunAkademik']);

        return view('akademik.cuti.print-surat', compact('cuti'));
    }

    /**
     * History status mahasiswa
     */
    public function history(Request $request)
    {
        $query = HistoryStatusMahasiswa::with(['mahasiswa', 'tahunAkademik', 'diubahOleh']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $histories = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('akademik.cuti.history', compact('histories'));
    }
}
