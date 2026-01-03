<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SekolahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sekolah::with(['provinsi', 'kabupaten', 'kecamatan'])
            ->withCount('mahasiswa');

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('npsn', 'like', "%{$search}%");
            });
        }

        // Filter jenjang
        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter provinsi
        if ($request->filled('provinsi_id')) {
            $query->where('provinsi_id', $request->provinsi_id);
        }

        // Filter kabupaten
        if ($request->filled('kabupaten_id')) {
            $query->where('kabupaten_id', $request->kabupaten_id);
        }

        // Filter aktif
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $sekolah = $query->orderBy('nama')->paginate(20)->withQueryString();
        $provinsi = Provinsi::orderBy('nama')->get();

        // Statistik
        $stats = [
            'total' => Sekolah::count(),
            'sma' => Sekolah::where('jenjang', 'SMA')->count(),
            'smk' => Sekolah::where('jenjang', 'SMK')->count(),
            'ma' => Sekolah::where('jenjang', 'MA')->count(),
            'mak' => Sekolah::where('jenjang', 'MAK')->count(),
            'negeri' => Sekolah::where('status', 'Negeri')->count(),
            'swasta' => Sekolah::where('status', 'Swasta')->count(),
        ];

        return view('admin.sekolah.index', compact('sekolah', 'provinsi', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $provinsi = Provinsi::orderBy('nama')->get();
        return view('admin.sekolah.create', compact('provinsi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'npsn' => 'nullable|string|max:20|unique:sekolah,npsn',
            'nama' => 'required|string|max:255',
            'jenjang' => 'required|in:SMA,SMK,MA,MAK',
            'status' => 'required|in:Negeri,Swasta',
            'provinsi_id' => 'required|exists:provinsi,id',
            'kabupaten_id' => 'required|exists:kabupaten,id',
            'kecamatan_id' => 'nullable|exists:kecamatan,id',
            'alamat' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        Sekolah::create([
            'npsn' => $request->npsn,
            'nama' => $request->nama,
            'jenjang' => $request->jenjang,
            'status' => $request->status,
            'provinsi_id' => $request->provinsi_id,
            'kabupaten_id' => $request->kabupaten_id,
            'kecamatan_id' => $request->kecamatan_id,
            'alamat' => $request->alamat,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('sekolah.index')
            ->with('success', 'Data sekolah berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sekolah $sekolah)
    {
        $sekolah->load(['provinsi', 'kabupaten', 'kecamatan', 'mahasiswa' => function ($q) {
            $q->latest()->take(10);
        }]);
        
        return view('admin.sekolah.show', compact('sekolah'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sekolah $sekolah)
    {
        $sekolah->load(['provinsi', 'kabupaten', 'kecamatan']);
        $provinsi = Provinsi::orderBy('nama')->get();
        return view('admin.sekolah.edit', compact('sekolah', 'provinsi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sekolah $sekolah)
    {
        $request->validate([
            'npsn' => 'nullable|string|max:20|unique:sekolah,npsn,' . $sekolah->id,
            'nama' => 'required|string|max:255',
            'jenjang' => 'required|in:SMA,SMK,MA,MAK',
            'status' => 'required|in:Negeri,Swasta',
            'provinsi_id' => 'required|exists:provinsi,id',
            'kabupaten_id' => 'required|exists:kabupaten,id',
            'kecamatan_id' => 'nullable|exists:kecamatan,id',
            'alamat' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $sekolah->update([
            'npsn' => $request->npsn,
            'nama' => $request->nama,
            'jenjang' => $request->jenjang,
            'status' => $request->status,
            'provinsi_id' => $request->provinsi_id,
            'kabupaten_id' => $request->kabupaten_id,
            'kecamatan_id' => $request->kecamatan_id,
            'alamat' => $request->alamat,
            'is_active' => $request->has('is_active') ? $request->is_active : $sekolah->is_active,
        ]);

        return redirect()->route('sekolah.index')
            ->with('success', 'Data sekolah berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sekolah $sekolah)
    {
        // Cek apakah ada mahasiswa yang terhubung
        if ($sekolah->mahasiswa()->exists()) {
            return redirect()->route('sekolah.index')
                ->with('error', 'Sekolah tidak dapat dihapus karena masih digunakan oleh data mahasiswa.');
        }

        $sekolah->delete();

        return redirect()->route('sekolah.index')
            ->with('success', 'Data sekolah berhasil dihapus.');
    }

    /**
     * Toggle status aktif sekolah
     */
    public function toggleActive(Sekolah $sekolah)
    {
        $sekolah->update(['is_active' => !$sekolah->is_active]);

        return redirect()->back()
            ->with('success', 'Status sekolah berhasil diubah.');
    }

    /**
     * Import sekolah dari file (untuk bulk import)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx|max:5120',
        ]);

        // TODO: Implement import logic
        return redirect()->route('sekolah.index')
            ->with('info', 'Fitur import sedang dalam pengembangan.');
    }
}
