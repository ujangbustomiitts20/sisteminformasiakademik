<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\KomponenGaji;
use App\Models\Pegawai;
use App\Models\PengaturanGaji;
use App\Models\SlipGaji;
use App\Models\SlipGajiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SlipGajiController extends Controller
{
    /**
     * Display a listing of slip gaji.
     */
    public function index(Request $request)
    {
        $query = SlipGaji::with(['dosen', 'pegawai', 'creator']);

        // Filter by tahun
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        } else {
            $query->where('tahun', date('Y'));
        }

        // Filter by bulan
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by tipe pegawai
        if ($request->filled('tipe')) {
            if ($request->tipe === 'dosen') {
                $query->whereNotNull('dosen_id');
            } elseif ($request->tipe === 'tendik') {
                $query->whereNotNull('pegawai_id');
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_slip', 'like', "%{$search}%")
                  ->orWhereHas('dosen', fn($q) => $q->where('nama', 'like', "%{$search}%"))
                  ->orWhereHas('pegawai', fn($q) => $q->where('nama', 'like', "%{$search}%"));
            });
        }

        $slipGajis = $query->orderBy('tanggal_slip', 'desc')->paginate(15)->withQueryString();

        // Stats
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan');
        
        $statsQuery = SlipGaji::where('tahun', $tahun);
        if ($bulan) {
            $statsQuery->where('bulan', $bulan);
        }

        $stats = [
            'total' => $statsQuery->count(),
            'draft' => (clone $statsQuery)->where('status', 'draft')->count(),
            'diproses' => (clone $statsQuery)->where('status', 'diproses')->count(),
            'disetujui' => (clone $statsQuery)->where('status', 'disetujui')->count(),
            'dibayar' => (clone $statsQuery)->where('status', 'dibayar')->count(),
            'total_gaji' => (clone $statsQuery)->where('status', 'dibayar')->sum('gaji_bersih'),
        ];

        // Available years
        $years = SlipGaji::selectRaw('DISTINCT tahun')->orderBy('tahun', 'desc')->pluck('tahun');
        if ($years->isEmpty()) {
            $years = collect([date('Y')]);
        }

        return view('kepegawaian.slip-gaji.index', compact('slipGajis', 'stats', 'years'));
    }

    /**
     * Show form to generate slip gaji
     */
    public function create(Request $request)
    {
        $dosens = Dosen::where('status', 'Aktif')->orderBy('nama')->get();
        $pegawais = Pegawai::where('status', 'aktif')->orderBy('nama')->get();
        $komponens = KomponenGaji::aktif()->orderBy('urutan')->get();

        return view('kepegawaian.slip-gaji.create', compact('dosens', 'pegawais', 'komponens'));
    }

    /**
     * Generate slip gaji massal
     */
    public function generateMassal(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|min:2020|max:2100',
            'bulan' => 'required|integer|min:1|max:12',
            'tipe' => 'required|in:semua,dosen,tendik',
        ]);

        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $tipe = $request->tipe;

        DB::beginTransaction();
        try {
            $count = 0;

            // Generate untuk dosen
            if (in_array($tipe, ['semua', 'dosen'])) {
                $dosens = Dosen::where('status', 'Aktif')->get();
                foreach ($dosens as $dosen) {
                    // Skip jika sudah ada slip gaji
                    $exists = SlipGaji::where('dosen_id', $dosen->id)
                        ->where('tahun', $tahun)
                        ->where('bulan', $bulan)
                        ->exists();
                    
                    if (!$exists) {
                        $this->createSlipGaji($dosen, null, $tahun, $bulan);
                        $count++;
                    }
                }
            }

            // Generate untuk tendik
            if (in_array($tipe, ['semua', 'tendik'])) {
                $pegawais = Pegawai::where('status', 'aktif')->get();
                foreach ($pegawais as $pegawai) {
                    // Skip jika sudah ada slip gaji
                    $exists = SlipGaji::where('pegawai_id', $pegawai->id)
                        ->where('tahun', $tahun)
                        ->where('bulan', $bulan)
                        ->exists();
                    
                    if (!$exists) {
                        $this->createSlipGaji(null, $pegawai, $tahun, $bulan);
                        $count++;
                    }
                }
            }

            DB::commit();
            return redirect()->route('kepegawaian.slip-gaji.index', ['tahun' => $tahun, 'bulan' => $bulan])
                ->with('success', "Berhasil generate {$count} slip gaji.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Generate slip gaji massal gagal: ' . $e->getMessage());
            return back()->with('error', 'Gagal generate slip gaji: ' . $e->getMessage());
        }
    }

    /**
     * Create slip gaji untuk satu pegawai
     */
    protected function createSlipGaji($dosen, $pegawai, $tahun, $bulan)
    {
        // Get pengaturan gaji
        $pengaturan = PengaturanGaji::where('dosen_id', $dosen?->id)
            ->where('pegawai_id', $pegawai?->id)
            ->where('aktif', true)
            ->first();

        $gajiPokok = $pengaturan?->gaji_pokok ?? 0;

        $slipGaji = SlipGaji::create([
            'dosen_id' => $dosen?->id,
            'pegawai_id' => $pegawai?->id,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'tanggal_slip' => now(),
            'gaji_pokok' => $gajiPokok,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        // Add komponen default
        $komponens = KomponenGaji::aktif()->where('wajib', true)->get();
        foreach ($komponens as $komponen) {
            // Check if there's custom value in pengaturan
            $nilaiKomponen = $komponen->nilai_default;
            if ($pengaturan) {
                $detail = $pengaturan->details()->where('komponen_gaji_id', $komponen->id)->first();
                if ($detail && $detail->aktif) {
                    $nilaiKomponen = $detail->nilai;
                }
            }

            SlipGajiDetail::create([
                'slip_gaji_id' => $slipGaji->id,
                'komponen_gaji_id' => $komponen->id,
                'nama_komponen' => $komponen->nama,
                'jenis' => $komponen->jenis,
                'nilai' => $nilaiKomponen,
            ]);
        }

        // Recalculate totals
        $slipGaji->recalculateFromDetails();

        return $slipGaji;
    }

    /**
     * Store individual slip gaji
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipe_pegawai' => 'required|in:dosen,tendik',
            'pegawai_id' => 'required',
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|min:1|max:12',
            'gaji_pokok' => 'required|numeric|min:0',
        ]);

        $dosenId = $request->tipe_pegawai === 'dosen' ? $request->pegawai_id : null;
        $pegawaiId = $request->tipe_pegawai === 'tendik' ? $request->pegawai_id : null;

        // Check duplicate
        $exists = SlipGaji::where('dosen_id', $dosenId)
            ->where('pegawai_id', $pegawaiId)
            ->where('tahun', $request->tahun)
            ->where('bulan', $request->bulan)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Slip gaji untuk periode ini sudah ada.');
        }

        DB::beginTransaction();
        try {
            $slipGaji = SlipGaji::create([
                'dosen_id' => $dosenId,
                'pegawai_id' => $pegawaiId,
                'tahun' => $request->tahun,
                'bulan' => $request->bulan,
                'tanggal_slip' => now(),
                'gaji_pokok' => $request->gaji_pokok,
                'status' => 'draft',
                'catatan' => $request->catatan,
                'created_by' => auth()->id(),
            ]);

            // Add komponen from request
            if ($request->has('komponen')) {
                foreach ($request->komponen as $komponenId => $data) {
                    if (isset($data['aktif']) && $data['aktif']) {
                        $komponen = KomponenGaji::find($komponenId);
                        if ($komponen) {
                            SlipGajiDetail::create([
                                'slip_gaji_id' => $slipGaji->id,
                                'komponen_gaji_id' => $komponen->id,
                                'nama_komponen' => $komponen->nama,
                                'jenis' => $komponen->jenis,
                                'nilai' => $data['nilai'] ?? 0,
                            ]);
                        }
                    }
                }
            }

            $slipGaji->recalculateFromDetails();

            DB::commit();
            return redirect()->route('kepegawaian.slip-gaji.show', $slipGaji->hashid)
                ->with('success', 'Slip gaji berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Create slip gaji gagal: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat slip gaji: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified slip gaji.
     */
    public function show(SlipGaji $slipGaji)
    {
        $slipGaji->load(['dosen', 'pegawai', 'details.komponenGaji', 'creator', 'approver']);
        
        $pendapatan = $slipGaji->details()->where('jenis', 'pendapatan')->get();
        $potongan = $slipGaji->details()->where('jenis', 'potongan')->get();

        // Get pengaturan gaji untuk perbandingan
        $pengaturanGaji = PengaturanGaji::where('dosen_id', $slipGaji->dosen_id)
            ->where('pegawai_id', $slipGaji->pegawai_id)
            ->where('aktif', true)
            ->first();

        return view('kepegawaian.slip-gaji.show', compact('slipGaji', 'pendapatan', 'potongan', 'pengaturanGaji'));
    }

    /**
     * Show form to edit slip gaji
     */
    public function edit(SlipGaji $slipGaji)
    {
        if (!in_array($slipGaji->status, ['draft', 'diproses'])) {
            return back()->with('error', 'Slip gaji tidak dapat diedit.');
        }

        $slipGaji->load(['dosen', 'pegawai', 'details']);
        $komponens = KomponenGaji::aktif()->orderBy('urutan')->get();

        return view('kepegawaian.slip-gaji.edit', compact('slipGaji', 'komponens'));
    }

    /**
     * Update slip gaji
     */
    public function update(Request $request, SlipGaji $slipGaji)
    {
        if (!in_array($slipGaji->status, ['draft', 'diproses'])) {
            return back()->with('error', 'Slip gaji tidak dapat diedit.');
        }

        $request->validate([
            'gaji_pokok' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $slipGaji->update([
                'gaji_pokok' => $request->gaji_pokok,
                'catatan' => $request->catatan,
            ]);

            // Update komponen
            $slipGaji->details()->delete();
            
            if ($request->has('komponen')) {
                foreach ($request->komponen as $komponenId => $data) {
                    if (isset($data['aktif']) && $data['aktif']) {
                        $komponen = KomponenGaji::find($komponenId);
                        if ($komponen) {
                            SlipGajiDetail::create([
                                'slip_gaji_id' => $slipGaji->id,
                                'komponen_gaji_id' => $komponen->id,
                                'nama_komponen' => $komponen->nama,
                                'jenis' => $komponen->jenis,
                                'nilai' => $data['nilai'] ?? 0,
                                'keterangan' => $data['keterangan'] ?? null,
                            ]);
                        }
                    }
                }
            }

            $slipGaji->recalculateFromDetails();

            DB::commit();
            return redirect()->route('kepegawaian.slip-gaji.show', $slipGaji->hashid)
                ->with('success', 'Slip gaji berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update slip gaji gagal: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui slip gaji: ' . $e->getMessage());
        }
    }

    /**
     * Approve slip gaji
     */
    public function approve(SlipGaji $slipGaji)
    {
        if ($slipGaji->status !== 'diproses') {
            return back()->with('error', 'Status slip gaji tidak valid untuk disetujui.');
        }

        $slipGaji->update([
            'status' => 'disetujui',
            'disetujui_oleh' => auth()->id(),
            'tanggal_disetujui' => now(),
        ]);

        return back()->with('success', 'Slip gaji berhasil disetujui.');
    }

    /**
     * Process slip gaji (mark as diproses)
     */
    public function process(SlipGaji $slipGaji)
    {
        if ($slipGaji->status !== 'draft') {
            return back()->with('error', 'Status slip gaji tidak valid.');
        }

        $slipGaji->update(['status' => 'diproses']);

        return back()->with('success', 'Slip gaji berhasil diproses.');
    }

    /**
     * Mark slip gaji as paid
     */
    public function bayar(Request $request, SlipGaji $slipGaji)
    {
        if ($slipGaji->status !== 'disetujui') {
            return back()->with('error', 'Slip gaji belum disetujui.');
        }

        $request->validate([
            'tanggal_bayar' => 'required|date',
            'metode_pembayaran' => 'required|string',
        ]);

        $slipGaji->update([
            'status' => 'dibayar',
            'tanggal_bayar' => $request->tanggal_bayar,
            'metode_pembayaran' => $request->metode_pembayaran,
            'no_referensi' => $request->no_referensi,
        ]);

        return back()->with('success', 'Slip gaji berhasil ditandai sebagai dibayar.');
    }

    /**
     * Sync slip gaji dari pengaturan gaji
     */
    public function syncFromPengaturan(SlipGaji $slipGaji)
    {
        if (!in_array($slipGaji->status, ['draft', 'diproses'])) {
            return back()->with('error', 'Hanya slip gaji draft/diproses yang dapat disinkronkan.');
        }

        // Get pengaturan gaji
        $pengaturan = PengaturanGaji::where('dosen_id', $slipGaji->dosen_id)
            ->where('pegawai_id', $slipGaji->pegawai_id)
            ->where('aktif', true)
            ->first();

        if (!$pengaturan) {
            return back()->with('error', 'Pengaturan gaji untuk pegawai ini tidak ditemukan.');
        }

        DB::beginTransaction();
        try {
            // Update gaji pokok
            $slipGaji->update(['gaji_pokok' => $pengaturan->gaji_pokok]);

            // Update komponen dari pengaturan
            $slipGaji->details()->delete();
            
            foreach ($pengaturan->details()->where('aktif', true)->get() as $detail) {
                SlipGajiDetail::create([
                    'slip_gaji_id' => $slipGaji->id,
                    'komponen_gaji_id' => $detail->komponen_gaji_id,
                    'nama_komponen' => $detail->komponenGaji->nama,
                    'jenis' => $detail->komponenGaji->jenis,
                    'nilai' => $detail->nilai,
                ]);
            }

            // Recalculate totals
            $slipGaji->recalculateFromDetails();

            DB::commit();
            return back()->with('success', 'Slip gaji berhasil disinkronkan dari Pengaturan Gaji. Gaji pokok: Rp ' . number_format($pengaturan->gaji_pokok, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sync slip gaji gagal: ' . $e->getMessage());
            return back()->with('error', 'Gagal sinkronisasi: ' . $e->getMessage());
        }
    }

    /**
     * Print slip gaji
     */
    public function cetak(SlipGaji $slipGaji)
    {
        $slipGaji->load(['dosen.programStudi', 'pegawai.unitKerja', 'details']);
        
        $pendapatan = $slipGaji->details()->where('jenis', 'pendapatan')->get();
        $potongan = $slipGaji->details()->where('jenis', 'potongan')->get();

        return view('cetak.slip-gaji', compact('slipGaji', 'pendapatan', 'potongan'));
    }

    /**
     * Delete slip gaji
     */
    public function destroy(SlipGaji $slipGaji)
    {
        if (!in_array($slipGaji->status, ['draft'])) {
            return back()->with('error', 'Hanya slip gaji draft yang dapat dihapus.');
        }

        $slipGaji->delete();

        return redirect()->route('kepegawaian.slip-gaji.index')
            ->with('success', 'Slip gaji berhasil dihapus.');
    }

    // ================================
    // KOMPONEN GAJI MANAGEMENT
    // ================================

    /**
     * Display komponen gaji list
     */
    public function komponenIndex()
    {
        $komponens = KomponenGaji::orderBy('jenis')->orderBy('urutan')->get();
        
        return view('kepegawaian.slip-gaji.komponen.index', compact('komponens'));
    }

    /**
     * Store komponen gaji
     */
    public function komponenStore(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:pendapatan,potongan',
            'tipe_nilai' => 'required|in:tetap,persentase',
            'nilai_default' => 'required|numeric|min:0',
        ]);

        KomponenGaji::create([
            'kode' => KomponenGaji::generateKode($request->jenis),
            'nama' => $request->nama,
            'jenis' => $request->jenis,
            'tipe_nilai' => $request->tipe_nilai,
            'nilai_default' => $request->nilai_default,
            'wajib' => $request->boolean('wajib'),
            'keterangan' => $request->keterangan,
            'aktif' => true,
            'urutan' => $request->urutan ?? 0,
        ]);

        return back()->with('success', 'Komponen gaji berhasil ditambahkan.');
    }

    /**
     * Update komponen gaji
     */
    public function komponenUpdate(Request $request, KomponenGaji $komponen)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:pendapatan,potongan',
            'tipe_nilai' => 'required|in:tetap,persentase',
            'nilai_default' => 'required|numeric|min:0',
        ]);

        $komponen->update([
            'nama' => $request->nama,
            'jenis' => $request->jenis,
            'tipe_nilai' => $request->tipe_nilai,
            'nilai_default' => $request->nilai_default,
            'wajib' => $request->boolean('wajib'),
            'keterangan' => $request->keterangan,
            'aktif' => $request->boolean('aktif'),
            'urutan' => $request->urutan ?? 0,
        ]);

        return back()->with('success', 'Komponen gaji berhasil diperbarui.');
    }

    /**
     * Delete komponen gaji
     */
    public function komponenDestroy(KomponenGaji $komponen)
    {
        // Check if used in slip gaji
        $used = SlipGajiDetail::where('komponen_gaji_id', $komponen->id)->exists();
        if ($used) {
            return back()->with('error', 'Komponen gaji sudah digunakan di slip gaji dan tidak dapat dihapus.');
        }

        $komponen->delete();

        return back()->with('success', 'Komponen gaji berhasil dihapus.');
    }

    // ================================
    // PENGATURAN GAJI PER PEGAWAI
    // ================================

    /**
     * Display pengaturan gaji list
     */
    public function pengaturanIndex(Request $request)
    {
        $query = PengaturanGaji::with(['dosen', 'pegawai', 'details.komponenGaji']);

        if ($request->filled('tipe')) {
            if ($request->tipe === 'dosen') {
                $query->whereNotNull('dosen_id');
            } elseif ($request->tipe === 'tendik') {
                $query->whereNotNull('pegawai_id');
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('dosen', fn($q) => $q->where('nama', 'like', "%{$search}%"))
                  ->orWhereHas('pegawai', fn($q) => $q->where('nama', 'like', "%{$search}%"));
            });
        }

        $pengaturans = $query->paginate(15)->withQueryString();
        $dosens = Dosen::where('status', 'Aktif')->orderBy('nama')->get();
        $pegawais = Pegawai::where('status', 'aktif')->orderBy('nama')->get();
        $komponens = KomponenGaji::aktif()->orderBy('urutan')->get();

        return view('kepegawaian.slip-gaji.pengaturan.index', compact('pengaturans', 'dosens', 'pegawais', 'komponens'));
    }

    /**
     * Store pengaturan gaji
     */
    public function pengaturanStore(Request $request)
    {
        $request->validate([
            'tipe_pegawai' => 'required|in:dosen,tendik',
            'pegawai_id' => 'required',
            'gaji_pokok' => 'required|numeric|min:0',
        ]);

        $dosenId = $request->tipe_pegawai === 'dosen' ? $request->pegawai_id : null;
        $pegawaiId = $request->tipe_pegawai === 'tendik' ? $request->pegawai_id : null;

        // Check duplicate
        $exists = PengaturanGaji::where('dosen_id', $dosenId)
            ->where('pegawai_id', $pegawaiId)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Pengaturan gaji untuk pegawai ini sudah ada.');
        }

        DB::beginTransaction();
        try {
            $pengaturan = PengaturanGaji::create([
                'dosen_id' => $dosenId,
                'pegawai_id' => $pegawaiId,
                'gaji_pokok' => $request->gaji_pokok,
                'aktif' => true,
                'berlaku_mulai' => $request->berlaku_mulai,
                'catatan' => $request->catatan,
            ]);

            // Add komponen
            if ($request->has('komponen')) {
                foreach ($request->komponen as $komponenId => $data) {
                    if (isset($data['aktif']) && $data['aktif']) {
                        $pengaturan->details()->create([
                            'komponen_gaji_id' => $komponenId,
                            'nilai' => $data['nilai'] ?? 0,
                            'aktif' => true,
                        ]);
                    }
                }
            }

            DB::commit();
            return back()->with('success', 'Pengaturan gaji berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store pengaturan gaji gagal: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan pengaturan gaji: ' . $e->getMessage());
        }
    }

    /**
     * Show pengaturan gaji detail
     */
    public function pengaturanShow(PengaturanGaji $pengaturan)
    {
        $pengaturan->load(['dosen', 'pegawai', 'details.komponenGaji']);
        $komponens = KomponenGaji::aktif()->orderBy('urutan')->get();

        return view('kepegawaian.slip-gaji.pengaturan.show', compact('pengaturan', 'komponens'));
    }

    /**
     * Update pengaturan gaji
     */
    public function pengaturanUpdate(Request $request, PengaturanGaji $pengaturan)
    {
        $request->validate([
            'gaji_pokok' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $pengaturan->update([
                'gaji_pokok' => $request->gaji_pokok,
                'aktif' => $request->boolean('aktif'),
                'berlaku_mulai' => $request->berlaku_mulai,
                'berlaku_sampai' => $request->berlaku_sampai,
                'catatan' => $request->catatan,
            ]);

            // Update komponen
            $pengaturan->details()->delete();
            
            if ($request->has('komponen')) {
                foreach ($request->komponen as $komponenId => $data) {
                    if (isset($data['aktif']) && $data['aktif']) {
                        $pengaturan->details()->create([
                            'komponen_gaji_id' => $komponenId,
                            'nilai' => $data['nilai'] ?? 0,
                            'aktif' => true,
                        ]);
                    }
                }
            }

            DB::commit();
            return back()->with('success', 'Pengaturan gaji berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update pengaturan gaji gagal: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui pengaturan gaji: ' . $e->getMessage());
        }
    }

    /**
     * Delete pengaturan gaji
     */
    public function pengaturanDestroy(PengaturanGaji $pengaturan)
    {
        $pengaturan->delete();

        return back()->with('success', 'Pengaturan gaji berhasil dihapus.');
    }

    /**
     * Generate pengaturan gaji massal untuk semua dosen/tendik
     */
    public function pengaturanGenerateMassal(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:dosen,tendik,semua',
            'gaji_pokok' => 'required|numeric|min:0',
        ]);

        $tipe = $request->tipe;
        $gajiPokok = $request->gaji_pokok;
        $overwrite = $request->boolean('overwrite');
        $komponenData = $request->input('komponen', []);

        DB::beginTransaction();
        try {
            $count = 0;
            $updated = 0;

            // Generate untuk dosen
            if (in_array($tipe, ['semua', 'dosen'])) {
                $dosens = Dosen::where('status', 'Aktif')->get();
                foreach ($dosens as $dosen) {
                    $existing = PengaturanGaji::where('dosen_id', $dosen->id)->first();
                    
                    if ($existing) {
                        if ($overwrite) {
                            $existing->update([
                                'gaji_pokok' => $gajiPokok,
                                'aktif' => true,
                            ]);
                            // Update komponen
                            $this->updatePengaturanKomponen($existing, $komponenData);
                            $updated++;
                        }
                    } else {
                        $pengaturan = PengaturanGaji::create([
                            'dosen_id' => $dosen->id,
                            'pegawai_id' => null,
                            'gaji_pokok' => $gajiPokok,
                            'aktif' => true,
                            'berlaku_mulai' => now(),
                        ]);
                        // Add komponen
                        $this->updatePengaturanKomponen($pengaturan, $komponenData);
                        $count++;
                    }
                }
            }

            // Generate untuk tendik
            if (in_array($tipe, ['semua', 'tendik'])) {
                $pegawais = Pegawai::where('status', 'aktif')->get();
                foreach ($pegawais as $pegawai) {
                    $existing = PengaturanGaji::where('pegawai_id', $pegawai->id)->first();
                    
                    if ($existing) {
                        if ($overwrite) {
                            $existing->update([
                                'gaji_pokok' => $gajiPokok,
                                'aktif' => true,
                            ]);
                            // Update komponen
                            $this->updatePengaturanKomponen($existing, $komponenData);
                            $updated++;
                        }
                    } else {
                        $pengaturan = PengaturanGaji::create([
                            'dosen_id' => null,
                            'pegawai_id' => $pegawai->id,
                            'gaji_pokok' => $gajiPokok,
                            'aktif' => true,
                            'berlaku_mulai' => now(),
                        ]);
                        // Add komponen
                        $this->updatePengaturanKomponen($pengaturan, $komponenData);
                        $count++;
                    }
                }
            }

            DB::commit();
            
            $message = "Berhasil membuat {$count} pengaturan gaji baru.";
            if ($updated > 0) {
                $message .= " {$updated} pengaturan diperbarui.";
            }
            
            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Generate pengaturan gaji massal gagal: ' . $e->getMessage());
            return back()->with('error', 'Gagal generate pengaturan gaji: ' . $e->getMessage());
        }
    }

    /**
     * Helper untuk update komponen pengaturan gaji
     */
    protected function updatePengaturanKomponen(PengaturanGaji $pengaturan, array $komponenData)
    {
        // Hapus komponen lama
        $pengaturan->details()->delete();
        
        // Tambah komponen baru
        foreach ($komponenData as $komponenId => $data) {
            if (isset($data['aktif']) && $data['aktif']) {
                $pengaturan->details()->create([
                    'komponen_gaji_id' => $komponenId,
                    'nilai' => $data['nilai'] ?? 0,
                    'aktif' => true,
                ]);
            }
        }
    }
}
