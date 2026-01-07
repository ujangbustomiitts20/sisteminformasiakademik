<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PejabatPenandatangan;
use App\Models\NamaJabatan;
use App\Models\Dosen;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PejabatPenandatanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PejabatPenandatangan::with(['namaJabatan', 'pegawai', 'dosen']);

        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('aktif', $request->status == 'aktif');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhereHas('namaJabatan', fn($q) => $q->where('nama', 'like', "%{$search}%"))
                  ->orWhereHas('pegawai', fn($q) => $q->where('nama', 'like', "%{$search}%"));
            });
        }

        $pejabats = $query->orderBy('urutan')->orderBy('kategori')->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total' => PejabatPenandatangan::count(),
            'aktif' => PejabatPenandatangan::where('aktif', true)->count(),
            'pimpinan' => PejabatPenandatangan::where('kategori', 'pimpinan')->where('aktif', true)->count(),
            'akademik' => PejabatPenandatangan::where('kategori', 'akademik')->where('aktif', true)->count(),
            'keuangan' => PejabatPenandatangan::where('kategori', 'keuangan')->where('aktif', true)->count(),
        ];

        $kategoris = PejabatPenandatangan::KATEGORI;
        $dokumens = PejabatPenandatangan::DOKUMEN;
        $dosens = Dosen::where('status', 'Aktif')->orderBy('nama')->get();
        $pegawais = Pegawai::where('status', 'aktif')->orderBy('nama')->get();
        $namaJabatans = NamaJabatan::aktif()->orderBy('level')->orderBy('urutan')->get();

        return view('admin.pejabat-penandatangan.index', compact(
            'pejabats', 'stats', 'kategoris', 'dokumens', 'dosens', 'pegawais', 'namaJabatans'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'nama_jabatan_id' => ['required', 'exists:nama_jabatan,id'],
            'pegawai_id' => ['nullable', 'exists:pegawai,id', 'required_without:dosen_id'],
            'dosen_id' => ['nullable', 'exists:dosen,id', 'required_without:pegawai_id'],
            'kategori' => ['required', 'string', Rule::in(array_keys(PejabatPenandatangan::KATEGORI))],
            'dokumen_terkait' => ['nullable', 'array'],
            'berlaku_mulai' => ['nullable', 'date'],
            'berlaku_sampai' => ['nullable', 'date', 'after_or_equal:berlaku_mulai'],
            'aktif' => ['boolean'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string'],
            'tanda_tangan' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:1024'],
            'stempel' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:1024'],
        ];
        
        $messages = [
            'nama_jabatan_id.required' => 'Nama Jabatan wajib dipilih.',
            'pegawai_id.required_without' => 'Pilih salah satu: Pegawai atau Dosen.',
            'dosen_id.required_without' => 'Pilih salah satu: Pegawai atau Dosen.',
            'kategori.required' => 'Kategori wajib dipilih.',
        ];
        
        // Manual validation for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            $validator = \Validator::make($request->all(), $rules, $messages);
            
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $validated = $validator->validated();
        } else {
            $validated = $request->validate($rules, $messages);
        }

        // Get jabatan data from NamaJabatan
        $namaJabatan = NamaJabatan::find($validated['nama_jabatan_id']);
        $validated['jabatan'] = $namaJabatan->nama;
        
        // Generate kode from nama jabatan kode + pegawai/dosen identifier
        $baseKode = $namaJabatan->kode ?? strtolower(str_replace(' ', '_', $namaJabatan->nama));
        $kodeCounter = 1;
        $kode = $baseKode;
        while (PejabatPenandatangan::where('kode', $kode)->exists()) {
            $kode = $baseKode . '_' . $kodeCounter;
            $kodeCounter++;
        }
        $validated['kode'] = $kode;

        // Get data from Pegawai or Dosen
        if (!empty($validated['pegawai_id'])) {
            $pegawai = Pegawai::find($validated['pegawai_id']);
            $validated['nama'] = $pegawai->nama;
            $validated['nip'] = $pegawai->nip;
            $validated['pangkat_golongan'] = $pegawai->pangkat ? $pegawai->pangkat . ' (' . $pegawai->golongan . ')' : null;
            $validated['dosen_id'] = null; // Clear dosen if pegawai selected
        } elseif (!empty($validated['dosen_id'])) {
            $dosen = Dosen::find($validated['dosen_id']);
            $validated['nama'] = $dosen->nama;
            $validated['nip'] = $dosen->nip ?? $dosen->nidn;
            $validated['gelar_depan'] = $dosen->gelar_depan;
            $validated['gelar_belakang'] = $dosen->gelar_belakang;
            $validated['pegawai_id'] = null; // Clear pegawai if dosen selected
        }

        // Handle file uploads
        if ($request->hasFile('tanda_tangan')) {
            $validated['tanda_tangan'] = $request->file('tanda_tangan')->store('pejabat/tanda-tangan', 'public');
        }

        if ($request->hasFile('stempel')) {
            $validated['stempel'] = $request->file('stempel')->store('pejabat/stempel', 'public');
        }

        $validated['aktif'] = $request->has('aktif');
        $validated['urutan'] = $validated['urutan'] ?? 0;

        PejabatPenandatangan::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pejabat penandatangan berhasil ditambahkan.'
            ]);
        }

        return redirect()->route('admin.pejabat-penandatangan.index')
            ->with('success', 'Pejabat penandatangan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PejabatPenandatangan $pejabatPenandatangan)
    {
        $kategoris = PejabatPenandatangan::KATEGORI;
        $dokumens = PejabatPenandatangan::DOKUMEN;

        return view('admin.pejabat-penandatangan.show', compact('pejabatPenandatangan', 'kategoris', 'dokumens'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PejabatPenandatangan $pejabatPenandatangan)
    {
        $pejabatPenandatangan->load(['namaJabatan', 'pegawai', 'dosen']);
        $kategoris = PejabatPenandatangan::KATEGORI;
        $dokumens = PejabatPenandatangan::DOKUMEN;
        $dosens = Dosen::where('status', 'Aktif')->orderBy('nama')->get();
        $pegawais = Pegawai::where('status', 'aktif')->orderBy('nama')->get();
        $namaJabatans = NamaJabatan::aktif()->orderBy('level')->orderBy('urutan')->get();

        return view('admin.pejabat-penandatangan.edit', compact(
            'pejabatPenandatangan', 'kategoris', 'dokumens', 'dosens', 'pegawais', 'namaJabatans'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PejabatPenandatangan $pejabatPenandatangan)
    {
        $validated = $request->validate([
            'nama_jabatan_id' => ['required', 'exists:nama_jabatan,id'],
            'pegawai_id' => ['nullable', 'exists:pegawai,id', 'required_without:dosen_id'],
            'dosen_id' => ['nullable', 'exists:dosen,id', 'required_without:pegawai_id'],
            'kategori' => ['required', 'string', Rule::in(array_keys(PejabatPenandatangan::KATEGORI))],
            'dokumen_terkait' => ['nullable', 'array'],
            'berlaku_mulai' => ['nullable', 'date'],
            'berlaku_sampai' => ['nullable', 'date', 'after_or_equal:berlaku_mulai'],
            'aktif' => ['boolean'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string'],
            'tanda_tangan' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:1024'],
            'stempel' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:1024'],
        ], [
            'nama_jabatan_id.required' => 'Nama Jabatan wajib dipilih.',
            'pegawai_id.required_without' => 'Pilih salah satu: Pegawai atau Dosen.',
            'dosen_id.required_without' => 'Pilih salah satu: Pegawai atau Dosen.',
        ]);

        // Get jabatan name from NamaJabatan
        $namaJabatan = NamaJabatan::find($validated['nama_jabatan_id']);
        $validated['jabatan'] = $namaJabatan->nama;
        
        // Re-generate kode if nama_jabatan changed
        if ($pejabatPenandatangan->nama_jabatan_id != $validated['nama_jabatan_id']) {
            $baseKode = $namaJabatan->kode ?? strtolower(str_replace(' ', '_', $namaJabatan->nama));
            $kodeCounter = 1;
            $kode = $baseKode;
            while (PejabatPenandatangan::where('kode', $kode)->where('id', '!=', $pejabatPenandatangan->id)->exists()) {
                $kode = $baseKode . '_' . $kodeCounter;
                $kodeCounter++;
            }
            $validated['kode'] = $kode;
        }

        // Get data from Pegawai or Dosen
        if (!empty($validated['pegawai_id'])) {
            $pegawai = Pegawai::find($validated['pegawai_id']);
            $validated['nama'] = $pegawai->nama;
            $validated['nip'] = $pegawai->nip;
            $validated['pangkat_golongan'] = $pegawai->pangkat ? $pegawai->pangkat . ' (' . $pegawai->golongan . ')' : null;
            $validated['gelar_depan'] = null;
            $validated['gelar_belakang'] = null;
            $validated['dosen_id'] = null; // Clear dosen if pegawai selected
        } elseif (!empty($validated['dosen_id'])) {
            $dosen = Dosen::find($validated['dosen_id']);
            $validated['nama'] = $dosen->nama;
            $validated['nip'] = $dosen->nip ?? $dosen->nidn;
            $validated['gelar_depan'] = $dosen->gelar_depan;
            $validated['gelar_belakang'] = $dosen->gelar_belakang;
            $validated['pangkat_golongan'] = null;
            $validated['pegawai_id'] = null; // Clear pegawai if dosen selected
        }

        // Handle file uploads
        if ($request->hasFile('tanda_tangan')) {
            // Delete old file
            if ($pejabatPenandatangan->tanda_tangan) {
                Storage::disk('public')->delete($pejabatPenandatangan->tanda_tangan);
            }
            $validated['tanda_tangan'] = $request->file('tanda_tangan')->store('pejabat/tanda-tangan', 'public');
        }

        if ($request->hasFile('stempel')) {
            // Delete old file
            if ($pejabatPenandatangan->stempel) {
                Storage::disk('public')->delete($pejabatPenandatangan->stempel);
            }
            $validated['stempel'] = $request->file('stempel')->store('pejabat/stempel', 'public');
        }

        $validated['aktif'] = $request->has('aktif');
        $validated['urutan'] = $validated['urutan'] ?? 0;

        $pejabatPenandatangan->update($validated);

        return redirect()->route('admin.pejabat-penandatangan.index')
            ->with('success', 'Pejabat penandatangan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PejabatPenandatangan $pejabatPenandatangan)
    {
        // Delete files
        if ($pejabatPenandatangan->tanda_tangan) {
            Storage::disk('public')->delete($pejabatPenandatangan->tanda_tangan);
        }
        if ($pejabatPenandatangan->stempel) {
            Storage::disk('public')->delete($pejabatPenandatangan->stempel);
        }

        $pejabatPenandatangan->delete();

        return redirect()->route('admin.pejabat-penandatangan.index')
            ->with('success', 'Pejabat penandatangan berhasil dihapus.');
    }

    /**
     * Delete tanda tangan file
     */
    public function deleteTandaTangan(PejabatPenandatangan $pejabatPenandatangan)
    {
        if ($pejabatPenandatangan->tanda_tangan) {
            Storage::disk('public')->delete($pejabatPenandatangan->tanda_tangan);
            $pejabatPenandatangan->update(['tanda_tangan' => null]);
        }

        return redirect()->back()->with('success', 'Tanda tangan berhasil dihapus.');
    }

    /**
     * Delete stempel file
     */
    public function deleteStempel(PejabatPenandatangan $pejabatPenandatangan)
    {
        if ($pejabatPenandatangan->stempel) {
            Storage::disk('public')->delete($pejabatPenandatangan->stempel);
            $pejabatPenandatangan->update(['stempel' => null]);
        }

        return redirect()->back()->with('success', 'Stempel berhasil dihapus.');
    }

    /**
     * Toggle status aktif
     */
    public function toggleStatus(PejabatPenandatangan $pejabatPenandatangan)
    {
        $pejabatPenandatangan->update(['aktif' => !$pejabatPenandatangan->aktif]);

        $status = $pejabatPenandatangan->aktif ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Pejabat berhasil {$status}.");
    }
}
