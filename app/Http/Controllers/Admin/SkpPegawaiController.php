<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SkpPegawai;
use App\Models\TargetSkp;
use App\Models\Dosen;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SkpPegawaiController extends Controller
{
    /**
     * Display a listing of SKP
     */
    public function index(Request $request)
    {
        $query = SkpPegawai::with(['dosen', 'pegawai']);

        // Filter tahun
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter predikat
        if ($request->filled('predikat')) {
            $query->where('predikat', $request->predikat);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_skp', 'like', "%{$search}%")
                  ->orWhereHas('dosen', fn($q) => $q->where('nama', 'like', "%{$search}%"))
                  ->orWhereHas('pegawai', fn($q) => $q->where('nama', 'like', "%{$search}%"));
            });
        }

        $skpList = $query->orderBy('tahun', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Stats
        $stats = [
            'total' => SkpPegawai::count(),
            'draft' => SkpPegawai::where('status', 'draft')->count(),
            'diajukan' => SkpPegawai::where('status', 'diajukan')->count(),
            'dinilai' => SkpPegawai::where('status', 'dinilai')->count(),
            'final' => SkpPegawai::where('status', 'final')->count(),
        ];

        // Get tahun list for filter
        $tahunList = SkpPegawai::distinct()->pluck('tahun')->sort()->reverse();
        if ($tahunList->isEmpty()) {
            $tahunList = collect([date('Y'), date('Y') - 1]);
        }

        // Get dosen list for create modal
        $dosenList = Dosen::where('status', 'aktif')->orderBy('nama')->get();

        return view('admin.skp.index', compact('skpList', 'stats', 'tahunList', 'dosenList'));
    }

    /**
     * Store a newly created SKP
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dosen_id' => 'required|exists:dosen,id',
            'tahun' => 'required|integer|min:2020|max:2100',
            'periode' => 'nullable|string|max:100',
            'tanggal_skp' => 'nullable|date',
            'jabatan' => 'nullable|string|max:100',
            'unit_kerja' => 'nullable|string|max:100',
            'atasan_penilai' => 'nullable|string|max:100',
        ], [
            'dosen_id.required' => 'Dosen wajib dipilih',
            'tahun.required' => 'Tahun wajib diisi',
        ]);

        // Check if SKP already exists for this dosen and tahun/periode
        $exists = SkpPegawai::where('dosen_id', $validated['dosen_id'])
            ->where('tahun', $validated['tahun'])
            ->where('periode', $validated['periode'] ?? null)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'SKP untuk dosen dan periode ini sudah ada!');
        }

        $skp = SkpPegawai::create([
            'dosen_id' => $validated['dosen_id'],
            'tahun' => $validated['tahun'],
            'periode' => $validated['periode'],
            'tanggal_skp' => $validated['tanggal_skp'],
            'status' => 'draft',
        ]);

        return redirect()->route('kepegawaian.skp.show', $skp)
            ->with('success', 'SKP berhasil dibuat! Silakan tambahkan target kinerja.');
    }

    /**
     * Display the specified SKP
     */
    public function show(SkpPegawai $skp)
    {
        $skp->load(['dosen.programStudi', 'pegawai', 'targetSkp', 'pejabatPenilai', 'atasanPenilai']);
        
        return view('admin.skp.show', compact('skp'));
    }

    /**
     * Show the form for editing SKP
     */
    public function edit(SkpPegawai $skp)
    {
        $skp->load(['dosen', 'targetSkp']);
        $dosenList = Dosen::where('status', 'aktif')->orderBy('nama')->get();
        
        return view('admin.skp.edit', compact('skp', 'dosenList'));
    }

    /**
     * Update the specified SKP
     */
    public function update(Request $request, SkpPegawai $skp)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer|min:2020|max:2100',
            'periode' => 'nullable|string|max:100',
            'tanggal_skp' => 'nullable|date',
            'jabatan' => 'nullable|string|max:100',
            'unit_kerja' => 'nullable|string|max:100',
            'atasan_penilai' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
        ]);

        $skp->update($validated);

        return redirect()->route('kepegawaian.skp.show', $skp)
            ->with('success', 'SKP berhasil diperbarui!');
    }

    /**
     * Remove the specified SKP
     * Admin can force delete final SKP with confirmation
     */
    public function destroy(Request $request, SkpPegawai $skp)
    {
        $isFinal = $skp->status === 'final';
        $forceDelete = $request->has('force') && $request->force === 'true';

        // Jika SKP final dan tidak ada konfirmasi force delete
        if ($isFinal && !$forceDelete) {
            return redirect()->back()->with('error', 'SKP yang sudah final memerlukan konfirmasi khusus untuk dihapus!');
        }

        // Log penghapusan SKP final untuk audit
        if ($isFinal) {
            \Log::warning('SKP Final dihapus oleh admin', [
                'skp_id' => $skp->id,
                'no_skp' => $skp->no_skp,
                'nama_pegawai' => $skp->nama_pegawai,
                'tahun' => $skp->tahun,
                'deleted_by' => auth()->user()->name ?? auth()->id(),
                'deleted_at' => now()->toDateTimeString(),
            ]);
        }

        $noSkp = $skp->no_skp;
        $skp->delete();

        $message = $isFinal 
            ? "SKP {$noSkp} (FINAL) berhasil dihapus!"
            : "SKP {$noSkp} berhasil dihapus!";

        return redirect()->route('kepegawaian.skp.index')
            ->with('success', $message);
    }

    /**
     * Input penilaian SKP
     */
    public function penilaian(Request $request, SkpPegawai $skp)
    {
        $validated = $request->validate([
            'nilai_skp' => 'required|numeric|min:0|max:100',
            'nilai_perilaku' => 'required|numeric|min:0|max:100',
            'orientasi_pelayanan' => 'nullable|numeric|min:0|max:100',
            'integritas' => 'nullable|numeric|min:0|max:100',
            'komitmen' => 'nullable|numeric|min:0|max:100',
            'disiplin' => 'nullable|numeric|min:0|max:100',
            'kerjasama' => 'nullable|numeric|min:0|max:100',
            'kepemimpinan' => 'nullable|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ], [
            'nilai_skp.required' => 'Nilai SKP wajib diisi',
            'nilai_perilaku.required' => 'Nilai Perilaku wajib diisi',
        ]);

        // Jika status belum final, set ke dinilai
        if ($skp->status !== 'final') {
            $validated['status'] = 'dinilai';
        }
        $validated['tanggal_penilaian'] = now();

        $skp->update($validated);

        return redirect()->route('kepegawaian.skp.show', $skp)
            ->with('success', 'Penilaian SKP berhasil disimpan!');
    }

    /**
     * Update status SKP
     */
    public function updateStatus(Request $request, SkpPegawai $skp)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(SkpPegawai::STATUS))],
            'catatan' => 'nullable|string',
        ]);

        $updateData = ['status' => $validated['status']];
        
        // Add catatan for revisi
        if ($validated['status'] === 'revisi' && !empty($validated['catatan'])) {
            $updateData['catatan'] = $validated['catatan'];
        }

        $skp->update($updateData);

        $statusLabel = SkpPegawai::STATUS[$validated['status']] ?? $validated['status'];
        return redirect()->back()
            ->with('success', "Status SKP berhasil diubah menjadi: {$statusLabel}");
    }

    /**
     * Approve target SKP (by Kaprodi/Dekan/Admin)
     */
    public function approve(SkpPegawai $skp)
    {
        if ($skp->status !== 'diajukan') {
            return redirect()->back()->with('error', 'SKP tidak dalam status diajukan!');
        }

        $skp->update(['status' => 'disetujui']);

        return redirect()->back()
            ->with('success', 'Target SKP berhasil disetujui!');
    }

    /**
     * Return SKP for revision
     */
    public function revisi(Request $request, SkpPegawai $skp)
    {
        if ($skp->status !== 'diajukan') {
            return redirect()->back()->with('error', 'SKP tidak dalam status diajukan!');
        }

        $validated = $request->validate([
            'catatan' => 'required|string',
        ], [
            'catatan.required' => 'Catatan revisi wajib diisi',
        ]);

        $skp->update([
            'status' => 'revisi',
            'catatan' => $validated['catatan'],
        ]);

        return redirect()->back()
            ->with('success', 'SKP dikembalikan untuk revisi!');
    }

    /**
     * Open for realisasi input
     */
    public function bukaRealisasi(SkpPegawai $skp)
    {
        if ($skp->status !== 'disetujui') {
            return redirect()->back()->with('error', 'SKP harus disetujui terlebih dahulu!');
        }

        $skp->update(['status' => 'realisasi']);

        return redirect()->back()
            ->with('success', 'SKP dibuka untuk input realisasi oleh pegawai!');
    }

    /**
     * Store target SKP
     */
    public function storeTarget(Request $request, SkpPegawai $skp)
    {
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
            ->with('success', 'Target SKP berhasil ditambahkan!');
    }

    /**
     * Update target SKP
     */
    public function updateTarget(Request $request, TargetSkp $target)
    {
        $validated = $request->validate([
            'uraian_kegiatan' => 'required|string',
            'satuan' => 'nullable|string|max:50',
            'target_kuantitas' => 'nullable|numeric|min:0',
            'target_kualitas' => 'nullable|numeric|min:0|max:100',
            'target_waktu' => 'nullable|numeric|min:0',
            'target_biaya' => 'nullable|numeric|min:0',
            'realisasi_kuantitas' => 'nullable|numeric|min:0',
            'realisasi_kualitas' => 'nullable|numeric|min:0|max:100',
            'realisasi_waktu' => 'nullable|numeric|min:0',
            'realisasi_biaya' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $target->update($validated);

        return redirect()->back()
            ->with('success', 'Target SKP berhasil diperbarui!');
    }

    /**
     * Delete target SKP
     */
    public function destroyTarget(TargetSkp $target)
    {
        $skp = $target->skpPegawai;
        $target->delete();

        return redirect()->route('kepegawaian.skp.show', $skp)
            ->with('success', 'Target SKP berhasil dihapus!');
    }

    /**
     * Cetak SKP
     */
    public function cetak(SkpPegawai $skp)
    {
        $skp->load(['dosen.programStudi', 'targetSkp']);
        
        return view('cetak.skp', compact('skp'));
    }
}
