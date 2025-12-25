<?php

namespace App\Http\Controllers;

use App\Models\Tarif;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class TarifController extends Controller
{
    public function index(Request $request)
    {
        $query = Tarif::with('programStudi');

        if ($request->jenis) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->program_studi_id) {
            $query->where('program_studi_id', $request->program_studi_id);
        }

        if ($request->status) {
            $query->where('is_active', $request->status === 'active');
        }

        $tarif = $query->orderBy('jenis')->orderBy('nama_tarif')->paginate(20);
        $programStudi = ProgramStudi::orderBy('nama')->get();
        $jenisTarif = Tarif::JENIS;

        return view('keuangan.tarif.index', compact('tarif', 'programStudi', 'jenisTarif'));
    }

    public function create()
    {
        $programStudi = ProgramStudi::orderBy('nama')->get();
        $jenisTarif = Tarif::JENIS;
        $periodeTarif = Tarif::PERIODE;

        return view('keuangan.tarif.create', compact('programStudi', 'jenisTarif', 'periodeTarif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:' . implode(',', array_keys(Tarif::JENIS)),
            'nama_tarif' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'periode' => 'required|in:' . implode(',', array_keys(Tarif::PERIODE)),
            'program_studi_id' => 'nullable|exists:program_studi,id',
            'angkatan' => 'nullable|string|max:4',
        ]);

        Tarif::create($request->all());

        return redirect()->route('tarif.index')->with('success', 'Tarif berhasil ditambahkan!');
    }

    public function edit(Tarif $tarif)
    {
        $programStudi = ProgramStudi::orderBy('nama')->get();
        $jenisTarif = Tarif::JENIS;
        $periodeTarif = Tarif::PERIODE;

        return view('keuangan.tarif.edit', compact('tarif', 'programStudi', 'jenisTarif', 'periodeTarif'));
    }

    public function update(Request $request, Tarif $tarif)
    {
        $request->validate([
            'jenis' => 'required|in:' . implode(',', array_keys(Tarif::JENIS)),
            'nama_tarif' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'periode' => 'required|in:' . implode(',', array_keys(Tarif::PERIODE)),
        ]);

        $tarif->update($request->all());

        return redirect()->route('tarif.index')->with('success', 'Tarif berhasil diperbarui!');
    }

    public function destroy(Tarif $tarif)
    {
        try {
            $tarif->delete();
            return redirect()->route('tarif.index')->with('success', 'Tarif berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Tidak dapat menghapus tarif yang sudah digunakan!');
        }
    }

    public function toggleStatus(Tarif $tarif)
    {
        $tarif->update(['is_active' => !$tarif->is_active]);
        $status = $tarif->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return back()->with('success', "Tarif berhasil {$status}!");
    }
}
