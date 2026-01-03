<?php

namespace App\Http\Controllers;

use App\Models\PeriodeKegiatanLapangan;
use App\Models\MitraKegiatan;
use App\Models\PendaftaranKegiatanLapangan;
use App\Models\LogKegiatanLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KegiatanLapanganMahasiswaController extends Controller
{
    /**
     * Daftar periode yang tersedia
     */
    public function index()
    {
        $mahasiswa = auth()->user()->mahasiswa;
        
        if (!$mahasiswa) {
            return back()->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        // Periode yang dibuka
        $periodeTersedia = PeriodeKegiatanLapangan::with('jenisKegiatan')
            ->where('status', 'dibuka')
            ->whereDate('tanggal_mulai_daftar', '<=', now())
            ->whereDate('tanggal_selesai_daftar', '>=', now())
            ->get();

        // Pendaftaran saya
        $pendaftaranSaya = PendaftaranKegiatanLapangan::with(['periode.jenisKegiatan', 'mitraDiterima'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mahasiswa.kegiatan-lapangan.index', compact('periodeTersedia', 'pendaftaranSaya'));
    }

    /**
     * Form pendaftaran
     */
    public function create()
    {
        $mahasiswa = auth()->user()->mahasiswa;
        
        if (!$mahasiswa) {
            return back()->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        // Periode yang dibuka
        $periodes = PeriodeKegiatanLapangan::with('jenisKegiatan')
            ->where('status', 'dibuka')
            ->whereDate('tanggal_mulai_daftar', '<=', now())
            ->whereDate('tanggal_selesai_daftar', '>=', now())
            ->get();

        if ($periodes->isEmpty()) {
            return back()->with('error', 'Tidak ada periode pendaftaran yang sedang dibuka!');
        }

        $mitras = MitraKegiatan::active()->orderBy('nama')->get();

        return view('mahasiswa.kegiatan-lapangan.create', compact('periodes', 'mitras'));
    }

    /**
     * Simpan pendaftaran
     */
    public function store(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_kegiatan_lapangan,id',
            'mitra_pilihan_1' => 'required|exists:mitra_kegiatan,id',
            'mitra_pilihan_2' => 'nullable|exists:mitra_kegiatan,id|different:mitra_pilihan_1',
            'mitra_pilihan_3' => 'nullable|exists:mitra_kegiatan,id|different:mitra_pilihan_1|different:mitra_pilihan_2',
            'rencana_kegiatan' => 'required|string|min:100',
            'surat_pengantar' => 'nullable|file|mimes:pdf|max:2048',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,zip|max:5120',
        ]);

        $mahasiswa = auth()->user()->mahasiswa;
        
        if (!$mahasiswa) {
            return back()->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        // Cek apakah sudah terdaftar di periode ini
        $existing = PendaftaranKegiatanLapangan::where('periode_id', $request->periode_id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        if ($existing) {
            return redirect()->route('mahasiswa.kegiatan-lapangan.show', $existing->hashid)
                ->with('warning', 'Anda sudah terdaftar di periode ini!');
        }

        // Upload dokumen
        $suratPath = $request->hasFile('surat_pengantar') 
            ? $request->file('surat_pengantar')->store('kegiatan-lapangan/surat', 'public') 
            : null;
        $dokumenPath = $request->hasFile('dokumen_pendukung') 
            ? $request->file('dokumen_pendukung')->store('kegiatan-lapangan/dokumen', 'public') 
            : null;

        $pendaftaran = PendaftaranKegiatanLapangan::create([
            'periode_id' => $request->periode_id,
            'mahasiswa_id' => $mahasiswa->id,
            'mitra_pilihan_1' => $request->mitra_pilihan_1,
            'mitra_pilihan_2' => $request->mitra_pilihan_2,
            'mitra_pilihan_3' => $request->mitra_pilihan_3,
            'rencana_kegiatan' => $request->rencana_kegiatan,
            'surat_pengantar' => $suratPath,
            'dokumen_pendukung' => $dokumenPath,
            'status' => 'diajukan',
        ]);

        return redirect()->route('mahasiswa.kegiatan-lapangan.show', $pendaftaran->hashid)
            ->with('success', 'Pendaftaran berhasil diajukan!');
    }

    /**
     * Detail pendaftaran
     */
    public function show(PendaftaranKegiatanLapangan $pendaftaran)
    {
        $mahasiswa = auth()->user()->mahasiswa;
        
        if ($pendaftaran->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        $pendaftaran->load([
            'periode.jenisKegiatan', 
            'mitraPilihan1', 
            'mitraPilihan2', 
            'mitraPilihan3',
            'mitraDiterima', 
            'dosenPembimbing',
            'logKegiatan' => function ($q) {
                $q->orderBy('tanggal', 'desc');
            },
            'penilaian'
        ]);

        return view('mahasiswa.kegiatan-lapangan.show', compact('pendaftaran'));
    }

    /**
     * Form tambah log kegiatan
     */
    public function logCreate(PendaftaranKegiatanLapangan $pendaftaran)
    {
        $mahasiswa = auth()->user()->mahasiswa;
        
        if ($pendaftaran->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        if ($pendaftaran->status != 'berlangsung') {
            return back()->with('error', 'Kegiatan belum dimulai atau sudah selesai!');
        }

        return view('mahasiswa.kegiatan-lapangan.log-create', compact('pendaftaran'));
    }

    /**
     * Simpan log kegiatan
     */
    public function logStore(Request $request, PendaftaranKegiatanLapangan $pendaftaran)
    {
        $mahasiswa = auth()->user()->mahasiswa;
        
        if ($pendaftaran->mahasiswa_id != $mahasiswa->id || $pendaftaran->status != 'berlangsung') {
            abort(403);
        }

        $request->validate([
            'tanggal' => 'required|date|before_or_equal:today',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'kegiatan' => 'required|string|min:50',
            'hasil' => 'nullable|string',
            'kendala' => 'nullable|string',
            'dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $fotoPath = $request->hasFile('dokumentasi') 
            ? $request->file('dokumentasi')->store('kegiatan-lapangan/dokumentasi', 'public') 
            : null;

        LogKegiatanLapangan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'kegiatan' => $request->kegiatan,
            'hasil' => $request->hasil,
            'kendala' => $request->kendala,
            'dokumentasi' => $fotoPath,
            'status' => 'diajukan',
        ]);

        return redirect()->route('mahasiswa.kegiatan-lapangan.show', $pendaftaran)
            ->with('success', 'Log kegiatan berhasil ditambahkan!');
    }

    /**
     * Edit log kegiatan (jika masih draft atau revisi)
     */
    public function logEdit(PendaftaranKegiatanLapangan $pendaftaran, LogKegiatanLapangan $log)
    {
        $mahasiswa = auth()->user()->mahasiswa;
        
        if ($pendaftaran->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        if (!in_array($log->status, ['draft', 'revisi'])) {
            return back()->with('error', 'Log kegiatan tidak dapat diedit!');
        }

        return view('mahasiswa.kegiatan-lapangan.log-edit', compact('pendaftaran', 'log'));
    }

    /**
     * Update log kegiatan
     */
    public function logUpdate(Request $request, PendaftaranKegiatanLapangan $pendaftaran, LogKegiatanLapangan $log)
    {
        $mahasiswa = auth()->user()->mahasiswa;
        
        if ($pendaftaran->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        if (!in_array($log->status, ['draft', 'revisi'])) {
            return back()->with('error', 'Log kegiatan tidak dapat diedit!');
        }

        $request->validate([
            'tanggal' => 'required|date|before_or_equal:today',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'kegiatan' => 'required|string|min:50',
            'hasil' => 'nullable|string',
            'kendala' => 'nullable|string',
            'dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except('dokumentasi');

        if ($request->hasFile('dokumentasi')) {
            // Hapus foto lama
            if ($log->dokumentasi) {
                Storage::disk('public')->delete($log->dokumentasi);
            }
            $data['dokumentasi'] = $request->file('dokumentasi')->store('kegiatan-lapangan/dokumentasi', 'public');
        }

        $data['status'] = 'diajukan';
        $log->update($data);

        return redirect()->route('mahasiswa.kegiatan-lapangan.show', $pendaftaran)
            ->with('success', 'Log kegiatan berhasil diupdate!');
    }

    /**
     * Hapus log kegiatan (jika masih draft)
     */
    public function logDestroy(PendaftaranKegiatanLapangan $pendaftaran, LogKegiatanLapangan $log)
    {
        $mahasiswa = auth()->user()->mahasiswa;
        
        if ($pendaftaran->mahasiswa_id != $mahasiswa->id || $log->status != 'draft') {
            abort(403);
        }

        if ($log->dokumentasi) {
            Storage::disk('public')->delete($log->dokumentasi);
        }

        $log->delete();

        return back()->with('success', 'Log kegiatan berhasil dihapus!');
    }
}
