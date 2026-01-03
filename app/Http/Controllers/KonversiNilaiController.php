<?php

namespace App\Http\Controllers;

use App\Models\PengajuanKonversi;
use App\Models\DetailKonversi;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KonversiNilaiController extends Controller
{
    /**
     * Admin: Daftar semua pengajuan konversi
     */
    public function index(Request $request)
    {
        $query = PengajuanKonversi::with(['mahasiswa', 'programStudiTujuan', 'prosesOleh', 'detailKonversi'])
            ->orderBy('created_at', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pengajuan', 'like', "%{$search}%")
                    ->orWhere('universitas_asal', 'like', "%{$search}%")
                    ->orWhere('nama_calon_mahasiswa', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($q2) use ($search) {
                        $q2->where('nama', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
            });
        }

        $pengajuan = $query->paginate(15);

        return view('admin.konversi-nilai.index', compact('pengajuan'));
    }

    /**
     * Admin: Detail pengajuan
     */
    public function show(PengajuanKonversi $pengajuanKonversi)
    {
        $pengajuanKonversi->load(['mahasiswa.programStudi', 'programStudiTujuan', 'prosesOleh', 'prosesKaprodiOleh', 'detailKonversi.mataKuliah']);
        $mataKuliahs = MataKuliah::orderBy('nama')->get();
        
        // Ambil mahasiswa dari prodi tujuan untuk finalisasi
        $mahasiswas = [];
        if ($pengajuanKonversi->program_studi_tujuan_id) {
            $mahasiswas = Mahasiswa::where('program_studi_id', $pengajuanKonversi->program_studi_tujuan_id)
                ->where('status', 'Aktif')
                ->orderBy('nama')
                ->get();
        }

        return view('admin.konversi-nilai.show', compact('pengajuanKonversi', 'mataKuliahs', 'mahasiswas'));
    }

    /**
     * Admin: Proses pengajuan (update detail konversi)
     */
    public function proses(Request $request, PengajuanKonversi $pengajuanKonversi)
    {
        $request->validate([
            'detail' => 'required|array',
            'detail.*.status' => 'required|in:disetujui,ditolak,tidak_dikonversi',
            'detail.*.mata_kuliah_id' => 'nullable|exists:mata_kuliah,id',
            'detail.*.nilai_konversi' => 'nullable|string',
            'detail.*.alasan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->detail as $id => $data) {
                $detail = DetailKonversi::find($id);
                if ($detail && $detail->pengajuan_konversi_id == $pengajuanKonversi->id) {
                    $detail->status = $data['status'];
                    $detail->mata_kuliah_id = $data['mata_kuliah_id'] ?? null;
                    $detail->nilai_konversi = $data['nilai_konversi'] ?? null;
                    $detail->bobot_konversi = DetailKonversi::nilaiKeBobot($data['nilai_konversi'] ?? '');
                    $detail->alasan = $data['alasan'] ?? null;
                    $detail->disetujui_oleh = auth()->id();
                    $detail->save();
                }
            }

            $pengajuanKonversi->status = 'diproses';
            $pengajuanKonversi->catatan = $request->catatan;
            $pengajuanKonversi->diproses_oleh = auth()->id();
            $pengajuanKonversi->tanggal_diproses = now();
            $pengajuanKonversi->save();

            DB::commit();
            return redirect()->route('admin.konversi-nilai.show', $pengajuanKonversi->hashid)
                ->with('success', 'Pengajuan konversi berhasil diproses!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    /**
     * Admin: Finalisasi pengajuan
     */
    public function finalisasi(Request $request, PengajuanKonversi $pengajuanKonversi)
    {
        // Validasi status pengajuan sebelum finalisasi
        if ($pengajuanKonversi->status != 'disetujui_kaprodi') {
            return back()->with('error', 'Hanya pengajuan dengan status "Disetujui Kaprodi" yang dapat difinalisasi!');
        }

        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'mahasiswa_id' => 'nullable|exists:mahasiswa,id',
            'catatan' => 'nullable|string',
        ]);

        // Jika disetujui, harus ada mahasiswa_id (calon sudah menjadi mahasiswa)
        if ($request->status == 'disetujui') {
            if (!$pengajuanKonversi->mahasiswa_id && !$request->mahasiswa_id) {
                return back()->with('error', 'Silakan pilih mahasiswa yang sudah terdaftar untuk finalisasi!');
            }
            
            // Update mahasiswa_id jika belum ada
            if (!$pengajuanKonversi->mahasiswa_id && $request->mahasiswa_id) {
                $pengajuanKonversi->mahasiswa_id = $request->mahasiswa_id;
            }
        }

        $pengajuanKonversi->status = $request->status;
        $pengajuanKonversi->catatan = $request->catatan ?? $pengajuanKonversi->catatan;
        $pengajuanKonversi->diproses_oleh = auth()->id();
        $pengajuanKonversi->tanggal_diproses = now();
        $pengajuanKonversi->save();

        // Jika disetujui dan ada mahasiswa_id, masukkan nilai ke tabel nilai
        if ($request->status == 'disetujui' && $pengajuanKonversi->mahasiswa_id) {
            $this->prosesNilaiKonversi($pengajuanKonversi);
        }

        return redirect()->route('admin.konversi-nilai.index')
            ->with('success', 'Pengajuan konversi berhasil ' . ($request->status == 'disetujui' ? 'disetujui' : 'ditolak') . '!');
    }

    /**
     * Admin: Ajukan ke Kaprodi untuk diproses
     */
    public function ajukanKeKaprodi(PengajuanKonversi $pengajuanKonversi)
    {
        if ($pengajuanKonversi->detailKonversi->count() == 0) {
            return back()->with('error', 'Tambahkan minimal 1 mata kuliah sebelum mengajukan ke Kaprodi!');
        }

        $pengajuanKonversi->status = 'menunggu_kaprodi';
        $pengajuanKonversi->save();

        return back()->with('success', 'Konversi nilai berhasil diajukan ke Kaprodi untuk diproses!');
    }

    /**
     * Proses nilai konversi ke tabel nilai
     */
    private function prosesNilaiKonversi(PengajuanKonversi $konversi)
    {
        $detailDisetujui = $konversi->detailKonversi()
            ->where('status', 'disetujui')
            ->whereNotNull('mata_kuliah_id')
            ->get();

        foreach ($detailDisetujui as $detail) {
            if ($detail->nilai_konversi) {
                // Cek apakah nilai sudah ada
                $existing = \App\Models\Nilai::where('mahasiswa_id', $konversi->mahasiswa_id)
                    ->where('mata_kuliah_id', $detail->mata_kuliah_id)
                    ->first();

                if (!$existing) {
                    \App\Models\Nilai::create([
                        'mahasiswa_id' => $konversi->mahasiswa_id,
                        'mata_kuliah_id' => $detail->mata_kuliah_id,
                        'tahun_akademik_id' => \App\Models\TahunAkademik::where('is_aktif', true)->first()->id ?? 1,
                        'nilai_huruf' => $detail->nilai_konversi,
                        'nilai_angka' => $detail->bobot_konversi * 25, // Estimasi
                        'keterangan' => 'Hasil Konversi dari ' . $konversi->universitas_asal,
                    ]);
                }
            }
        }
    }

    /**
     * Admin: Form tambah konversi nilai untuk calon mahasiswa pindahan
     */
    public function create()
    {
        $programStudis = ProgramStudi::with('fakultas')->orderBy('nama')->get();
        
        return view('admin.konversi-nilai.create', compact('programStudis'));
    }

    /**
     * Admin: Simpan konversi nilai baru (untuk calon mahasiswa)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_calon_mahasiswa' => 'required|string|max:255',
            'email_calon' => 'nullable|email|max:255',
            'no_hp_calon' => 'nullable|string|max:20',
            'program_studi_tujuan_id' => 'required|exists:program_studi,id',
            'universitas_asal' => 'required|string|max:255',
            'program_studi_asal' => 'required|string|max:255',
            'nim_asal' => 'required|string|max:50',
            'tahun_masuk_asal' => 'required|integer|min:1990|max:' . date('Y'),
            'dokumen_transkrip' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'dokumen_silabus' => 'nullable|file|mimes:pdf|max:10240',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,zip|max:10240',
            'catatan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Upload dokumen
            $transkripPath = null;
            $silabusPath = null;
            $pendukungPath = null;

            if ($request->hasFile('dokumen_transkrip')) {
                $transkripPath = $request->file('dokumen_transkrip')->store('konversi/transkrip', 'public');
            }
            if ($request->hasFile('dokumen_silabus')) {
                $silabusPath = $request->file('dokumen_silabus')->store('konversi/silabus', 'public');
            }
            if ($request->hasFile('dokumen_pendukung')) {
                $pendukungPath = $request->file('dokumen_pendukung')->store('konversi/pendukung', 'public');
            }

            // Buat pengajuan konversi
            $pengajuan = PengajuanKonversi::create([
                'nama_calon_mahasiswa' => $request->nama_calon_mahasiswa,
                'email_calon' => $request->email_calon,
                'no_hp_calon' => $request->no_hp_calon,
                'program_studi_tujuan_id' => $request->program_studi_tujuan_id,
                'universitas_asal' => $request->universitas_asal,
                'program_studi_asal' => $request->program_studi_asal,
                'nim_asal' => $request->nim_asal,
                'tahun_masuk_asal' => $request->tahun_masuk_asal,
                'dokumen_transkrip' => $transkripPath,
                'dokumen_silabus' => $silabusPath,
                'dokumen_pendukung' => $pendukungPath,
                'catatan' => $request->catatan,
                'status' => 'draft',
                'diproses_oleh' => auth()->id(),
            ]);

            DB::commit();
            return redirect()->route('admin.konversi-nilai.show', $pengajuan->hashid)
                ->with('success', 'Data konversi nilai berhasil dibuat! Silakan tambahkan detail mata kuliah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Admin: Form edit konversi nilai
     */
    public function edit(PengajuanKonversi $pengajuanKonversi)
    {
        if (!in_array($pengajuanKonversi->status, ['draft', 'diproses'])) {
            return back()->with('error', 'Konversi nilai yang sudah selesai tidak dapat diedit!');
        }

        $pengajuanKonversi->load(['mahasiswa.programStudi', 'detailKonversi']);
        $mahasiswas = Mahasiswa::with('programStudi')
            ->where('status', 'Aktif')
            ->orderBy('nama')
            ->get();

        return view('admin.konversi-nilai.edit', compact('pengajuanKonversi', 'mahasiswas'));
    }

    /**
     * Admin: Update konversi nilai
     */
    public function update(Request $request, PengajuanKonversi $pengajuanKonversi)
    {
        if (!in_array($pengajuanKonversi->status, ['draft', 'diproses'])) {
            return back()->with('error', 'Konversi nilai yang sudah selesai tidak dapat diedit!');
        }

        $request->validate([
            'universitas_asal' => 'required|string|max:255',
            'program_studi_asal' => 'required|string|max:255',
            'nim_asal' => 'required|string|max:50',
            'tahun_masuk_asal' => 'required|integer|min:1990|max:' . date('Y'),
            'dokumen_transkrip' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'dokumen_silabus' => 'nullable|file|mimes:pdf|max:10240',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,zip|max:10240',
            'catatan' => 'nullable|string',
        ]);

        try {
            // Upload dokumen jika ada
            if ($request->hasFile('dokumen_transkrip')) {
                if ($pengajuanKonversi->dokumen_transkrip) {
                    Storage::disk('public')->delete($pengajuanKonversi->dokumen_transkrip);
                }
                $pengajuanKonversi->dokumen_transkrip = $request->file('dokumen_transkrip')->store('konversi/transkrip', 'public');
            }
            if ($request->hasFile('dokumen_silabus')) {
                if ($pengajuanKonversi->dokumen_silabus) {
                    Storage::disk('public')->delete($pengajuanKonversi->dokumen_silabus);
                }
                $pengajuanKonversi->dokumen_silabus = $request->file('dokumen_silabus')->store('konversi/silabus', 'public');
            }
            if ($request->hasFile('dokumen_pendukung')) {
                if ($pengajuanKonversi->dokumen_pendukung) {
                    Storage::disk('public')->delete($pengajuanKonversi->dokumen_pendukung);
                }
                $pengajuanKonversi->dokumen_pendukung = $request->file('dokumen_pendukung')->store('konversi/pendukung', 'public');
            }

            $pengajuanKonversi->universitas_asal = $request->universitas_asal;
            $pengajuanKonversi->program_studi_asal = $request->program_studi_asal;
            $pengajuanKonversi->nim_asal = $request->nim_asal;
            $pengajuanKonversi->tahun_masuk_asal = $request->tahun_masuk_asal;
            $pengajuanKonversi->catatan = $request->catatan;
            $pengajuanKonversi->save();

            return redirect()->route('admin.konversi-nilai.show', $pengajuanKonversi->hashid)
                ->with('success', 'Data konversi nilai berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Admin: Hapus konversi nilai
     */
    public function destroy(PengajuanKonversi $pengajuanKonversi)
    {
        if (!in_array($pengajuanKonversi->status, ['draft', 'diproses'])) {
            return back()->with('error', 'Konversi nilai yang sudah selesai tidak dapat dihapus!');
        }

        try {
            // Hapus file dokumen
            if ($pengajuanKonversi->dokumen_transkrip) {
                Storage::disk('public')->delete($pengajuanKonversi->dokumen_transkrip);
            }
            if ($pengajuanKonversi->dokumen_silabus) {
                Storage::disk('public')->delete($pengajuanKonversi->dokumen_silabus);
            }
            if ($pengajuanKonversi->dokumen_pendukung) {
                Storage::disk('public')->delete($pengajuanKonversi->dokumen_pendukung);
            }

            // Hapus detail konversi
            $pengajuanKonversi->detailKonversi()->delete();
            
            // Hapus pengajuan
            $pengajuanKonversi->delete();

            return redirect()->route('admin.konversi-nilai.index')
                ->with('success', 'Data konversi nilai berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * Admin: Tambah detail mata kuliah ke konversi
     */
    public function tambahDetail(Request $request, PengajuanKonversi $pengajuanKonversi)
    {
        if (!in_array($pengajuanKonversi->status, ['draft', 'diproses'])) {
            return back()->with('error', 'Tidak dapat menambah mata kuliah ke konversi yang sudah selesai!');
        }

        $request->validate([
            'kode_mk_asal' => 'required|string|max:20',
            'nama_mk_asal' => 'required|string|max:255',
            'sks_asal' => 'required|integer|min:1|max:8',
            'nilai_asal' => 'required|string|max:5',
        ]);

        DetailKonversi::create([
            'pengajuan_konversi_id' => $pengajuanKonversi->id,
            'kode_mk_asal' => $request->kode_mk_asal,
            'nama_mk_asal' => $request->nama_mk_asal,
            'sks_asal' => $request->sks_asal,
            'nilai_asal' => $request->nilai_asal,
            'bobot_asal' => DetailKonversi::nilaiKeBobot($request->nilai_asal),
        ]);

        return back()->with('success', 'Mata kuliah berhasil ditambahkan!');
    }

    /**
     * Admin: Hapus detail mata kuliah dari konversi
     */
    public function hapusDetail(DetailKonversi $detailKonversi)
    {
        $pengajuan = $detailKonversi->pengajuanKonversi;
        
        if (!in_array($pengajuan->status, ['draft', 'diproses'])) {
            return back()->with('error', 'Tidak dapat menghapus mata kuliah dari konversi yang sudah selesai!');
        }

        $detailKonversi->delete();

        return back()->with('success', 'Mata kuliah berhasil dihapus!');
    }

    // ========== MAHASISWA ROUTES ==========

    /**
     * Mahasiswa: Daftar konversi nilai saya
     */
    public function mahasiswaIndex()
    {
        $mahasiswa = auth()->user()->mahasiswa;
        
        if (!$mahasiswa) {
            return back()->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        $pengajuans = PengajuanKonversi::where('mahasiswa_id', $mahasiswa->id)
            ->with('detailKonversi.mataKuliah')
            ->orderBy('created_at', 'desc')
            ->get();

        // Cek apakah ada pengajuan yang masih draft
        $hasDraft = $pengajuans->where('status', 'draft')->count() > 0;

        return view('mahasiswa.konversi-nilai.index', compact('pengajuans', 'hasDraft'));
    }

    /**
     * Mahasiswa: Form pengajuan konversi nilai baru
     */
    public function mahasiswaCreate()
    {
        $mahasiswa = auth()->user()->mahasiswa;
        
        if (!$mahasiswa) {
            return back()->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        // Cek apakah sudah ada pengajuan yang masih dalam proses
        $existingPengajuan = PengajuanKonversi::where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('status', ['draft', 'menunggu_kaprodi', 'diproses_kaprodi'])
            ->first();

        if ($existingPengajuan) {
            return redirect()->route('mahasiswa.konversi-nilai.show', $existingPengajuan->hashid)
                ->with('warning', 'Anda masih memiliki pengajuan konversi yang belum selesai!');
        }

        return view('mahasiswa.konversi-nilai.create', compact('mahasiswa'));
    }

    /**
     * Mahasiswa: Simpan pengajuan konversi nilai baru
     */
    public function mahasiswaStore(Request $request)
    {
        $mahasiswa = auth()->user()->mahasiswa;
        
        if (!$mahasiswa) {
            return back()->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        $request->validate([
            'universitas_asal' => 'required|string|max:255',
            'program_studi_asal' => 'required|string|max:255',
            'nim_asal' => 'required|string|max:50',
            'tahun_masuk_asal' => 'required|integer|min:1990|max:' . date('Y'),
            'transkrip_asal' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'silabus' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'alasan' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Upload dokumen
            $transkripPath = $request->file('transkrip_asal')->store('konversi/transkrip', 'public');
            $silabusPath = null;
            if ($request->hasFile('silabus')) {
                $silabusPath = $request->file('silabus')->store('konversi/silabus', 'public');
            }

            // Buat pengajuan konversi
            $pengajuan = PengajuanKonversi::create([
                'mahasiswa_id' => $mahasiswa->id,
                'nama_calon_mahasiswa' => $mahasiswa->nama,
                'email_calon' => auth()->user()->email,
                'no_hp_calon' => $mahasiswa->no_hp,
                'program_studi_tujuan_id' => $mahasiswa->program_studi_id,
                'universitas_asal' => $request->universitas_asal,
                'program_studi_asal' => $request->program_studi_asal,
                'nim_asal' => $request->nim_asal,
                'tahun_masuk_asal' => $request->tahun_masuk_asal,
                'dokumen_transkrip' => $transkripPath,
                'dokumen_silabus' => $silabusPath,
                'catatan' => $request->alasan,
                'status' => 'draft',
            ]);

            DB::commit();
            return redirect()->route('mahasiswa.konversi-nilai.show', $pengajuan->hashid)
                ->with('success', 'Pengajuan konversi nilai berhasil dibuat! Silakan tambahkan detail mata kuliah yang akan dikonversi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Mahasiswa: Detail konversi nilai
     */
    public function mahasiswaShow(PengajuanKonversi $pengajuanKonversi)
    {
        $mahasiswa = auth()->user()->mahasiswa;
        
        if ($pengajuanKonversi->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        $pengajuanKonversi->load('detailKonversi.mataKuliah');

        return view('mahasiswa.konversi-nilai.show', compact('pengajuanKonversi'));
    }

    /**
     * Mahasiswa: Tambah detail mata kuliah ke konversi
     */
    public function mahasiswaTambahDetail(Request $request, PengajuanKonversi $pengajuanKonversi)
    {
        $mahasiswa = auth()->user()->mahasiswa;
        
        if ($pengajuanKonversi->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        if ($pengajuanKonversi->status != 'draft') {
            return back()->with('error', 'Tidak dapat menambah mata kuliah ke pengajuan yang sudah diajukan!');
        }

        $request->validate([
            'kode_mk_asal' => 'required|string|max:20',
            'nama_mk_asal' => 'required|string|max:255',
            'sks_asal' => 'required|integer|min:1|max:8',
            'nilai_asal' => 'required|string|max:5',
        ]);

        DetailKonversi::create([
            'pengajuan_konversi_id' => $pengajuanKonversi->id,
            'kode_mk_asal' => $request->kode_mk_asal,
            'nama_mk_asal' => $request->nama_mk_asal,
            'sks_asal' => $request->sks_asal,
            'nilai_asal' => $request->nilai_asal,
            'bobot_asal' => DetailKonversi::nilaiKeBobot($request->nilai_asal),
        ]);

        return back()->with('success', 'Mata kuliah berhasil ditambahkan!');
    }

    /**
     * Mahasiswa: Hapus detail mata kuliah dari konversi
     */
    public function mahasiswaHapusDetail(PengajuanKonversi $pengajuanKonversi, DetailKonversi $detailKonversi)
    {
        $mahasiswa = auth()->user()->mahasiswa;
        
        if ($pengajuanKonversi->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        if ($pengajuanKonversi->status != 'draft') {
            return back()->with('error', 'Tidak dapat menghapus mata kuliah dari pengajuan yang sudah diajukan!');
        }

        if ($detailKonversi->pengajuan_konversi_id != $pengajuanKonversi->id) {
            abort(403);
        }

        $detailKonversi->delete();

        return back()->with('success', 'Mata kuliah berhasil dihapus!');
    }

    /**
     * Mahasiswa: Ajukan konversi nilai ke admin
     */
    public function mahasiswaAjukan(PengajuanKonversi $pengajuanKonversi)
    {
        $mahasiswa = auth()->user()->mahasiswa;
        
        if ($pengajuanKonversi->mahasiswa_id != $mahasiswa->id) {
            abort(403);
        }

        if ($pengajuanKonversi->status != 'draft') {
            return back()->with('error', 'Pengajuan sudah diajukan sebelumnya!');
        }

        if ($pengajuanKonversi->detailKonversi->count() == 0) {
            return back()->with('error', 'Tambahkan minimal 1 mata kuliah sebelum mengajukan!');
        }

        $pengajuanKonversi->status = 'menunggu_kaprodi';
        $pengajuanKonversi->save();

        return redirect()->route('mahasiswa.konversi-nilai.index')
            ->with('success', 'Pengajuan konversi nilai berhasil dikirim! Silakan tunggu proses verifikasi.');
    }
}
