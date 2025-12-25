<?php

namespace App\Http\Controllers;

use App\Models\Kurikulum;
use App\Models\KurikulumMataKuliah;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class KurikulumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Kurikulum::with('programStudi');

        if ($request->filled('prodi')) {
            $query->where('program_studi_id', $request->prodi);
        }

        if ($request->filled('status')) {
            $query->where('is_aktif', $request->status === 'aktif');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $kurikulums = $query->orderBy('tahun_mulai', 'desc')->paginate(10);
        $prodis = ProgramStudi::orderBy('nama')->get();

        return view('akademik.kurikulum.index', compact('kurikulums', 'prodis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $prodis = ProgramStudi::orderBy('nama')->get();
        return view('akademik.kurikulum.create', compact('prodis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_studi_id' => 'required|exists:program_studi,id',
            'kode' => 'required|string|max:20|unique:kurikulum,kode',
            'nama' => 'required|string|max:255',
            'tahun_mulai' => 'required|integer|min:2000|max:2100',
            'tahun_selesai' => 'nullable|integer|min:2000|max:2100|gte:tahun_mulai',
            'total_sks_wajib' => 'required|integer|min:0',
            'total_sks_pilihan' => 'required|integer|min:0',
            'total_sks_lulus' => 'required|integer|min:1',
            'minimal_semester' => 'required|integer|min:1',
            'maksimal_semester' => 'required|integer|min:1|gte:minimal_semester',
            'deskripsi' => 'nullable|string',
            'is_aktif' => 'boolean',
        ]);

        $validated['is_aktif'] = $request->has('is_aktif');

        Kurikulum::create($validated);

        return redirect()->route('kurikulum.index')
            ->with('success', 'Kurikulum berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kurikulum $kurikulum)
    {
        $kurikulum->load(['programStudi', 'mataKuliah']);
        
        // Group MK by semester
        $mkBySemester = [];
        for ($i = 1; $i <= 8; $i++) {
            $mkBySemester[$i] = $kurikulum->mataKuliah()
                ->wherePivot('semester_rekomendasi', $i)
                ->orderBy('kode')
                ->get();
        }

        // Hitung total SKS
        $totalSks = $kurikulum->hitungTotalSks();

        return view('akademik.kurikulum.show', compact('kurikulum', 'mkBySemester', 'totalSks'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kurikulum $kurikulum)
    {
        $prodis = ProgramStudi::orderBy('nama')->get();
        return view('akademik.kurikulum.edit', compact('kurikulum', 'prodis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kurikulum $kurikulum)
    {
        $validated = $request->validate([
            'program_studi_id' => 'required|exists:program_studi,id',
            'kode' => 'required|string|max:20|unique:kurikulum,kode,' . $kurikulum->id,
            'nama' => 'required|string|max:255',
            'tahun_mulai' => 'required|integer|min:2000|max:2100',
            'tahun_selesai' => 'nullable|integer|min:2000|max:2100|gte:tahun_mulai',
            'total_sks_wajib' => 'required|integer|min:0',
            'total_sks_pilihan' => 'required|integer|min:0',
            'total_sks_lulus' => 'required|integer|min:1',
            'minimal_semester' => 'required|integer|min:1',
            'maksimal_semester' => 'required|integer|min:1|gte:minimal_semester',
            'deskripsi' => 'nullable|string',
            'is_aktif' => 'boolean',
        ]);

        $validated['is_aktif'] = $request->has('is_aktif');

        $kurikulum->update($validated);

        return redirect()->route('kurikulum.index')
            ->with('success', 'Kurikulum berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kurikulum $kurikulum)
    {
        // Cek apakah ada mahasiswa yang menggunakan kurikulum ini
        if ($kurikulum->mahasiswa()->count() > 0) {
            return redirect()->route('kurikulum.index')
                ->with('error', 'Tidak dapat menghapus kurikulum yang masih digunakan oleh mahasiswa!');
        }

        $kurikulum->delete();

        return redirect()->route('kurikulum.index')
            ->with('success', 'Kurikulum berhasil dihapus!');
    }

    /**
     * Kelola mata kuliah dalam kurikulum
     */
    public function mataKuliah(Kurikulum $kurikulum)
    {
        $kurikulum->load(['programStudi', 'mataKuliah']);
        
        // Ambil MK yang belum ada di kurikulum ini
        $availableMk = MataKuliah::where('program_studi_id', $kurikulum->program_studi_id)
            ->whereNotIn('id', $kurikulum->mataKuliah->pluck('id'))
            ->orderBy('semester')
            ->orderBy('kode')
            ->get();

        // Group MK kurikulum by semester
        $mkBySemester = [];
        for ($i = 1; $i <= 8; $i++) {
            $mkBySemester[$i] = $kurikulum->mataKuliah()
                ->wherePivot('semester_rekomendasi', $i)
                ->orderBy('kode')
                ->get();
        }

        return view('akademik.kurikulum.mata-kuliah', compact('kurikulum', 'availableMk', 'mkBySemester'));
    }

    /**
     * Tambah MK ke kurikulum
     */
    public function addMataKuliah(Request $request, Kurikulum $kurikulum)
    {
        $validated = $request->validate([
            'mata_kuliah_id' => 'required|string',
            'semester_rekomendasi' => 'required|integer|min:1|max:14',
            'kategori' => 'required|in:Wajib,Pilihan,Wajib Prodi,Pilihan Prodi,MKU',
        ]);

        // Decode hashid to id
        $mataKuliahId = MataKuliah::decodeHashid($validated['mata_kuliah_id']);
        if (!$mataKuliahId) {
            return response()->json(['error' => 'Mata kuliah tidak valid!'], 422);
        }

        // Cek apakah sudah ada
        $exists = $kurikulum->mataKuliah()->where('mata_kuliah_id', $mataKuliahId)->exists();
        if ($exists) {
            return response()->json(['error' => 'Mata kuliah sudah ada di kurikulum ini!'], 422);
        }

        $kurikulum->mataKuliah()->attach($mataKuliahId, [
            'semester_rekomendasi' => $validated['semester_rekomendasi'],
            'kategori' => $validated['kategori'],
        ]);

        // Update total SKS
        $mk = MataKuliah::find($mataKuliahId);
        if (in_array($validated['kategori'], ['Wajib', 'Wajib Prodi', 'MKU'])) {
            $kurikulum->increment('total_sks_wajib', $mk->sks);
        } else {
            $kurikulum->increment('total_sks_pilihan', $mk->sks);
        }

        return response()->json(['success' => true, 'message' => 'Mata kuliah berhasil ditambahkan!']);
    }

    /**
     * Hapus MK dari kurikulum
     */
    public function removeMataKuliah(Kurikulum $kurikulum, MataKuliah $mataKuliah)
    {
        $pivot = $kurikulum->mataKuliah()->where('mata_kuliah_id', $mataKuliah->id)->first();
        
        if ($pivot) {
            // Update total SKS
            if (in_array($pivot->pivot->kategori, ['Wajib', 'Wajib Prodi', 'MKU'])) {
                $kurikulum->decrement('total_sks_wajib', $mataKuliah->sks);
            } else {
                $kurikulum->decrement('total_sks_pilihan', $mataKuliah->sks);
            }
            
            $kurikulum->mataKuliah()->detach($mataKuliah->id);
        }

        return response()->json(['success' => true, 'message' => 'Mata kuliah berhasil dihapus dari kurikulum!']);
    }

    /**
     * Update MK dalam kurikulum
     */
    public function updateMataKuliah(Request $request, Kurikulum $kurikulum, MataKuliah $mataKuliah)
    {
        $validated = $request->validate([
            'semester_rekomendasi' => 'required|integer|min:1|max:14',
            'kategori' => 'required|in:Wajib,Pilihan,Wajib Prodi,Pilihan Prodi,MKU',
        ]);

        $oldPivot = $kurikulum->mataKuliah()->where('mata_kuliah_id', $mataKuliah->id)->first();
        
        if ($oldPivot) {
            // Update total SKS jika kategori berubah
            $oldKategori = $oldPivot->pivot->kategori;
            $newKategori = $validated['kategori'];
            
            $wasWajib = in_array($oldKategori, ['Wajib', 'Wajib Prodi', 'MKU']);
            $isWajib = in_array($newKategori, ['Wajib', 'Wajib Prodi', 'MKU']);
            
            if ($wasWajib && !$isWajib) {
                $kurikulum->decrement('total_sks_wajib', $mataKuliah->sks);
                $kurikulum->increment('total_sks_pilihan', $mataKuliah->sks);
            } elseif (!$wasWajib && $isWajib) {
                $kurikulum->decrement('total_sks_pilihan', $mataKuliah->sks);
                $kurikulum->increment('total_sks_wajib', $mataKuliah->sks);
            }
        }

        $kurikulum->mataKuliah()->updateExistingPivot($mataKuliah->id, $validated);

        return response()->json(['success' => true, 'message' => 'Mata kuliah berhasil diperbarui!']);
    }

    /**
     * Copy kurikulum
     */
    public function copy(Kurikulum $kurikulum)
    {
        $newKurikulum = $kurikulum->replicate();
        $newKurikulum->kode = $kurikulum->kode . '-COPY';
        $newKurikulum->nama = $kurikulum->nama . ' (Copy)';
        $newKurikulum->is_aktif = false;
        $newKurikulum->save();

        // Copy mata kuliah
        foreach ($kurikulum->mataKuliah as $mk) {
            $newKurikulum->mataKuliah()->attach($mk->id, [
                'semester_rekomendasi' => $mk->pivot->semester_rekomendasi,
                'kategori' => $mk->pivot->kategori,
            ]);
        }

        return redirect()->route('kurikulum.edit', $newKurikulum)
            ->with('success', 'Kurikulum berhasil di-copy! Silakan edit sesuai kebutuhan.');
    }

    /**
     * Set kurikulum sebagai aktif
     */
    public function setAktif(Kurikulum $kurikulum)
    {
        // Nonaktifkan kurikulum lain di prodi yang sama
        Kurikulum::where('program_studi_id', $kurikulum->program_studi_id)
            ->where('id', '!=', $kurikulum->id)
            ->update(['is_aktif' => false]);

        $kurikulum->update(['is_aktif' => true]);

        return redirect()->route('kurikulum.index')
            ->with('success', 'Kurikulum berhasil diaktifkan!');
    }
}
