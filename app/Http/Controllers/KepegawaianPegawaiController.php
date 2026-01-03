<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\RiwayatPendidikan;
use App\Models\RiwayatJabatan;
use App\Models\RiwayatPangkat;
use App\Models\RiwayatPelatihan;
use App\Models\DokumenKepegawaian;
use Illuminate\Support\Facades\Storage;

class KepegawaianPegawaiController extends Controller
{
    /**
     * Display kepegawaian index for a specific pegawai
     */
    public function index(Pegawai $pegawai)
    {
        $pegawai->load([
            'riwayatPendidikan',
            'riwayatJabatan',
            'riwayatPangkat',
            'riwayatPelatihan',
            'dokumenKepegawaian',
            'unitKerja'
        ]);
        
        return view('kepegawaian.pegawai.riwayat.index', compact('pegawai'));
    }
    
    // ============================================
    // RIWAYAT PENDIDIKAN
    // ============================================
    
    public function createPendidikan(Pegawai $pegawai)
    {
        return view('kepegawaian.pegawai.riwayat.pendidikan.create', compact('pegawai'));
    }
    
    public function storePendidikan(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'jenjang' => 'required|in:SD,SMP,SMA,SMK,D1,D2,D3,D4,S1,S2,S3,Profesi,Spesialis',
            'nama_institusi' => 'required|string|max:255',
            'jurusan' => 'nullable|string|max:255',
            'tahun_masuk' => 'required|integer|min:1950|max:' . date('Y'),
            'tahun_lulus' => 'nullable|integer|min:1950|max:' . date('Y'),
            'no_ijazah' => 'nullable|string|max:100',
            'tanggal_ijazah' => 'nullable|date',
            'ipk' => 'nullable|numeric|min:0|max:4',
            'file_ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        
        if ($request->hasFile('file_ijazah')) {
            $validated['file_ijazah'] = $request->file('file_ijazah')->store('kepegawaian/ijazah', 'public');
        }
        
        $validated['pegawai_id'] = $pegawai->id;
        
        RiwayatPendidikan::create($validated);
        
        return redirect()
            ->route('kepegawaian.pegawai.riwayat.index', $pegawai)
            ->with('success', 'Riwayat pendidikan berhasil ditambahkan');
    }
    
    public function editPendidikan(Pegawai $pegawai, RiwayatPendidikan $pendidikan)
    {
        return view('kepegawaian.pegawai.riwayat.pendidikan.edit', compact('pegawai', 'pendidikan'));
    }
    
