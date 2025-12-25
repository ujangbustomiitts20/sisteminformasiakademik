<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    public function index(Request $request)
    {
        $query = MataKuliah::with('programStudi.fakultas');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('kode', 'like', "%{$request->search}%")
                    ->orWhere('nama', 'like', "%{$request->search}%");
            });
        }

        if ($request->program_studi_id) {
            $query->where('program_studi_id', $request->program_studi_id);
        }

        if ($request->semester) {
            $query->where('semester', $request->semester);
        }

        $mataKuliah = $query->orderBy('semester')->orderBy('kode')->paginate(15);
        $programStudi = ProgramStudi::all();

        return view('mata-kuliah.index', compact('mataKuliah', 'programStudi'));
    }

    public function create()
    {
        $programStudi = ProgramStudi::with('fakultas')->get();
        return view('mata-kuliah.create', compact('programStudi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:mata_kuliah,kode|max:20',
            'nama' => 'required|max:255',
            'program_studi_id' => 'required|exists:program_studi,id',
            'sks' => 'required|integer|min:1|max:6',
            'jumlah_pertemuan' => 'required|integer|min:1|max:32',
            'semester' => 'required|integer|min:1|max:14',
            'jenis' => 'required|in:Wajib,Pilihan',
        ]);

        MataKuliah::create($request->all());

        return redirect()->route('mata-kuliah.index')->with('success', 'Mata kuliah berhasil ditambahkan!');
    }

    public function show(MataKuliah $mataKuliah)
    {
        $mataKuliah->load(['programStudi.fakultas', 'jadwalKuliah.dosen']);
        return view('mata-kuliah.show', compact('mataKuliah'));
    }

    public function edit(MataKuliah $mataKuliah)
    {
        $programStudi = ProgramStudi::with('fakultas')->get();
        return view('mata-kuliah.edit', compact('mataKuliah', 'programStudi'));
    }

    public function update(Request $request, MataKuliah $mataKuliah)
    {
        $request->validate([
            'kode' => 'required|max:20|unique:mata_kuliah,kode,' . $mataKuliah->id,
            'nama' => 'required|max:255',
            'program_studi_id' => 'required|exists:program_studi,id',
            'sks' => 'required|integer|min:1|max:6',
            'jumlah_pertemuan' => 'required|integer|min:1|max:32',
            'semester' => 'required|integer|min:1|max:14',
            'jenis' => 'required|in:Wajib,Pilihan',
        ]);

        $mataKuliah->update($request->all());

        return redirect()->route('mata-kuliah.index')->with('success', 'Mata kuliah berhasil diperbarui!');
    }

    public function destroy(MataKuliah $mataKuliah)
    {
        try {
            $mataKuliah->delete();
            return redirect()->route('mata-kuliah.index')->with('success', 'Mata kuliah berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Tidak dapat menghapus mata kuliah yang sudah memiliki jadwal!');
        }
    }
}
