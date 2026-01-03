<?php

namespace App\Http\Controllers;

use App\Models\PeriodeWisuda;
use App\Models\PendaftaranWisuda;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WisudaController extends Controller
{
    /**
     * Display list of periode wisuda
     */
    public function index(Request $request)
    {
        $query = PeriodeWisuda::with('tahunAkademik')->withCount('pendaftaran');

        if ($request->filled('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $periodeWisuda = $query->orderBy('tanggal_wisuda', 'desc')->paginate(10);
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();

        $stats = [
            'total_periode' => PeriodeWisuda::count(),
            'periode_aktif' => PeriodeWisuda::active()->count(),
            'total_pendaftar' => PendaftaranWisuda::count(),
            'lulus_wisuda' => PendaftaranWisuda::where('status', 'Lulus')->count(),
        ];

        return view('akademik.wisuda.index', compact('periodeWisuda', 'tahunAkademik', 'stats'));
    }

    /**
     * Show form to create new periode wisuda
     */
    public function create()
    {
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();

        return view('akademik.wisuda.create', compact('tahunAkademik'));
    }

    /**
     * Store new periode wisuda
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'nama' => 'required|string|max:255',
            'tanggal_wisuda' => 'required|date',
            'tanggal_buka_pendaftaran' => 'required|date|before:tanggal_wisuda',
            'tanggal_tutup_pendaftaran' => 'required|date|after:tanggal_buka_pendaftaran|before:tanggal_wisuda',
            'tanggal_yudisium' => 'nullable|date|after:tanggal_tutup_pendaftaran|before:tanggal_wisuda',
            'lokasi' => 'nullable|string|max:255',
            'kuota' => 'nullable|integer|min:1',
            'biaya_wisuda' => 'required|numeric|min:0',
            'persyaratan' => 'nullable|string',
        ]);

        $validated['status'] = 'Draft';

        PeriodeWisuda::create($validated);

        return redirect()->route('wisuda.index')
            ->with('success', 'Periode wisuda berhasil dibuat.');
    }

    /**
     * Show periode wisuda detail with pendaftaran list
     */
    public function show(PeriodeWisuda $wisuda)
    {
        $wisuda->load('tahunAkademik');
        
        $pendaftaran = PendaftaranWisuda::with(['mahasiswa.programStudi', 'verifikator'])
            ->where('periode_wisuda_id', $wisuda->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => $wisuda->pendaftaran()->count(),
            'pending' => $wisuda->pendaftaran()->where('status', 'Pending')->count(),
            'verifikasi' => $wisuda->pendaftaran()->where('status', 'Verifikasi Berkas')->count(),
            'lolos' => $wisuda->pendaftaran()->where('status', 'Lolos Yudisium')->count(),
            'lulus' => $wisuda->pendaftaran()->where('status', 'Lulus')->count(),
        ];

        return view('akademik.wisuda.show', compact('wisuda', 'pendaftaran', 'stats'));
    }

    /**
     * Show edit form
     */
    public function edit(PeriodeWisuda $wisuda)
    {
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();

        return view('akademik.wisuda.edit', compact('wisuda', 'tahunAkademik'));
    }

    /**
     * Update periode wisuda
     */
    public function update(Request $request, PeriodeWisuda $wisuda)
    {
        $validated = $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'nama' => 'required|string|max:255',
            'tanggal_wisuda' => 'required|date',
            'tanggal_buka_pendaftaran' => 'required|date|before:tanggal_wisuda',
            'tanggal_tutup_pendaftaran' => 'required|date|after:tanggal_buka_pendaftaran|before:tanggal_wisuda',
            'tanggal_yudisium' => 'nullable|date|after:tanggal_tutup_pendaftaran|before:tanggal_wisuda',
            'lokasi' => 'nullable|string|max:255',
            'kuota' => 'nullable|integer|min:1',
            'biaya_wisuda' => 'required|numeric|min:0',
            'persyaratan' => 'nullable|string',
        ]);

        $wisuda->update($validated);

        return redirect()->route('wisuda.show', $wisuda)
            ->with('success', 'Periode wisuda berhasil diperbarui.');
    }

    /**
     * Delete periode wisuda
     */
    public function destroy(PeriodeWisuda $wisuda)
    {
        if ($wisuda->pendaftaran()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus periode yang sudah memiliki pendaftar.');
        }

        $wisuda->delete();

        return redirect()->route('wisuda.index')
            ->with('success', 'Periode wisuda berhasil dihapus.');
    }

    /**
     * Toggle status periode
     */
    public function toggleStatus(Request $request, PeriodeWisuda $wisuda)
    {
        $request->validate([
            'status' => 'required|in:Draft,Dibuka,Ditutup,Selesai',
        ]);

        $wisuda->update(['status' => $request->status]);

        return back()->with('success', 'Status periode wisuda berhasil diubah.');
    }

    /**
     * Show pendaftaran detail
     */
    public function showPendaftaran(PendaftaranWisuda $pendaftaran)
    {
        $pendaftaran->load(['mahasiswa.programStudi', 'periodeWisuda', 'verifikator', 'yudisium']);

        return view('akademik.wisuda.pendaftaran_show', compact('pendaftaran'));
    }

    /**
     * Verify pendaftaran
     */
    public function verifyPendaftaran(Request $request, PendaftaranWisuda $pendaftaran)
    {
        $request->validate([
            'status' => 'required|in:Verifikasi Berkas,Lolos Yudisium,Ditolak',
            'catatan_verifikasi' => 'nullable|string|max:500',
        ]);

        $pendaftaran->update([
            'status' => $request->status,
            'catatan_verifikasi' => $request->catatan_verifikasi,
            'diverifikasi_oleh' => Auth::id(),
            'tanggal_verifikasi' => now(),
        ]);

        return back()->with('success', 'Status pendaftaran berhasil diperbarui.');
    }

    /**
     * Create manual pendaftaran (by admin)
     */
    public function createPendaftaran(PeriodeWisuda $wisuda)
    {
        $mahasiswa = Mahasiswa::where('status', 'Aktif')
            ->whereDoesntHave('pendaftaranWisuda', function ($query) use ($wisuda) {
                $query->where('periode_wisuda_id', $wisuda->id);
            })
            ->with('programStudi')
            ->orderBy('nim')
            ->get();

        return view('akademik.wisuda.pendaftaran_create', compact('wisuda', 'mahasiswa'));
    }

    /**
     * Store manual pendaftaran
     */
    public function storePendaftaran(Request $request, PeriodeWisuda $wisuda)
    {
        $validated = $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'ipk' => 'required|numeric|min:0|max:4',
            'total_sks' => 'required|integer|min:1',
            'judul_skripsi' => 'nullable|string|max:500',
            'tanggal_lulus_sidang' => 'nullable|date',
        ]);

        // Check if already registered
        $exists = PendaftaranWisuda::where('mahasiswa_id', $validated['mahasiswa_id'])
            ->where('periode_wisuda_id', $wisuda->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Mahasiswa sudah terdaftar di periode ini.');
        }

        $validated['periode_wisuda_id'] = $wisuda->id;
        $validated['status'] = 'Pending';

        PendaftaranWisuda::create($validated);

        return redirect()->route('wisuda.show', $wisuda)
            ->with('success', 'Pendaftaran wisuda berhasil ditambahkan.');
    }

    /**
     * Search mahasiswa for registration
     */
    public function searchMahasiswa(Request $request)
    {
        $query = $request->get('q');
        
        $mahasiswa = Mahasiswa::where('status', 'Aktif')
            ->where(function ($q) use ($query) {
                $q->where('nim', 'like', "%{$query}%")
                  ->orWhere('nama', 'like', "%{$query}%");
            })
            ->with('programStudi')
            ->limit(10)
            ->get();

        return response()->json($mahasiswa);
    }
}
