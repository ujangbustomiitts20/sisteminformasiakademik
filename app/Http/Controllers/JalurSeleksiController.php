<?php

namespace App\Http\Controllers;

use App\Models\JalurSeleksi;
use Illuminate\Http\Request;

class JalurSeleksiController extends Controller
{
    public function index()
    {
        $jalurs = JalurSeleksi::withCount('calonMahasiswa')
            ->orderBy('kode')
            ->paginate(10);

        return view('pmb.jalur-seleksi.index', compact('jalurs'));
    }

    public function create()
    {
        return view('pmb.jalur-seleksi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:jalur_seleksi,kode',
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'persyaratan' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        JalurSeleksi::create($validated);

        return redirect()->route('pmb.jalur-seleksi.index')
            ->with('success', 'Jalur Seleksi berhasil ditambahkan.');
    }

    public function show(string $hashid)
    {
        $jalur = JalurSeleksi::findByHashidOrFail($hashid);
        $jalur->loadCount('calonMahasiswa');

        return view('pmb.jalur-seleksi.show', compact('jalur'));
    }

    public function edit(string $hashid)
    {
        $jalur = JalurSeleksi::findByHashidOrFail($hashid);

        return view('pmb.jalur-seleksi.edit', compact('jalur'));
    }

    public function update(Request $request, string $hashid)
    {
        $jalur = JalurSeleksi::findByHashidOrFail($hashid);

        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:jalur_seleksi,kode,' . $jalur->id,
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'persyaratan' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $jalur->update($validated);

        return redirect()->route('pmb.jalur-seleksi.index')
            ->with('success', 'Jalur Seleksi berhasil diperbarui.');
    }

    public function destroy(string $hashid)
    {
        $jalur = JalurSeleksi::findByHashidOrFail($hashid);

        if ($jalur->calonMahasiswa()->exists()) {
            return back()->with('error', 'Jalur Seleksi tidak dapat dihapus karena memiliki data pendaftar.');
        }

        $jalur->delete();

        return redirect()->route('pmb.jalur-seleksi.index')
            ->with('success', 'Jalur Seleksi berhasil dihapus.');
    }

    public function toggleActive(string $hashid)
    {
        $jalur = JalurSeleksi::findByHashidOrFail($hashid);
        $jalur->update(['is_active' => !$jalur->is_active]);

        $status = $jalur->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Jalur Seleksi berhasil {$status}.");
    }
}
