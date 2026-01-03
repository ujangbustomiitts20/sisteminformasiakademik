<?php

namespace App\Http\Controllers;

use App\Models\BiayaPendaftaran;
use App\Models\GelombangPmb;
use App\Models\JalurSeleksi;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class BiayaPendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $query = BiayaPendaftaran::with(['gelombangPmb.periodePmb', 'jalurSeleksi', 'programStudi']);

        // Filter by gelombang
        if ($request->filled('gelombang')) {
            $query->where('gelombang_pmb_id', $request->gelombang);
        }

        // Filter by jalur
        if ($request->filled('jalur')) {
            $query->where('jalur_seleksi_id', $request->jalur);
        }

        $biayaList = $query->orderBy('gelombang_pmb_id', 'desc')
            ->orderBy('jalur_seleksi_id')
            ->orderBy('program_studi_id')
            ->paginate(15);

        $gelombangs = GelombangPmb::with('periodePmb')
            ->orderBy('periode_pmb_id', 'desc')
            ->orderBy('nomor_gelombang')
            ->get();
        $jalurs = JalurSeleksi::where('is_active', true)->orderBy('nama')->get();
        $prodis = ProgramStudi::orderBy('nama')->get();

        return view('pmb.biaya-pendaftaran.index', compact('biayaList', 'gelombangs', 'jalurs', 'prodis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gelombang_pmb_id' => 'required|exists:gelombang_pmb,id',
            'jalur_seleksi_id' => 'required|exists:jalur_seleksi,id',
            'program_studi_id' => 'nullable|exists:program_studi,id',
            'biaya_formulir' => 'required|numeric|min:0',
            'biaya_ujian' => 'required|numeric|min:0',
        ]);

        // Check if already exists
        $exists = BiayaPendaftaran::where('gelombang_pmb_id', $validated['gelombang_pmb_id'])
            ->where('jalur_seleksi_id', $validated['jalur_seleksi_id'])
            ->where(function ($q) use ($validated) {
                if (empty($validated['program_studi_id'])) {
                    $q->whereNull('program_studi_id');
                } else {
                    $q->where('program_studi_id', $validated['program_studi_id']);
                }
            })
            ->exists();

        if ($exists) {
            return back()->with('error', 'Biaya pendaftaran untuk kombinasi ini sudah ada.');
        }

        BiayaPendaftaran::create($validated);

        return redirect()->route('pmb.biaya-pendaftaran.index')
            ->with('success', 'Biaya pendaftaran berhasil ditambahkan.');
    }

    public function edit(string $hashid)
    {
        $biaya = BiayaPendaftaran::findByHashidOrFail($hashid);
        $biaya->load(['gelombangPmb.periodePmb', 'jalurSeleksi', 'programStudi']);

        $gelombangs = GelombangPmb::with('periodePmb')
            ->orderBy('periode_pmb_id', 'desc')
            ->orderBy('nomor_gelombang')
            ->get();
        $jalurs = JalurSeleksi::where('is_active', true)->orderBy('nama')->get();
        $prodis = ProgramStudi::orderBy('nama')->get();

        return view('pmb.biaya-pendaftaran.edit', compact('biaya', 'gelombangs', 'jalurs', 'prodis'));
    }

    public function update(Request $request, string $hashid)
    {
        $biaya = BiayaPendaftaran::findByHashidOrFail($hashid);

        $validated = $request->validate([
            'gelombang_pmb_id' => 'required|exists:gelombang_pmb,id',
            'jalur_seleksi_id' => 'required|exists:jalur_seleksi,id',
            'program_studi_id' => 'nullable|exists:program_studi,id',
            'biaya_formulir' => 'required|numeric|min:0',
            'biaya_ujian' => 'required|numeric|min:0',
        ]);

        // Check if already exists (exclude current)
        $exists = BiayaPendaftaran::where('gelombang_pmb_id', $validated['gelombang_pmb_id'])
            ->where('jalur_seleksi_id', $validated['jalur_seleksi_id'])
            ->where('id', '!=', $biaya->id)
            ->where(function ($q) use ($validated) {
                if (empty($validated['program_studi_id'])) {
                    $q->whereNull('program_studi_id');
                } else {
                    $q->where('program_studi_id', $validated['program_studi_id']);
                }
            })
            ->exists();

        if ($exists) {
            return back()->with('error', 'Biaya pendaftaran untuk kombinasi ini sudah ada.');
        }

        $biaya->update($validated);

        return redirect()->route('pmb.biaya-pendaftaran.index')
            ->with('success', 'Biaya pendaftaran berhasil diperbarui.');
    }

    public function destroy(string $hashid)
    {
        $biaya = BiayaPendaftaran::findByHashidOrFail($hashid);

        // Check if used in any pembayaran
        $isUsed = \App\Models\PembayaranPmb::whereHas('calonMahasiswa', function ($q) use ($biaya) {
            $q->where('gelombang_pmb_id', $biaya->gelombang_pmb_id)
              ->where('jalur_seleksi_id', $biaya->jalur_seleksi_id);
        })->exists();

        if ($isUsed) {
            return back()->with('error', 'Biaya pendaftaran tidak dapat dihapus karena sudah digunakan.');
        }

        $biaya->delete();

        return redirect()->route('pmb.biaya-pendaftaran.index')
            ->with('success', 'Biaya pendaftaran berhasil dihapus.');
    }

    /**
     * Generate biaya pendaftaran massal
     */
    public function generateBatch(Request $request)
    {
        $validated = $request->validate([
            'gelombang_pmb_id' => 'required|exists:gelombang_pmb,id',
            'biaya_formulir' => 'required|numeric|min:0',
            'biaya_ujian' => 'required|numeric|min:0',
            'generate_type' => 'required|in:all_jalur,specific_prodi',
            'jalur_seleksi_ids' => 'required_if:generate_type,all_jalur|array',
            'jalur_seleksi_ids.*' => 'exists:jalur_seleksi,id',
            'program_studi_ids' => 'required_if:generate_type,specific_prodi|array',
            'program_studi_ids.*' => 'exists:program_studi,id',
        ]);

        $created = 0;
        $skipped = 0;

        if ($validated['generate_type'] === 'all_jalur') {
            // Generate biaya umum untuk semua jalur yang dipilih (tanpa prodi spesifik)
            foreach ($validated['jalur_seleksi_ids'] as $jalurId) {
                $exists = BiayaPendaftaran::where('gelombang_pmb_id', $validated['gelombang_pmb_id'])
                    ->where('jalur_seleksi_id', $jalurId)
                    ->whereNull('program_studi_id')
                    ->exists();

                if (!$exists) {
                    BiayaPendaftaran::create([
                        'gelombang_pmb_id' => $validated['gelombang_pmb_id'],
                        'jalur_seleksi_id' => $jalurId,
                        'program_studi_id' => null,
                        'biaya_formulir' => $validated['biaya_formulir'],
                        'biaya_ujian' => $validated['biaya_ujian'],
                    ]);
                    $created++;
                } else {
                    $skipped++;
                }
            }
        } else {
            // Generate biaya spesifik untuk kombinasi jalur + prodi
            $jalurs = JalurSeleksi::where('is_active', true)->pluck('id');
            foreach ($jalurs as $jalurId) {
                foreach ($validated['program_studi_ids'] as $prodiId) {
                    $exists = BiayaPendaftaran::where('gelombang_pmb_id', $validated['gelombang_pmb_id'])
                        ->where('jalur_seleksi_id', $jalurId)
                        ->where('program_studi_id', $prodiId)
                        ->exists();

                    if (!$exists) {
                        BiayaPendaftaran::create([
                            'gelombang_pmb_id' => $validated['gelombang_pmb_id'],
                            'jalur_seleksi_id' => $jalurId,
                            'program_studi_id' => $prodiId,
                            'biaya_formulir' => $validated['biaya_formulir'],
                            'biaya_ujian' => $validated['biaya_ujian'],
                        ]);
                        $created++;
                    } else {
                        $skipped++;
                    }
                }
            }
        }

        return redirect()->route('pmb.biaya-pendaftaran.index')
            ->with('success', "Berhasil membuat {$created} biaya pendaftaran. {$skipped} dilewati (sudah ada).");
    }

    /**
     * Copy biaya dari gelombang lain
     */
    public function copyFromGelombang(Request $request)
    {
        $validated = $request->validate([
            'source_gelombang_id' => 'required|exists:gelombang_pmb,id',
            'target_gelombang_id' => 'required|exists:gelombang_pmb,id|different:source_gelombang_id',
        ]);

        $sourceBiaya = BiayaPendaftaran::where('gelombang_pmb_id', $validated['source_gelombang_id'])->get();

        if ($sourceBiaya->isEmpty()) {
            return back()->with('error', 'Tidak ada biaya pendaftaran di gelombang sumber.');
        }

        $created = 0;
        $skipped = 0;

        foreach ($sourceBiaya as $biaya) {
            $exists = BiayaPendaftaran::where('gelombang_pmb_id', $validated['target_gelombang_id'])
                ->where('jalur_seleksi_id', $biaya->jalur_seleksi_id)
                ->where(function ($q) use ($biaya) {
                    if (is_null($biaya->program_studi_id)) {
                        $q->whereNull('program_studi_id');
                    } else {
                        $q->where('program_studi_id', $biaya->program_studi_id);
                    }
                })
                ->exists();

            if (!$exists) {
                BiayaPendaftaran::create([
                    'gelombang_pmb_id' => $validated['target_gelombang_id'],
                    'jalur_seleksi_id' => $biaya->jalur_seleksi_id,
                    'program_studi_id' => $biaya->program_studi_id,
                    'biaya_formulir' => $biaya->biaya_formulir,
                    'biaya_ujian' => $biaya->biaya_ujian,
                ]);
                $created++;
            } else {
                $skipped++;
            }
        }

        return redirect()->route('pmb.biaya-pendaftaran.index')
            ->with('success', "Berhasil menyalin {$created} biaya pendaftaran. {$skipped} dilewati (sudah ada).");
    }
}
