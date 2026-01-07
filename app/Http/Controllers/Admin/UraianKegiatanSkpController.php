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

        // Filter tipe pegawai
        if ($request->filled('tipe_pegawai')) {
            $query->where('tipe_pegawai', $request->tipe_pegawai);
        }

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

        $items = $query->orderBy('tipe_pegawai')
            ->orderBy('kategori')
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
            'tendik' => UraianKegiatanSkp::where('tipe_pegawai', 'tendik')->count(),
        ];

        return view('admin.uraian-kegiatan-skp.index', compact('items', 'stats'));
    }

    /**
     * Store new record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipe_pegawai' => ['required', Rule::in(array_keys(UraianKegiatanSkp::TIPE_PEGAWAI))],
            'kategori' => ['required', Rule::in(array_keys(UraianKegiatanSkp::KATEGORI))],
            'sub_kategori' => ['nullable'],
            'uraian_kegiatan' => 'required|string|max:1000',
            'satuan' => 'nullable|string|max:50',
            'target_default' => 'nullable|numeric|min:0',
            'bobot' => 'nullable|numeric|min:0|max:100',
            'keterangan' => 'nullable|string',
            'urutan' => 'nullable|integer|min:0',
        ], [
            'tipe_pegawai.required' => 'Tipe pegawai wajib dipilih',
            'kategori.required' => 'Kategori wajib dipilih',
            'uraian_kegiatan.required' => 'Uraian kegiatan wajib diisi',
        ]);

        // Clean up sub_kategori if empty
        if (empty($validated['sub_kategori'])) {
            $validated['sub_kategori'] = null;
        }

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
            'tipe_pegawai' => ['required', Rule::in(array_keys(UraianKegiatanSkp::TIPE_PEGAWAI))],
            'kategori' => ['required', Rule::in(array_keys(UraianKegiatanSkp::KATEGORI))],
            'sub_kategori' => ['nullable'],
            'uraian_kegiatan' => 'required|string|max:1000',
            'satuan' => 'nullable|string|max:50',
            'target_default' => 'nullable|numeric|min:0',
            'bobot' => 'nullable|numeric|min:0|max:100',
            'keterangan' => 'nullable|string',
            'urutan' => 'nullable|integer|min:0',
        ]);

        // Clean up sub_kategori if empty
        if (empty($validated['sub_kategori'])) {
            $validated['sub_kategori'] = null;
        }

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
     * API: Get uraian kegiatan for dropdown (used by dosen/pegawai SKP)
     */
    public function getList(Request $request)
    {
        $query = UraianKegiatanSkp::active();

        // Filter by tipe_pegawai (dosen, tendik, or semua)
        if ($request->filled('tipe_pegawai')) {
            $tipe = $request->tipe_pegawai;
            $query->where(function ($q) use ($tipe) {
                $q->where('tipe_pegawai', $tipe)
                  ->orWhere('tipe_pegawai', 'semua');
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $items = $query->orderBy('tipe_pegawai')
            ->orderBy('kategori')
            ->orderBy('sub_kategori')
            ->orderBy('urutan')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'hashid' => $item->hashid,
                    'kode' => $item->kode,
                    'tipe_pegawai' => $item->tipe_pegawai,
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
