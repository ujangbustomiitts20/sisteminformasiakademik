<?php

namespace App\Http\Controllers;

use App\Models\PeriodePmb;
use Illuminate\Http\Request;

class PeriodePmbController extends Controller
{
    public function index()
    {
        $periodes = PeriodePmb::orderBy('tahun_akademik', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pmb.periode.index', compact('periodes'));
    }

    public function create()
    {
        return view('pmb.periode.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'tahun_akademik' => 'required|string|max:20',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Jika active, nonaktifkan yang lain
        if ($request->is_active) {
            PeriodePmb::where('is_active', true)->update(['is_active' => false]);
        }

        PeriodePmb::create($validated);

        return redirect()->route('pmb.periode.index')
            ->with('success', 'Periode PMB berhasil ditambahkan.');
    }

    public function show(string $hashid)
    {
        $periode = PeriodePmb::findByHashidOrFail($hashid);
        $periode->load('gelombang');

        return view('pmb.periode.show', compact('periode'));
    }

    public function edit(string $hashid)
    {
        $periode = PeriodePmb::findByHashidOrFail($hashid);

        return view('pmb.periode.edit', compact('periode'));
    }

    public function update(Request $request, string $hashid)
    {
        $periode = PeriodePmb::findByHashidOrFail($hashid);

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'tahun_akademik' => 'required|string|max:20',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Jika active, nonaktifkan yang lain
        if ($request->is_active && !$periode->is_active) {
            PeriodePmb::where('is_active', true)->update(['is_active' => false]);
        }

        $periode->update($validated);

        return redirect()->route('pmb.periode.index')
            ->with('success', 'Periode PMB berhasil diperbarui.');
    }

    public function destroy(string $hashid)
    {
        $periode = PeriodePmb::findByHashidOrFail($hashid);

        if ($periode->gelombang()->exists()) {
            return back()->with('error', 'Periode tidak dapat dihapus karena memiliki data gelombang.');
        }

        $periode->delete();

        return redirect()->route('pmb.periode.index')
            ->with('success', 'Periode PMB berhasil dihapus.');
    }

    public function setActive(string $hashid)
    {
        $periode = PeriodePmb::findByHashidOrFail($hashid);

        // Nonaktifkan semua periode
        PeriodePmb::where('is_active', true)->update(['is_active' => false]);

        // Aktifkan periode ini
        $periode->update(['is_active' => true]);

        return back()->with('success', 'Periode PMB berhasil diaktifkan.');
    }
}
