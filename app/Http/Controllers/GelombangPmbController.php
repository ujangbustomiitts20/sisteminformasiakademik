<?php

namespace App\Http\Controllers;

use App\Models\GelombangPmb;
use App\Models\PeriodePmb;
use Illuminate\Http\Request;

class GelombangPmbController extends Controller
{
    public function index()
    {
        $gelombangs = GelombangPmb::with('periodePmb')
            ->orderBy('periode_pmb_id', 'desc')
            ->orderBy('nomor_gelombang')
            ->paginate(10);

        return view('pmb.gelombang.index', compact('gelombangs'));
    }

    public function create()
    {
        $periodes = PeriodePmb::orderBy('tahun_akademik', 'desc')->get();

        return view('pmb.gelombang.create', compact('periodes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'periode_pmb_id' => 'required|exists:periode_pmb,id',
            'nama' => 'required|string|max:50',
            'nomor_gelombang' => 'required|integer|min:1',
            'tanggal_mulai_daftar' => 'required|date',
            'tanggal_selesai_daftar' => 'required|date|after_or_equal:tanggal_mulai_daftar',
            'tanggal_ujian' => 'nullable|date|after_or_equal:tanggal_selesai_daftar',
            'tanggal_pengumuman' => 'nullable|date|after_or_equal:tanggal_ujian',
            'tanggal_daftar_ulang_mulai' => 'nullable|date|after_or_equal:tanggal_pengumuman',
            'tanggal_daftar_ulang_selesai' => 'nullable|date|after_or_equal:tanggal_daftar_ulang_mulai',
            'is_active' => 'boolean',
        ]);

        // Jika active, nonaktifkan yang lain dalam periode yang sama
        if ($request->is_active) {
            GelombangPmb::where('periode_pmb_id', $request->periode_pmb_id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        GelombangPmb::create($validated);

        return redirect()->route('pmb.gelombang.index')
            ->with('success', 'Gelombang PMB berhasil ditambahkan.');
    }

    public function show(string $hashid)
    {
        $gelombang = GelombangPmb::findByHashidOrFail($hashid);
        $gelombang->load(['periodePmb', 'calonMahasiswa', 'kuota.programStudi', 'biayaPendaftaran']);

        return view('pmb.gelombang.show', compact('gelombang'));
    }

    public function edit(string $hashid)
    {
        $gelombang = GelombangPmb::findByHashidOrFail($hashid);
        $periodes = PeriodePmb::orderBy('tahun_akademik', 'desc')->get();

        return view('pmb.gelombang.edit', compact('gelombang', 'periodes'));
    }

    public function update(Request $request, string $hashid)
    {
        $gelombang = GelombangPmb::findByHashidOrFail($hashid);

        $validated = $request->validate([
            'periode_pmb_id' => 'required|exists:periode_pmb,id',
            'nama' => 'required|string|max:50',
            'nomor_gelombang' => 'required|integer|min:1',
            'tanggal_mulai_daftar' => 'required|date',
            'tanggal_selesai_daftar' => 'required|date|after_or_equal:tanggal_mulai_daftar',
            'tanggal_ujian' => 'nullable|date|after_or_equal:tanggal_selesai_daftar',
            'tanggal_pengumuman' => 'nullable|date|after_or_equal:tanggal_ujian',
            'tanggal_daftar_ulang_mulai' => 'nullable|date|after_or_equal:tanggal_pengumuman',
            'tanggal_daftar_ulang_selesai' => 'nullable|date|after_or_equal:tanggal_daftar_ulang_mulai',
            'is_active' => 'boolean',
        ]);

        // Jika active, nonaktifkan yang lain
        if ($request->is_active && !$gelombang->is_active) {
            GelombangPmb::where('periode_pmb_id', $request->periode_pmb_id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $gelombang->update($validated);

        return redirect()->route('pmb.gelombang.index')
            ->with('success', 'Gelombang PMB berhasil diperbarui.');
    }

    public function destroy(string $hashid)
    {
        $gelombang = GelombangPmb::findByHashidOrFail($hashid);

        if ($gelombang->calonMahasiswa()->exists()) {
            return back()->with('error', 'Gelombang tidak dapat dihapus karena memiliki data pendaftar.');
        }

        $gelombang->delete();

        return redirect()->route('pmb.gelombang.index')
            ->with('success', 'Gelombang PMB berhasil dihapus.');
    }

    public function setActive(string $hashid)
    {
        $gelombang = GelombangPmb::findByHashidOrFail($hashid);

        // Nonaktifkan semua gelombang dalam periode yang sama
        GelombangPmb::where('periode_pmb_id', $gelombang->periode_pmb_id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        // Aktifkan gelombang ini
        $gelombang->update(['is_active' => true]);

        return back()->with('success', 'Gelombang PMB berhasil diaktifkan.');
    }
}
