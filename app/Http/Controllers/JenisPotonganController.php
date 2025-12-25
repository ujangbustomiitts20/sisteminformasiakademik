<?php

namespace App\Http\Controllers;

use App\Models\JenisPotongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JenisPotonganController extends Controller
{
    public function index(Request $request)
    {
        $query = JenisPotongan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $jenisPotongan = $query->orderBy('prioritas')
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => JenisPotongan::count(),
            'aktif' => JenisPotongan::where('is_active', true)->count(),
            'diskon' => JenisPotongan::where('kategori', 'diskon')->count(),
            'potongan_khusus' => JenisPotongan::where('kategori', 'potongan_khusus')->count(),
            'promo' => JenisPotongan::where('kategori', 'promo')->count(),
            'keringanan' => JenisPotongan::where('kategori', 'keringanan')->count(),
        ];

        return view('keuangan.potongan.jenis.index', compact('jenisPotongan', 'stats'));
    }

    public function create()
    {
        return view('keuangan.potongan.jenis.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kategori' => 'required|in:diskon,potongan_khusus,promo,keringanan',
            'deskripsi' => 'nullable|string',
            'tipe_nilai' => 'required|in:persen,nominal',
            'nilai_default' => 'required|numeric|min:0',
            'nilai_max' => 'nullable|numeric|min:0',
            'is_stackable' => 'boolean',
            'prioritas' => 'integer|min:0',
        ]);

        $validated['is_active'] = true;
        $validated['is_stackable'] = $request->boolean('is_stackable');

        JenisPotongan::create($validated);

        return redirect()->route('jenis-potongan.index')
            ->with('success', 'Jenis potongan berhasil ditambahkan.');
    }

    public function show(JenisPotongan $jenisPotongan)
    {
        $jenisPotongan->load(['periodeDiskon' => function ($q) {
            $q->latest()->limit(10);
        }, 'potonganMahasiswa' => function ($q) {
            $q->with('mahasiswa')->latest()->limit(10);
        }]);

        return view('keuangan.potongan.jenis.show', compact('jenisPotongan'));
    }

    public function edit(JenisPotongan $jenisPotongan)
    {
        return view('keuangan.potongan.jenis.edit', compact('jenisPotongan'));
    }

    public function update(Request $request, JenisPotongan $jenisPotongan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kategori' => 'required|in:diskon,potongan_khusus,promo,keringanan',
            'deskripsi' => 'nullable|string',
            'tipe_nilai' => 'required|in:persen,nominal',
            'nilai_default' => 'required|numeric|min:0',
            'nilai_max' => 'nullable|numeric|min:0',
            'is_stackable' => 'boolean',
            'prioritas' => 'integer|min:0',
        ]);

        $validated['is_stackable'] = $request->boolean('is_stackable');

        $jenisPotongan->update($validated);

        return redirect()->route('jenis-potongan.index')
            ->with('success', 'Jenis potongan berhasil diperbarui.');
    }

    public function destroy(JenisPotongan $jenisPotongan)
    {
        // Check if has relations
        if ($jenisPotongan->periodeDiskon()->exists() || $jenisPotongan->potonganMahasiswa()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus jenis potongan yang sudah digunakan.');
        }

        $jenisPotongan->delete();

        return redirect()->route('jenis-potongan.index')
            ->with('success', 'Jenis potongan berhasil dihapus.');
    }

    public function toggleStatus(JenisPotongan $jenisPotongan)
    {
        $jenisPotongan->update([
            'is_active' => !$jenisPotongan->is_active
        ]);

        $status = $jenisPotongan->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Jenis potongan berhasil {$status}.");
    }
}
