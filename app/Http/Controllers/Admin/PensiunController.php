<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pensiun;
use App\Models\Dosen;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PensiunController extends Controller
{
    public function index(Request $request)
    {
        $query = Pensiun::with(['dosen', 'pegawai']);

        // Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis_pensiun')) {
            $query->where('jenis_pensiun', $request->jenis_pensiun);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_bup', $request->tahun);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('dosen', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%");
                })->orWhereHas('pegawai', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%");
                });
            });
        }

        $pensiun = $query->orderBy('tanggal_bup', 'asc')->paginate(15);

        // Statistics
        $stats = [
            'total' => Pensiun::count(),
            'akan_pensiun_tahun_ini' => Pensiun::pensiunTahunIni()->where('status', 'prediksi')->count(),
            'akan_pensiun_6_bulan' => Pensiun::akanPensiun(6)->where('status', 'prediksi')->count(),
            'dalam_proses' => Pensiun::where('status', 'proses')->count(),
        ];

        $jenisPensiunList = Pensiun::JENIS_PENSIUN;
        $statusList = Pensiun::STATUS;

        return view('kepegawaian.pensiun.index', compact('pensiun', 'stats', 'jenisPensiunList', 'statusList'));
    }

    public function create()
    {
        $dosens = Dosen::where('status', 'Aktif')
            ->whereNotNull('tanggal_lahir')
            ->orderBy('nama')
            ->get();
        $pegawais = Pegawai::where('status', 'Aktif')
            ->whereNotNull('tanggal_lahir')
            ->orderBy('nama')
            ->get();
        $jenisPensiunList = Pensiun::JENIS_PENSIUN;

        return view('kepegawaian.pensiun.create', compact('dosens', 'pegawais', 'jenisPensiunList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_pegawai' => 'required|in:dosen,pegawai',
            'dosen_id' => 'required_if:tipe_pegawai,dosen|nullable|exists:dosen,id',
            'pegawai_id' => 'required_if:tipe_pegawai,pegawai|nullable|exists:pegawai,id',
            'jenis_pensiun' => 'required|in:' . implode(',', array_keys(Pensiun::JENIS_PENSIUN)),
            'usia_bup' => 'required|integer|min:50|max:75',
            'tanggal_lahir' => 'required|date',
            'tanggal_pensiun' => 'required|date',
            'pangkat_terakhir' => 'nullable|string',
            'golongan_terakhir' => 'nullable|string',
            'jabatan_terakhir' => 'nullable|string',
            'masa_kerja_tahun' => 'nullable|integer|min:0',
            'masa_kerja_bulan' => 'nullable|integer|min:0|max:11',
            'gaji_pokok_terakhir' => 'nullable|numeric|min:0',
            'no_taspen' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->except(['tipe_pegawai']);
        $data['status'] = 'prediksi';
        $data['tanggal_bup'] = \Carbon\Carbon::parse($request->tanggal_lahir)->addYears($request->usia_bup);

        Pensiun::create($data);

        return redirect()->route('kepegawaian.pensiun.index')
            ->with('success', 'Data pensiun berhasil ditambahkan');
    }

    public function show(Pensiun $pensiun)
    {
        $pensiun->load(['dosen', 'pegawai', 'diprosesOleh']);
        return view('kepegawaian.pensiun.show', compact('pensiun'));
    }

    public function edit(Pensiun $pensiun)
    {
        $jenisPensiunList = Pensiun::JENIS_PENSIUN;
        $statusList = Pensiun::STATUS;

        return view('kepegawaian.pensiun.edit', compact('pensiun', 'jenisPensiunList', 'statusList'));
    }

    public function update(Request $request, Pensiun $pensiun)
    {
        $request->validate([
            'jenis_pensiun' => 'required|in:' . implode(',', array_keys(Pensiun::JENIS_PENSIUN)),
            'usia_bup' => 'required|integer|min:50|max:75',
            'tanggal_lahir' => 'required|date',
            'tanggal_pensiun' => 'required|date',
            'pangkat_terakhir' => 'nullable|string',
            'golongan_terakhir' => 'nullable|string',
            'jabatan_terakhir' => 'nullable|string',
            'masa_kerja_tahun' => 'nullable|integer|min:0',
            'masa_kerja_bulan' => 'nullable|integer|min:0|max:11',
            'gaji_pokok_terakhir' => 'nullable|numeric|min:0',
            'dana_pensiun' => 'nullable|numeric|min:0',
            'no_taspen' => 'nullable|string',
            'alamat_pensiun' => 'nullable|string',
            'no_telepon_pensiun' => 'nullable|string',
            'no_rekening_pensiun' => 'nullable|string',
            'nama_bank' => 'nullable|string',
            'status' => 'required|in:' . implode(',', array_keys(Pensiun::STATUS)),
            'catatan' => 'nullable|string',
            'dokumen_sk' => 'nullable|file|mimes:pdf|max:5120',
            'dokumen_karpeg' => 'nullable|file|mimes:pdf|max:5120',
            'dokumen_taspen' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $data = $request->except(['dokumen_sk', 'dokumen_karpeg', 'dokumen_taspen']);
        $data['tanggal_bup'] = \Carbon\Carbon::parse($request->tanggal_lahir)->addYears($request->usia_bup);

        if ($request->hasFile('dokumen_sk')) {
            if ($pensiun->dokumen_sk) {
                Storage::disk('public')->delete($pensiun->dokumen_sk);
            }
            $data['dokumen_sk'] = $request->file('dokumen_sk')
                ->store('pensiun/sk', 'public');
        }
        if ($request->hasFile('dokumen_karpeg')) {
            if ($pensiun->dokumen_karpeg) {
                Storage::disk('public')->delete($pensiun->dokumen_karpeg);
            }
            $data['dokumen_karpeg'] = $request->file('dokumen_karpeg')
                ->store('pensiun/karpeg', 'public');
        }
        if ($request->hasFile('dokumen_taspen')) {
            if ($pensiun->dokumen_taspen) {
                Storage::disk('public')->delete($pensiun->dokumen_taspen);
            }
            $data['dokumen_taspen'] = $request->file('dokumen_taspen')
                ->store('pensiun/taspen', 'public');
        }

        $pensiun->update($data);

        return redirect()->route('kepegawaian.pensiun.index')
            ->with('success', 'Data pensiun berhasil diperbarui');
    }

    public function destroy(Pensiun $pensiun)
    {
        // Delete files
        if ($pensiun->dokumen_sk) {
            Storage::disk('public')->delete($pensiun->dokumen_sk);
        }
        if ($pensiun->dokumen_karpeg) {
            Storage::disk('public')->delete($pensiun->dokumen_karpeg);
        }
        if ($pensiun->dokumen_taspen) {
            Storage::disk('public')->delete($pensiun->dokumen_taspen);
        }

        $pensiun->delete();

        return redirect()->route('kepegawaian.pensiun.index')
            ->with('success', 'Data pensiun berhasil dihapus');
    }

    // Proses pensiun
    public function proses(Request $request, Pensiun $pensiun)
    {
        $request->validate([
            'no_sk' => 'required|string',
            'tanggal_sk' => 'required|date',
            'pejabat_penandatangan' => 'nullable|string',
            'dana_pensiun' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        $pensiun->update([
            'status' => 'proses',
            'no_sk' => $request->no_sk,
            'tanggal_sk' => $request->tanggal_sk,
            'pejabat_penandatangan' => $request->pejabat_penandatangan,
            'dana_pensiun' => $request->dana_pensiun,
            'catatan' => $request->catatan,
            'diproses_oleh' => auth()->id(),
        ]);

        return redirect()->route('kepegawaian.pensiun.index')
            ->with('success', 'Pensiun sedang diproses');
    }

    // Selesaikan pensiun
    public function selesaikan(Request $request, Pensiun $pensiun)
    {
        $request->validate([
            'tanggal_serah_terima' => 'required|date',
            'alamat_pensiun' => 'nullable|string',
            'no_telepon_pensiun' => 'nullable|string',
            'no_rekening_pensiun' => 'nullable|string',
            'nama_bank' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $pensiun->update([
            'status' => 'selesai',
            'sudah_serah_terima' => true,
            'tanggal_serah_terima' => $request->tanggal_serah_terima,
            'alamat_pensiun' => $request->alamat_pensiun,
            'no_telepon_pensiun' => $request->no_telepon_pensiun,
            'no_rekening_pensiun' => $request->no_rekening_pensiun,
            'nama_bank' => $request->nama_bank,
            'catatan' => $request->catatan,
        ]);

        // Update status pegawai menjadi tidak aktif
        if ($pensiun->dosen_id) {
            $pensiun->dosen->update(['status' => 'Pensiun']);
        } elseif ($pensiun->pegawai_id) {
            $pensiun->pegawai->update(['status' => 'Pensiun']);
        }

        return redirect()->route('kepegawaian.pensiun.index')
            ->with('success', 'Proses pensiun selesai');
    }

    // Monitoring akan pensiun
    public function monitoring(Request $request)
    {
        $bulan = $request->bulan ?? 12;

        $akanPensiun = Pensiun::with(['dosen', 'pegawai'])
            ->akanPensiun($bulan)
            ->where('status', 'prediksi')
            ->orderBy('tanggal_bup', 'asc')
            ->get();

        // Group by bulan
        $grouped = $akanPensiun->groupBy(function ($item) {
            return $item->tanggal_bup->format('Y-m');
        });

        // Summary by tahun
        $tahunIni = now()->year;
        $summary = [
            'tahun_ini' => Pensiun::whereYear('tanggal_bup', $tahunIni)->count(),
            'tahun_depan' => Pensiun::whereYear('tanggal_bup', $tahunIni + 1)->count(),
            '6_bulan_kedepan' => Pensiun::akanPensiun(6)->count(),
            'dalam_proses' => Pensiun::where('status', 'proses')->count(),
        ];

        return view('kepegawaian.pensiun.monitoring', compact('akanPensiun', 'grouped', 'bulan', 'summary'));
    }

    // Generate prediksi pensiun
    public function generatePrediksi()
    {
        Pensiun::generatePrediksiPensiun();

        return redirect()->route('kepegawaian.pensiun.monitoring')
            ->with('success', 'Prediksi pensiun berhasil di-generate');
    }

    // Laporan pensiun
    public function laporan(Request $request)
    {
        $tahun = $request->tahun ?? now()->year;

        $pensiun = Pensiun::with(['dosen', 'pegawai'])
            ->whereYear('tanggal_bup', $tahun)
            ->orderBy('tanggal_bup', 'asc')
            ->get();

        // Chart data - pensiun per bulan
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData['labels'][] = \Carbon\Carbon::create()->month($i)->locale('id')->monthName;
            $chartData['data'][] = $pensiun->filter(function ($item) use ($i) {
                return $item->tanggal_bup->month == $i;
            })->count();
        }

        return view('kepegawaian.pensiun.laporan', compact('pensiun', 'tahun', 'chartData'));
    }
}
