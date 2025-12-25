<?php

namespace App\Http\Controllers;

use App\Models\ProgramStudi;
use App\Models\Fakultas;
use Illuminate\Http\Request;

class ProgramStudiController extends Controller
{
    public function index()
    {
        $programStudi = ProgramStudi::with('fakultas')
            ->withCount(['mahasiswa', 'dosen'])
            ->orderBy('nama')
            ->get();
        $fakultas = Fakultas::orderBy('nama')->get();
        return view('master.program-studi', compact('programStudi', 'fakultas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fakultas_id' => 'required|exists:fakultas,id',
            'kode' => 'required|unique:program_studi,kode|max:10',
            'nama' => 'required|max:191',
            'jenjang' => 'required|in:D3,S1,S2,S3',
            'total_sks' => 'required|integer|min:100|max:200',
        ]);

        ProgramStudi::create($request->all());
        return redirect()->route('program-studi.index')->with('success', 'Program Studi berhasil ditambahkan!');
    }

    public function update(Request $request, ProgramStudi $programStudi)
    {
        $request->validate([
            'fakultas_id' => 'required|exists:fakultas,id',
            'kode' => 'required|max:10|unique:program_studi,kode,' . $programStudi->id,
            'nama' => 'required|max:191',
            'jenjang' => 'required|in:D3,S1,S2,S3',
            'total_sks' => 'required|integer|min:100|max:200',
        ]);

        $programStudi->update($request->all());
        return redirect()->route('program-studi.index')->with('success', 'Program Studi berhasil diperbarui!');
    }

    public function destroy(ProgramStudi $programStudi)
    {
        try {
            $programStudi->delete();
            return redirect()->route('program-studi.index')->with('success', 'Program Studi berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Tidak dapat menghapus program studi yang memiliki data!');
        }
    }
}
