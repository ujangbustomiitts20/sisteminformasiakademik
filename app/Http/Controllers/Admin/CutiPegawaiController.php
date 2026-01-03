<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CutiPegawai;
use App\Models\SaldoCuti;
use App\Models\Dosen;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CutiPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = CutiPegawai::with(['dosen', 'pegawai', 'atasanLangsung', 'disetujuiOleh']);

        // Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis_cuti')) {
            $query->where('jenis_cuti', $request->jenis_cuti);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_pengajuan', 'like', "%{$search}%")
                  ->orWhereHas('dosen', function ($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('pegawai', function ($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
            });
        }
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_mulai', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_selesai', '<=', $request->tanggal_sampai);
        }

        $cutiPegawai = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total' => CutiPegawai::count(),
            'pending' => CutiPegawai::where('status', 'diajukan')->count(),
            'diajukan' => CutiPegawai::where('status', 'diajukan')->count(),
            'disetujui_atasan' => CutiPegawai::where('status', 'disetujui_atasan')->count(),
            'disetujui' => CutiPegawai::where('status', 'disetujui')->count(),
            'ditolak' => CutiPegawai::where('status', 'ditolak')->count(),
        ];

        return view('kepegawaian.cuti.index', compact('cutiPegawai', 'stats'));
    }

    public function create()
    {
        $dosens = Dosen::where('status', 'Aktif')->orderBy('nama')->get();
        $pegawais = Pegawai::where('status', 'Aktif')->orderBy('nama')->get();
        $jenisCuti = CutiPegawai::JENIS_CUTI;

        return view('kepegawaian.cuti.create', compact('dosens', 'pegawais', 'jenisCuti'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_pegawai' => 'required|in:dosen,pegawai',
            'dosen_id' => 'required_if:tipe_pegawai,dosen|nullable|exists:dosen,id',
            'pegawai_id' => 'required_if:tipe_pegawai,pegawai|nullable|exists:pegawai,id',
            'jenis_cuti' => 'required|in:' . implode(',', array_keys(CutiPegawai::JENIS_CUTI)),
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string',
            'alamat_selama_cuti' => 'nullable|string',
            'no_telepon_selama_cuti' => 'nullable|string',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['tipe_pegawai', 'dokumen_pendukung']);

        // Handle file upload
        if ($request->hasFile('dokumen_pendukung')) {
            $data['dokumen_pendukung'] = $request->file('dokumen_pendukung')
                ->store('cuti/dokumen', 'public');
        }

        // Get saldo cuti
        $dosenId = $request->tipe_pegawai == 'dosen' ? $request->dosen_id : null;
        $pegawaiId = $request->tipe_pegawai == 'pegawai' ? $request->pegawai_id : null;
        
        $saldo = SaldoCuti::getOrCreate($dosenId, $pegawaiId);
        $data['sisa_cuti_sebelum'] = $saldo->sisa_cuti;

        // Set status awal
        $data['status'] = 'diajukan';

        CutiPegawai::create($data);

        return redirect()->route('kepegawaian.cuti.index')
            ->with('success', 'Pengajuan cuti berhasil dibuat');
    }

    public function show(CutiPegawai $cuti)
    {
        $cuti->load(['dosen', 'pegawai', 'atasanLangsung', 'disetujuiOleh']);
        return view('kepegawaian.cuti.show', compact('cuti'));
    }

    public function edit(CutiPegawai $cuti)
    {
        if (!in_array($cuti->status, ['draft', 'diajukan'])) {
            return redirect()->route('kepegawaian.cuti.index')
                ->with('error', 'Cuti yang sudah diproses tidak dapat diedit');
        }

        $dosens = Dosen::where('status', 'Aktif')->orderBy('nama')->get();
        $pegawais = Pegawai::where('status', 'Aktif')->orderBy('nama')->get();
        $jenisCuti = CutiPegawai::JENIS_CUTI;

        return view('kepegawaian.cuti.edit', compact('cuti', 'dosens', 'pegawais', 'jenisCuti'));
    }

    public function update(Request $request, CutiPegawai $cuti)
    {
        if (!in_array($cuti->status, ['draft', 'diajukan'])) {
            return redirect()->route('kepegawaian.cuti.index')
                ->with('error', 'Cuti yang sudah diproses tidak dapat diedit');
        }

        $request->validate([
            'jenis_cuti' => 'required|in:' . implode(',', array_keys(CutiPegawai::JENIS_CUTI)),
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string',
            'alamat_selama_cuti' => 'nullable|string',
            'no_telepon_selama_cuti' => 'nullable|string',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['dokumen_pendukung']);

        if ($request->hasFile('dokumen_pendukung')) {
            // Delete old file
            if ($cuti->dokumen_pendukung) {
                Storage::disk('public')->delete($cuti->dokumen_pendukung);
            }
            $data['dokumen_pendukung'] = $request->file('dokumen_pendukung')
                ->store('cuti/dokumen', 'public');
        }

        $cuti->update($data);

        return redirect()->route('kepegawaian.cuti.index')
            ->with('success', 'Pengajuan cuti berhasil diperbarui');
    }

    public function destroy(CutiPegawai $cuti)
    {
        if (!in_array($cuti->status, ['draft', 'diajukan'])) {
            return redirect()->route('kepegawaian.cuti.index')
                ->with('error', 'Cuti yang sudah diproses tidak dapat dihapus');
        }

        if ($cuti->dokumen_pendukung) {
            Storage::disk('public')->delete($cuti->dokumen_pendukung);
        }

        $cuti->delete();

        return redirect()->route('kepegawaian.cuti.index')
            ->with('success', 'Pengajuan cuti berhasil dihapus');
    }

    public function approve(Request $request, CutiPegawai $cuti)
    {
        $request->validate([
            'catatan_admin' => 'nullable|string',
        ]);

        if ($cuti->status != 'diajukan') {
            return redirect()->back()->with('error', 'Status cuti tidak valid untuk disetujui');
        }

        // Update saldo cuti
        $saldo = SaldoCuti::getOrCreate($cuti->dosen_id, $cuti->pegawai_id);
        
        if ($cuti->jenis_cuti == 'tahunan') {
            if ($saldo->sisa_cuti < $cuti->jumlah_hari) {
                return redirect()->back()->with('error', 'Sisa cuti tidak mencukupi');
            }
            $saldo->cuti_digunakan += $cuti->jumlah_hari;
            $saldo->sisa_cuti -= $cuti->jumlah_hari;
            $saldo->save();
        }

        $cuti->update([
            'status' => 'disetujui',
            'disetujui_oleh' => auth()->id(),
            'tanggal_disetujui' => now(),
            'catatan_admin' => $request->catatan_admin,
            'sisa_cuti_sesudah' => $saldo->sisa_cuti,
        ]);

        return redirect()->route('kepegawaian.cuti.index')
            ->with('success', 'Cuti berhasil disetujui');
    }

    public function reject(Request $request, CutiPegawai $cuti)
    {
        $request->validate([
            'catatan_admin' => 'required|string',
        ]);

        if ($cuti->status != 'diajukan') {
            return redirect()->back()->with('error', 'Status cuti tidak valid untuk ditolak');
        }

        $cuti->update([
            'status' => 'ditolak',
            'disetujui_oleh' => auth()->id(),
            'tanggal_disetujui' => now(),
            'catatan_admin' => $request->catatan_admin,
        ]);

        return redirect()->route('kepegawaian.cuti.index')
            ->with('success', 'Cuti berhasil ditolak');
    }

    // Saldo Cuti
    public function saldoCuti(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        
        $query = SaldoCuti::with(['dosen', 'pegawai'])
            ->where('tahun', $tahun);

        // Filter berdasarkan tipe pegawai
        if ($request->filled('tipe')) {
            if ($request->tipe == 'dosen') {
                $query->whereNotNull('dosen_id');
            } elseif ($request->tipe == 'pegawai') {
                $query->whereNotNull('pegawai_id');
            }
        }

        // Filter berdasarkan pencarian (NIP/NIDN atau nama)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('dosen', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nidn', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%");
                })
                ->orWhereHas('pegawai', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%");
                });
            });
        }

        $saldoCuti = $query->paginate(20)->withQueryString();

        $tahunList = SaldoCuti::selectRaw('DISTINCT tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('kepegawaian.cuti.saldo', compact('saldoCuti', 'tahun', 'tahunList'));
    }

    public function generateSaldoCuti(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        // Generate untuk semua dosen aktif
        $dosens = Dosen::where('status', 'Aktif')->get();
        foreach ($dosens as $dosen) {
            SaldoCuti::getOrCreate($dosen->id, null, $tahun);
        }

        // Generate untuk semua pegawai aktif
        $pegawais = Pegawai::where('status', 'Aktif')->get();
        foreach ($pegawais as $pegawai) {
            SaldoCuti::getOrCreate(null, $pegawai->id, $tahun);
        }

        return redirect()->route('kepegawaian.cuti.saldo', ['tahun' => $tahun])
            ->with('success', 'Saldo cuti berhasil di-generate untuk tahun ' . $tahun);
    }

    public function updateSaldoCuti(Request $request, SaldoCuti $saldo)
    {
        $request->validate([
            'jatah_cuti' => 'required|integer|min:0',
            'cuti_digunakan' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $saldo->update([
            'jatah_cuti' => $request->jatah_cuti,
            'cuti_digunakan' => $request->cuti_digunakan,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('kepegawaian.cuti.saldo', ['tahun' => $saldo->tahun])
            ->with('success', 'Saldo cuti berhasil diperbarui');
    }
}
