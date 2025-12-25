<?php

namespace App\Http\Controllers;

use App\Models\TahunAkademik;
use Illuminate\Http\Request;

class TahunAkademikController extends Controller
{
    public function index()
    {
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();
        return view('master.tahun-akademik', compact('tahunAkademik'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|max:10',
            'semester' => 'required|in:Ganjil,Genap,ganjil,genap',
        ]);

        $semester = ucfirst(strtolower($request->semester));

        // Check duplikat
        $exists = TahunAkademik::where('tahun', $request->tahun)
            ->where('semester', $semester)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Tahun akademik sudah ada!')->withInput();
        }

        // Jika is_aktif true, nonaktifkan semua yang lain
        if ($request->is_aktif) {
            TahunAkademik::where('is_aktif', true)->update(['is_aktif' => false]);
        }

        TahunAkademik::create([
            'tahun' => $request->tahun,
            'semester' => $semester,
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addMonths(6),
            'is_aktif' => $request->is_aktif ? true : false,
        ]);
        return redirect()->route('tahun-akademik.index')->with('success', 'Tahun akademik berhasil ditambahkan!');
    }

    public function update(Request $request, TahunAkademik $tahunAkademik)
    {
        $request->validate([
            'tahun' => 'required|max:10',
            'semester' => 'required|in:Ganjil,Genap,ganjil,genap',
        ]);

        $semester = ucfirst(strtolower($request->semester));

        // Jika is_aktif true, nonaktifkan semua yang lain
        if ($request->is_aktif) {
            TahunAkademik::where('is_aktif', true)->where('id', '!=', $tahunAkademik->id)->update(['is_aktif' => false]);
        }

        $tahunAkademik->update([
            'tahun' => $request->tahun,
            'semester' => $semester,
            'is_aktif' => $request->is_aktif ? true : false,
        ]);
        return redirect()->route('tahun-akademik.index')->with('success', 'Tahun akademik berhasil diperbarui!');
    }

    public function destroy(TahunAkademik $tahunAkademik)
    {
        try {
            $tahunAkademik->delete();
            return redirect()->route('tahun-akademik.index')->with('success', 'Tahun akademik berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Tidak dapat menghapus tahun akademik yang memiliki data!');
        }
    }

    // Set tahun akademik aktif
    public function activate(TahunAkademik $tahunAkademik)
    {
        // Nonaktifkan semua
        TahunAkademik::where('is_aktif', true)->update(['is_aktif' => false]);
        
        // Aktifkan yang dipilih
        $tahunAkademik->update(['is_aktif' => true]);

        return back()->with('success', 'Tahun akademik berhasil diaktifkan!');
    }
}