    public function updatePendidikan(Request $request, Pegawai $pegawai, RiwayatPendidikan $pendidikan)
    {
        $validated = $request->validate([
            'jenjang' => 'required|in:SD,SMP,SMA,SMK,D1,D2,D3,D4,S1,S2,S3,Profesi,Spesialis',
            'nama_institusi' => 'required|string|max:255',
            'jurusan' => 'nullable|string|max:255',
            'tahun_masuk' => 'required|integer|min:1950|max:' . date('Y'),
            'tahun_lulus' => 'nullable|integer|min:1950|max:' . date('Y'),
            'no_ijazah' => 'nullable|string|max:100',
            'tanggal_ijazah' => 'nullable|date',
            'ipk' => 'nullable|numeric|min:0|max:4',
            'file_ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        
        if ($request->hasFile('file_ijazah')) {
            if ($pendidikan->file_ijazah) {
                Storage::disk('public')->delete($pendidikan->file_ijazah);
            }
            $validated['file_ijazah'] = $request->file('file_ijazah')->store('kepegawaian/ijazah', 'public');
        }
        
        $pendidikan->update($validated);
        
        return redirect()
            ->route('kepegawaian.pegawai.riwayat.index', $pegawai)
            ->with('success', 'Riwayat pendidikan berhasil diupdate');
    }
    
    public function destroyPendidikan(Pegawai $pegawai, RiwayatPendidikan $pendidikan)
    {
        if ($pendidikan->file_ijazah) {
            Storage::disk('public')->delete($pendidikan->file_ijazah);
        }
        
        $pendidikan->delete();
        
        return redirect()
            ->route('kepegawaian.pegawai.riwayat.index', $pegawai)
            ->with('success', 'Riwayat pendidikan berhasil dihapus');
    }
    
    // ============================================
    // RIWAYAT JABATAN
    // ============================================
    
    public function createJabatan(Pegawai $pegawai)
    {
        return view('kepegawaian.pegawai.riwayat.jabatan.create', compact('pegawai'));
    }
    
    public function storeJabatan(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'nama_jabatan' => 'required|string|max:255',
            'jenis_jabatan' => 'required|in:Struktural,Fungsional,Akademik',
            'unit_kerja' => 'nullable|string|max:255',
            'tmt_jabatan' => 'required|date',
            'tmt_selesai' => 'nullable|date|after:tmt_jabatan',
            'no_sk' => 'nullable|string|max:100',
            'tanggal_sk' => 'nullable|date',
            'pejabat_sk' => 'nullable|string|max:255',
            'file_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        
        if ($request->hasFile('file_sk')) {
            $validated['file_sk'] = $request->file('file_sk')->store('kepegawaian/sk_jabatan', 'public');
        }
        
        // Map unit_kerja ke unit_kerja_jabatan
        if (isset($validated['unit_kerja'])) {
            $validated['unit_kerja_jabatan'] = $validated['unit_kerja'];
            unset($validated['unit_kerja']);
        }
        
        $validated['pegawai_id'] = $pegawai->id;
        
        RiwayatJabatan::create($validated);
        
        return redirect()
            ->route('kepegawaian.pegawai.riwayat.index', $pegawai)
            ->with('success', 'Riwayat jabatan berhasil ditambahkan');
    }
    
    public function editJabatan(Pegawai $pegawai, RiwayatJabatan $jabatan)
    {
        return view('kepegawaian.pegawai.riwayat.jabatan.edit', compact('pegawai', 'jabatan'));
    }
    
    public function updateJabatan(Request $request, Pegawai $pegawai, RiwayatJabatan $jabatan)
    {
        $validated = $request->validate([
            'nama_jabatan' => 'required|string|max:255',
            'jenis_jabatan' => 'required|in:Struktural,Fungsional,Akademik',
            'unit_kerja' => 'nullable|string|max:255',
            'tmt_jabatan' => 'required|date',
            'tmt_selesai' => 'nullable|date|after:tmt_jabatan',
            'no_sk' => 'nullable|string|max:100',
            'tanggal_sk' => 'nullable|date',
            'pejabat_sk' => 'nullable|string|max:255',
            'file_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        
        if ($request->hasFile('file_sk')) {
            if ($jabatan->file_sk) {
                Storage::disk('public')->delete($jabatan->file_sk);
            }
            $validated['file_sk'] = $request->file('file_sk')->store('kepegawaian/sk_jabatan', 'public');
        }
        
        // Map unit_kerja ke unit_kerja_jabatan
        if (isset($validated['unit_kerja'])) {
            $validated['unit_kerja_jabatan'] = $validated['unit_kerja'];
            unset($validated['unit_kerja']);
        }
        
        $jabatan->update($validated);
        
        return redirect()
            ->route('kepegawaian.pegawai.riwayat.index', $pegawai)
            ->with('success', 'Riwayat jabatan berhasil diupdate');
    }
    
    public function destroyJabatan(Pegawai $pegawai, RiwayatJabatan $jabatan)
    {
        if ($jabatan->file_sk) {
            Storage::disk('public')->delete($jabatan->file_sk);
        }
        
        $jabatan->delete();
        
        return redirect()
            ->route('kepegawaian.pegawai.riwayat.index', $pegawai)
            ->with('success', 'Riwayat jabatan berhasil dihapus');
    }
    
    // ============================================
    // RIWAYAT PANGKAT
    // ============================================
    
    public function createPangkat(Pegawai $pegawai)
    {
        return view('kepegawaian.pegawai.riwayat.pangkat.create', compact('pegawai'));
    }
    
    public function storePangkat(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'golongan' => 'required|string|max:10',
            'pangkat' => 'required|string|max:100',
            'tmt_pangkat' => 'required|date',
            'masa_kerja_tahun' => 'nullable|integer|min:0',
            'masa_kerja_bulan' => 'nullable|integer|min:0|max:11',
            'no_sk' => 'nullable|string|max:100',
            'tanggal_sk' => 'nullable|date',
            'pejabat_sk' => 'nullable|string|max:255',
            'file_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        
        if ($request->hasFile('file_sk')) {
            $validated['file_sk'] = $request->file('file_sk')->store('kepegawaian/sk_pangkat', 'public');
        }
        
        $validated['pegawai_id'] = $pegawai->id;
        
        RiwayatPangkat::create($validated);
        
        return redirect()
            ->route('kepegawaian.pegawai.riwayat.index', $pegawai)
            ->with('success', 'Riwayat pangkat berhasil ditambahkan');
    }
    
    public function editPangkat(Pegawai $pegawai, RiwayatPangkat $pangkat)
    {
        return view('kepegawaian.pegawai.riwayat.pangkat.edit', compact('pegawai', 'pangkat'));
    }
    
    public function updatePangkat(Request $request, Pegawai $pegawai, RiwayatPangkat $pangkat)
    {
        $validated = $request->validate([
            'golongan' => 'required|string|max:10',
            'pangkat' => 'required|string|max:100',
            'tmt_pangkat' => 'required|date',
            'masa_kerja_tahun' => 'nullable|integer|min:0',
            'masa_kerja_bulan' => 'nullable|integer|min:0|max:11',
            'no_sk' => 'nullable|string|max:100',
            'tanggal_sk' => 'nullable|date',
            'pejabat_sk' => 'nullable|string|max:255',
            'file_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        
        if ($request->hasFile('file_sk')) {
            if ($pangkat->file_sk) {
                Storage::disk('public')->delete($pangkat->file_sk);
            }
            $validated['file_sk'] = $request->file('file_sk')->store('kepegawaian/sk_pangkat', 'public');
        }
        
        $pangkat->update($validated);
        
        return redirect()
            ->route('kepegawaian.pegawai.riwayat.index', $pegawai)
            ->with('success', 'Riwayat pangkat berhasil diupdate');
    }
    
    public function destroyPangkat(Pegawai $pegawai, RiwayatPangkat $pangkat)
    {
        if ($pangkat->file_sk) {
            Storage::disk('public')->delete($pangkat->file_sk);
        }
        
        $pangkat->delete();
        
        return redirect()
            ->route('kepegawaian.pegawai.riwayat.index', $pegawai)
            ->with('success', 'Riwayat pangkat berhasil dihapus');
    }
    
    // ============================================
    // RIWAYAT PELATIHAN
    // ============================================
    
    public function createPelatihan(Pegawai $pegawai)
    {
        return view('kepegawaian.pegawai.riwayat.pelatihan.create', compact('pegawai'));
    }
    
    public function storePelatihan(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'nama_pelatihan' => 'required|string|max:255',
            'jenis_pelatihan' => 'required|in:Diklat,Workshop,Seminar,Kursus,Sertifikasi,Lainnya',
            'penyelenggara' => 'nullable|string|max:255',
            'tempat' => 'nullable|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'tahun' => 'required|integer|min:1990|max:' . date('Y'),
            'jumlah_jam' => 'nullable|integer|min:0',
            'no_sertifikat' => 'nullable|string|max:100',
            'tanggal_sertifikat' => 'nullable|date',
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        
        if ($request->hasFile('file_sertifikat')) {
            $validated['file_sertifikat'] = $request->file('file_sertifikat')->store('kepegawaian/sertifikat', 'public');
        }
        
        $validated['pegawai_id'] = $pegawai->id;
        
        RiwayatPelatihan::create($validated);
        
        return redirect()
            ->route('kepegawaian.pegawai.riwayat.index', $pegawai)
            ->with('success', 'Riwayat pelatihan berhasil ditambahkan');
    }
    
    public function editPelatihan(Pegawai $pegawai, RiwayatPelatihan $pelatihan)
    {
        return view('kepegawaian.pegawai.riwayat.pelatihan.edit', compact('pegawai', 'pelatihan'));
    }
    
    public function updatePelatihan(Request $request, Pegawai $pegawai, RiwayatPelatihan $pelatihan)
    {
        $validated = $request->validate([
            'nama_pelatihan' => 'required|string|max:255',
            'jenis_pelatihan' => 'required|in:Diklat,Workshop,Seminar,Kursus,Sertifikasi,Lainnya',
            'penyelenggara' => 'nullable|string|max:255',
            'tempat' => 'nullable|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'tahun' => 'required|integer|min:1990|max:' . date('Y'),
            'jumlah_jam' => 'nullable|integer|min:0',
            'no_sertifikat' => 'nullable|string|max:100',
            'tanggal_sertifikat' => 'nullable|date',
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        
        if ($request->hasFile('file_sertifikat')) {
            if ($pelatihan->file_sertifikat) {
                Storage::disk('public')->delete($pelatihan->file_sertifikat);
            }
            $validated['file_sertifikat'] = $request->file('file_sertifikat')->store('kepegawaian/sertifikat', 'public');
        }
        
        $pelatihan->update($validated);
        
        return redirect()
            ->route('kepegawaian.pegawai.riwayat.index', $pegawai)
            ->with('success', 'Riwayat pelatihan berhasil diupdate');
    }
    
    public function destroyPelatihan(Pegawai $pegawai, RiwayatPelatihan $pelatihan)
    {
        if ($pelatihan->file_sertifikat) {
            Storage::disk('public')->delete($pelatihan->file_sertifikat);
        }
        
        $pelatihan->delete();
        
        return redirect()
            ->route('kepegawaian.pegawai.riwayat.index', $pegawai)
            ->with('success', 'Riwayat pelatihan berhasil dihapus');
    }
    
    // ============================================
    // DOKUMEN KEPEGAWAIAN
    // ============================================
    
    public function createDokumen(Pegawai $pegawai)
    {
        return view('kepegawaian.pegawai.riwayat.dokumen.create', compact('pegawai'));
    }
    
    public function storeDokumen(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'jenis_dokumen' => 'required|in:SK_CPNS,SK_PNS,SK_Pengangkatan,SK_Kenaikan_Pangkat,SK_Jabatan,Sertifikat,Piagam,Ijazah,KTP,NPWP,BPJS,KK,Lainnya',
            'nama_dokumen' => 'required|string|max:255',
            'nomor_dokumen' => 'nullable|string|max:100',
            'tanggal_terbit' => 'nullable|date',
            'penerbit' => 'nullable|string|max:255',
            'tanggal_berlaku' => 'nullable|date',
            'tanggal_expired' => 'nullable|date|after:tanggal_berlaku',
            'file_dokumen' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|string',
        ]);
        
        $validated['file_dokumen'] = $request->file('file_dokumen')->store('kepegawaian/dokumen', 'public');
        $validated['pegawai_id'] = $pegawai->id;
        
        DokumenKepegawaian::create($validated);
        
        return redirect()
            ->route('kepegawaian.pegawai.riwayat.index', $pegawai)
            ->with('success', 'Dokumen kepegawaian berhasil ditambahkan');
    }
    
    public function editDokumen(Pegawai $pegawai, DokumenKepegawaian $dokumen)
    {
        return view('kepegawaian.pegawai.riwayat.dokumen.edit', compact('pegawai', 'dokumen'));
    }
    
    public function updateDokumen(Request $request, Pegawai $pegawai, DokumenKepegawaian $dokumen)
    {
        $validated = $request->validate([
            'jenis_dokumen' => 'required|in:SK_CPNS,SK_PNS,SK_Pengangkatan,SK_Kenaikan_Pangkat,SK_Jabatan,Sertifikat,Piagam,Ijazah,KTP,NPWP,BPJS,KK,Lainnya',
            'nama_dokumen' => 'required|string|max:255',
            'nomor_dokumen' => 'nullable|string|max:100',
            'tanggal_terbit' => 'nullable|date',
            'penerbit' => 'nullable|string|max:255',
            'tanggal_berlaku' => 'nullable|date',
            'tanggal_expired' => 'nullable|date|after:tanggal_berlaku',
            'file_dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|string',
        ]);
        
        if ($request->hasFile('file_dokumen')) {
            if ($dokumen->file_dokumen) {
                Storage::disk('public')->delete($dokumen->file_dokumen);
            }
            $validated['file_dokumen'] = $request->file('file_dokumen')->store('kepegawaian/dokumen', 'public');
        }
        
        $dokumen->update($validated);
        
        return redirect()
            ->route('kepegawaian.pegawai.riwayat.index', $pegawai)
            ->with('success', 'Dokumen kepegawaian berhasil diupdate');
    }
    
    public function destroyDokumen(Pegawai $pegawai, DokumenKepegawaian $dokumen)
    {
        if ($dokumen->file_dokumen) {
            Storage::disk('public')->delete($dokumen->file_dokumen);
        }
        
        $dokumen->delete();
        
        return redirect()
            ->route('kepegawaian.pegawai.riwayat.index', $pegawai)
            ->with('success', 'Dokumen kepegawaian berhasil dihapus');
    }
}
