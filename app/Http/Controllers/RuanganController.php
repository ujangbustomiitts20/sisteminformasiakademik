<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public function index()
    {
        $ruangan = Ruangan::orderBy('gedung')->orderBy('nama')->get();
        return view('master.ruangan', compact('ruangan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:ruangan,kode|max:20',
            'nama' => 'required|max:191',
            'kapasitas' => 'required|integer|min:1',
            'jenis' => 'required|in:Kelas,Lab,Aula,Lainnya',
        ]);

        Ruangan::create($request->all());
        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil ditambahkan!');
    }

    public function update(Request $request, Ruangan $ruangan)
    {
        $request->validate([
            'kode' => 'required|max:20|unique:ruangan,kode,' . $ruangan->id,
            'nama' => 'required|max:191',
            'kapasitas' => 'required|integer|min:1',
            'jenis' => 'required|in:Kelas,Lab,Aula,Lainnya',
        ]);

        $ruangan->update($request->all());
        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil diperbarui!');
    }

    public function destroy(Ruangan $ruangan)
    {
        try {
            $ruangan->delete();
            return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Tidak dapat menghapus ruangan yang sudah digunakan!');
        }
    }
}
