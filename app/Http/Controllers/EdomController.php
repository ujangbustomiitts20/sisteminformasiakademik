<?php

namespace App\Http\Controllers;

use App\Models\PeriodeEdom;
use App\Models\PertanyaanEdom;
use App\Models\JawabanEdom;
use App\Models\RekapEdom;
use App\Models\KomentarEdom;
use App\Models\TahunAkademik;
use App\Models\Dosen;
use App\Models\JadwalKuliah;
use Illuminate\Http\Request;

class EdomController extends Controller
{
    /**
     * Display a listing of periode EDOM (Admin)
     */
    public function index()
    {
        $periodes = PeriodeEdom::with('tahunAkademik')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.edom.index', compact('periodes'));
    }

    /**
     * Show the form for creating a new periode EDOM
     */
    public function create()
    {
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        return view('admin.edom.create', compact('tahunAkademik'));
    }

    /**
     * Store a newly created periode EDOM
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'required|in:draft,aktif,selesai',
            'deskripsi' => 'nullable|string',
        ]);

        PeriodeEdom::create($request->all());

        return redirect()->route('admin.edom.index')
            ->with('success', 'Periode EDOM berhasil dibuat!');
    }

    /**
     * Display the specified periode EDOM
     */
    public function show(PeriodeEdom $edom)
    {
        $edom->load('tahunAkademik');
        
        $rekap = RekapEdom::where('periode_edom_id', $edom->id)
            ->with(['dosen', 'jadwalKuliah.mataKuliah'])
            ->orderBy('rata_rata_total', 'desc')
            ->paginate(20);

        $statistik = [
            'total_responden' => JawabanEdom::where('periode_edom_id', $edom->id)
                ->distinct('mahasiswa_id')
                ->count('mahasiswa_id'),
            'total_dosen' => RekapEdom::where('periode_edom_id', $edom->id)
                ->distinct('dosen_id')
                ->count('dosen_id'),
            'rata_rata_total' => RekapEdom::where('periode_edom_id', $edom->id)->avg('rata_rata_total'),
        ];

        return view('admin.edom.show', compact('edom', 'rekap', 'statistik'));
    }

    /**
     * Show the form for editing periode EDOM
     */
    public function edit(PeriodeEdom $edom)
    {
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        return view('admin.edom.edit', compact('edom', 'tahunAkademik'));
    }

    /**
     * Update the specified periode EDOM
     */
    public function update(Request $request, PeriodeEdom $edom)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'required|in:draft,aktif,selesai',
            'deskripsi' => 'nullable|string',
        ]);

        $edom->update($request->all());

        return redirect()->route('admin.edom.index')
            ->with('success', 'Periode EDOM berhasil diperbarui!');
    }

    /**
     * Remove the specified periode EDOM
     */
    public function destroy(PeriodeEdom $edom)
    {
        $edom->delete();

        return redirect()->route('admin.edom.index')
            ->with('success', 'Periode EDOM berhasil dihapus!');
    }

    /**
     * Hitung ulang rekap untuk periode tertentu
     */
    public function hitungRekap(PeriodeEdom $edom)
    {
        // Ambil semua kombinasi dosen-jadwal yang ada jawabannya
        $kombinasi = JawabanEdom::where('periode_edom_id', $edom->id)
            ->select('dosen_id', 'jadwal_kuliah_id')
            ->distinct()
            ->get();

        foreach ($kombinasi as $item) {
            RekapEdom::hitungRekap($edom->id, $item->dosen_id, $item->jadwal_kuliah_id);
        }

        return back()->with('success', 'Rekap EDOM berhasil dihitung ulang!');
    }

    // ===== PERTANYAAN EDOM =====

    /**
     * Display listing of pertanyaan EDOM
     */
    public function pertanyaan()
    {
        $pertanyaan = PertanyaanEdom::orderBy('kategori')
            ->orderBy('urutan')
            ->paginate(20);

        $kategoris = PertanyaanEdom::getKategoriOptions();

        return view('admin.edom.pertanyaan.index', compact('pertanyaan', 'kategoris'));
    }

    /**
     * Show the form for creating a new pertanyaan
     */
    public function createPertanyaan()
    {
        $kategoris = PertanyaanEdom::getKategoriOptions();
        return view('admin.edom.pertanyaan.create', compact('kategoris'));
    }

    /**
     * Store a newly created pertanyaan
     */
    public function storePertanyaan(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:20|unique:pertanyaan_edom,kode',
            'pertanyaan' => 'required|string',
            'kategori' => 'required|in:pedagogik,profesional,kepribadian,sosial',
            'urutan' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        PertanyaanEdom::create([
            'kode' => $request->kode,
            'pertanyaan' => $request->pertanyaan,
            'kategori' => $request->kategori,
            'urutan' => $request->urutan,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.edom.pertanyaan')
            ->with('success', 'Pertanyaan EDOM berhasil ditambahkan!');
    }

    /**
     * Show the form for editing pertanyaan
     */
    public function editPertanyaan(PertanyaanEdom $pertanyaan)
    {
        $kategoris = PertanyaanEdom::getKategoriOptions();
        return view('admin.edom.pertanyaan.edit', compact('pertanyaan', 'kategoris'));
    }

    /**
     * Update the specified pertanyaan
     */
    public function updatePertanyaan(Request $request, PertanyaanEdom $pertanyaan)
    {
        $request->validate([
            'kode' => 'required|string|max:20|unique:pertanyaan_edom,kode,' . $pertanyaan->id,
            'pertanyaan' => 'required|string',
            'kategori' => 'required|in:pedagogik,profesional,kepribadian,sosial',
            'urutan' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $pertanyaan->update([
            'kode' => $request->kode,
            'pertanyaan' => $request->pertanyaan,
            'kategori' => $request->kategori,
            'urutan' => $request->urutan,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.edom.pertanyaan')
            ->with('success', 'Pertanyaan EDOM berhasil diperbarui!');
    }

    /**
     * Remove the specified pertanyaan
     */
    public function destroyPertanyaan(PertanyaanEdom $pertanyaan)
    {
        $pertanyaan->delete();

        return redirect()->route('admin.edom.pertanyaan')
            ->with('success', 'Pertanyaan EDOM berhasil dihapus!');
    }

    // ===== REKAP PER DOSEN =====

    /**
     * Detail rekap per dosen
     */
    public function rekapDosen(PeriodeEdom $edom, Dosen $dosen)
    {
        $rekap = RekapEdom::where('periode_edom_id', $edom->id)
            ->where('dosen_id', $dosen->id)
            ->with(['jadwalKuliah.mataKuliah'])
            ->get();

        $komentar = KomentarEdom::where('periode_edom_id', $edom->id)
            ->where('dosen_id', $dosen->id)
            ->get();

        return view('admin.edom.rekap-dosen', compact('edom', 'dosen', 'rekap', 'komentar'));
    }
}
