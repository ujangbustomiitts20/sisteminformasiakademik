<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KenaikanPangkat;
use App\Models\Dosen;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KenaikanPangkatController extends Controller
{
    public function index(Request $request)
    {
        $query = KenaikanPangkat::with(['dosen', 'pegawai']);

        // Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('periode')) {
            $query->where('periode', $request->periode);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_sk', 'like', "%{$search}%")
                  ->orWhereHas('dosen', function ($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('pegawai', function ($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $kenaikanPangkat = $query->orderBy('tahun', 'desc')
            ->orderBy('periode', 'desc')
            ->paginate(15);

        // Statistics
        $stats = [
            'total' => KenaikanPangkat::count(),
            'diusulkan' => KenaikanPangkat::where('status', 'diusulkan')->count(),
            'verifikasi' => KenaikanPangkat::where('status', 'verifikasi')->count(),
            'disetujui' => KenaikanPangkat::where('status', 'disetujui')->count(),
        ];

        $periodeList = KenaikanPangkat::PERIODE;
        $jenisList = KenaikanPangkat::JENIS;
        $statusList = KenaikanPangkat::STATUS;

        return view('kepegawaian.kenaikan-pangkat.index', compact(
            'kenaikanPangkat', 'stats', 'periodeList', 'jenisList', 'statusList'
        ));
    }

    public function create()
    {
        $dosens = Dosen::where('status', 'Aktif')->orderBy('nama')->get();
        $pegawais = Pegawai::where('status', 'Aktif')->orderBy('nama')->get();
        $periodeList = KenaikanPangkat::PERIODE;
        $jenisList = KenaikanPangkat::JENIS;
        $pangkatList = KenaikanPangkat::PANGKAT;

        return view('kepegawaian.kenaikan-pangkat.create', compact(
            'dosens', 'pegawais', 'periodeList', 'jenisList', 'pangkatList'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_pegawai' => 'required|in:dosen,pegawai',
            'dosen_id' => 'required_if:tipe_pegawai,dosen|nullable|exists:dosen,id',
            'pegawai_id' => 'required_if:tipe_pegawai,pegawai|nullable|exists:pegawai,id',
            'periode' => 'required|in:' . implode(',', array_keys(KenaikanPangkat::PERIODE)),
            'tahun' => 'required|integer|min:2000|max:2100',
            'pangkat_lama' => 'nullable|string',
            'golongan_lama' => 'nullable|string',
            'tmt_pangkat_lama' => 'nullable|date',
            'pangkat_baru' => 'required|string',
            'golongan_baru' => 'required|string',
            'tmt_pangkat_baru' => 'required|date',
            'jenis' => 'required|in:' . implode(',', array_keys(KenaikanPangkat::JENIS)),
            'masa_kerja_tahun' => 'required|integer|min:0',
            'masa_kerja_bulan' => 'required|integer|min:0|max:11',
            'pendidikan_terakhir' => 'nullable|string',
            'angka_kredit' => 'nullable|numeric|min:0',
            'penilaian_kinerja' => 'nullable|string',
            'dokumen_sk' => 'nullable|file|mimes:pdf|max:5120',
            'dokumen_pak' => 'nullable|file|mimes:pdf|max:5120',
            'dokumen_skp' => 'nullable|file|mimes:pdf|max:5120',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->except(['tipe_pegawai', 'dokumen_sk', 'dokumen_pak', 'dokumen_skp']);
        $data['status'] = 'diusulkan';
        $data['diusulkan_oleh'] = auth()->id();

        if ($request->hasFile('dokumen_sk')) {
            $data['dokumen_sk'] = $request->file('dokumen_sk')
                ->store('kenaikan-pangkat/sk', 'public');
        }
        if ($request->hasFile('dokumen_pak')) {
            $data['dokumen_pak'] = $request->file('dokumen_pak')
                ->store('kenaikan-pangkat/pak', 'public');
        }
        if ($request->hasFile('dokumen_skp')) {
            $data['dokumen_skp'] = $request->file('dokumen_skp')
                ->store('kenaikan-pangkat/skp', 'public');
        }

        KenaikanPangkat::create($data);

        return redirect()->route('kepegawaian.kenaikan-pangkat.index')
            ->with('success', 'Usulan kenaikan pangkat berhasil ditambahkan');
    }

    public function show(KenaikanPangkat $kenaikanPangkat)
    {
        $kenaikanPangkat->load(['dosen', 'pegawai', 'diusulkanOleh', 'diverifikasiOleh', 'disetujuiOleh']);
        return view('kepegawaian.kenaikan-pangkat.show', compact('kenaikanPangkat'));
    }

    public function edit(KenaikanPangkat $kenaikanPangkat)
    {
        if (!in_array($kenaikanPangkat->status, ['draft', 'diusulkan'])) {
            return redirect()->route('kepegawaian.kenaikan-pangkat.index')
                ->with('error', 'Data yang sudah diverifikasi tidak dapat diedit');
        }

        $dosens = Dosen::where('status', 'Aktif')->orderBy('nama')->get();
        $pegawais = Pegawai::where('status', 'Aktif')->orderBy('nama')->get();
        $periodeList = KenaikanPangkat::PERIODE;
        $jenisList = KenaikanPangkat::JENIS;
        $pangkatList = KenaikanPangkat::PANGKAT;

        return view('kepegawaian.kenaikan-pangkat.edit', compact(
            'kenaikanPangkat', 'dosens', 'pegawais', 'periodeList', 'jenisList', 'pangkatList'
        ));
    }

    public function update(Request $request, KenaikanPangkat $kenaikanPangkat)
    {
        if (!in_array($kenaikanPangkat->status, ['draft', 'diusulkan'])) {
            return redirect()->route('kepegawaian.kenaikan-pangkat.index')
                ->with('error', 'Data yang sudah diverifikasi tidak dapat diedit');
        }

        $request->validate([
            'periode' => 'required|in:' . implode(',', array_keys(KenaikanPangkat::PERIODE)),
            'tahun' => 'required|integer|min:2000|max:2100',
            'pangkat_lama' => 'nullable|string',
            'golongan_lama' => 'nullable|string',
            'tmt_pangkat_lama' => 'nullable|date',
            'pangkat_baru' => 'required|string',
            'golongan_baru' => 'required|string',
            'tmt_pangkat_baru' => 'required|date',
            'jenis' => 'required|in:' . implode(',', array_keys(KenaikanPangkat::JENIS)),
            'masa_kerja_tahun' => 'required|integer|min:0',
            'masa_kerja_bulan' => 'required|integer|min:0|max:11',
            'pendidikan_terakhir' => 'nullable|string',
            'angka_kredit' => 'nullable|numeric|min:0',
            'penilaian_kinerja' => 'nullable|string',
            'dokumen_sk' => 'nullable|file|mimes:pdf|max:5120',
            'dokumen_pak' => 'nullable|file|mimes:pdf|max:5120',
            'dokumen_skp' => 'nullable|file|mimes:pdf|max:5120',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->except(['dokumen_sk', 'dokumen_pak', 'dokumen_skp']);

        if ($request->hasFile('dokumen_sk')) {
            if ($kenaikanPangkat->dokumen_sk) {
                Storage::disk('public')->delete($kenaikanPangkat->dokumen_sk);
            }
            $data['dokumen_sk'] = $request->file('dokumen_sk')
                ->store('kenaikan-pangkat/sk', 'public');
        }
        if ($request->hasFile('dokumen_pak')) {
            if ($kenaikanPangkat->dokumen_pak) {
                Storage::disk('public')->delete($kenaikanPangkat->dokumen_pak);
            }
            $data['dokumen_pak'] = $request->file('dokumen_pak')
                ->store('kenaikan-pangkat/pak', 'public');
        }
        if ($request->hasFile('dokumen_skp')) {
            if ($kenaikanPangkat->dokumen_skp) {
                Storage::disk('public')->delete($kenaikanPangkat->dokumen_skp);
            }
            $data['dokumen_skp'] = $request->file('dokumen_skp')
                ->store('kenaikan-pangkat/skp', 'public');
        }

        $kenaikanPangkat->update($data);

        return redirect()->route('kepegawaian.kenaikan-pangkat.index')
            ->with('success', 'Usulan kenaikan pangkat berhasil diperbarui');
    }

    public function destroy(KenaikanPangkat $kenaikanPangkat)
    {
        if (!in_array($kenaikanPangkat->status, ['draft', 'diusulkan'])) {
            return redirect()->route('kepegawaian.kenaikan-pangkat.index')
                ->with('error', 'Data yang sudah diverifikasi tidak dapat dihapus');
        }

        // Delete files
        if ($kenaikanPangkat->dokumen_sk) {
            Storage::disk('public')->delete($kenaikanPangkat->dokumen_sk);
        }
        if ($kenaikanPangkat->dokumen_pak) {
            Storage::disk('public')->delete($kenaikanPangkat->dokumen_pak);
        }
        if ($kenaikanPangkat->dokumen_skp) {
            Storage::disk('public')->delete($kenaikanPangkat->dokumen_skp);
        }

        $kenaikanPangkat->delete();

        return redirect()->route('kepegawaian.kenaikan-pangkat.index')
            ->with('success', 'Usulan kenaikan pangkat berhasil dihapus');
    }

    public function verifikasi(Request $request, KenaikanPangkat $kenaikanPangkat)
    {
        $request->validate([
            'catatan' => 'nullable|string',
        ]);

        $kenaikanPangkat->update([
            'status' => 'verifikasi',
            'diverifikasi_oleh' => auth()->id(),
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('kepegawaian.kenaikan-pangkat.index')
            ->with('success', 'Kenaikan pangkat berhasil diverifikasi');
    }

    public function approve(Request $request, KenaikanPangkat $kenaikanPangkat)
    {
        $request->validate([
            'no_sk' => 'required|string',
            'tanggal_sk' => 'required|date',
            'pejabat_penandatangan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $kenaikanPangkat->update([
            'status' => 'disetujui',
            'no_sk' => $request->no_sk,
            'tanggal_sk' => $request->tanggal_sk,
            'pejabat_penandatangan' => $request->pejabat_penandatangan,
            'catatan' => $request->catatan,
            'disetujui_oleh' => auth()->id(),
        ]);

        // Update pangkat di master dosen/pegawai
        if ($kenaikanPangkat->dosen_id) {
            $kenaikanPangkat->dosen->update([
                'pangkat' => $kenaikanPangkat->pangkat_baru,
                'golongan' => $kenaikanPangkat->golongan_baru,
            ]);
        } elseif ($kenaikanPangkat->pegawai_id) {
            $kenaikanPangkat->pegawai->update([
                'pangkat' => $kenaikanPangkat->pangkat_baru,
                'golongan' => $kenaikanPangkat->golongan_baru,
            ]);
        }

        return redirect()->route('kepegawaian.kenaikan-pangkat.index')
            ->with('success', 'Kenaikan pangkat berhasil disetujui');
    }

    public function reject(Request $request, KenaikanPangkat $kenaikanPangkat)
    {
        $request->validate([
            'catatan' => 'required|string',
        ]);

        $kenaikanPangkat->update([
            'status' => 'ditolak',
            'catatan' => $request->catatan,
            'disetujui_oleh' => auth()->id(),
        ]);

        return redirect()->route('kepegawaian.kenaikan-pangkat.index')
            ->with('success', 'Kenaikan pangkat ditolak');
    }

    // Monitoring periode kenaikan pangkat
    public function monitoring(Request $request)
    {
        $periode = $request->periode ?? (now()->month <= 4 ? 'april' : 'oktober');
        $tahun = $request->tahun ?? now()->year;

        $kenaikanPangkat = KenaikanPangkat::with(['dosen', 'pegawai'])
            ->where('periode', $periode)
            ->where('tahun', $tahun)
            ->get();

        $summary = [
            'total' => $kenaikanPangkat->count(),
            'diusulkan' => $kenaikanPangkat->where('status', 'diusulkan')->count(),
            'verifikasi' => $kenaikanPangkat->where('status', 'verifikasi')->count(),
            'disetujui' => $kenaikanPangkat->where('status', 'disetujui')->count(),
            'ditolak' => $kenaikanPangkat->where('status', 'ditolak')->count(),
        ];

        return view('kepegawaian.kenaikan-pangkat.monitoring', compact(
            'kenaikanPangkat', 'periode', 'tahun', 'summary'
        ));
    }
}
