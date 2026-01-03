<?php

namespace App\Http\Controllers;

use App\Models\KuotaPmb;
use App\Models\GelombangPmb;
use App\Models\JalurSeleksi;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class KuotaPmbController extends Controller
{
    public function index(Request $request)
    {
        $query = KuotaPmb::with(['gelombangPmb.periodePmb', 'jalurSeleksi', 'programStudi']);

        // Filter by gelombang
        if ($request->filled('gelombang')) {
            $query->where('gelombang_pmb_id', $request->gelombang);
        }

        // Filter by prodi
        if ($request->filled('prodi')) {
            $query->where('program_studi_id', $request->prodi);
        }

        // Filter by jalur
        if ($request->filled('jalur')) {
            $query->where('jalur_seleksi_id', $request->jalur);
        }

        $kuotaList = $query->orderBy('gelombang_pmb_id', 'desc')
            ->orderBy('program_studi_id')
            ->orderBy('jalur_seleksi_id')
            ->paginate(20);

        $gelombangs = GelombangPmb::with('periodePmb')
            ->orderBy('periode_pmb_id', 'desc')
            ->orderBy('nomor_gelombang')
            ->get();
        $jalurs = JalurSeleksi::where('is_active', true)->orderBy('nama')->get();
        $prodis = ProgramStudi::orderBy('nama')->get();

        return view('pmb.kuota.index', compact('kuotaList', 'gelombangs', 'jalurs', 'prodis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gelombang_pmb_id' => 'required|exists:gelombang_pmb,id',
            'program_studi_id' => 'required|exists:program_studi,id',
            'jalur_seleksi_id' => 'required|exists:jalur_seleksi,id',
            'kuota' => 'required|integer|min:0',
            'passing_grade' => 'nullable|numeric|min:0|max:100',
        ]);

        // Check if already exists
        $exists = KuotaPmb::where('gelombang_pmb_id', $validated['gelombang_pmb_id'])
            ->where('program_studi_id', $validated['program_studi_id'])
            ->where('jalur_seleksi_id', $validated['jalur_seleksi_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kuota untuk kombinasi Gelombang + Prodi + Jalur ini sudah ada.');
        }

        $validated['terisi'] = 0;
        KuotaPmb::create($validated);

        return redirect()->route('pmb.kuota.index')
            ->with('success', 'Kuota berhasil ditambahkan.');
    }

    public function edit(string $hashid)
    {
        $kuota = KuotaPmb::findByHashidOrFail($hashid);
        $kuota->load(['gelombangPmb.periodePmb', 'jalurSeleksi', 'programStudi']);

        $gelombangs = GelombangPmb::with('periodePmb')
            ->orderBy('periode_pmb_id', 'desc')
            ->orderBy('nomor_gelombang')
            ->get();
        $jalurs = JalurSeleksi::where('is_active', true)->orderBy('nama')->get();
        $prodis = ProgramStudi::orderBy('nama')->get();

        return view('pmb.kuota.edit', compact('kuota', 'gelombangs', 'jalurs', 'prodis'));
    }

    public function update(Request $request, string $hashid)
    {
        $kuota = KuotaPmb::findByHashidOrFail($hashid);

        $validated = $request->validate([
            'gelombang_pmb_id' => 'required|exists:gelombang_pmb,id',
            'program_studi_id' => 'required|exists:program_studi,id',
            'jalur_seleksi_id' => 'required|exists:jalur_seleksi,id',
            'kuota' => 'required|integer|min:0',
            'passing_grade' => 'nullable|numeric|min:0|max:100',
        ]);

        // Check if already exists (exclude current)
        $exists = KuotaPmb::where('gelombang_pmb_id', $validated['gelombang_pmb_id'])
            ->where('program_studi_id', $validated['program_studi_id'])
            ->where('jalur_seleksi_id', $validated['jalur_seleksi_id'])
            ->where('id', '!=', $kuota->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kuota untuk kombinasi Gelombang + Prodi + Jalur ini sudah ada.');
        }

        $kuota->update($validated);

        return redirect()->route('pmb.kuota.index')
            ->with('success', 'Kuota berhasil diperbarui.');
    }

    public function destroy(string $hashid)
    {
        $kuota = KuotaPmb::findByHashidOrFail($hashid);

        if ($kuota->terisi > 0) {
            return back()->with('error', 'Kuota tidak dapat dihapus karena sudah ada yang terisi.');
        }

        $kuota->delete();

        return redirect()->route('pmb.kuota.index')
            ->with('success', 'Kuota berhasil dihapus.');
    }

    /**
     * Generate kuota massal untuk gelombang
     */
    public function generateBatch(Request $request)
    {
        $validated = $request->validate([
            'gelombang_pmb_id' => 'required|exists:gelombang_pmb,id',
            'kuota_default' => 'required|integer|min:1',
            'passing_grade_default' => 'nullable|numeric|min:0|max:100',
            'program_studi_ids' => 'required|array|min:1',
            'program_studi_ids.*' => 'exists:program_studi,id',
            'jalur_seleksi_ids' => 'required|array|min:1',
            'jalur_seleksi_ids.*' => 'exists:jalur_seleksi,id',
        ]);

        $created = 0;
        $skipped = 0;

        foreach ($validated['program_studi_ids'] as $prodiId) {
            foreach ($validated['jalur_seleksi_ids'] as $jalurId) {
                $exists = KuotaPmb::where('gelombang_pmb_id', $validated['gelombang_pmb_id'])
                    ->where('program_studi_id', $prodiId)
                    ->where('jalur_seleksi_id', $jalurId)
                    ->exists();

                if (!$exists) {
                    KuotaPmb::create([
                        'gelombang_pmb_id' => $validated['gelombang_pmb_id'],
                        'program_studi_id' => $prodiId,
                        'jalur_seleksi_id' => $jalurId,
                        'kuota' => $validated['kuota_default'],
                        'passing_grade' => $validated['passing_grade_default'],
                        'terisi' => 0,
                    ]);
                    $created++;
                } else {
                    $skipped++;
                }
            }
        }

        return redirect()->route('pmb.kuota.index')
            ->with('success', "Berhasil membuat {$created} kuota. {$skipped} dilewati (sudah ada).");
    }

    /**
     * Copy kuota dari gelombang lain
     */
    public function copyFromGelombang(Request $request)
    {
        $validated = $request->validate([
            'source_gelombang_id' => 'required|exists:gelombang_pmb,id',
            'target_gelombang_id' => 'required|exists:gelombang_pmb,id|different:source_gelombang_id',
        ]);

        $sourceKuota = KuotaPmb::where('gelombang_pmb_id', $validated['source_gelombang_id'])->get();

        if ($sourceKuota->isEmpty()) {
            return back()->with('error', 'Tidak ada kuota di gelombang sumber.');
        }

        $created = 0;
        $skipped = 0;

        foreach ($sourceKuota as $kuota) {
            $exists = KuotaPmb::where('gelombang_pmb_id', $validated['target_gelombang_id'])
                ->where('program_studi_id', $kuota->program_studi_id)
                ->where('jalur_seleksi_id', $kuota->jalur_seleksi_id)
                ->exists();

            if (!$exists) {
                KuotaPmb::create([
                    'gelombang_pmb_id' => $validated['target_gelombang_id'],
                    'program_studi_id' => $kuota->program_studi_id,
                    'jalur_seleksi_id' => $kuota->jalur_seleksi_id,
                    'kuota' => $kuota->kuota,
                    'passing_grade' => $kuota->passing_grade,
                    'terisi' => 0,
                ]);
                $created++;
            } else {
                $skipped++;
            }
        }

        return redirect()->route('pmb.kuota.index')
            ->with('success', "Berhasil menyalin {$created} kuota. {$skipped} dilewati (sudah ada).");
    }

    /**
     * Reset terisi ke 0
     */
    public function resetTerisi(Request $request)
    {
        $validated = $request->validate([
            'gelombang_pmb_id' => 'required|exists:gelombang_pmb,id',
        ]);

        $updated = KuotaPmb::where('gelombang_pmb_id', $validated['gelombang_pmb_id'])
            ->update(['terisi' => 0]);

        return redirect()->route('pmb.kuota.index')
            ->with('success', "Berhasil mereset {$updated} kuota.");
    }
}
