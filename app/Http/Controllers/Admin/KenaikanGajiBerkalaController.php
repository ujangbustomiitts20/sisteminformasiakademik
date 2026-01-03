<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KenaikanGajiBerkala;
use App\Models\Dosen;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KenaikanGajiBerkalaController extends Controller
{
    public function index(Request $request)
    {
        $query = KenaikanGajiBerkala::with(['dosen', 'pegawai']);

        // Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tmt_kgb', $request->tahun);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_sk', 'like', "%{$search}%")
                  ->orWhere('golongan_ruang', 'like', "%{$search}%")
                  ->orWhereHas('dosen', function ($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('pegawai', function ($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $kgb = $query->orderBy('tmt_kgb_berikutnya', 'asc')->paginate(15);

        // Statistics
        $stats = [
            'total' => KenaikanGajiBerkala::count(),
            'pending' => KenaikanGajiBerkala::where('status', 'pending')->count(),
            'akan_jatuh_tempo' => KenaikanGajiBerkala::akanJatuhTempo(3)->count(),
            'sudah_jatuh_tempo' => KenaikanGajiBerkala::sudahJatuhTempo()->count(),
        ];

        return view('kepegawaian.kgb.index', compact('kgb', 'stats'));
    }

    public function create()
    {
        $dosens = Dosen::where('status', 'Aktif')->orderBy('nama')->get();
        $pegawais = Pegawai::where('status', 'Aktif')->orderBy('nama')->get();

        return view('kepegawaian.kgb.create', compact('dosens', 'pegawais'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_pegawai' => 'required|in:dosen,pegawai',
            'dosen_id' => 'required_if:tipe_pegawai,dosen|nullable|exists:dosen,id',
            'pegawai_id' => 'required_if:tipe_pegawai,pegawai|nullable|exists:pegawai,id',
            'tmt_kgb' => 'required|date',
            'golongan_ruang' => 'required|string',
            'masa_kerja_golongan_tahun' => 'required|integer|min:0',
            'masa_kerja_golongan_bulan' => 'required|integer|min:0|max:11',
            'gaji_pokok_lama' => 'nullable|numeric|min:0',
            'gaji_pokok_baru' => 'required|numeric|min:0',
            'masa_kerja_total_tahun' => 'required|integer|min:0',
            'masa_kerja_total_bulan' => 'required|integer|min:0|max:11',
            'no_sk' => 'nullable|string',
            'tanggal_sk' => 'nullable|date',
            'dokumen_sk' => 'nullable|file|mimes:pdf|max:5120',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->except(['tipe_pegawai', 'dokumen_sk']);
        $data['status'] = 'pending';
        $data['is_otomatis'] = false;

        if ($request->hasFile('dokumen_sk')) {
            $data['dokumen_sk'] = $request->file('dokumen_sk')
                ->store('kgb/sk', 'public');
        }

        KenaikanGajiBerkala::create($data);

        return redirect()->route('kepegawaian.kgb.index')
            ->with('success', 'Data KGB berhasil ditambahkan');
    }

    public function show(KenaikanGajiBerkala $kgb)
    {
        $kgb->load(['dosen', 'pegawai', 'diprosesOleh']);
        return view('kepegawaian.kgb.show', compact('kgb'));
    }

    public function edit(KenaikanGajiBerkala $kgb)
    {
        $dosens = Dosen::where('status', 'Aktif')->orderBy('nama')->get();
        $pegawais = Pegawai::where('status', 'Aktif')->orderBy('nama')->get();

        return view('kepegawaian.kgb.edit', compact('kgb', 'dosens', 'pegawais'));
    }

    public function update(Request $request, KenaikanGajiBerkala $kgb)
    {
        $request->validate([
            'tmt_kgb' => 'required|date',
            'golongan_ruang' => 'required|string',
            'masa_kerja_golongan_tahun' => 'required|integer|min:0',
            'masa_kerja_golongan_bulan' => 'required|integer|min:0|max:11',
            'gaji_pokok_lama' => 'nullable|numeric|min:0',
            'gaji_pokok_baru' => 'required|numeric|min:0',
            'masa_kerja_total_tahun' => 'required|integer|min:0',
            'masa_kerja_total_bulan' => 'required|integer|min:0|max:11',
            'no_sk' => 'nullable|string',
            'tanggal_sk' => 'nullable|date',
            'dokumen_sk' => 'nullable|file|mimes:pdf|max:5120',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->except(['dokumen_sk']);

        if ($request->hasFile('dokumen_sk')) {
            if ($kgb->dokumen_sk) {
                Storage::disk('public')->delete($kgb->dokumen_sk);
            }
            $data['dokumen_sk'] = $request->file('dokumen_sk')
                ->store('kgb/sk', 'public');
        }

        $kgb->update($data);

        return redirect()->route('kepegawaian.kgb.index')
            ->with('success', 'Data KGB berhasil diperbarui');
    }

    public function destroy(KenaikanGajiBerkala $kgb)
    {
        if ($kgb->dokumen_sk) {
            Storage::disk('public')->delete($kgb->dokumen_sk);
        }

        $kgb->delete();

        return redirect()->route('kepegawaian.kgb.index')
            ->with('success', 'Data KGB berhasil dihapus');
    }

    public function approve(Request $request, KenaikanGajiBerkala $kgb)
    {
        $request->validate([
            'no_sk' => 'required|string',
            'tanggal_sk' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        $kgb->update([
            'status' => 'disetujui',
            'no_sk' => $request->no_sk,
            'tanggal_sk' => $request->tanggal_sk,
            'catatan' => $request->catatan,
            'diproses_oleh' => auth()->id(),
            'tanggal_diproses' => now(),
        ]);

        return redirect()->route('kepegawaian.kgb.index')
            ->with('success', 'KGB berhasil disetujui');
    }

    public function reject(Request $request, KenaikanGajiBerkala $kgb)
    {
        $request->validate([
            'catatan' => 'required|string',
        ]);

        $kgb->update([
            'status' => 'ditolak',
            'catatan' => $request->catatan,
            'diproses_oleh' => auth()->id(),
            'tanggal_diproses' => now(),
        ]);

        return redirect()->route('kepegawaian.kgb.index')
            ->with('success', 'KGB berhasil ditolak');
    }

    // Monitoring KGB akan jatuh tempo
    public function monitoring(Request $request)
    {
        $bulan = $request->bulan ?? 3;

        $akanJatuhTempo = KenaikanGajiBerkala::with(['dosen', 'pegawai'])
            ->akanJatuhTempo($bulan)
            ->where('status', 'pending')
            ->orderBy('tmt_kgb_berikutnya', 'asc')
            ->get();

        $sudahJatuhTempo = KenaikanGajiBerkala::with(['dosen', 'pegawai'])
            ->sudahJatuhTempo()
            ->orderBy('tmt_kgb_berikutnya', 'asc')
            ->get();

        return view('kepegawaian.kgb.monitoring', compact('akanJatuhTempo', 'sudahJatuhTempo', 'bulan'));
    }

    // Generate KGB otomatis untuk pegawai yang akan jatuh tempo
    public function generateKgb()
    {
        $count = 0;

        // Get latest KGB for each pegawai yang akan jatuh tempo dalam 3 bulan
        $kgbAkanJatuhTempo = KenaikanGajiBerkala::with(['dosen', 'pegawai'])
            ->where('status', 'disetujui')
            ->whereBetween('tmt_kgb_berikutnya', [now(), now()->addMonths(3)])
            ->get();

        foreach ($kgbAkanJatuhTempo as $kgb) {
            // Check if already has pending KGB
            $existingPending = KenaikanGajiBerkala::where('status', 'pending');
            if ($kgb->dosen_id) {
                $existingPending->where('dosen_id', $kgb->dosen_id);
            } else {
                $existingPending->where('pegawai_id', $kgb->pegawai_id);
            }

            if ($existingPending->exists()) {
                continue;
            }

            // Create new KGB record
            KenaikanGajiBerkala::create([
                'dosen_id' => $kgb->dosen_id,
                'pegawai_id' => $kgb->pegawai_id,
                'tmt_kgb' => $kgb->tmt_kgb_berikutnya,
                'golongan_ruang' => $kgb->golongan_ruang,
                'masa_kerja_golongan_tahun' => $kgb->masa_kerja_golongan_tahun + 2,
                'masa_kerja_golongan_bulan' => $kgb->masa_kerja_golongan_bulan,
                'gaji_pokok_lama' => $kgb->gaji_pokok_baru,
                'gaji_pokok_baru' => $kgb->gaji_pokok_baru, // Need to calculate from salary table
                'masa_kerja_total_tahun' => $kgb->masa_kerja_total_tahun + 2,
                'masa_kerja_total_bulan' => $kgb->masa_kerja_total_bulan,
                'status' => 'pending',
                'is_otomatis' => true,
            ]);

            $count++;
        }

        return redirect()->route('kepegawaian.kgb.monitoring')
            ->with('success', "Berhasil generate {$count} data KGB baru");
    }
}
