<?php

namespace App\Http\Controllers;

use App\Models\Yudisium;
use App\Models\PendaftaranWisuda;
use App\Models\PeriodeWisuda;
use App\Models\Mahasiswa;
use App\Models\Krs;
use App\Models\Nilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class YudisiumController extends Controller
{
    /**
     * Display list of yudisium
     */
    public function index(Request $request)
    {
        $query = Yudisium::with(['mahasiswa.programStudi', 'pendaftaranWisuda.periodeWisuda']);

        if ($request->filled('periode_wisuda_id')) {
            $query->whereHas('pendaftaranWisuda', function ($q) use ($request) {
                $q->where('periode_wisuda_id', $request->periode_wisuda_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('predikat')) {
            $query->where('predikat', $request->predikat);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('mahasiswa', function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $yudisium = $query->orderBy('tanggal_yudisium', 'desc')->paginate(15);
        
        $periodeWisuda = PeriodeWisuda::orderBy('tanggal_wisuda', 'desc')->get();

        $stats = [
            'total' => Yudisium::count(),
            'pending' => Yudisium::pending()->count(),
            'disetujui' => Yudisium::disetujui()->count(),
            'cum_laude' => Yudisium::where('predikat', Yudisium::PREDIKAT_CUM_LAUDE)->disetujui()->count(),
        ];

        return view('akademik.yudisium.index', compact('yudisium', 'periodeWisuda', 'stats'));
    }

    /**
     * Show candidates for yudisium (pendaftaran that passed verification)
     */
    public function candidates(Request $request)
    {
        $query = PendaftaranWisuda::with(['mahasiswa.programStudi', 'periodeWisuda'])
            ->where('status', 'Lolos Yudisium')
            ->doesntHave('yudisium');

        if ($request->filled('periode_wisuda_id')) {
            $query->where('periode_wisuda_id', $request->periode_wisuda_id);
        }

        $candidates = $query->orderBy('created_at', 'desc')->paginate(15);
        $periodeWisuda = PeriodeWisuda::active()->orderBy('tanggal_wisuda', 'desc')->get();

        return view('akademik.yudisium.candidates', compact('candidates', 'periodeWisuda'));
    }

    /**
     * Process yudisium for a pendaftaran
     */
    public function create(PendaftaranWisuda $pendaftaran)
    {
        if ($pendaftaran->status !== 'Lolos Yudisium') {
            return back()->with('error', 'Pendaftaran belum lolos verifikasi yudisium.');
        }

        if ($pendaftaran->yudisium) {
            return back()->with('error', 'Yudisium sudah pernah diproses untuk pendaftaran ini.');
        }

        $pendaftaran->load(['mahasiswa.programStudi', 'periodeWisuda']);
        $mahasiswa = $pendaftaran->mahasiswa;

        // Calculate academic data
        $academicData = $this->calculateAcademicData($mahasiswa);

        return view('akademik.yudisium.create', compact('pendaftaran', 'mahasiswa', 'academicData'));
    }

    /**
     * Store yudisium
     */
    public function store(Request $request, PendaftaranWisuda $pendaftaran)
    {
        $validated = $request->validate([
            'tanggal_yudisium' => 'required|date',
            'tanggal_lulus' => 'required|date',
            'ipk_akhir' => 'required|numeric|min:0|max:4',
            'total_sks_lulus' => 'required|integer|min:1',
            'no_ijazah' => 'nullable|string|max:100',
            'no_transkrip' => 'nullable|string|max:100',
            'catatan' => 'nullable|string|max:500',
        ]);

        $mahasiswa = $pendaftaran->mahasiswa;

        // Calculate masa studi
        $tanggalMasuk = $mahasiswa->created_at; // Atau field khusus tanggal_masuk
        $tanggalLulus = \Carbon\Carbon::parse($validated['tanggal_lulus']);
        $masaStudiBulan = $tanggalMasuk->diffInMonths($tanggalLulus);

        // Calculate predikat
        $predikat = Yudisium::hitungPredikat(
            $validated['ipk_akhir'],
            $masaStudiBulan,
            $validated['total_sks_lulus']
        );

        DB::transaction(function () use ($validated, $pendaftaran, $mahasiswa, $masaStudiBulan, $predikat) {
            $yudisium = Yudisium::create([
                'pendaftaran_wisuda_id' => $pendaftaran->id,
                'mahasiswa_id' => $mahasiswa->id,
                'tanggal_yudisium' => $validated['tanggal_yudisium'],
                'ipk_akhir' => $validated['ipk_akhir'],
                'total_sks_lulus' => $validated['total_sks_lulus'],
                'predikat' => $predikat,
                'tanggal_masuk' => $mahasiswa->created_at,
                'tanggal_lulus' => $validated['tanggal_lulus'],
                'masa_studi_bulan' => $masaStudiBulan,
                'no_ijazah' => $validated['no_ijazah'],
                'no_transkrip' => $validated['no_transkrip'],
                'status' => 'Pending',
                'catatan' => $validated['catatan'],
            ]);
        });

        return redirect()->route('yudisium.index')
            ->with('success', 'Data yudisium berhasil dibuat.');
    }

    /**
     * Show yudisium detail
     */
    public function show(Yudisium $yudisium)
    {
        $yudisium->load([
            'mahasiswa.programStudi.fakultas',
            'pendaftaranWisuda.periodeWisuda',
            'prosesOleh'
        ]);

        return view('akademik.yudisium.show', compact('yudisium'));
    }

    /**
     * Approve yudisium
     */
    public function approve(Yudisium $yudisium)
    {
        if ($yudisium->status !== 'Pending') {
            return back()->with('error', 'Yudisium sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($yudisium) {
            $yudisium->update([
                'status' => 'Disetujui',
                'diproses_oleh' => Auth::id(),
                'tanggal_proses' => now(),
            ]);

            // Update pendaftaran wisuda
            $yudisium->pendaftaranWisuda->update(['status' => 'Lulus']);

            // Update status mahasiswa
            $yudisium->mahasiswa->update(['status' => 'Lulus']);
        });

        return back()->with('success', 'Yudisium berhasil disetujui. Mahasiswa dinyatakan LULUS.');
    }

    /**
     * Reject yudisium
     */
    public function reject(Request $request, Yudisium $yudisium)
    {
        if ($yudisium->status !== 'Pending') {
            return back()->with('error', 'Yudisium sudah diproses sebelumnya.');
        }

        $request->validate([
            'alasan_tolak' => 'required|string|max:500',
        ]);

        $yudisium->update([
            'status' => 'Ditolak',
            'catatan' => $yudisium->catatan . "\n\nAlasan ditolak: " . $request->alasan_tolak,
            'diproses_oleh' => Auth::id(),
            'tanggal_proses' => now(),
        ]);

        return back()->with('success', 'Yudisium berhasil ditolak.');
    }

    /**
     * Bulk process yudisium
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'yudisium_ids' => 'required|array',
            'yudisium_ids.*' => 'exists:yudisium,id',
        ]);

        $count = 0;
        DB::transaction(function () use ($request, &$count) {
            foreach ($request->yudisium_ids as $id) {
                $yudisium = Yudisium::find($id);
                if ($yudisium && $yudisium->status === 'Pending') {
                    $yudisium->update([
                        'status' => 'Disetujui',
                        'diproses_oleh' => Auth::id(),
                        'tanggal_proses' => now(),
                    ]);
                    $yudisium->pendaftaranWisuda->update(['status' => 'Lulus']);
                    $yudisium->mahasiswa->update(['status' => 'Lulus']);
                    $count++;
                }
            }
        });

        return back()->with('success', "{$count} yudisium berhasil disetujui.");
    }

    /**
     * Print yudisium letter
     */
    public function print(Yudisium $yudisium)
    {
        if ($yudisium->status !== 'Disetujui') {
            return back()->with('error', 'Hanya yudisium yang disetujui dapat dicetak.');
        }

        $yudisium->load([
            'mahasiswa.programStudi.fakultas',
            'pendaftaranWisuda.periodeWisuda'
        ]);

        $pdf = Pdf::loadView('akademik.yudisium.print', compact('yudisium'));
        
        return $pdf->download("yudisium-{$yudisium->mahasiswa->nim}.pdf");
    }

    /**
     * Export yudisium data
     */
    public function export(Request $request)
    {
        $query = Yudisium::with(['mahasiswa.programStudi', 'pendaftaranWisuda.periodeWisuda'])
            ->where('status', 'Disetujui');

        if ($request->filled('periode_wisuda_id')) {
            $query->whereHas('pendaftaranWisuda', function ($q) use ($request) {
                $q->where('periode_wisuda_id', $request->periode_wisuda_id);
            });
        }

        $yudisium = $query->orderBy('tanggal_lulus', 'desc')->get();

        $pdf = Pdf::loadView('akademik.yudisium.export', compact('yudisium'));
        
        return $pdf->download("laporan-yudisium-" . now()->format('Y-m-d') . ".pdf");
    }

    /**
     * Calculate academic data for a mahasiswa
     */
    private function calculateAcademicData(Mahasiswa $mahasiswa)
    {
        // Get all KRS with nilai
        $krsList = Krs::with(['jadwalKuliah.mataKuliah', 'nilai'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'Disetujui')
            ->whereHas('nilai', function ($q) {
                $q->whereNotNull('nilai_akhir');
            })
            ->get();

        $totalSks = 0;
        $totalBobot = 0;
        $jumlahMk = 0;

        foreach ($krsList as $krs) {
            if ($krs->nilai && $krs->nilai->bobot >= 1.0) { // Lulus minimal D
                $sks = $krs->jadwalKuliah->mataKuliah->sks ?? 0;
                $bobot = $krs->nilai->bobot ?? 0;
                
                $totalSks += $sks;
                $totalBobot += ($sks * $bobot);
                $jumlahMk++;
            }
        }

        $ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;

        return [
            'total_sks' => $totalSks,
            'total_mk' => $jumlahMk,
            'ipk' => $ipk,
            'tanggal_masuk' => $mahasiswa->created_at,
        ];
    }
}
