<?php

namespace App\Http\Controllers;

use App\Models\BimbinganAkademik;
use App\Models\PersetujuanKrs;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\TahunAkademik;
use App\Models\Krs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BimbinganAkademikController extends Controller
{
    /**
     * Display a listing for admin.
     */
    public function index(Request $request)
    {
        $query = BimbinganAkademik::with(['mahasiswa', 'dosen', 'tahunAkademik']);

        if ($request->filled('dosen')) {
            $query->where('dosen_id', $request->dosen);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $bimbingans = $query->orderBy('tanggal_bimbingan', 'desc')->paginate(15);
        $dosens = Dosen::orderBy('nama')->get();
        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();

        return view('akademik.bimbingan.index', compact('bimbingans', 'dosens', 'tahunAkademik'));
    }

    /**
     * Display bimbingan for dosen
     */
    public function dosenIndex(Request $request)
    {
        $dosen = Auth::user()->dosen;
        
        if (!$dosen) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak terdaftar sebagai dosen!');
        }

        $query = BimbinganAkademik::with(['mahasiswa', 'tahunAkademik'])
            ->where('dosen_id', $dosen->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bimbingans = $query->orderBy('tanggal_bimbingan', 'desc')->paginate(15);
        
        // Ambil daftar mahasiswa perwalian
        $mahasiswaPerwalian = Mahasiswa::where('dosen_wali_id', $dosen->id)
            ->where('status', 'Aktif')
            ->orderBy('nama')
            ->get();

        // Statistik
        $stats = [
            'total' => BimbinganAkademik::where('dosen_id', $dosen->id)->count(),
            'dijadwalkan' => BimbinganAkademik::where('dosen_id', $dosen->id)->where('status', 'Dijadwalkan')->count(),
            'selesai' => BimbinganAkademik::where('dosen_id', $dosen->id)->where('status', 'Selesai')->count(),
            'mahasiswa_perwalian' => $mahasiswaPerwalian->count(),
        ];

        return view('akademik.bimbingan.dosen-index', compact('bimbingans', 'mahasiswaPerwalian', 'stats', 'dosen'));
    }

    /**
     * Display bimbingan for mahasiswa
     */
    public function mahasiswaIndex(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        $bimbingans = BimbinganAkademik::with(['dosen', 'tahunAkademik'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('tanggal_bimbingan', 'desc')
            ->paginate(10);

        $dosenWali = $mahasiswa->dosenWali;

        return view('akademik.bimbingan.mahasiswa-index', compact('bimbingans', 'mahasiswa', 'dosenWali'));
    }

    /**
     * Show form for creating new bimbingan (mahasiswa)
     */
    public function create()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        if (!$mahasiswa->dosenWali) {
            return redirect()->route('bimbingan.mahasiswa')
                ->with('error', 'Anda belum memiliki dosen wali. Silakan hubungi admin!');
        }

        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();

        return view('akademik.bimbingan.create', compact('mahasiswa', 'tahunAkademik'));
    }

    /**
     * Store new bimbingan request
     */
    public function store(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if (!$mahasiswa || !$mahasiswa->dosenWali) {
            return redirect()->route('dashboard')->with('error', 'Data tidak valid!');
        }

        $validated = $request->validate([
            'tanggal_bimbingan' => 'required|date|after_or_equal:today',
            'jenis' => 'required|in:KRS,Akademik,Pribadi,Karir,Lainnya',
            'topik' => 'required|string|max:500',
            'catatan_mahasiswa' => 'nullable|string|max:1000',
        ]);

        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();

        BimbinganAkademik::create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $mahasiswa->dosen_wali_id,
            'tahun_akademik_id' => $tahunAkademik->id,
            'tanggal_bimbingan' => $validated['tanggal_bimbingan'],
            'jenis' => $validated['jenis'],
            'topik' => $validated['topik'],
            'catatan_mahasiswa' => $validated['catatan_mahasiswa'],
            'status' => 'Dijadwalkan',
        ]);

        return redirect()->route('bimbingan.mahasiswa')
            ->with('success', 'Jadwal bimbingan berhasil diajukan!');
    }

    /**
     * Show detail bimbingan
     */
    public function show(BimbinganAkademik $bimbingan)
    {
        $bimbingan->load(['mahasiswa', 'dosen', 'tahunAkademik']);
        
        return view('akademik.bimbingan.show', compact('bimbingan'));
    }

    /**
     * Respond to bimbingan (dosen)
     */
    public function respond(Request $request, BimbinganAkademik $bimbingan)
    {
        $validated = $request->validate([
            'status' => 'required|in:Dijadwalkan,Selesai,Dibatalkan',
            'catatan_dosen' => 'nullable|string|max:1000',
            'rekomendasi' => 'nullable|string|max:1000',
        ]);

        $bimbingan->update($validated);

        return redirect()->route('bimbingan.dosen')
            ->with('success', 'Bimbingan berhasil diperbarui!');
    }

    /**
     * Update bimbingan (dosen)
     */
    public function update(Request $request, BimbinganAkademik $bimbingan)
    {
        $validated = $request->validate([
            'status' => 'required|in:Dijadwalkan,Selesai,Dibatalkan',
            'catatan_dosen' => 'nullable|string|max:1000',
            'rekomendasi' => 'nullable|string|max:1000',
        ]);

        $bimbingan->update($validated);

        return redirect()->back()->with('success', 'Data bimbingan berhasil diperbarui!');
    }

    /**
     * Cancel bimbingan (mahasiswa)
     */
    public function cancel(BimbinganAkademik $bimbingan)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if ($bimbingan->mahasiswa_id !== $mahasiswa->id) {
            return redirect()->back()->with('error', 'Akses ditolak!');
        }

        if ($bimbingan->status !== 'Dijadwalkan') {
            return redirect()->back()->with('error', 'Hanya bimbingan yang dijadwalkan yang dapat dibatalkan!');
        }

        $bimbingan->update(['status' => 'Dibatalkan']);

        return redirect()->route('bimbingan.mahasiswa')
            ->with('success', 'Jadwal bimbingan berhasil dibatalkan!');
    }

    // ==========================================
    // PERSETUJUAN KRS
    // ==========================================

    /**
     * Daftar persetujuan KRS untuk dosen wali
     */
    public function persetujuanKrs(Request $request)
    {
        $dosen = Auth::user()->dosen;
        
        if (!$dosen) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak terdaftar sebagai dosen!');
        }

        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();

        $query = PersetujuanKrs::with(['mahasiswa', 'tahunAkademik'])
            ->where('dosen_id', $dosen->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($tahunAkademik && !$request->filled('all')) {
            $query->where('tahun_akademik_id', $tahunAkademik->id);
        }

        $persetujuans = $query->orderBy('tanggal_pengajuan', 'desc')->paginate(15);

        $stats = [
            'pending' => PersetujuanKrs::where('dosen_id', $dosen->id)->where('status', 'Pending')->count(),
            'disetujui' => PersetujuanKrs::where('dosen_id', $dosen->id)->where('status', 'Disetujui')->count(),
            'ditolak' => PersetujuanKrs::where('dosen_id', $dosen->id)->where('status', 'Ditolak')->count(),
        ];

        return view('akademik.bimbingan.persetujuan-krs', compact('persetujuans', 'stats', 'tahunAkademik', 'dosen'));
    }

    /**
     * Detail KRS mahasiswa untuk persetujuan
     */
    public function detailKrs(Mahasiswa $mahasiswa)
    {
        $dosen = Auth::user()->dosen;
        
        if ($mahasiswa->dosen_wali_id !== $dosen->id) {
            return redirect()->back()->with('error', 'Akses ditolak! Mahasiswa bukan perwalian Anda.');
        }

        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();
        $mahasiswa->load(['programStudi']);
        
        // Ambil KRS mahasiswa
        $krs = Krs::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'jadwalKuliah.ruangan'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $tahunAkademik->id)
            ->get();

        $totalSks = $krs->sum(fn($k) => $k->jadwalKuliah->mataKuliah->sks ?? 0);

        return view('akademik.bimbingan.detail-krs', compact('mahasiswa', 'krs', 'totalSks', 'tahunAkademik'));
    }

    /**
     * Approve KRS
     */
    public function approveKrs(Krs $krs)
    {
        $dosen = Auth::user()->dosen;
        $mahasiswa = $krs->mahasiswa;
        
        if ($mahasiswa->dosen_wali_id !== $dosen->id) {
            return redirect()->back()->with('error', 'Akses ditolak!');
        }

        $krs->update([
            'status' => 'Disetujui',
            'tanggal_persetujuan' => now(),
        ]);

        return redirect()->back()->with('success', 'KRS berhasil disetujui!');
    }

    /**
     * Reject KRS
     */
    public function rejectKrs(Request $request, Krs $krs)
    {
        $dosen = Auth::user()->dosen;
        $mahasiswa = $krs->mahasiswa;
        
        if ($mahasiswa->dosen_wali_id !== $dosen->id) {
            return redirect()->back()->with('error', 'Akses ditolak!');
        }

        $krs->update([
            'status' => 'Ditolak',
        ]);

        return redirect()->back()->with('success', 'KRS berhasil ditolak!');
    }

    /**
     * Form buat jadwal bimbingan (dosen)
     */
    public function buatJadwalForm(Mahasiswa $mahasiswa)
    {
        $dosen = Auth::user()->dosen;
        
        if ($mahasiswa->dosen_wali_id !== $dosen->id) {
            return redirect()->back()->with('error', 'Mahasiswa bukan perwalian Anda!');
        }

        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();
        
        $historyBimbingan = BimbinganAkademik::where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('tanggal_bimbingan', 'desc')
            ->take(5)
            ->get();

        return view('akademik.bimbingan.buat-jadwal', compact('mahasiswa', 'dosen', 'tahunAkademik', 'historyBimbingan'));
    }

    /**
     * Store jadwal bimbingan (dosen)
     */
    public function buatJadwal(Request $request, Mahasiswa $mahasiswa)
    {
        $dosen = Auth::user()->dosen;
        
        if ($mahasiswa->dosen_wali_id !== $dosen->id) {
            return redirect()->back()->with('error', 'Mahasiswa bukan perwalian Anda!');
        }

        $validated = $request->validate([
            'tanggal_bimbingan' => 'required|date',
            'jenis' => 'required|in:KRS,Akademik,Pribadi,Karir,Lainnya',
            'topik' => 'required|string|max:500',
            'catatan_dosen' => 'nullable|string|max:1000',
        ]);

        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();

        BimbinganAkademik::create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'tahun_akademik_id' => $tahunAkademik->id,
            'tanggal_bimbingan' => $validated['tanggal_bimbingan'],
            'jenis' => $validated['jenis'],
            'topik' => $validated['topik'],
            'catatan_dosen' => $validated['catatan_dosen'],
            'status' => 'Dijadwalkan',
        ]);

        return redirect()->route('bimbingan.dosen')
            ->with('success', 'Jadwal bimbingan berhasil dibuat!');
    }

    /**
     * Proses persetujuan KRS
     */
    public function prosesPersetujuan(Request $request, PersetujuanKrs $persetujuan)
    {
        $dosen = Auth::user()->dosen;
        
        if ($persetujuan->dosen_id !== $dosen->id) {
            return response()->json(['error' => 'Akses ditolak!'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:Disetujui,Ditolak,Revisi',
            'catatan_dosen' => 'nullable|string|max:500',
        ]);

        $persetujuan->update([
            'status' => $validated['status'],
            'catatan_dosen' => $validated['catatan_dosen'],
            'tanggal_persetujuan' => now(),
        ]);

        // Update status KRS mahasiswa jika disetujui
        if ($validated['status'] === 'Disetujui') {
            Krs::where('mahasiswa_id', $persetujuan->mahasiswa_id)
                ->where('tahun_akademik_id', $persetujuan->tahun_akademik_id)
                ->where('status', 'Pending')
                ->update(['status' => 'Disetujui', 'tanggal_persetujuan' => now()]);
        } elseif ($validated['status'] === 'Ditolak') {
            Krs::where('mahasiswa_id', $persetujuan->mahasiswa_id)
                ->where('tahun_akademik_id', $persetujuan->tahun_akademik_id)
                ->where('status', 'Pending')
                ->update(['status' => 'Ditolak']);
        }

        return response()->json(['success' => true, 'message' => 'KRS berhasil diproses!']);
    }

    /**
     * Create bimbingan by dosen
     */
    public function dosenCreate(Request $request)
    {
        $dosen = Auth::user()->dosen;
        
        if (!$dosen) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak terdaftar sebagai dosen!');
        }

        // Ambil mahasiswa perwalian
        $mahasiswaPerwalian = Mahasiswa::where('dosen_wali_id', $dosen->id)
            ->where('status', 'Aktif')
            ->orderBy('nama')
            ->get();

        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();

        return view('akademik.bimbingan.dosen-create', compact('mahasiswaPerwalian', 'tahunAkademik', 'dosen'));
    }

    /**
     * Store bimbingan by dosen
     */
    public function dosenStore(Request $request)
    {
        $dosen = Auth::user()->dosen;
        
        if (!$dosen) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak terdaftar sebagai dosen!');
        }

        $validated = $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'tanggal_bimbingan' => 'required|date',
            'jenis' => 'required|in:KRS,Akademik,Pribadi,Karir,Lainnya',
            'topik' => 'required|string|max:500',
            'catatan_dosen' => 'nullable|string|max:1000',
            'rekomendasi' => 'nullable|string|max:1000',
            'status' => 'required|in:Dijadwalkan,Selesai',
        ]);

        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();

        BimbinganAkademik::create([
            'mahasiswa_id' => $validated['mahasiswa_id'],
            'dosen_id' => $dosen->id,
            'tahun_akademik_id' => $tahunAkademik->id,
            'tanggal_bimbingan' => $validated['tanggal_bimbingan'],
            'jenis' => $validated['jenis'],
            'topik' => $validated['topik'],
            'catatan_dosen' => $validated['catatan_dosen'],
            'rekomendasi' => $validated['rekomendasi'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('bimbingan.dosen')
            ->with('success', 'Data bimbingan berhasil disimpan!');
    }
}
