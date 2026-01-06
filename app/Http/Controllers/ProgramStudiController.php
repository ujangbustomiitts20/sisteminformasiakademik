<?php

namespace App\Http\Controllers;

use App\Models\ProgramStudi;
use App\Models\Fakultas;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProgramStudiController extends Controller
{
    public function index(Request $request)
    {
        $query = ProgramStudi::with('fakultas')
            ->withCount(['mahasiswa', 'dosen']);
        
        // Filters
        if ($request->filled('fakultas')) {
            $query->where('fakultas_id', $request->fakultas);
        }
        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }
        if ($request->filled('akreditasi')) {
            $query->where('akreditasi', $request->akreditasi);
        }
        
        $programStudi = $query->orderBy('nama')->get();
        $fakultas = Fakultas::orderBy('nama')->get();
        $dosen = Dosen::where('status', 'Aktif')->orderBy('nama')->get();
        
        return view('master.program-studi', compact('programStudi', 'fakultas', 'dosen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fakultas_id' => 'required|exists:fakultas,id',
            'kode' => 'required|unique:program_studi,kode|max:20',
            'nama' => 'required|max:191',
            'jenjang' => 'required|in:D3,S1,S2,S3',
            'total_sks' => 'nullable|integer|min:100|max:200',
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
            $data['logo'] = $request->file('logo')->store('prodi', 'public');
        }

        ProgramStudi::create($data);
        return redirect()->route('program-studi.index')->with('success', 'Program Studi berhasil ditambahkan!');
    }

    public function update(Request $request, ProgramStudi $programStudi)
    {
        $request->validate([
            'fakultas_id' => 'required|exists:fakultas,id',
            'kode' => 'required|max:20|unique:program_studi,kode,' . $programStudi->id,
            'nama' => 'required|max:191',
            'jenjang' => 'required|in:D3,S1,S2,S3',
            'total_sks' => 'nullable|integer|min:100|max:200',
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
            if ($programStudi->logo) {
                Storage::disk('public')->delete($programStudi->logo);
            }
            $data['logo'] = $request->file('logo')->store('prodi', 'public');
        }

        $programStudi->update($data);
        return redirect()->route('program-studi.index')->with('success', 'Program Studi berhasil diperbarui!');
    }

    public function destroy(ProgramStudi $programStudi)
    {
        try {
            // Delete logo if exists
            if ($programStudi->logo) {
                Storage::disk('public')->delete($programStudi->logo);
            }
            $programStudi->delete();
            return redirect()->route('program-studi.index')->with('success', 'Program Studi berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Tidak dapat menghapus program studi yang memiliki data!');
        }
    }
}
