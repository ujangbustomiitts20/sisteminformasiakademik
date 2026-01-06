<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluasiKinerja;
use App\Models\EvaluasiKinerjaDetail;
use App\Models\PeriodeEvaluasi;
use App\Models\KriteriaEvaluasi;
use App\Models\Dosen;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class EvaluasiKinerjaController extends Controller
{
    public function index(Request $request)
    {
        $query = EvaluasiKinerja::with(['periodeEvaluasi', 'dosen', 'pegawai', 'penilai']);

        if ($request->filled('periode_evaluasi_id')) {
            $query->where('periode_evaluasi_id', $request->periode_evaluasi_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('predikat')) {
            $query->where('predikat', $request->predikat);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('dosen', fn($q) => $q->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('pegawai', fn($q) => $q->where('nama', 'like', "%{$search}%"));
            });
        }

        $evaluasiList = $query->orderBy('created_at', 'desc')->paginate(15);
        $periodeList = PeriodeEvaluasi::orderBy('tahun', 'desc')->get();

        $stats = [
            'total' => EvaluasiKinerja::count(),
            'draft' => EvaluasiKinerja::where('status', 'draft')->count(),
            'diajukan' => EvaluasiKinerja::where('status', 'diajukan')->count(),
            'disetujui' => EvaluasiKinerja::where('status', 'disetujui')->count(),
        ];

        return view('kepegawaian.evaluasi.index', compact('evaluasiList', 'periodeList', 'stats'));
    }

    public function create()
    {
        $periodeList = PeriodeEvaluasi::where('status', 'aktif')->get();
        $dosenList = Dosen::orderBy('nama')->get();
        $pegawaiList = Pegawai::orderBy('nama')->get();
        $kriterias = KriteriaEvaluasi::active()->orderBy('urutan')->get();

        return view('kepegawaian.evaluasi.create', compact('periodeList', 'dosenList', 'pegawaiList', 'kriterias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'periode_evaluasi_id' => 'required|exists:periode_evaluasi,id',
            'tipe_pegawai' => 'required|in:dosen,pegawai',
            'dosen_id' => 'required_if:tipe_pegawai,dosen|nullable|exists:dosen,id',
            'pegawai_id' => 'required_if:tipe_pegawai,pegawai|nullable|exists:pegawai,id',
            'catatan' => 'nullable|string',
            'nilai' => 'required|array',
            'nilai.*' => 'required|numeric|min:0|max:100',
        ]);

        $evaluasi = EvaluasiKinerja::create([
            'periode_evaluasi_id' => $request->periode_evaluasi_id,
            'dosen_id' => $request->tipe_pegawai === 'dosen' ? $request->dosen_id : null,
            'pegawai_id' => $request->tipe_pegawai === 'pegawai' ? $request->pegawai_id : null,
            'penilai_id' => auth()->id(),
            'catatan' => $request->catatan,
            'status' => 'draft',
            'tanggal_penilaian' => now(),
        ]);

        // Simpan detail nilai
        foreach ($request->nilai as $kriteriaId => $nilai) {
            EvaluasiKinerjaDetail::create([
                'evaluasi_kinerja_id' => $evaluasi->id,
                'kriteria_evaluasi_id' => $kriteriaId,
                'nilai' => $nilai,
                'keterangan' => $request->keterangan_kriteria[$kriteriaId] ?? null,
            ]);
        }

        // Hitung nilai total
        $evaluasi->hitungNilaiTotal();

        return redirect()->route('kepegawaian.evaluasi.show', $evaluasi)
            ->with('success', 'Evaluasi kinerja berhasil ditambahkan.');
    }

    public function show(EvaluasiKinerja $evaluasi)
    {
        $evaluasi->load(['periodeEvaluasi', 'dosen', 'pegawai', 'penilai', 'details.kriteriaEvaluasi']);
        return view('kepegawaian.evaluasi.show', compact('evaluasi'));
    }

    public function edit(EvaluasiKinerja $evaluasi)
    {
        $evaluasi->load(['details.kriteriaEvaluasi']);
        $periodeList = PeriodeEvaluasi::orderBy('tahun', 'desc')->get();
        
        $kriteriaList = $evaluasi->dosen_id 
            ? KriteriaEvaluasi::active()->forDosen()->orderBy('urutan')->get()
            : KriteriaEvaluasi::active()->forPegawai()->orderBy('urutan')->get();

        return view('kepegawaian.evaluasi.edit', compact('evaluasi', 'periodeList', 'kriteriaList'));
    }

    public function update(Request $request, EvaluasiKinerja $evaluasi)
    {
        $request->validate([
            'catatan' => 'nullable|string',
            'rekomendasi' => 'nullable|string',
            'nilai' => 'required|array',
            'nilai.*' => 'required|numeric|min:0|max:100',
        ]);

        $evaluasi->update([
            'catatan' => $request->catatan,
            'rekomendasi' => $request->rekomendasi,
        ]);

        // Update detail nilai
        foreach ($request->nilai as $kriteriaId => $nilai) {
            EvaluasiKinerjaDetail::updateOrCreate(
                [
                    'evaluasi_kinerja_id' => $evaluasi->id,
                    'kriteria_evaluasi_id' => $kriteriaId,
                ],
                [
                    'nilai' => $nilai,
                    'keterangan' => $request->keterangan_kriteria[$kriteriaId] ?? null,
                ]
            );
        }

        // Hitung ulang nilai total
        $evaluasi->hitungNilaiTotal();

        return redirect()->route('kepegawaian.evaluasi.show', $evaluasi)
            ->with('success', 'Evaluasi kinerja berhasil diperbarui.');
    }

    public function destroy(EvaluasiKinerja $evaluasi)
    {
        $evaluasi->delete();
        return redirect()->route('kepegawaian.evaluasi.index')
            ->with('success', 'Evaluasi kinerja berhasil dihapus.');
    }

    public function ajukan(EvaluasiKinerja $evaluasi)
    {
        if ($evaluasi->status !== 'draft') {
            return redirect()->back()->with('error', 'Evaluasi tidak dalam status draft.');
        }

        $evaluasi->update(['status' => 'diajukan']);
        return redirect()->back()->with('success', 'Evaluasi berhasil diajukan.');
    }

    public function approve(EvaluasiKinerja $evaluasi)
    {
        if ($evaluasi->status !== 'diajukan') {
            return redirect()->back()->with('error', 'Evaluasi tidak dalam status diajukan.');
        }

        $evaluasi->update(['status' => 'disetujui']);
        return redirect()->back()->with('success', 'Evaluasi berhasil disetujui.');
    }

    public function reject(Request $request, EvaluasiKinerja $evaluasi)
    {
        if ($evaluasi->status !== 'diajukan') {
            return redirect()->back()->with('error', 'Evaluasi tidak dalam status diajukan.');
        }

        $evaluasi->update([
            'status' => 'ditolak',
            'catatan' => $evaluasi->catatan . "\n\nAlasan Penolakan: " . $request->alasan,
        ]);
        
        return redirect()->back()->with('success', 'Evaluasi berhasil ditolak.');
    }

    // Periode Evaluasi
    public function periodeIndex()
    {
        $periodeList = PeriodeEvaluasi::orderBy('tahun', 'desc')->paginate(15);
        return view('kepegawaian.evaluasi.periode', compact('periodeList'));
    }

    public function periodeStore(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'tahun' => 'required|integer|min:2020|max:2100',
            'semester' => 'required|in:Ganjil,Genap,Tahunan',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'keterangan' => 'nullable|string',
        ]);

        PeriodeEvaluasi::create($request->all());

        return redirect()->route('kepegawaian.evaluasi.periode.index')
            ->with('success', 'Periode evaluasi berhasil ditambahkan.');
    }

    public function periodeUpdate(Request $request, PeriodeEvaluasi $periode)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'tahun' => 'required|integer|min:2020|max:2100',
            'semester' => 'required|in:Ganjil,Genap,Tahunan',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'required|in:draft,aktif,selesai',
            'keterangan' => 'nullable|string',
        ]);

        $periode->update($request->all());

        return redirect()->route('kepegawaian.evaluasi.periode.index')
            ->with('success', 'Periode evaluasi berhasil diperbarui.');
    }

    public function periodeDestroy(PeriodeEvaluasi $periode)
    {
        if ($periode->evaluasiKinerja()->exists()) {
            return redirect()->back()->with('error', 'Periode tidak dapat dihapus karena sudah ada data evaluasi.');
        }

        $periode->delete();
        return redirect()->route('kepegawaian.evaluasi.periode.index')
            ->with('success', 'Periode evaluasi berhasil dihapus.');
    }

    // Kriteria Evaluasi
    public function kriteriaIndex()
    {
        $kriteriaList = KriteriaEvaluasi::orderBy('urutan')->paginate(15);
        return view('kepegawaian.evaluasi.kriteria', compact('kriteriaList'));
    }

    public function kriteriaStore(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'bobot' => 'required|numeric|min:0|max:100',
            'kategori' => 'required|in:dosen,pegawai,semua',
            'urutan' => 'required|integer|min:0',
        ]);

        KriteriaEvaluasi::create($request->all());

        return redirect()->route('kepegawaian.evaluasi.kriteria.index')
            ->with('success', 'Kriteria evaluasi berhasil ditambahkan.');
    }

    public function kriteriaUpdate(Request $request, KriteriaEvaluasi $kriteria)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'bobot' => 'required|numeric|min:0|max:100',
            'kategori' => 'required|in:dosen,pegawai,semua',
            'urutan' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $kriteria->update($request->all());

        return redirect()->route('kepegawaian.evaluasi.kriteria.index')
            ->with('success', 'Kriteria evaluasi berhasil diperbarui.');
    }

    public function kriteriaDestroy(KriteriaEvaluasi $kriteria)
    {
        if ($kriteria->evaluasiKinerjaDetail()->exists()) {
            return redirect()->back()->with('error', 'Kriteria tidak dapat dihapus karena sudah digunakan.');
        }

        $kriteria->delete();
        return redirect()->route('kepegawaian.evaluasi.kriteria.index')
            ->with('success', 'Kriteria evaluasi berhasil dihapus.');
    }
}
