<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanLembur;
use App\Models\TarifLembur;
use App\Models\Dosen;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class LemburController extends Controller
{
    public function index(Request $request)
    {
        $query = PengajuanLembur::with(['dosen', 'pegawai', 'disetujuiOleh']);

        if ($request->filled('status')) {
            // Handle 'pending' as 'diajukan' for consistency
            $status = $request->status === 'pending' ? 'diajukan' : $request->status;
            $query->where('status', $status);
        }
        if ($request->filled('bulan')) {
            // Format dari input type="month" adalah Y-m (contoh: 2026-01)
            $parts = explode('-', $request->bulan);
            if (count($parts) == 2) {
                $query->whereYear('tanggal', $parts[0])
                      ->whereMonth('tanggal', $parts[1]);
            }
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('alasan', 'like', "%{$search}%")
                    ->orWhereHas('dosen', fn($q) => $q->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('pegawai', fn($q) => $q->where('nama', 'like', "%{$search}%"));
            });
        }

        $lemburList = $query->orderBy('created_at', 'desc')->paginate(15);
        $dosenList = Dosen::orderBy('nama')->get();
        $pegawaiList = Pegawai::orderBy('nama')->get();

        $stats = [
            'total' => PengajuanLembur::count(),
            'pending' => PengajuanLembur::whereIn('status', ['diajukan', 'menunggu_admin'])->count(),
            'menunggu_kaprodi' => PengajuanLembur::where('status', 'diajukan')->where('status_kaprodi', 'pending')->count(),
            'menunggu_admin' => PengajuanLembur::where('status', 'menunggu_admin')->count(),
            'disetujui' => PengajuanLembur::where('status', 'disetujui')->count(),
            'total_jam' => PengajuanLembur::where('status', 'disetujui')->bulanIni()->sum('durasi_jam'),
            'total_biaya_bulan_ini' => PengajuanLembur::where('status', 'disetujui')->bulanIni()->sum('total_bayar'),
        ];

        return view('kepegawaian.lembur.index', compact('lemburList', 'dosenList', 'pegawaiList', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_pegawai' => 'required|in:dosen,pegawai',
            'dosen_id' => 'required_if:tipe_pegawai,dosen|nullable|exists:dosen,id',
            'pegawai_id' => 'required_if:tipe_pegawai,pegawai|nullable|exists:pegawai,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'alasan' => 'required|string',
            'pekerjaan_yang_dilakukan' => 'nullable|string',
        ]);

        $data = $request->only(['tanggal', 'jam_mulai', 'jam_selesai', 'alasan', 'pekerjaan_yang_dilakukan']);
        $data['status'] = 'diajukan';
        $data['status_kaprodi'] = 'pending'; // Set default status kaprodi
        $data['approval_level'] = 'kaprodi'; // Default approval level

        if ($request->tipe_pegawai === 'dosen') {
            $data['dosen_id'] = $request->dosen_id;
        } else {
            $data['pegawai_id'] = $request->pegawai_id;
            // Untuk pegawai non-dosen, langsung ke admin
            $data['status_kaprodi'] = null;
            $data['approval_level'] = 'admin';
        }

        PengajuanLembur::create($data);

        return redirect()->route('kepegawaian.lembur.index')
            ->with('success', 'Pengajuan lembur berhasil ditambahkan.');
    }

    public function show(PengajuanLembur $lembur)
    {
        $lembur->load(['dosen', 'pegawai', 'disetujuiOleh']);
        return view('kepegawaian.lembur.show', compact('lembur'));
    }

    public function update(Request $request, PengajuanLembur $lembur)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'alasan' => 'required|string',
            'pekerjaan_yang_dilakukan' => 'nullable|string',
        ]);

        $lembur->update($request->only(['tanggal', 'jam_mulai', 'jam_selesai', 'alasan', 'pekerjaan_yang_dilakukan']));

        return redirect()->route('kepegawaian.lembur.index')
            ->with('success', 'Pengajuan lembur berhasil diperbarui.');
    }

    public function destroy(PengajuanLembur $lembur)
    {
        $lembur->delete();
        return redirect()->route('kepegawaian.lembur.index')
            ->with('success', 'Pengajuan lembur berhasil dihapus.');
    }

    public function approve(Request $request, PengajuanLembur $lembur)
    {
        // Admin bisa approve yang sudah disetujui kaprodi atau pengajuan langsung
        if (!in_array($lembur->status, ['diajukan', 'menunggu_admin'])) {
            return redirect()->back()->with('error', 'Lembur tidak dalam status yang dapat disetujui.');
        }

        $request->validate([
            'tarif_per_jam' => 'required|numeric|min:0',
        ]);

        $lembur->update([
            'status' => 'disetujui',
            'disetujui_oleh' => auth()->id(),
            'tanggal_disetujui' => now(),
            'tarif_per_jam' => $request->tarif_per_jam,
            'total_bayar' => $lembur->durasi_jam * $request->tarif_per_jam,
            'catatan_approval' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Lembur berhasil disetujui.');
    }

    public function reject(Request $request, PengajuanLembur $lembur)
    {
        if (!in_array($lembur->status, ['diajukan', 'menunggu_admin'])) {
            return redirect()->back()->with('error', 'Lembur tidak dalam status yang dapat ditolak.');
        }

        $request->validate([
            'catatan' => 'required|string',
        ]);

        $lembur->update([
            'status' => 'ditolak',
            'disetujui_oleh' => auth()->id(),
            'tanggal_disetujui' => now(),
            'catatan_approval' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Lembur berhasil ditolak.');
    }

    public function selesai(PengajuanLembur $lembur)
    {
        if ($lembur->status !== 'disetujui') {
            return redirect()->back()->with('error', 'Lembur tidak dalam status disetujui.');
        }

        $lembur->update(['status' => 'selesai']);

        return redirect()->back()->with('success', 'Lembur telah selesai.');
    }

    // Tarif Lembur
    public function tarifIndex()
    {
        $tarifList = TarifLembur::paginate(15);
        return view('kepegawaian.lembur.tarif', compact('tarifList'));
    }

    public function tarifStore(Request $request)
    {
        $request->validate([
            'kode' => 'nullable|string|max:20|unique:tarif_lembur,kode',
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'jenis_hari' => 'required|in:kerja,libur,libur_nasional',
            'jenis_jam' => 'required|in:jam_pertama,jam_kedua_dst,semua',
            'persentase' => 'nullable|numeric|min:0|max:1000',
            'nominal_tetap' => 'nullable|numeric|min:0',
        ]);

        TarifLembur::create($request->all());

        return redirect()->route('kepegawaian.lembur.tarif.index')
            ->with('success', 'Tarif lembur berhasil ditambahkan.');
    }

    public function tarifUpdate(Request $request, TarifLembur $tarif)
    {
        $request->validate([
            'kode' => 'nullable|string|max:20|unique:tarif_lembur,kode,' . $tarif->id,
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'jenis_hari' => 'required|in:kerja,libur,libur_nasional',
            'jenis_jam' => 'required|in:jam_pertama,jam_kedua_dst,semua',
            'persentase' => 'nullable|numeric|min:0|max:1000',
            'nominal_tetap' => 'nullable|numeric|min:0',
            'is_active' => 'nullable',
        ]);

        $tarif->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'jenis_hari' => $request->jenis_hari,
            'jenis_jam' => $request->jenis_jam,
            'persentase' => $request->persentase ?? 100,
            'nominal_tetap' => $request->nominal_tetap ?? 0,
            'is_active' => $request->is_active == '1',
        ]);

        return redirect()->route('kepegawaian.lembur.tarif.index')
            ->with('success', 'Tarif lembur berhasil diperbarui.');
    }

    public function tarifDestroy(TarifLembur $tarif)
    {
        $tarif->delete();
        return redirect()->route('kepegawaian.lembur.tarif.index')
            ->with('success', 'Tarif lembur berhasil dihapus.');
    }

    // Laporan
    public function laporan(Request $request)
    {
        $bulanMulai = $request->bulan_mulai ?? date('Y-01');
        $bulanSelesai = $request->bulan_selesai ?? date('Y-m');
        $status = $request->status;

        // Parse bulan mulai dan selesai dengan error handling
        try {
            $startDate = \Carbon\Carbon::createFromFormat('Y-m', $bulanMulai)->startOfMonth();
            $endDate = \Carbon\Carbon::createFromFormat('Y-m', $bulanSelesai)->endOfMonth();
        } catch (\Exception $e) {
            $startDate = now()->startOfYear();
            $endDate = now()->endOfMonth();
            $bulanMulai = $startDate->format('Y-m');
            $bulanSelesai = $endDate->format('Y-m');
        }

        // Query lembur sesuai filter
        $query = PengajuanLembur::with(['dosen', 'pegawai'])
            ->whereBetween('tanggal', [$startDate, $endDate]);
        
        if ($status) {
            $query->where('status', $status);
        } else {
            $query->whereIn('status', ['selesai', 'disetujui']);
        }

        $lemburList = $query->orderBy('tanggal')->get();

        // Rekap per pegawai
        $rekapPegawai = $lemburList->groupBy(function ($item) {
            return $item->dosen_id ? 'dosen_' . $item->dosen_id : 'pegawai_' . $item->pegawai_id;
        })->map(function ($items) {
            $first = $items->first();
            return (object) [
                'nama_pegawai' => $first->dosen?->nama ?? $first->pegawai?->nama ?? '-',
                'tipe' => $first->dosen_id ? 'Dosen' : 'Pegawai',
                'jumlah_lembur' => $items->count(),
                'total_jam' => $items->sum('durasi_jam'),
                'total_upah' => $items->sum('total_bayar'),
            ];
        })->values();

        // Summary
        $summary = [
            'total_pegawai' => $rekapPegawai->count(),
            'total_jam' => $lemburList->sum('durasi_jam'),
            'total_upah' => $lemburList->sum('total_bayar'),
        ];

        // Chart data - per bulan dalam range
        $chartLabels = [];
        $chartJam = [];
        $chartUpah = [];
        
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $monthLabel = $current->translatedFormat('M Y');
            $chartLabels[] = $monthLabel;
            
            $monthData = $lemburList->filter(function ($item) use ($current) {
                return \Carbon\Carbon::parse($item->tanggal)->format('Y-m') === $current->format('Y-m');
            });
            
            $chartJam[] = $monthData->sum('durasi_jam');
            $chartUpah[] = round($monthData->sum('total_bayar') / 1000000, 2); // dalam juta
            
            $current->addMonth();
        }

        // Filter values untuk view
        $filter = [
            'bulan_mulai' => $bulanMulai,
            'bulan_selesai' => $bulanSelesai,
            'status' => $status,
        ];

        return view('kepegawaian.lembur.laporan', compact(
            'lemburList', 
            'rekapPegawai', 
            'summary', 
            'chartLabels', 
            'chartJam', 
            'chartUpah',
            'filter'
        ));
    }
}
