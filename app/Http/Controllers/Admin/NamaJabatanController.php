<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NamaJabatan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NamaJabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = NamaJabatan::query();

        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('aktif', $request->status == 'aktif');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhere('nama_singkat', 'like', "%{$search}%");
            });
        }

        $jabatans = $query->orderBy('level')->orderBy('urutan')->orderBy('nama')->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total' => NamaJabatan::count(),
            'aktif' => NamaJabatan::where('aktif', true)->count(),
            'pimpinan' => NamaJabatan::where('kategori', 'pimpinan')->where('aktif', true)->count(),
            'akademik' => NamaJabatan::where('kategori', 'akademik')->where('aktif', true)->count(),
            'keuangan' => NamaJabatan::where('kategori', 'keuangan')->where('aktif', true)->count(),
        ];

        $kategoris = NamaJabatan::KATEGORI;
        $levels = NamaJabatan::LEVEL;

        return view('admin.nama-jabatan.index', compact('jabatans', 'stats', 'kategoris', 'levels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:50', 'unique:nama_jabatan,kode'],
            'nama' => ['required', 'string', 'max:255'],
            'nama_singkat' => ['nullable', 'string', 'max:100'],
            'kategori' => ['required', 'string', Rule::in(array_keys(NamaJabatan::KATEGORI))],
            'level' => ['nullable', 'integer', 'min:0', 'max:10'],
            'deskripsi' => ['nullable', 'string'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'aktif' => ['boolean'],
        ]);

        $validated['aktif'] = $request->has('aktif');
        $validated['urutan'] = $validated['urutan'] ?? 0;
        $validated['level'] = $validated['level'] ?? 0;

        NamaJabatan::create($validated);

        return redirect()->route('admin.nama-jabatan.index')
            ->with('success', 'Nama jabatan berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NamaJabatan $namaJabatan)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:50', Rule::unique('nama_jabatan', 'kode')->ignore($namaJabatan->id)],
            'nama' => ['required', 'string', 'max:255'],
            'nama_singkat' => ['nullable', 'string', 'max:100'],
            'kategori' => ['required', 'string', Rule::in(array_keys(NamaJabatan::KATEGORI))],
            'level' => ['nullable', 'integer', 'min:0', 'max:10'],
            'deskripsi' => ['nullable', 'string'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'aktif' => ['boolean'],
        ]);

        $validated['aktif'] = $request->has('aktif');
        $validated['urutan'] = $validated['urutan'] ?? 0;
        $validated['level'] = $validated['level'] ?? 0;

        $namaJabatan->update($validated);

        return redirect()->route('admin.nama-jabatan.index')
            ->with('success', 'Nama jabatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NamaJabatan $namaJabatan)
    {
        // Check if jabatan is used by any pejabat
        if ($namaJabatan->pejabatPenandatangan()->exists()) {
            return redirect()->route('admin.nama-jabatan.index')
                ->with('error', 'Jabatan tidak dapat dihapus karena masih digunakan oleh pejabat penandatangan.');
        }

        $namaJabatan->delete();

        return redirect()->route('admin.nama-jabatan.index')
            ->with('success', 'Nama jabatan berhasil dihapus.');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(NamaJabatan $namaJabatan)
    {
        $namaJabatan->update(['aktif' => !$namaJabatan->aktif]);

        return redirect()->route('admin.nama-jabatan.index')
            ->with('success', 'Status jabatan berhasil diubah.');
    }

    /**
     * Get jabatan by kategori (for AJAX)
     */
    public function getByKategori(Request $request)
    {
        $kategori = $request->get('kategori');
        
        $query = NamaJabatan::aktif()->orderBy('level')->orderBy('urutan');
        
        if ($kategori) {
            $query->where('kategori', $kategori);
        }
        
        $jabatans = $query->get(['id', 'kode', 'nama', 'nama_singkat', 'kategori']);
        
        return response()->json($jabatans);
    }
}
