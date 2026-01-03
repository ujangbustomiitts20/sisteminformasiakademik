<?php

namespace App\Http\Controllers;

use App\Models\CalonMahasiswa;
use App\Models\GelombangPmb;
use App\Models\JalurSeleksi;
use App\Models\ProgramStudi;
use App\Models\DokumenCamaba;
use App\Models\SettingDokumenPmb;
use App\Models\BiayaPendaftaran;
use App\Models\PembayaranPmb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CalonMahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = CalonMahasiswa::with(['gelombangPmb', 'jalurSeleksi', 'programStudi']);

        // Filter
        if ($request->filled('gelombang')) {
            $query->where('gelombang_pmb_id', $request->gelombang);
        }
        if ($request->filled('jalur')) {
            $query->where('jalur_seleksi_id', $request->jalur);
        }
        if ($request->filled('prodi')) {
            $query->where('program_studi_id', $request->prodi);
        }
        if ($request->filled('status')) {
            $query->where(function($q) use ($request) {
                $q->where('status', $request->status)
                  ->orWhere('status_pendaftaran', $request->status);
            });
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $calonMahasiswas = $query->orderBy('created_at', 'desc')->paginate(15);

        // Data untuk filter
        $gelombangs = GelombangPmb::with('periodePmb')->orderBy('id', 'desc')->get();
        $jalurs = JalurSeleksi::active()->get();
        $prodis = ProgramStudi::orderBy('nama')->get();

        return view('pmb.calon-mahasiswa.index', compact(
            'calonMahasiswas', 'gelombangs', 'jalurs', 'prodis'
        ));
    }

    public function create()
    {
        $gelombangs = GelombangPmb::with('periodePmb')
            ->where('is_active', true)
            ->orWhere(function($q) {
                $q->where('tanggal_mulai_daftar', '<=', now())
                  ->where('tanggal_selesai_daftar', '>=', now());
            })
            ->get();
        
        // Jika tidak ada gelombang aktif, tampilkan semua
        if ($gelombangs->isEmpty()) {
            $gelombangs = GelombangPmb::with('periodePmb')->orderBy('id', 'desc')->get();
        }
        
        $jalurs = JalurSeleksi::active()->get();
        
        // Jika tidak ada jalur aktif, tampilkan semua
        if ($jalurs->isEmpty()) {
            $jalurs = JalurSeleksi::all();
        }
        
        $prodis = ProgramStudi::orderBy('nama')->get();
        $settingDokumen = SettingDokumenPmb::active()->ordered()->get();

        return view('pmb.calon-mahasiswa.create', compact('gelombangs', 'jalurs', 'prodis', 'settingDokumen'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gelombang_pmb_id' => 'required|exists:gelombang_pmb,id',
            'jalur_seleksi_id' => 'required|exists:jalur_seleksi,id',
            'program_studi_id' => 'required|exists:program_studi,id',
            'program_studi_2_id' => 'nullable|exists:program_studi,id|different:program_studi_id',
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20',
            'nisn' => 'nullable|string|max:20',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before:today',
            'agama' => 'nullable|string|max:20',
            'email' => 'required|email|unique:calon_mahasiswa,email',
            'no_hp' => 'required|string|max:20',
            'no_wa' => 'nullable|string|max:20',
            'alamat' => 'required|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'asal_sekolah' => 'required|string|max:255',
            'npsn_sekolah' => 'nullable|string|max:20',
            'jurusan_sekolah' => 'nullable|string|max:100',
            'tahun_lulus' => 'required|integer|min:2000|max:' . date('Y'),
            'nilai_rata_rata' => 'nullable|numeric|min:0|max:100',
            'nama_ayah' => 'nullable|string|max:255',
            'pekerjaan_ayah' => 'nullable|string|max:100',
            'no_hp_ayah' => 'nullable|string|max:20',
            'nama_ibu' => 'nullable|string|max:255',
            'pekerjaan_ibu' => 'nullable|string|max:100',
            'no_hp_ibu' => 'nullable|string|max:20',
            'penghasilan_ortu' => 'nullable|numeric|min:0',
            'foto' => 'nullable|image|max:2048',
            'dokumen' => 'nullable|array',
            'dokumen.*' => 'nullable|file|max:5120', // Max 5MB per file
        ]);

        DB::beginTransaction();
        try {
            // Handle foto upload
            if ($request->hasFile('foto')) {
                $validated['foto'] = $request->file('foto')->store('camaba/foto', 'public');
            }

            // Generate password default
            $validated['password'] = Hash::make(date('dmY', strtotime($validated['tanggal_lahir'])));
            $validated['status_pendaftaran'] = 'menunggu_bayar';

            // Remove dokumen from validated data before creating
            $dokumenFiles = $request->file('dokumen', []);
            unset($validated['dokumen']);

            $calonMahasiswa = CalonMahasiswa::create($validated);

            // Buat tagihan pembayaran pendaftaran otomatis
            $biayaPendaftaran = BiayaPendaftaran::where('gelombang_pmb_id', $validated['gelombang_pmb_id'])
                ->where('jalur_seleksi_id', $validated['jalur_seleksi_id'])
                ->first();

            if ($biayaPendaftaran) {
                $noPembayaran = 'PAY' . date('Ymd') . str_pad(PembayaranPmb::whereDate('created_at', today())->count() + 1, 5, '0', STR_PAD_LEFT);
                
                PembayaranPmb::create([
                    'calon_mahasiswa_id' => $calonMahasiswa->id,
                    'no_pembayaran' => $noPembayaran,
                    'jenis_pembayaran' => 'pendaftaran',
                    'jumlah' => $biayaPendaftaran->total_biaya,
                    'status' => 'pending',
                    'tanggal_expired' => now()->addDays(7),
                ]);
            }

            // Upload dokumen
            if (!empty($dokumenFiles)) {
                foreach ($dokumenFiles as $kode => $file) {
                    if ($file) {
                        $path = $file->store('camaba/dokumen/' . $calonMahasiswa->id, 'public');
                        DokumenCamaba::create([
                            'calon_mahasiswa_id' => $calonMahasiswa->id,
                            'jenis_dokumen' => $kode,
                            'nama_file' => $file->getClientOriginalName(),
                            'path_file' => $path,
                            'status_verifikasi' => 'pending',
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('pmb.calon-mahasiswa.show', $calonMahasiswa->hashid)
                ->with('success', 'Calon Mahasiswa berhasil didaftarkan dengan No. Pendaftaran: ' . $calonMahasiswa->no_pendaftaran);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal mendaftarkan calon mahasiswa: ' . $e->getMessage())->withInput();
        }
    }

    public function show(string $hashid)
    {
        $calonMahasiswa = CalonMahasiswa::findByHashidOrFail($hashid);
        $calonMahasiswa->load([
            'gelombangPmb.periodePmb',
            'jalurSeleksi',
            'programStudi',
            'programStudi2',
            'dokumen',
            'pembayaran',
            'nilaiSeleksi',
            'hasilSeleksi',
            'daftarUlang',
        ]);

        $settingDokumen = SettingDokumenPmb::active()->ordered()->get();

        return view('pmb.calon-mahasiswa.show', compact('calonMahasiswa', 'settingDokumen'));
    }

    public function edit(string $hashid)
    {
        $calonMahasiswa = CalonMahasiswa::findByHashidOrFail($hashid);
        $gelombangs = GelombangPmb::with('periodePmb')->orderBy('id', 'desc')->get();
        $jalurs = JalurSeleksi::all();
        $prodis = ProgramStudi::orderBy('nama')->get();
        $settingDokumen = SettingDokumenPmb::active()->ordered()->get();

        return view('pmb.calon-mahasiswa.edit', compact('calonMahasiswa', 'gelombangs', 'jalurs', 'prodis', 'settingDokumen'));
    }

    public function update(Request $request, string $hashid)
    {
        $calonMahasiswa = CalonMahasiswa::findByHashidOrFail($hashid);

        $validated = $request->validate([
            'gelombang_pmb_id' => 'required|exists:gelombang_pmb,id',
            'jalur_seleksi_id' => 'required|exists:jalur_seleksi,id',
            'program_studi_id' => 'required|exists:program_studi,id',
            'program_studi_2_id' => 'nullable|exists:program_studi,id|different:program_studi_id',
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20',
            'nisn' => 'nullable|string|max:20',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before:today',
            'agama' => 'nullable|string|max:20',
            'email' => 'required|email|unique:calon_mahasiswa,email,' . $calonMahasiswa->id,
            'no_hp' => 'required|string|max:20',
            'no_wa' => 'nullable|string|max:20',
            'alamat' => 'required|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'asal_sekolah' => 'required|string|max:255',
            'npsn_sekolah' => 'nullable|string|max:20',
            'jurusan_sekolah' => 'nullable|string|max:100',
            'tahun_lulus' => 'required|integer|min:2000|max:' . date('Y'),
            'nilai_rata_rata' => 'nullable|numeric|min:0|max:100',
            'nama_ayah' => 'nullable|string|max:255',
            'pekerjaan_ayah' => 'nullable|string|max:100',
            'no_hp_ayah' => 'nullable|string|max:20',
            'nama_ibu' => 'nullable|string|max:255',
            'pekerjaan_ibu' => 'nullable|string|max:100',
            'no_hp_ibu' => 'nullable|string|max:20',
            'penghasilan_ortu' => 'nullable|numeric|min:0',
            'foto' => 'nullable|image|max:2048',
            'status_pendaftaran' => 'nullable|in:draft,menunggu_bayar,terdaftar,mengikuti_ujian,lulus,tidak_lulus,daftar_ulang,menjadi_mahasiswa,batal',
        ]);

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Delete old foto
            if ($calonMahasiswa->foto) {
                Storage::disk('public')->delete($calonMahasiswa->foto);
            }
            $validated['foto'] = $request->file('foto')->store('camaba/foto', 'public');
        }

        $calonMahasiswa->update($validated);

        return redirect()->route('pmb.calon-mahasiswa.show', $calonMahasiswa->hashid)
            ->with('success', 'Data Calon Mahasiswa berhasil diperbarui.');
    }

    public function destroy(string $hashid)
    {
        $calonMahasiswa = CalonMahasiswa::findByHashidOrFail($hashid);

        // Delete foto
        if ($calonMahasiswa->foto) {
            Storage::disk('public')->delete($calonMahasiswa->foto);
        }

        // Delete dokumen
        foreach ($calonMahasiswa->dokumen as $dokumen) {
            Storage::disk('public')->delete($dokumen->path_file);
        }

        $calonMahasiswa->delete();

        return redirect()->route('pmb.calon-mahasiswa.index')
            ->with('success', 'Data Calon Mahasiswa berhasil dihapus.');
    }

    public function verifikasiDokumen(string $hashid)
    {
        $calonMahasiswa = CalonMahasiswa::findByHashidOrFail($hashid);
        $calonMahasiswa->load('dokumen');
        $settingDokumen = SettingDokumenPmb::active()->ordered()->get();

        return view('pmb.calon-mahasiswa.verifikasi-dokumen', compact('calonMahasiswa', 'settingDokumen'));
    }

    public function updateVerifikasiDokumen(Request $request, string $hashid)
    {
        $calonMahasiswa = CalonMahasiswa::findByHashidOrFail($hashid);

        $validated = $request->validate([
            'dokumen' => 'required|array',
            'dokumen.*.id' => 'required|exists:dokumen_camaba,id',
            'dokumen.*.status' => 'required|in:pending,valid,tidak_valid',
            'dokumen.*.catatan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['dokumen'] as $dok) {
                DokumenCamaba::where('id', $dok['id'])->update([
                    'status_verifikasi' => $dok['status'],
                    'catatan_verifikasi' => $dok['catatan'] ?? null,
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);
            }

            // Check if all wajib documents are valid
            $dokumenWajib = SettingDokumenPmb::wajib()->pluck('kode')->toArray();
            $dokumenValid = $calonMahasiswa->dokumen()
                ->whereIn('jenis_dokumen', $dokumenWajib)
                ->where('status_verifikasi', 'valid')
                ->count();

            $calonMahasiswa->update([
                'is_dokumen_lengkap' => $dokumenValid >= count($dokumenWajib)
            ]);

            DB::commit();

            return back()->with('success', 'Verifikasi dokumen berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal memverifikasi dokumen: ' . $e->getMessage());
        }
    }

    public function cetakKartuPeserta(string $hashid)
    {
        $calonMahasiswa = CalonMahasiswa::findByHashidOrFail($hashid);
        $calonMahasiswa->load(['gelombangPmb.periodePmb', 'jalurSeleksi', 'programStudi', 'pesertaUjian.jadwalUjian']);

        $pdf = \PDF::loadView('pmb.calon-mahasiswa.kartu-peserta', compact('calonMahasiswa'));
        return $pdf->download('kartu-peserta-' . $calonMahasiswa->no_pendaftaran . '.pdf');
    }

    /**
     * Update status calon mahasiswa
     */
    public function updateStatus(Request $request, string $hashid)
    {
        $calonMahasiswa = CalonMahasiswa::findByHashidOrFail($hashid);
        
        $validated = $request->validate([
            'status' => 'required|in:draft,mendaftar,menunggu_bayar,terdaftar,verifikasi_dokumen,lulus_administrasi,mengikuti_ujian,lulus,tidak_lulus,daftar_ulang,menjadi_mahasiswa,batal',
            'catatan' => 'nullable|string|max:500',
        ]);

        $oldStatus = $calonMahasiswa->status_pendaftaran ?? $calonMahasiswa->status;
        
        DB::beginTransaction();
        try {
            // Update both status fields to keep them in sync
            $calonMahasiswa->update([
                'status' => $validated['status'],
                'status_pendaftaran' => $validated['status'],
            ]);

            // Log activity jika perlu
            // ActivityLog::create([...]);

            DB::commit();

            return back()->with('success', "Status berhasil diubah dari '{$oldStatus}' menjadi '{$validated['status']}'");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * Upload dokumen tambahan
     */
    public function uploadDokumen(Request $request, string $hashid)
    {
        $calonMahasiswa = CalonMahasiswa::findByHashidOrFail($hashid);

        $request->validate([
            'jenis_dokumen' => 'required|string|max:50',
            'file' => 'required|file|max:5120', // Max 5MB
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $path = $file->store('camaba/dokumen/' . $calonMahasiswa->id, 'public');

            // Hapus dokumen lama dengan jenis yang sama jika ada
            $existingDokumen = $calonMahasiswa->dokumen()->where('jenis_dokumen', $request->jenis_dokumen)->first();
            if ($existingDokumen) {
                Storage::disk('public')->delete($existingDokumen->path_file);
                $existingDokumen->delete();
            }

            DokumenCamaba::create([
                'calon_mahasiswa_id' => $calonMahasiswa->id,
                'jenis_dokumen' => $request->jenis_dokumen,
                'nama_file' => $file->getClientOriginalName(),
                'path_file' => $path,
                'status_verifikasi' => 'pending',
            ]);

            DB::commit();

            return back()->with('success', 'Dokumen berhasil diupload.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal mengupload dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Hapus dokumen
     */
    public function deleteDokumen(string $hashid, string $dokumenHashid)
    {
        $calonMahasiswa = CalonMahasiswa::findByHashidOrFail($hashid);
        $dokumen = DokumenCamaba::findByHashidOrFail($dokumenHashid);

        // Pastikan dokumen milik calon mahasiswa ini
        if ($dokumen->calon_mahasiswa_id !== $calonMahasiswa->id) {
            return back()->with('error', 'Dokumen tidak ditemukan.');
        }

        DB::beginTransaction();
        try {
            Storage::disk('public')->delete($dokumen->path_file);
            $dokumen->delete();

            DB::commit();

            return back()->with('success', 'Dokumen berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Set status lulus administrasi
     */
    public function setLulusAdministrasi(string $hashid)
    {
        $calonMahasiswa = CalonMahasiswa::findByHashidOrFail($hashid);

        // Cek syarat: dokumen harus lengkap dan valid
        if (!$calonMahasiswa->is_dokumen_lengkap) {
            return back()->with('error', 'Tidak dapat mengubah status. Dokumen belum lengkap/valid.');
        }

        $calonMahasiswa->update([
            'status_pendaftaran' => 'lulus_administrasi',
        ]);

        return back()->with('success', 'Status berhasil diubah menjadi Lulus Administrasi.');
    }
}
