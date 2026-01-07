<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\AktivitasHarian;
use App\Models\Dosen;
use App\Models\UraianKegiatanSkp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AktivitasHarianController extends Controller
{
    /**
     * Get current dosen
     */
    protected function getDosen()
    {
        return Dosen::where('user_id', Auth::id())->first();
    }

    /**
     * Display listing aktivitas harian
     */
    public function index(Request $request)
    {
        $dosen = $this->getDosen();
        
        if (!$dosen) {
            return redirect()->route('dosen.dashboard')
                ->with('error', 'Data dosen tidak ditemukan!');
        }

        $query = AktivitasHarian::where('dosen_id', $dosen->id);

        // Filter tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // Filter bulan & tahun
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal', $request->bulan)
                  ->whereYear('tanggal', $request->tahun);
        } elseif (!$request->filled('tanggal')) {
            // Default: bulan ini
            $query->whereMonth('tanggal', now()->month)
                  ->whereYear('tanggal', now()->year);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('uraian_kegiatan', 'like', "%{$search}%")
                  ->orWhere('output_hasil', 'like', "%{$search}%");
            });
        }

        $aktivitas = $query->with('uraianKegiatanSkp')
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Stats bulan ini
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;
        
        $stats = [
            'total' => AktivitasHarian::where('dosen_id', $dosen->id)
                ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->count(),
            'draft' => AktivitasHarian::where('dosen_id', $dosen->id)
                ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                ->where('status', 'draft')->count(),
            'diajukan' => AktivitasHarian::where('dosen_id', $dosen->id)
                ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                ->where('status', 'diajukan')->count(),
            'disetujui' => AktivitasHarian::where('dosen_id', $dosen->id)
                ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                ->where('status', 'disetujui')->count(),
        ];

        // Get uraian kegiatan untuk dropdown
        $uraianList = UraianKegiatanSkp::where('is_active', true)
            ->where(function ($q) {
                $q->where('tipe_pegawai', 'dosen')
                  ->orWhere('tipe_pegawai', 'semua');
            })
            ->orderBy('kategori')
            ->orderBy('uraian_kegiatan')
            ->get();

        return view('dosen.aktivitas-harian.index', compact('aktivitas', 'stats', 'uraianList', 'dosen'));
    }

    /**
     * Store new aktivitas
     */
    public function store(Request $request)
    {
        $dosen = $this->getDosen();
        
        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan!');
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i|after_or_equal:jam_mulai',
            'uraian_kegiatan_skp_id' => 'nullable|exists:uraian_kegiatan_skp,id',
            'uraian_kegiatan' => 'required|string|max:1000',
            'output_hasil' => 'nullable|string|max:500',
            'volume' => 'nullable|numeric|min:0',
            'satuan' => 'nullable|string|max:50',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi',
            'uraian_kegiatan.required' => 'Uraian kegiatan wajib diisi',
            'jam_selesai.after_or_equal' => 'Jam selesai harus setelah jam mulai',
        ]);

        $validated['dosen_id'] = $dosen->id;
        $validated['status'] = 'draft';

        // If uraian_kegiatan_skp_id selected, copy satuan from master
        if (!empty($validated['uraian_kegiatan_skp_id']) && empty($validated['satuan'])) {
            $uraianSkp = UraianKegiatanSkp::find($validated['uraian_kegiatan_skp_id']);
            if ($uraianSkp) {
                $validated['satuan'] = $uraianSkp->satuan;
            }
        }

        AktivitasHarian::create($validated);

        return redirect()->back()->with('success', 'Aktivitas harian berhasil ditambahkan!');
    }

    /**
     * Update aktivitas
     */
    public function update(Request $request, AktivitasHarian $aktivitasHarian)
    {
        $dosen = $this->getDosen();
        
        // Check ownership
        if ($aktivitasHarian->dosen_id !== $dosen?->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses!');
        }

        // Check if can edit
        if (!$aktivitasHarian->canEdit()) {
            return redirect()->back()->with('error', 'Aktivitas tidak dapat diedit karena sudah diajukan/disetujui!');
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i|after_or_equal:jam_mulai',
            'uraian_kegiatan_skp_id' => 'nullable|exists:uraian_kegiatan_skp,id',
            'uraian_kegiatan' => 'required|string|max:1000',
            'output_hasil' => 'nullable|string|max:500',
            'volume' => 'nullable|numeric|min:0',
            'satuan' => 'nullable|string|max:50',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        // Reset status if rejected
        if ($aktivitasHarian->status === 'ditolak') {
            $validated['status'] = 'draft';
            $validated['catatan_atasan'] = null;
        }

        $aktivitasHarian->update($validated);

        return redirect()->back()->with('success', 'Aktivitas harian berhasil diperbarui!');
    }

    /**
     * Delete aktivitas
     */
    public function destroy(AktivitasHarian $aktivitasHarian)
    {
        $dosen = $this->getDosen();
        
        // Check ownership
        if ($aktivitasHarian->dosen_id !== $dosen?->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses!');
        }

        // Check if can delete
        if (!$aktivitasHarian->canDelete()) {
            return redirect()->back()->with('error', 'Aktivitas tidak dapat dihapus karena sudah diajukan/disetujui!');
        }

        $aktivitasHarian->delete();

        return redirect()->back()->with('success', 'Aktivitas harian berhasil dihapus!');
    }

    /**
     * Ajukan aktivitas ke atasan
     */
    public function ajukan(Request $request)
    {
        $dosen = $this->getDosen();
        
        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan!');
        }

        $validated = $request->validate([
            'aktivitas_ids' => 'required|array|min:1',
            'aktivitas_ids.*' => 'exists:aktivitas_harian,id',
        ], [
            'aktivitas_ids.required' => 'Pilih minimal 1 aktivitas untuk diajukan',
        ]);

        $count = 0;
        foreach ($validated['aktivitas_ids'] as $id) {
            $aktivitas = AktivitasHarian::where('id', $id)
                ->where('dosen_id', $dosen->id)
                ->where('status', 'draft')
                ->first();
            
            if ($aktivitas) {
                $aktivitas->update(['status' => 'diajukan']);
                $count++;
            }
        }

        if ($count > 0) {
            return redirect()->back()->with('success', "{$count} aktivitas berhasil diajukan ke atasan!");
        }

        return redirect()->back()->with('error', 'Tidak ada aktivitas yang dapat diajukan!');
    }

    /**
     * Ajukan semua draft bulan ini
     */
    public function ajukanSemua(Request $request)
    {
        $dosen = $this->getDosen();
        
        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan!');
        }

        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $count = AktivitasHarian::where('dosen_id', $dosen->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'draft')
            ->update(['status' => 'diajukan']);

        if ($count > 0) {
            return redirect()->back()->with('success', "{$count} aktivitas berhasil diajukan ke atasan!");
        }

        return redirect()->back()->with('error', 'Tidak ada aktivitas draft yang dapat diajukan!');
    }

    /**
     * Rekap bulanan
     */
    public function rekap(Request $request)
    {
        $dosen = $this->getDosen();
        
        if (!$dosen) {
            return redirect()->route('dosen.dashboard')
                ->with('error', 'Data dosen tidak ditemukan!');
        }

        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        // Get aktivitas grouped by date
        $aktivitasPerTanggal = AktivitasHarian::where('dosen_id', $dosen->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->with('uraianKegiatanSkp')
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy(function ($item) {
                return $item->tanggal->format('Y-m-d');
            });

        // Stats
        $stats = AktivitasHarian::getRekapBulanan($dosen->id, $bulan, $tahun);

        // Get aktivitas by kategori
        $aktivitasPerKategori = AktivitasHarian::where('dosen_id', $dosen->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->whereNotNull('uraian_kegiatan_skp_id')
            ->with('uraianKegiatanSkp')
            ->get()
            ->groupBy(function ($item) {
                return $item->uraianKegiatanSkp?->kategori ?? 'lainnya';
            });

        return view('dosen.aktivitas-harian.rekap', compact(
            'aktivitasPerTanggal', 
            'aktivitasPerKategori',
            'stats', 
            'dosen', 
            'bulan', 
            'tahun'
        ));
    }
}
