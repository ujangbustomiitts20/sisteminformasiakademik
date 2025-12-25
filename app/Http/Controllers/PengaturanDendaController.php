<?php

namespace App\Http\Controllers;

use App\Models\PengaturanDenda;
use Illuminate\Http\Request;

class PengaturanDendaController extends Controller
{
    public function index()
    {
        $pengaturan = PengaturanDenda::orderBy('nama')->paginate(15);
        return view('keuangan.denda.index', compact('pengaturan'));
    }

    public function create()
    {
        return view('keuangan.denda.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:' . implode(',', array_keys(PengaturanDenda::TIPE)),
            'nilai' => 'required|numeric|min:0',
            'periode' => 'required|in:' . implode(',', array_keys(PengaturanDenda::PERIODE)),
            'grace_period' => 'nullable|integer|min:0',
            'maksimal_denda' => 'nullable|numeric|min:0',
        ]);

        if ($request->tipe === 'Persen' && $request->nilai > 100) {
            return back()->with('error', 'Nilai denda persen tidak boleh lebih dari 100%')->withInput();
        }

        PengaturanDenda::create($request->all());

        return redirect()->route('pengaturan-denda.index')->with('success', 'Pengaturan denda berhasil ditambahkan!');
    }

    public function edit(PengaturanDenda $pengaturanDenda)
    {
        return view('keuangan.denda.edit', compact('pengaturanDenda'));
    }

    public function update(Request $request, PengaturanDenda $pengaturanDenda)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:' . implode(',', array_keys(PengaturanDenda::TIPE)),
            'nilai' => 'required|numeric|min:0',
            'periode' => 'required|in:' . implode(',', array_keys(PengaturanDenda::PERIODE)),
            'grace_period' => 'nullable|integer|min:0',
            'maksimal_denda' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        if ($request->tipe === 'Persen' && $request->nilai > 100) {
            return back()->with('error', 'Nilai denda persen tidak boleh lebih dari 100%')->withInput();
        }

        $pengaturanDenda->update($request->all());

        return redirect()->route('pengaturan-denda.index')->with('success', 'Pengaturan denda berhasil diperbarui!');
    }

    public function destroy(PengaturanDenda $pengaturanDenda)
    {
        $pengaturanDenda->delete();
        return redirect()->route('pengaturan-denda.index')->with('success', 'Pengaturan denda berhasil dihapus!');
    }

    public function toggleStatus(PengaturanDenda $pengaturanDenda)
    {
        $pengaturanDenda->update(['is_active' => !$pengaturanDenda->is_active]);
        $status = $pengaturanDenda->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Pengaturan denda berhasil {$status}!");
    }
}
