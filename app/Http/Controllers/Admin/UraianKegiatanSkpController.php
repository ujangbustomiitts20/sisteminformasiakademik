<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UraianKegiatanSkp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UraianKegiatanSkpController extends Controller
{
    /**
     * Display listing
     */
    public function index(Request $request)
    {
        $query = UraianKegiatanSkp::query();

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter sub kategori
        if ($request->filled('sub_kategori')) {
            $query->where('sub_kategori', $request->sub_kategori);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('uraian_kegiatan', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('kategori')
            ->orderBy('sub_kategori')
            ->orderBy('urutan')
            ->paginate(20)
            ->withQueryString();

        // Stats
        $stats = [
            'total' => UraianKegiatanSkp::count(),
            'aktif' => UraianKegiatanSkp::where('is_active', true)->count(),
            'tri_dharma' => UraianKegiatanSkp::where('kategori', 'tri_dharma')->count(),
            'penunjang' => UraianKegiatanSkp::where('kategori', 'penunjang')->count(),
        ];

        return view('admin.uraian-kegiatan-skp.index', compact('items', 'stats'));
    }

    /**
     * Store new record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => ['required', Rule::in(array_keys(UraianKegiatanSkp::KATEGORI))],
            'sub_kategori' => ['nullable', Rule::in(array_keys(UraianKegiatanSkp::SUB_KATEGORI))],
            'uraian_kegiatan' => 'required|string|max:1000',
            'satuan' => 'nullable|string|max:50',
            'target_default' => 'nullable|numeric|min:0',
            'bobot' => 'nullable|numeric|min:0|max:100',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
            'urutan' => 'nullable|integer|min:0',
        ], [
            'kategori.required' => 'Kategori wajib dipilih',
            'uraian_kegiatan.required' => 'Uraian kegiatan wajib diisi',
        ]);

        // Generate kode
        $validated['kode'] = UraianKegiatanSkp::generateKode($validated['kategori']);
        $validated['is_active'] = $request->has('is_active');

        UraianKegiatanSkp::create($validated);

        return redirect()->back()->with('success', 'Uraian kegiatan berhasil ditambahkan!');
    }

    /**
     * Update record
     */
    public function update(Request $request, UraianKegiatanSkp $uraianKegiatanSkp)
    {
        $validated = $request->validate([
            'kategori' => ['required', Rule::in(array_keys(UraianKegiatanSkp::KATEGORI))],
            'sub_kategori' => ['nullable', Rule::in(array_keys(UraianKegiatanSkp::SUB_KATEGORI))],
            'uraian_kegiatan' => 'required|string|max:1000',
            'satuan' => 'nullable|string|max:50',
            'target_default' => 'nullable|numeric|min:0',
            'bobot' => 'nullable|numeric|min:0|max:100',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
            'urutan' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $uraianKegiatanSkp->update($validated);

        return redirect()->back()->with('success', 'Uraian kegiatan berhasil diperbarui!');
    }

    /**
     * Delete record
     */
    public function destroy(UraianKegiatanSkp $uraianKegiatanSkp)
    {
        $uraianKegiatanSkp->delete();

        return redirect()->back()->with('success', 'Uraian kegiatan berhasil dihapus!');
    }

    /**
     * Toggle status
     */
    public function toggleStatus(UraianKegiatanSkp $uraianKegiatanSkp)
    {
        $uraianKegiatanSkp->update(['is_active' => !$uraianKegiatanSkp->is_active]);

        $status = $uraianKegiatanSkp->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Uraian kegiatan berhasil {$status}!");
    }

    /**
     * API: Get uraian kegiatan for dropdown (used by dosen SKP)
     */
    public function getList(Request $request)
    {
        $query = UraianKegiatanSkp::active();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $items = $query->orderBy('kategori')
            ->orderBy('sub_kategori')
            ->orderBy('urutan')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'hashid' => $item->hashid,
                    'kode' => $item->kode,
                    'kategori' => $item->kategori,
                    'kategori_label' => $item->kategori_label,
                    'sub_kategori' => $item->sub_kategori,
                    'sub_kategori_label' => $item->sub_kategori_label,
                    'uraian_kegiatan' => $item->uraian_kegiatan,
                    'satuan' => $item->satuan,
                    'target_default' => $item->target_default,
                    'bobot' => $item->bobot,
                ];
            });

        return response()->json($items);
    }
}
