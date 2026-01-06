<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\SkpPegawai;
use App\Models\TargetSkp;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkpController extends Controller
{
    /**
     * Display SKP list for logged in dosen
     */
    public function index(Request $request)
    {
        $dosen = Auth::user()->dosen;

        if (!$dosen) {
            return redirect()->route('dosen.dashboard')
                ->with('error', 'Data dosen tidak ditemukan.');
        }

        $query = SkpPegawai::with(['targetSkp'])
            ->where('dosen_id', $dosen->id);

        // Filter tahun
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $skpList = $query->orderBy('tahun', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Stats
        $stats = [
            'total' => SkpPegawai::where('dosen_id', $dosen->id)->count(),
            'draft' => SkpPegawai::where('dosen_id', $dosen->id)->where('status', 'draft')->count(),
            'diajukan' => SkpPegawai::where('dosen_id', $dosen->id)->where('status', 'diajukan')->count(),
            'disetujui' => SkpPegawai::where('dosen_id', $dosen->id)->where('status', 'disetujui')->count(),
            'final' => SkpPegawai::where('dosen_id', $dosen->id)->where('status', 'final')->count(),
        ];

        // Get available years
        $tahunList = SkpPegawai::where('dosen_id', $dosen->id)
            ->distinct()
            ->pluck('tahun')
            ->sort()
            ->reverse();

        if ($tahunList->isEmpty()) {
            $tahunList = collect([date('Y'), date('Y') - 1]);
        }

        return view('dosen.skp.index', compact('skpList', 'stats', 'tahunList', 'dosen'));
    }

    /**
     * Show form to create new SKP
     */
    public function create()
    {
        $dosen = Auth::user()->dosen;

        if (!$dosen) {
            return redirect()->route('dosen.dashboard')
                ->with('error', 'Data dosen tidak ditemukan.');
        }

        return view('dosen.skp.create', compact('dosen'));
    }

    /**
     * Store new SKP
     */
    public function store(Request $request)
    {
        $dosen = Auth::user()->dosen;

        if (!$dosen) {
            return redirect()->route('dosen.dashboard')
                ->with('error', 'Data dosen tidak ditemukan.');
        }

        $validated = $request->validate([
            'tahun' => 'required|integer|min:2020|max:2100',
            'periode' => 'nullable|string|max:100',
        ], [
            'tahun.required' => 'Tahun wajib dipilih',
        ]);

        // Check if SKP already exists
        $exists = SkpPegawai::where('dosen_id', $dosen->id)
            ->where('tahun', $validated['tahun'])
            ->where('periode', $validated['periode'] ?? null)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'SKP untuk tahun dan periode ini sudah ada!')
                ->withInput();
        }

        $skp = SkpPegawai::create([
            'dosen_id' => $dosen->id,
            'tahun' => $validated['tahun'],
            'periode' => $validated['periode'],
            'tanggal_skp' => now(),
            'jabatan' => $dosen->jabatan_fungsional ?? $dosen->jabatan_akademik ?? 'Dosen',
            'unit_kerja' => $dosen->programStudi->nama ?? '-',
            'status' => 'draft',
        ]);

        return redirect()->route('dosen.skp.show', $skp)
            ->with('success', 'SKP berhasil dibuat! Silakan tambahkan target kinerja.');
    }

    /**
     * Display SKP detail
     */
    public function show(SkpPegawai $skp)
    {
        $dosen = Auth::user()->dosen;

        // Check ownership
        if ($skp->dosen_id !== $dosen->id) {
            abort(403, 'Anda tidak memiliki akses ke SKP ini.');
        }

        $skp->load(['targetSkp' => function ($q) {
            $q->orderBy('urutan');
        }]);

        return view('dosen.skp.show', compact('skp', 'dosen'));
    }

    /**
     * Show edit form
     */
    public function edit(SkpPegawai $skp)
    {
        $dosen = Auth::user()->dosen;

        if ($skp->dosen_id !== $dosen->id) {
            abort(403, 'Anda tidak memiliki akses ke SKP ini.');
        }

        // Only draft or revisi can be edited
        if (!in_array($skp->status, ['draft', 'revisi'])) {
            return redirect()->route('dosen.skp.show', $skp)
                ->with('error', 'SKP tidak dapat diedit pada status ini.');
        }

        $skp->load('targetSkp');

        return view('dosen.skp.edit', compact('skp', 'dosen'));
    }

    /**
     * Update SKP
     */
    public function update(Request $request, SkpPegawai $skp)
    {
        $dosen = Auth::user()->dosen;

        if ($skp->dosen_id !== $dosen->id) {
            abort(403);
        }

        if (!in_array($skp->status, ['draft', 'revisi'])) {
            return redirect()->back()->with('error', 'SKP tidak dapat diedit.');
        }

        $validated = $request->validate([
            'periode' => 'nullable|string|max:100',
        ]);

        $skp->update($validated);

        return redirect()->route('dosen.skp.show', $skp)
            ->with('success', 'SKP berhasil diperbarui!');
    }

    /**
     * Delete SKP (only draft)
     */
    public function destroy(SkpPegawai $skp)
    {
        $dosen = Auth::user()->dosen;

        if ($skp->dosen_id !== $dosen->id) {
            abort(403);
        }

        if ($skp->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Hanya SKP berstatus draft yang dapat dihapus.');
        }

        $skp->delete();

        return redirect()->route('dosen.skp.index')
            ->with('success', 'SKP berhasil dihapus!');
    }

    /**
     * Submit SKP for approval
     */
    public function ajukan(SkpPegawai $skp)
    {
        $dosen = Auth::user()->dosen;

        if ($skp->dosen_id !== $dosen->id) {
            abort(403);
        }

        if (!in_array($skp->status, ['draft', 'revisi'])) {
            return redirect()->back()
                ->with('error', 'SKP tidak dapat diajukan pada status ini.');
        }

        // Check if has targets
        if ($skp->targetSkp()->count() === 0) {
            return redirect()->back()
                ->with('error', 'Tambahkan minimal 1 target kinerja sebelum mengajukan SKP.');
        }

        $skp->update(['status' => 'diajukan']);

        return redirect()->route('dosen.skp.show', $skp)
            ->with('success', 'SKP berhasil diajukan ke atasan untuk persetujuan.');
    }

    /**
     * Store target SKP
     */
    public function storeTarget(Request $request, SkpPegawai $skp)
    {
        $dosen = Auth::user()->dosen;

        if ($skp->dosen_id !== $dosen->id) {
            abort(403);
        }

        if (!in_array($skp->status, ['draft', 'revisi'])) {
            return redirect()->back()
                ->with('error', 'Target tidak dapat ditambah pada status ini.');
        }

        $validated = $request->validate([
            'uraian_kegiatan' => 'required|string',
            'satuan' => 'nullable|string|max:50',
            'target_kuantitas' => 'nullable|numeric|min:0',
            'target_kualitas' => 'nullable|numeric|min:0|max:100',
            'target_waktu' => 'nullable|numeric|min:0',
            'target_biaya' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ], [
            'uraian_kegiatan.required' => 'Uraian kegiatan wajib diisi',
        ]);

        $urutan = $skp->targetSkp()->max('urutan') + 1;
        $validated['urutan'] = $urutan;

        $skp->targetSkp()->create($validated);

        return redirect()->back()
            ->with('success', 'Target kinerja berhasil ditambahkan!');
    }

    /**
     * Update target SKP
     */
    public function updateTarget(Request $request, TargetSkp $target)
    {
        $dosen = Auth::user()->dosen;
        $skp = $target->skpPegawai;

        if ($skp->dosen_id !== $dosen->id) {
            abort(403);
        }

        // Draft/revisi = edit target, realisasi = input realisasi
        if (!in_array($skp->status, ['draft', 'revisi', 'realisasi'])) {
            return redirect()->back()
                ->with('error', 'Target tidak dapat diedit pada status ini.');
        }

        $rules = [
            'uraian_kegiatan' => 'required|string',
            'satuan' => 'nullable|string|max:50',
            'target_kuantitas' => 'nullable|numeric|min:0',
            'target_kualitas' => 'nullable|numeric|min:0|max:100',
            'target_waktu' => 'nullable|numeric|min:0',
            'target_biaya' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ];

        // Allow realisasi input when status is realisasi
        if ($skp->status === 'realisasi') {
            $rules['realisasi_kuantitas'] = 'nullable|numeric|min:0';
            $rules['realisasi_kualitas'] = 'nullable|numeric|min:0|max:100';
            $rules['realisasi_waktu'] = 'nullable|numeric|min:0';
            $rules['realisasi_biaya'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        $target->update($validated);

        return redirect()->back()
            ->with('success', 'Target kinerja berhasil diperbarui!');
    }

    /**
     * Delete target SKP
     */
    public function destroyTarget(TargetSkp $target)
    {
        $dosen = Auth::user()->dosen;
        $skp = $target->skpPegawai;

        if ($skp->dosen_id !== $dosen->id) {
            abort(403);
        }

        if (!in_array($skp->status, ['draft', 'revisi'])) {
            return redirect()->back()
                ->with('error', 'Target tidak dapat dihapus pada status ini.');
        }

        $target->delete();

        return redirect()->back()
            ->with('success', 'Target kinerja berhasil dihapus!');
    }

    /**
     * Input realisasi capaian
     */
    public function inputRealisasi(Request $request, SkpPegawai $skp)
    {
        $dosen = Auth::user()->dosen;

        if ($skp->dosen_id !== $dosen->id) {
            abort(403);
        }

        if ($skp->status !== 'realisasi') {
            return redirect()->back()
                ->with('error', 'Realisasi hanya dapat diinput pada status Input Realisasi.');
        }

        $validated = $request->validate([
            'targets' => 'required|array',
            'targets.*.id' => 'required|exists:target_skp,id',
            'targets.*.realisasi_kuantitas' => 'nullable|numeric|min:0',
            'targets.*.realisasi_kualitas' => 'nullable|numeric|min:0|max:100',
            'targets.*.realisasi_waktu' => 'nullable|numeric|min:0',
            'targets.*.realisasi_biaya' => 'nullable|numeric|min:0',
        ]);

        foreach ($validated['targets'] as $targetData) {
            $target = TargetSkp::find($targetData['id']);
            if ($target && $target->skp_pegawai_id === $skp->id) {
                $target->update([
                    'realisasi_kuantitas' => $targetData['realisasi_kuantitas'] ?? null,
                    'realisasi_kualitas' => $targetData['realisasi_kualitas'] ?? null,
                    'realisasi_waktu' => $targetData['realisasi_waktu'] ?? null,
                    'realisasi_biaya' => $targetData['realisasi_biaya'] ?? null,
                ]);
            }
        }

        return redirect()->route('dosen.skp.show', $skp)
            ->with('success', 'Realisasi capaian berhasil disimpan!');
    }

    /**
     * Cetak SKP
     */
    public function cetak(SkpPegawai $skp)
    {
        $dosen = Auth::user()->dosen;

        if ($skp->dosen_id !== $dosen->id) {
            abort(403);
        }

        $skp->load(['dosen.programStudi', 'targetSkp']);

        return view('cetak.skp', compact('skp'));
    }
}
