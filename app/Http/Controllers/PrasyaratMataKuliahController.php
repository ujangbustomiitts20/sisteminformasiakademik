<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\PrasyaratMataKuliah;
use Illuminate\Http\Request;

class PrasyaratMataKuliahController extends Controller
{
    /**
     * Tampilkan daftar mata kuliah dengan prasyaratnya
     */
    public function index(Request $request)
    {
        $query = MataKuliah::with(['programStudi', 'prasyarat'])
            ->withCount('prasyaratDetail');

        // Filter by program studi
        if ($request->filled('program_studi_id')) {
            $query->where('program_studi_id', $request->program_studi_id);
        }

        // Filter by semester
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        // Filter hanya yang punya prasyarat
        if ($request->filled('has_prasyarat')) {
            $query->has('prasyaratDetail');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $mataKuliah = $query->orderBy('semester')->orderBy('kode')->paginate(20);
        $programStudi = \App\Models\ProgramStudi::orderBy('nama')->get();

        return view('akademik.prasyarat.index', compact('mataKuliah', 'programStudi'));
    }

    /**
     * Form untuk mengelola prasyarat mata kuliah tertentu
     */
    public function edit(MataKuliah $mataKuliah)
    {
        $mataKuliah->load(['programStudi', 'prasyaratDetail.mataKuliahPrasyarat']);
        
        // Ambil semua MK dari prodi yang sama dan semester lebih rendah
        $mataKuliahTersedia = MataKuliah::where('program_studi_id', $mataKuliah->program_studi_id)
            ->where('id', '!=', $mataKuliah->id)
            ->where('semester', '<', $mataKuliah->semester) // Prasyarat harus dari semester lebih rendah
            ->orderBy('semester')
            ->orderBy('kode')
            ->get();

        return view('akademik.prasyarat.edit', compact('mataKuliah', 'mataKuliahTersedia'));
    }

    /**
     * Simpan/update prasyarat
     */
    public function update(Request $request, MataKuliah $mataKuliah)
    {
        $request->validate([
            'prasyarat' => 'nullable|array',
            'prasyarat.*.mata_kuliah_prasyarat_id' => 'required|exists:mata_kuliah,id',
            'prasyarat.*.jenis_prasyarat' => 'required|in:wajib,pilihan',
            'prasyarat.*.nilai_minimal' => 'nullable|in:A,A-,B+,B,B-,C+,C,D',
        ]);

        // Hapus prasyarat lama
        PrasyaratMataKuliah::where('mata_kuliah_id', $mataKuliah->id)->delete();

        // Simpan prasyarat baru
        if ($request->filled('prasyarat')) {
            foreach ($request->prasyarat as $p) {
                // Validasi: prasyarat harus dari semester lebih rendah
                $mkPrasyarat = MataKuliah::find($p['mata_kuliah_prasyarat_id']);
                if ($mkPrasyarat && $mkPrasyarat->semester < $mataKuliah->semester) {
                    PrasyaratMataKuliah::create([
                        'mata_kuliah_id' => $mataKuliah->id,
                        'mata_kuliah_prasyarat_id' => $p['mata_kuliah_prasyarat_id'],
                        'jenis_prasyarat' => $p['jenis_prasyarat'],
                        'nilai_minimal' => $p['nilai_minimal'] ?? 'D',
                    ]);
                }
            }
        }

        return redirect()->route('prasyarat.index')
            ->with('success', 'Prasyarat mata kuliah ' . $mataKuliah->nama . ' berhasil diupdate.');
    }

    /**
     * Tambah prasyarat via AJAX
     */
    public function addPrasyarat(Request $request, MataKuliah $mataKuliah)
    {
        $request->validate([
            'mata_kuliah_prasyarat_id' => 'required|exists:mata_kuliah,id',
            'jenis_prasyarat' => 'required|in:wajib,pilihan',
            'nilai_minimal' => 'nullable|in:A,A-,B+,B,B-,C+,C,D',
        ]);

        // Cek apakah sudah ada
        $exists = PrasyaratMataKuliah::where('mata_kuliah_id', $mataKuliah->id)
            ->where('mata_kuliah_prasyarat_id', $request->mata_kuliah_prasyarat_id)
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Prasyarat sudah ada'], 422);
        }

        // Validasi semester
        $mkPrasyarat = MataKuliah::find($request->mata_kuliah_prasyarat_id);
        if ($mkPrasyarat->semester >= $mataKuliah->semester) {
            return response()->json(['error' => 'Prasyarat harus dari semester yang lebih rendah'], 422);
        }

        $prasyarat = PrasyaratMataKuliah::create([
            'mata_kuliah_id' => $mataKuliah->id,
            'mata_kuliah_prasyarat_id' => $request->mata_kuliah_prasyarat_id,
            'jenis_prasyarat' => $request->jenis_prasyarat,
            'nilai_minimal' => $request->nilai_minimal ?? 'D',
        ]);

        return response()->json([
            'success' => true,
            'prasyarat' => $prasyarat->load('mataKuliahPrasyarat')
        ]);
    }

