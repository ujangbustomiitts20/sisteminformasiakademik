<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use Illuminate\Http\Request;

class FakultasController extends Controller
{
    public function index()
    {
        $fakultas = Fakultas::withCount('programStudi')->orderBy('nama')->get();
        return view('master.fakultas', compact('fakultas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:fakultas,kode|max:10',
            'nama' => 'required|max:191',
        ]);

        Fakultas::create($request->all());
        return redirect()->route('fakultas.index')->with('success', 'Fakultas berhasil ditambahkan!');
    }

    public function update(Request $request, Fakultas $fakultas)
    {
        $request->validate([
            'kode' => 'required|max:10|unique:fakultas,kode,' . $fakultas->id,
            'nama' => 'required|max:191',
        ]);

        $fakultas->update($request->all());
        return redirect()->route('fakultas.index')->with('success', 'Fakultas berhasil diperbarui!');
    }

    public function destroy(Fakultas $fakultas)
    {
        try {
            $fakultas->delete();
            return redirect()->route('fakultas.index')->with('success', 'Fakultas berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Tidak dapat menghapus fakultas yang memiliki program studi!');
        }
    }
}
