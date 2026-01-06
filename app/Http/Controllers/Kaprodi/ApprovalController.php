<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\IzinKeluar;
use App\Models\PengajuanLembur;
use App\Models\Dosen;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    /**
     * Get the program studi ID of the logged-in kaprodi
     */
    private function getKaprodiProdi()
    {
        $user = auth()->user();
        $dosen = Dosen::where('user_id', $user->id)->first();
        
        if (!$dosen || !$dosen->program_studi_id) {
            return null;
        }
        
        return $dosen;
    }

    /**
     * Dashboard approval untuk Kaprodi
     */
    public function dashboard()
    {
        $kaprodi = $this->getKaprodiProdi();
        
        if (!$kaprodi) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses sebagai Kaprodi.');
        }

        // Hitung pengajuan yang perlu di-approve
        $pendingIzinKeluar = IzinKeluar::whereHas('dosen', function ($q) use ($kaprodi) {
                $q->where('program_studi_id', $kaprodi->program_studi_id);
            })
            ->where('status', 'diajukan')
            ->where('status_kaprodi', 'pending')
            ->count();

        $pendingLembur = PengajuanLembur::whereHas('dosen', function ($q) use ($kaprodi) {
                $q->where('program_studi_id', $kaprodi->program_studi_id);
            })
            ->where('status', 'diajukan')
            ->where('status_kaprodi', 'pending')
            ->count();

        $stats = [
            'pending_izin_keluar' => $pendingIzinKeluar,
            'pending_lembur' => $pendingLembur,
            'total_pending' => $pendingIzinKeluar + $pendingLembur,
        ];

        return view('kaprodi.approval.dashboard', compact('stats', 'kaprodi'));
    }

    // ===================== IZIN KELUAR =====================

    /**
     * List izin keluar untuk approval kaprodi
     */
    public function izinKeluarIndex(Request $request)
    {
        $kaprodi = $this->getKaprodiProdi();
        
        if (!$kaprodi) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses sebagai Kaprodi.');
        }

        $query = IzinKeluar::with(['dosen', 'dosen.programStudi'])
            ->whereHas('dosen', function ($q) use ($kaprodi) {
                $q->where('program_studi_id', $kaprodi->program_studi_id);
            });

        // Filter status
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('status', 'diajukan')->where('status_kaprodi', 'pending');
            } elseif ($request->status === 'disetujui_kaprodi') {
                $query->where('status_kaprodi', 'disetujui');
            } elseif ($request->status === 'ditolak_kaprodi') {
                $query->where('status_kaprodi', 'ditolak');
            } else {
                $query->where('status', $request->status);
            }
        }

        $izinKeluarList = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'pending' => IzinKeluar::whereHas('dosen', fn($q) => $q->where('program_studi_id', $kaprodi->program_studi_id))
                ->where('status', 'diajukan')->where('status_kaprodi', 'pending')->count(),
            'disetujui' => IzinKeluar::whereHas('dosen', fn($q) => $q->where('program_studi_id', $kaprodi->program_studi_id))
                ->where('status_kaprodi', 'disetujui')->count(),
            'ditolak' => IzinKeluar::whereHas('dosen', fn($q) => $q->where('program_studi_id', $kaprodi->program_studi_id))
                ->where('status_kaprodi', 'ditolak')->count(),
        ];

        return view('kaprodi.approval.izin-keluar.index', compact('izinKeluarList', 'stats', 'kaprodi'));
    }

    /**
     * Detail izin keluar
     */
    public function izinKeluarShow(IzinKeluar $izinKeluar)
    {
        $kaprodi = $this->getKaprodiProdi();
        
        if (!$kaprodi || $izinKeluar->dosen?->program_studi_id !== $kaprodi->program_studi_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        $izinKeluar->load(['dosen', 'dosen.programStudi']);
        return view('kaprodi.approval.izin-keluar.show', compact('izinKeluar', 'kaprodi'));
    }

    /**
     * Approve izin keluar oleh kaprodi
     */
    public function izinKeluarApprove(Request $request, IzinKeluar $izinKeluar)
    {
        $kaprodi = $this->getKaprodiProdi();
        
        if (!$kaprodi || $izinKeluar->dosen?->program_studi_id !== $kaprodi->program_studi_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses.');
        }

        if ($izinKeluar->status_kaprodi !== 'pending') {
            return redirect()->back()->with('error', 'Izin keluar sudah diproses sebelumnya.');
        }

        $izinKeluar->update([
            'status_kaprodi' => 'disetujui',
            'kaprodi_id' => $kaprodi->id,
            'tanggal_approval_kaprodi' => now(),
            'catatan_kaprodi' => $request->catatan,
            'status' => 'menunggu_admin', // Lanjut ke admin untuk approval final
        ]);

        return redirect()->back()->with('success', 'Izin keluar berhasil disetujui dan diteruskan ke Admin.');
    }

    /**
     * Reject izin keluar oleh kaprodi
     */
    public function izinKeluarReject(Request $request, IzinKeluar $izinKeluar)
    {
        $kaprodi = $this->getKaprodiProdi();
        
        if (!$kaprodi || $izinKeluar->dosen?->program_studi_id !== $kaprodi->program_studi_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses.');
        }

        if ($izinKeluar->status_kaprodi !== 'pending') {
            return redirect()->back()->with('error', 'Izin keluar sudah diproses sebelumnya.');
        }

        $request->validate([
            'catatan' => 'required|string|min:10',
        ], [
            'catatan.required' => 'Alasan penolakan wajib diisi.',
            'catatan.min' => 'Alasan penolakan minimal 10 karakter.',
        ]);

        $izinKeluar->update([
            'status_kaprodi' => 'ditolak',
            'kaprodi_id' => $kaprodi->id,
            'tanggal_approval_kaprodi' => now(),
            'catatan_kaprodi' => $request->catatan,
            'status' => 'ditolak',
        ]);

        return redirect()->back()->with('success', 'Izin keluar berhasil ditolak.');
    }

    // ===================== LEMBUR =====================

    /**
     * List pengajuan lembur untuk approval kaprodi
     */
    public function lemburIndex(Request $request)
    {
        $kaprodi = $this->getKaprodiProdi();
        
        if (!$kaprodi) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses sebagai Kaprodi.');
        }

        $query = PengajuanLembur::with(['dosen', 'dosen.programStudi'])
            ->whereHas('dosen', function ($q) use ($kaprodi) {
                $q->where('program_studi_id', $kaprodi->program_studi_id);
            });

        // Filter status
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('status', 'diajukan')->where('status_kaprodi', 'pending');
            } elseif ($request->status === 'disetujui_kaprodi') {
                $query->where('status_kaprodi', 'disetujui');
            } elseif ($request->status === 'ditolak_kaprodi') {
                $query->where('status_kaprodi', 'ditolak');
            } else {
                $query->where('status', $request->status);
            }
        }

        $lemburList = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'pending' => PengajuanLembur::whereHas('dosen', fn($q) => $q->where('program_studi_id', $kaprodi->program_studi_id))
                ->where('status', 'diajukan')->where('status_kaprodi', 'pending')->count(),
            'disetujui' => PengajuanLembur::whereHas('dosen', fn($q) => $q->where('program_studi_id', $kaprodi->program_studi_id))
                ->where('status_kaprodi', 'disetujui')->count(),
            'ditolak' => PengajuanLembur::whereHas('dosen', fn($q) => $q->where('program_studi_id', $kaprodi->program_studi_id))
                ->where('status_kaprodi', 'ditolak')->count(),
        ];

        return view('kaprodi.approval.lembur.index', compact('lemburList', 'stats', 'kaprodi'));
    }

    /**
     * Detail pengajuan lembur
     */
    public function lemburShow(PengajuanLembur $pengajuanLembur)
    {
        $kaprodi = $this->getKaprodiProdi();
        
        if (!$kaprodi || $pengajuanLembur->dosen?->program_studi_id !== $kaprodi->program_studi_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        $pengajuanLembur->load(['dosen', 'dosen.programStudi', 'tarifLembur']);
        $lembur = $pengajuanLembur; // Alias for view
        return view('kaprodi.approval.lembur.show', compact('lembur', 'kaprodi'));
    }

    /**
     * Approve lembur oleh kaprodi
     */
    public function lemburApprove(Request $request, PengajuanLembur $pengajuanLembur)
    {
        $kaprodi = $this->getKaprodiProdi();
        
        if (!$kaprodi || $pengajuanLembur->dosen?->program_studi_id !== $kaprodi->program_studi_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses.');
        }

        if ($pengajuanLembur->status_kaprodi !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan lembur sudah diproses sebelumnya.');
        }

        $pengajuanLembur->update([
            'status_kaprodi' => 'disetujui',
            'kaprodi_id' => $kaprodi->id,
            'tanggal_approval_kaprodi' => now(),
            'catatan_kaprodi' => $request->catatan,
            'status' => 'menunggu_admin', // Lanjut ke admin untuk approval final
        ]);

        return redirect()->back()->with('success', 'Pengajuan lembur berhasil disetujui dan diteruskan ke Admin.');
    }

    /**
     * Reject lembur oleh kaprodi
     */
    public function lemburReject(Request $request, PengajuanLembur $pengajuanLembur)
    {
        $kaprodi = $this->getKaprodiProdi();
        
        if (!$kaprodi || $pengajuanLembur->dosen?->program_studi_id !== $kaprodi->program_studi_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses.');
        }

        if ($pengajuanLembur->status_kaprodi !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan lembur sudah diproses sebelumnya.');
        }

        $request->validate([
            'catatan' => 'required|string|min:10',
        ], [
            'catatan.required' => 'Alasan penolakan wajib diisi.',
            'catatan.min' => 'Alasan penolakan minimal 10 karakter.',
        ]);

        $pengajuanLembur->update([
            'status_kaprodi' => 'ditolak',
            'kaprodi_id' => $kaprodi->id,
            'tanggal_approval_kaprodi' => now(),
            'catatan_kaprodi' => $request->catatan,
            'status' => 'ditolak',
        ]);

        return redirect()->back()->with('success', 'Pengajuan lembur berhasil ditolak.');
    }
}