    /**
     * Hapus prasyarat
     */
    public function deletePrasyarat(PrasyaratMataKuliah $prasyarat)
    {
        $mataKuliah = $prasyarat->mataKuliah;
        $prasyarat->delete();

        return redirect()->back()
            ->with('success', 'Prasyarat berhasil dihapus.');
    }

    /**
     * Lihat struktur kurikulum dengan prasyarat (tampilan visual)
     */
    public function kurikulum(Request $request)
    {
        $programStudiId = $request->program_studi_id;
        $programStudi = \App\Models\ProgramStudi::orderBy('nama')->get();
        
        $kurikulum = [];
        
        if ($programStudiId) {
            // Ambil semua MK dari prodi tersebut, group by semester
            $mataKuliah = MataKuliah::with(['prasyarat'])
                ->where('program_studi_id', $programStudiId)
                ->orderBy('semester')
                ->orderBy('kode')
                ->get()
                ->groupBy('semester');
            
            $kurikulum = $mataKuliah;
        }

        return view('akademik.prasyarat.kurikulum', compact('kurikulum', 'programStudi', 'programStudiId'));
    }

    /**
     * Cek prasyarat untuk mahasiswa (API untuk KRS)
     */
    public function cekPrasyaratMahasiswa(Request $request)
    {
        $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
        ]);

        $mataKuliah = MataKuliah::findOrFail($request->mata_kuliah_id);
        $hasil = $mataKuliah->cekPrasyarat($request->mahasiswa_id);

        return response()->json($hasil);
    }

    /**
     * Copy prasyarat dari MK lain
     */
    public function copyPrasyarat(Request $request, MataKuliah $mataKuliah)
    {
        $request->validate([
            'source_mata_kuliah_id' => 'required|exists:mata_kuliah,id|different:' . $mataKuliah->id,
        ]);

        $source = MataKuliah::with('prasyaratDetail')->find($request->source_mata_kuliah_id);
        
        // Hapus prasyarat lama
        PrasyaratMataKuliah::where('mata_kuliah_id', $mataKuliah->id)->delete();

        // Copy prasyarat
        foreach ($source->prasyaratDetail as $p) {
            // Validasi semester
            $mkPrasyarat = MataKuliah::find($p->mata_kuliah_prasyarat_id);
            if ($mkPrasyarat && $mkPrasyarat->semester < $mataKuliah->semester) {
                PrasyaratMataKuliah::create([
                    'mata_kuliah_id' => $mataKuliah->id,
                    'mata_kuliah_prasyarat_id' => $p->mata_kuliah_prasyarat_id,
                    'jenis_prasyarat' => $p->jenis_prasyarat,
                    'nilai_minimal' => $p->nilai_minimal,
                ]);
            }
        }

        return redirect()->back()
            ->with('success', 'Prasyarat berhasil dicopy dari ' . $source->nama);
    }
}
