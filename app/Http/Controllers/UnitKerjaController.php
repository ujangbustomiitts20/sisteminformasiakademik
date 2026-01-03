<?php

namespace App\Http\Controllers;

use App\Models\UnitKerja;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class UnitKerjaController extends Controller
{
    public function index(Request $request)
    {
        $query = UnitKerja::withCount('pegawai');

        if ($request->filled('search')) {
            $query->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('kode', 'like', "%{$request->search}%");
        }

        $unitKerja = $query->orderBy('nama')->paginate(15);

        return view('kepegawaian.unit-kerja.index', compact('unitKerja'));
    }

    public function create()
    {
        $parents = UnitKerja::where('is_active', true)->orderBy('nama')->get();
        $pegawai = Pegawai::where('status', 'Aktif')->orderBy('nama')->get();
        
        return view('kepegawaian.unit-kerja.create', compact('parents', 'pegawai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:unit_kerja,kode|max:20',
            'nama' => 'required|max:255',
            'parent_id' => 'nullable|exists:unit_kerja,id',
            'kepala_id' => 'nullable|exists:pegawai,id',
            'deskripsi' => 'nullable|max:500',
            'is_active' => 'boolean',
        ]);

        UnitKerja::create($request->all());

        return redirect()->route('kepegawaian.unit-kerja.index')
            ->with('success', 'Unit kerja berhasil ditambahkan!');
    }

    public function show(UnitKerja $unitKerja)
    {
        $unitKerja->load(['parent', 'kepala', 'children', 'pegawai']);
        
        return view('kepegawaian.unit-kerja.show', compact('unitKerja'));
    }

    public function edit(UnitKerja $unitKerja)
    {
        $parents = UnitKerja::where('is_active', true)
            ->where('id', '!=', $unitKerja->id)
            ->orderBy('nama')
            ->get();
        $pegawai = Pegawai::where('status', 'Aktif')->orderBy('nama')->get();
        
        return view('kepegawaian.unit-kerja.edit', compact('unitKerja', 'parents', 'pegawai'));
    }

    public function update(Request $request, UnitKerja $unitKerja)
    {
        $request->validate([
            'kode' => 'required|max:20|unique:unit_kerja,kode,' . $unitKerja->id,
            'nama' => 'required|max:255',
            'parent_id' => 'nullable|exists:unit_kerja,id',
            'kepala_id' => 'nullable|exists:pegawai,id',
            'deskripsi' => 'nullable|max:500',
            'is_active' => 'boolean',
        ]);

        $unitKerja->update($request->all());

        return redirect()->route('kepegawaian.unit-kerja.index')
            ->with('success', 'Unit kerja berhasil diperbarui!');
    }

    public function destroy(UnitKerja $unitKerja)
    {
        if ($unitKerja->pegawai()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus unit kerja yang masih memiliki pegawai!');
        }

        if ($unitKerja->children()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus unit kerja yang masih memiliki sub-unit!');
        }

        $unitKerja->delete();

        return redirect()->route('kepegawaian.unit-kerja.index')
            ->with('success', 'Unit kerja berhasil dihapus!');
    }
}
