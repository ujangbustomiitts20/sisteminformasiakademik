<?php

namespace App\Http\Controllers;

use App\Models\Beasiswa;
use App\Models\PenerimaBeasiswa;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BeasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Beasiswa::withCount(['penerima', 'penerima as penerima_aktif_count' => function($q) {
            $q->where('status', 'Disetujui');
        }]);

        if ($request->search) {
            $query->where('nama', 'like', "%{$request->search}%");
        }

        if ($request->jenis) {
            $query->where('jenis', $request->jenis);
        }

        $beasiswa = $query->orderBy('nama')->paginate(15);

        return view('keuangan.beasiswa.index', compact('beasiswa'));
    }

    public function create()
    {
        return view('keuangan.beasiswa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:beasiswa,kode',
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:' . implode(',', array_keys(Beasiswa::JENIS)),
            'tipe_potongan' => 'required|in:' . implode(',', array_keys(Beasiswa::TIPE_POTONGAN)),
            'nilai_potongan' => 'required|numeric|min:0',
            'kuota' => 'nullable|integer|min:1',
            'sumber_dana' => 'nullable|string|max:255',
            'persyaratan' => 'nullable|string',
        ]);

        // Validate percentage
        if ($request->tipe_potongan === 'Persen' && $request->nilai_potongan > 100) {
            return back()->with('error', 'Nilai potongan persen tidak boleh lebih dari 100%')->withInput();
        }

        Beasiswa::create($request->all());

        return redirect()->route('beasiswa.index')->with('success', 'Beasiswa berhasil ditambahkan!');
    }

    public function show(Beasiswa $beasiswa)
    {
        $beasiswa->load(['penerima.mahasiswa.programStudi', 'penerima.tahunAkademik']);
        return view('keuangan.beasiswa.show', compact('beasiswa'));
    }

    public function edit(Beasiswa $beasiswa)
    {
        return view('keuangan.beasiswa.edit', compact('beasiswa'));
    }

    public function update(Request $request, Beasiswa $beasiswa)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:beasiswa,kode,' . $beasiswa->id,
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:' . implode(',', array_keys(Beasiswa::JENIS)),
            'tipe_potongan' => 'required|in:' . implode(',', array_keys(Beasiswa::TIPE_POTONGAN)),
            'nilai_potongan' => 'required|numeric|min:0',
            'kuota' => 'nullable|integer|min:1',
            'sumber_dana' => 'nullable|string|max:255',
            'persyaratan' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($request->tipe_potongan === 'Persen' && $request->nilai_potongan > 100) {
            return back()->with('error', 'Nilai potongan persen tidak boleh lebih dari 100%')->withInput();
        }

        $beasiswa->update($request->all());

        return redirect()->route('beasiswa.index')->with('success', 'Beasiswa berhasil diperbarui!');
    }

    public function destroy(Beasiswa $beasiswa)
    {
        if ($beasiswa->penerima()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus beasiswa yang sudah memiliki penerima!');
        }

        $beasiswa->delete();
        return redirect()->route('beasiswa.index')->with('success', 'Beasiswa berhasil dihapus!');
    }

    public function toggleStatus(Beasiswa $beasiswa)
    {
        $beasiswa->update(['is_active' => !$beasiswa->is_active]);
        $status = $beasiswa->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Beasiswa berhasil {$status}!");
    }

    /**
     * Daftar penerima beasiswa
     */
    public function penerimIndex(Request $request)
    {
        $query = PenerimaBeasiswa::with(['beasiswa', 'mahasiswa.programStudi', 'tahunAkademik', 'approver']);

        if ($request->search) {
            $query->whereHas('mahasiswa', function($q) use ($request) {
                $q->where('nim', 'like', "%{$request->search}%")
                    ->orWhere('nama', 'like', "%{$request->search}%");
            });
        }

        if ($request->beasiswa_id) {
            $query->where('beasiswa_id', $request->beasiswa_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->tahun_akademik_id) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }

        $penerima = $query->orderBy('created_at', 'desc')->paginate(20);
        $beasiswa = Beasiswa::active()->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();

        // Statistics
        $stats = [
            'total_penerima' => PenerimaBeasiswa::aktif()->count(),
            'menunggu_approval' => PenerimaBeasiswa::where('status', 'Diajukan')->count(),
        ];

        return view('keuangan.beasiswa.penerima-index', compact('penerima', 'beasiswa', 'tahunAkademik', 'stats'));
    }

    /**
     * Form tambah penerima
     */
    public function penerimCreate()
    {
        $beasiswa = Beasiswa::active()->get();
        $mahasiswa = Mahasiswa::where('status', 'Aktif')->orderBy('nim')->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();

        return view('keuangan.beasiswa.penerima-create', compact('beasiswa', 'mahasiswa', 'tahunAkademik'));
    }

    /**
     * Simpan penerima baru
     */
    public function penerimStore(Request $request)
    {
        $request->validate([
            'beasiswa_id' => 'required|exists:beasiswa,id',
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
        ]);

        // Check if already registered
        $exists = PenerimaBeasiswa::where('beasiswa_id', $request->beasiswa_id)
            ->where('mahasiswa_id', $request->mahasiswa_id)
            ->where('tahun_akademik_id', $request->tahun_akademik_id)
            ->whereNotIn('status', ['Ditolak', 'Dicabut'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Mahasiswa sudah terdaftar untuk beasiswa ini di tahun akademik yang sama!');
        }

        // Check kuota
        $beasiswa = Beasiswa::find($request->beasiswa_id);
        if ($beasiswa->kuota && $beasiswa->sisa_kuota <= 0) {
            return back()->with('error', 'Kuota beasiswa sudah habis!');
        }

        PenerimaBeasiswa::create([
            'beasiswa_id' => $request->beasiswa_id,
            'mahasiswa_id' => $request->mahasiswa_id,
            'tahun_akademik_id' => $request->tahun_akademik_id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => 'Disetujui', // Admin langsung approve
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('beasiswa.penerima.index')->with('success', 'Penerima beasiswa berhasil ditambahkan!');
    }

    /**
     * Approve pengajuan beasiswa
     */
    public function approve(PenerimaBeasiswa $penerima)
    {
        if ($penerima->status !== 'Diajukan') {
            return back()->with('error', 'Status pengajuan tidak valid.');
        }

        // Check kuota
        if ($penerima->beasiswa->kuota && $penerima->beasiswa->sisa_kuota <= 0) {
            return back()->with('error', 'Kuota beasiswa sudah habis!');
        }

        $penerima->update([
            'status' => 'Disetujui',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan beasiswa disetujui!');
    }

    /**
     * Tolak pengajuan beasiswa
     */
    public function reject(Request $request, PenerimaBeasiswa $penerima)
    {
        if ($penerima->status !== 'Diajukan') {
            return back()->with('error', 'Status pengajuan tidak valid.');
        }

        $request->validate([
            'catatan' => 'required|string|max:500',
        ]);

        $penerima->update([
            'status' => 'Ditolak',
            'catatan' => $request->catatan,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan beasiswa ditolak.');
    }

    /**
     * Cabut beasiswa
     */
    public function revoke(Request $request, PenerimaBeasiswa $penerima)
    {
        if ($penerima->status !== 'Disetujui') {
            return back()->with('error', 'Hanya beasiswa yang disetujui yang dapat dicabut.');
        }

        $request->validate([
            'catatan' => 'required|string|max:500',
        ]);

        $penerima->update([
            'status' => 'Dicabut',
            'catatan' => $request->catatan,
        ]);

        return back()->with('success', 'Beasiswa telah dicabut.');
    }

    /**
     * Pengajuan beasiswa oleh mahasiswa
     */
    public function ajukan(Request $request, Beasiswa $beasiswa)
    {
        $user = auth()->user();
        
        if (!$user->isMahasiswa()) {
            abort(403);
        }

        $mahasiswa = $user->mahasiswa;
        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();

        if (!$tahunAkademik) {
            return back()->with('error', 'Tidak ada tahun akademik aktif.');
        }

        // Check existing
        $exists = PenerimaBeasiswa::where('beasiswa_id', $beasiswa->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $tahunAkademik->id)
            ->whereNotIn('status', ['Ditolak', 'Dicabut'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah mengajukan/menerima beasiswa ini!');
        }

        PenerimaBeasiswa::create([
            'beasiswa_id' => $beasiswa->id,
            'mahasiswa_id' => $mahasiswa->id,
            'tahun_akademik_id' => $tahunAkademik->id,
            'tanggal_mulai' => now(),
            'status' => 'Diajukan',
        ]);

        return back()->with('success', 'Pengajuan beasiswa berhasil dikirim. Menunggu persetujuan.');
    }

    /**
     * Beasiswa yang tersedia untuk mahasiswa
     */
    public function available()
    {
        $user = auth()->user();
        
        if (!$user->isMahasiswa()) {
            abort(403);
        }

        $mahasiswa = $user->mahasiswa;
        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();

        $beasiswa = Beasiswa::active()
            ->withCount(['penerima as kuota_terpakai' => function($q) use ($tahunAkademik) {
                $q->aktif()->where('tahun_akademik_id', $tahunAkademik?->id);
            }])
            ->get();

        // Get mahasiswa's existing pengajuan
        $pengajuanSaya = [];
        if ($tahunAkademik) {
            $pengajuanSaya = PenerimaBeasiswa::where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->pluck('status', 'beasiswa_id')
                ->toArray();
        }

        return view('keuangan.beasiswa.available', compact('beasiswa', 'mahasiswa', 'pengajuanSaya', 'tahunAkademik'));
    }
}
