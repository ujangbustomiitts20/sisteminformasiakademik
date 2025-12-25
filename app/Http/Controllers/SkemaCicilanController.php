<?php

namespace App\Http\Controllers;

use App\Models\SkemaCicilan;
use Illuminate\Http\Request;

class SkemaCicilanController extends Controller
{
    public function index()
    {
        $skemaCicilan = SkemaCicilan::orderBy('jumlah_cicilan')->get();

        $stats = [
            'total' => $skemaCicilan->count(),
            'aktif' => $skemaCicilan->where('is_active', true)->count(),
            'nonaktif' => $skemaCicilan->where('is_active', false)->count(),
        ];

        return view('keuangan.cicilan.skema-index', compact('skemaCicilan', 'stats'));
    }

    public function create()
    {
        return view('keuangan.cicilan.skema-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'jumlah_cicilan' => 'required|integer|min:2|max:24',
            'biaya_admin' => 'nullable|numeric|min:0',
            'persentase_bunga' => 'nullable|numeric|min:0|max:100',
            'minimal_tagihan' => 'nullable|numeric|min:0',
            'interval_hari' => 'required|integer|min:7|max:90',
            'keterangan' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['biaya_admin'] = $validated['biaya_admin'] ?? 0;
        $validated['persentase_bunga'] = $validated['persentase_bunga'] ?? 0;
        $validated['minimal_tagihan'] = $validated['minimal_tagihan'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        SkemaCicilan::create($validated);

        return redirect()->route('skema-cicilan.index')
            ->with('success', 'Skema cicilan berhasil ditambahkan.');
    }

    public function edit(SkemaCicilan $skemaCicilan)
    {
        return view('keuangan.cicilan.skema-edit', compact('skemaCicilan'));
    }

    public function update(Request $request, SkemaCicilan $skemaCicilan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'jumlah_cicilan' => 'required|integer|min:2|max:24',
            'biaya_admin' => 'nullable|numeric|min:0',
            'persentase_bunga' => 'nullable|numeric|min:0|max:100',
            'minimal_tagihan' => 'nullable|numeric|min:0',
            'interval_hari' => 'required|integer|min:7|max:90',
            'keterangan' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['biaya_admin'] = $validated['biaya_admin'] ?? 0;
        $validated['persentase_bunga'] = $validated['persentase_bunga'] ?? 0;
        $validated['minimal_tagihan'] = $validated['minimal_tagihan'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        $skemaCicilan->update($validated);

        return redirect()->route('skema-cicilan.index')
            ->with('success', 'Skema cicilan berhasil diperbarui.');
    }

    public function destroy(SkemaCicilan $skemaCicilan)
    {
        // Check if skema used
        if ($skemaCicilan->cicilan()->exists()) {
            return back()->with('error', 'Skema cicilan tidak dapat dihapus karena sudah digunakan.');
        }

        $skemaCicilan->delete();

        return redirect()->route('skema-cicilan.index')
            ->with('success', 'Skema cicilan berhasil dihapus.');
    }

    public function toggleStatus(SkemaCicilan $skemaCicilan)
    {
        $skemaCicilan->is_active = !$skemaCicilan->is_active;
        $skemaCicilan->save();

        $status = $skemaCicilan->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Skema cicilan berhasil {$status}.");
    }
}
