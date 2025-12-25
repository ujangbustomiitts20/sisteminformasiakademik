<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PengajuanSuratController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->isMahasiswa()) {
            $mahasiswa = Auth::user()->mahasiswa;
            $pengajuanSurat = PengajuanSurat::where('mahasiswa_id', $mahasiswa->id)
                ->with(['diproses_oleh_user'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            return view('mahasiswa.pengajuan-surat.index', compact('pengajuanSurat'));
        }
        
        // Untuk admin/staff
        $pengajuanSurat = PengajuanSurat::with(['mahasiswa.programStudi', 'diproses_oleh_user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.pengajuan-surat.index', compact('pengajuanSurat'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jenisSurat = [
            'Surat Keterangan Aktif Kuliah',
            'Surat Pengantar Penelitian',
            'Surat Pengantar Magang/PKL',
            'Surat Keterangan Lulus',
            'Surat Keterangan Berkelakuan Baik',
            'Surat Rekomendasi',
            'Surat Keterangan Sedang Cuti',
            'Lainnya'
        ];
        
        return view('mahasiswa.pengajuan-surat.create', compact('jenisSurat'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_surat' => 'required|string',
            'keperluan' => 'required|string|max:1000',
            'ditujukan_kepada' => 'nullable|string|max:255',
            'keterangan_tambahan' => 'nullable|string|max:1000',
        ]);
        
        $mahasiswa = Auth::user()->mahasiswa;
        
        PengajuanSurat::create([
            'mahasiswa_id' => $mahasiswa->id,
            'jenis_surat' => $request->jenis_surat,
            'keperluan' => $request->keperluan,
            'ditujukan_kepada' => $request->ditujukan_kepada,
            'keterangan_tambahan' => $request->keterangan_tambahan,
            'status' => 'pending',
        ]);
        
        return redirect()->route('pengajuan-surat.index')
            ->with('success', 'Pengajuan surat berhasil dikirim. Silakan tunggu proses verifikasi.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PengajuanSurat $pengajuanSurat)
    {
        $pengajuanSurat->load(['mahasiswa.programStudi', 'diproses_oleh_user']);
        
        if (Auth::user()->isMahasiswa()) {
            // Pastikan mahasiswa hanya bisa lihat pengajuannya sendiri
            if ($pengajuanSurat->mahasiswa_id != Auth::user()->mahasiswa->id) {
                abort(403);
            }
            return view('mahasiswa.pengajuan-surat.show', compact('pengajuanSurat'));
        }
        
        return view('admin.pengajuan-surat.show', compact('pengajuanSurat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Update status pengajuan (untuk admin).
     */
    public function updateStatus(Request $request, PengajuanSurat $pengajuanSurat)
    {
        $request->validate([
            'status' => 'required|in:diproses,disetujui,ditolak',
            'catatan_admin' => 'nullable|string',
            'nomor_surat' => 'nullable|string|max:100',
        ]);
        
        $pengajuanSurat->update([
            'status' => $request->status,
            'catatan_admin' => $request->catatan_admin,
            'nomor_surat' => $request->nomor_surat,
            'diproses_oleh' => Auth::id(),
            'tanggal_diproses' => now(),
        ]);
        
        // Jika disetujui, generate PDF
        if ($request->status == 'disetujui' && $request->nomor_surat) {
            $this->generateSuratPDF($pengajuanSurat);
        }
        
        return redirect()->back()
            ->with('success', 'Status pengajuan berhasil diupdate.');
    }

    /**
     * Generate PDF surat.
     */
    private function generateSuratPDF(PengajuanSurat $pengajuanSurat)
    {
        $pengajuanSurat->load(['mahasiswa.programStudi']);
        
        $pdf = Pdf::loadView('pdf.surat-mahasiswa', [
            'pengajuan' => $pengajuanSurat,
            'mahasiswa' => $pengajuanSurat->mahasiswa,
        ]);
        
        $filename = 'surat_' . $pengajuanSurat->id . '_' . time() . '.pdf';
        $path = 'surat/' . $filename;
        
        // Simpan PDF
        Storage::disk('public')->put($path, $pdf->output());
        
        $pengajuanSurat->update(['file_surat' => $path]);
    }

    /**
     * Download surat PDF.
     */
    public function download(PengajuanSurat $pengajuanSurat)
    {
        if (Auth::user()->isMahasiswa()) {
            // Pastikan mahasiswa hanya bisa download suratnya sendiri
            if ($pengajuanSurat->mahasiswa_id != Auth::user()->mahasiswa->id) {
                abort(403);
            }
        }
        
        // Pastikan surat sudah disetujui
        if ($pengajuanSurat->status != 'disetujui') {
            return redirect()->back()->with('error', 'Surat belum disetujui, tidak dapat didownload.');
        }
        
        // Jika file belum ada, generate terlebih dahulu
        if (!$pengajuanSurat->file_surat || !Storage::disk('public')->exists($pengajuanSurat->file_surat)) {
            $this->generateSuratPDF($pengajuanSurat);
            $pengajuanSurat->refresh();
        }
        
        if (!$pengajuanSurat->file_surat || !Storage::disk('public')->exists($pengajuanSurat->file_surat)) {
            return redirect()->back()->with('error', 'Gagal generate file surat.');
        }
        
        return Storage::disk('public')->download($pengajuanSurat->file_surat, 
            'Surat_' . $pengajuanSurat->mahasiswa->nim . '_' . Str::slug($pengajuanSurat->jenis_surat) . '.pdf');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PengajuanSurat $pengajuanSurat)
    {
        // Hanya bisa cancel jika status masih pending
        if ($pengajuanSurat->status != 'pending') {
            return redirect()->back()
                ->with('error', 'Tidak dapat membatalkan pengajuan yang sudah diproses.');
        }
        
        if (Auth::user()->isMahasiswa()) {
            // Pastikan mahasiswa hanya bisa cancel pengajuannya sendiri
            if ($pengajuanSurat->mahasiswa_id != Auth::user()->mahasiswa->id) {
                abort(403);
            }
        }
        
        $pengajuanSurat->delete();
        
        return redirect()->route('pengajuan-surat.index')
            ->with('success', 'Pengajuan surat berhasil dibatalkan.');
    }
}
