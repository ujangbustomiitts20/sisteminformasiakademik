<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PenugasanMutasi;
use App\Models\Dosen;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PenugasanMutasiController extends Controller
{
    public function index(Request $request)
    {
        $query = PenugasanMutasi::with(['dosen', 'pegawai', 'unitKerjaAsal', 'unitKerjaTujuan']);

        // Filter
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_sk', 'like', "%{$search}%")
                  ->orWhere('nama_tugas', 'like', "%{$search}%")
                  ->orWhereHas('dosen', function ($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('pegawai', function ($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $penugasan = $query->orderBy('tmt', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total' => PenugasanMutasi::count(),
            'aktif' => PenugasanMutasi::where('status', 'aktif')->count(),
            'penugasan' => PenugasanMutasi::where('jenis', 'penugasan')->where('status', 'aktif')->count(),
            'mutasi' => PenugasanMutasi::where('jenis', 'mutasi')->where('status', 'aktif')->count(),
        ];

        $jenisList = PenugasanMutasi::JENIS;
        $statusList = PenugasanMutasi::STATUS;

        return view('kepegawaian.penugasan.index', compact('penugasan', 'stats', 'jenisList', 'statusList'));
    }

    public function create()
    {
        $dosens = Dosen::where('status', 'Aktif')->orderBy('nama')->get();
        $pegawais = Pegawai::where('status', 'Aktif')->orderBy('nama')->get();
        $unitKerja = UnitKerja::orderBy('nama')->get();
        $jenisList = PenugasanMutasi::JENIS;

        return view('kepegawaian.penugasan.create', compact('dosens', 'pegawais', 'unitKerja', 'jenisList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_pegawai' => 'required|in:dosen,pegawai',
            'dosen_id' => 'required_if:tipe_pegawai,dosen|nullable|exists:dosen,id',
            'pegawai_id' => 'required_if:tipe_pegawai,pegawai|nullable|exists:pegawai,id',
            'jenis' => 'required|in:' . implode(',', array_keys(PenugasanMutasi::JENIS)),
            'no_sk' => 'required|string',
            'tanggal_sk' => 'required|date',
            'tmt' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tmt',
            'unit_kerja_asal_id' => 'nullable|exists:unit_kerja,id',
            'unit_kerja_tujuan_id' => 'nullable|exists:unit_kerja,id',
            'jabatan_asal' => 'nullable|string',
            'jabatan_tujuan' => 'nullable|string',
            'nama_tugas' => 'nullable|string',
            'deskripsi_tugas' => 'nullable|string',
            'lokasi_penugasan' => 'nullable|string',
            'alasan' => 'nullable|string',
            'dokumen_sk' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $data = $request->except(['tipe_pegawai', 'dokumen_sk']);
        $data['status'] = 'aktif';
        $data['created_by'] = auth()->id();

        if ($request->hasFile('dokumen_sk')) {
            $data['dokumen_sk'] = $request->file('dokumen_sk')
                ->store('penugasan/sk', 'public');
        }

        PenugasanMutasi::create($data);

        return redirect()->route('kepegawaian.penugasan.index')
            ->with('success', 'Data penugasan/mutasi berhasil ditambahkan');
    }

    public function show(PenugasanMutasi $penugasan)
    {
        $penugasan->load(['dosen', 'pegawai', 'unitKerjaAsal', 'unitKerjaTujuan', 'createdBy']);
        return view('kepegawaian.penugasan.show', compact('penugasan'));
    }

    public function edit(PenugasanMutasi $penugasan)
    {
        $dosens = Dosen::where('status', 'Aktif')->orderBy('nama')->get();
        $pegawais = Pegawai::where('status', 'Aktif')->orderBy('nama')->get();
        $unitKerja = UnitKerja::orderBy('nama')->get();
        $jenisList = PenugasanMutasi::JENIS;
        $statusList = PenugasanMutasi::STATUS;

        return view('kepegawaian.penugasan.edit', compact('penugasan', 'dosens', 'pegawais', 'unitKerja', 'jenisList', 'statusList'));
    }

    public function update(Request $request, PenugasanMutasi $penugasan)
    {
        $request->validate([
            'jenis' => 'required|in:' . implode(',', array_keys(PenugasanMutasi::JENIS)),
            'no_sk' => 'required|string',
            'tanggal_sk' => 'required|date',
            'tmt' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tmt',
            'unit_kerja_asal_id' => 'nullable|exists:unit_kerja,id',
            'unit_kerja_tujuan_id' => 'nullable|exists:unit_kerja,id',
            'jabatan_asal' => 'nullable|string',
            'jabatan_tujuan' => 'nullable|string',
            'nama_tugas' => 'nullable|string',
            'deskripsi_tugas' => 'nullable|string',
            'lokasi_penugasan' => 'nullable|string',
            'alasan' => 'nullable|string',
            'status' => 'required|in:' . implode(',', array_keys(PenugasanMutasi::STATUS)),
            'dokumen_sk' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $data = $request->except(['dokumen_sk']);

        if ($request->hasFile('dokumen_sk')) {
            if ($penugasan->dokumen_sk) {
                Storage::disk('public')->delete($penugasan->dokumen_sk);
            }
            $data['dokumen_sk'] = $request->file('dokumen_sk')
                ->store('penugasan/sk', 'public');
        }

        $penugasan->update($data);

        return redirect()->route('kepegawaian.penugasan.index')
            ->with('success', 'Data penugasan/mutasi berhasil diperbarui');
    }

    public function destroy(PenugasanMutasi $penugasan)
    {
        if ($penugasan->dokumen_sk) {
            Storage::disk('public')->delete($penugasan->dokumen_sk);
        }

        $penugasan->delete();

        return redirect()->route('kepegawaian.penugasan.index')
            ->with('success', 'Data penugasan/mutasi berhasil dihapus');
    }

    public function selesaikan(PenugasanMutasi $penugasan)
    {
        $penugasan->update([
            'status' => 'selesai',
            'tanggal_selesai' => $penugasan->tanggal_selesai ?? now(),
        ]);

        return redirect()->route('kepegawaian.penugasan.index')
            ->with('success', 'Penugasan/mutasi berhasil diselesaikan');
    }

    public function batalkan(Request $request, PenugasanMutasi $penugasan)
    {
        $request->validate([
            'catatan' => 'required|string',
        ]);

        $penugasan->update([
            'status' => 'dibatalkan',
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('kepegawaian.penugasan.index')
            ->with('success', 'Penugasan/mutasi berhasil dibatalkan');
    }
}
