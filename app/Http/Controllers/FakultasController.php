<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FakultasController extends Controller
{
    public function index()
    {
        $fakultas = Fakultas::withCount('programStudi')->orderBy('nama')->get();
        $dosen = Dosen::where('status', 'Aktif')->orderBy('nama')->get();
        return view('master.fakultas', compact('fakultas', 'dosen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:fakultas,kode|max:10',
            'nama' => 'required|max:191',
            'singkatan' => 'nullable|max:20',
            'email' => 'nullable|email|max:191',
            'website' => 'nullable|url|max:191',
            'logo' => 'nullable|image|mimes:jpeg,png,gif|max:2048',
            'tanggal_akreditasi' => 'nullable|date',
            'tanggal_berdiri' => 'nullable|date',
        ]);

        $data = $request->except('logo');

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('fakultas', 'public');
        }

        Fakultas::create($data);
        return redirect()->route('fakultas.index')->with('success', 'Fakultas berhasil ditambahkan!');
    }

    public function update(Request $request, Fakultas $fakulta)
    {
        $request->validate([
            'kode' => 'required|max:10|unique:fakultas,kode,' . $fakulta->id,
            'nama' => 'required|max:191',
            'singkatan' => 'nullable|max:20',
            'email' => 'nullable|email|max:191',
            'website' => 'nullable|url|max:191',
            'logo' => 'nullable|image|mimes:jpeg,png,gif|max:2048',
            'tanggal_akreditasi' => 'nullable|date',
            'tanggal_berdiri' => 'nullable|date',
        ]);

        $data = $request->except('logo');

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($fakulta->logo) {
                Storage::disk('public')->delete($fakulta->logo);
            }
            $data['logo'] = $request->file('logo')->store('fakultas', 'public');
        }

        $fakulta->update($data);
        return redirect()->route('fakultas.index')->with('success', 'Fakultas berhasil diperbarui!');
    }

    public function destroy(Fakultas $fakulta)
    {
        try {
            // Delete logo if exists
            if ($fakulta->logo) {
                Storage::disk('public')->delete($fakulta->logo);
            }
            $fakulta->delete();
            return redirect()->route('fakultas.index')->with('success', 'Fakultas berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Tidak dapat menghapus fakultas yang memiliki program studi!');
        }
    }
}
