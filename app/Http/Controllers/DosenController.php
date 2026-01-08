<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\User;
use App\Models\ProgramStudi;
use App\Models\UnitKerja;
use App\Models\NamaJabatan;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        $dosen = Dosen::with(['programStudi.fakultas'])->orderBy('nama')->get();

        return view('dosen.index', compact('dosen'));
    }

    public function create()
    {
        $programStudi = ProgramStudi::with('fakultas')->get();
        $unitKerja = UnitKerja::where('is_active', true)->orderBy('nama')->get();
        $namaJabatans = NamaJabatan::aktif()->orderBy('level')->orderBy('urutan')->get();
        $provinsi = Provinsi::orderBy('nama')->get();
        return view('dosen.create', compact('programStudi', 'unitKerja', 'namaJabatans', 'provinsi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nidn' => 'required|unique:dosen,nidn|max:20',
            'nama' => 'required|max:255',
            'email' => 'required|email|unique:dosen,email|unique:users,email',
            'program_studi_id' => 'required|exists:program_studi,id',
            'unit_kerja_id' => 'nullable|exists:unit_kerja,id',
            'nama_jabatan_id' => 'nullable|exists:nama_jabatan,id',
            'jabatan_struktural' => 'nullable|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'jabatan_fungsional' => 'nullable|max:100',
            'golongan' => 'nullable|max:20',
            'gelar_depan' => 'nullable|max:50',
            'gelar_belakang' => 'nullable|max:50',
            'pendidikan_terakhir' => 'nullable|in:S1,S2,S3',
            'bidang_keahlian' => 'nullable|max:255',
            'rumpun_ilmu' => 'nullable|max:255',
            'sinta_id' => 'nullable|max:50',
            'scopus_id' => 'nullable|max:50',
            'google_scholar_id' => 'nullable|max:50',
            'orcid' => 'nullable|max:50',
            'no_sertifikasi_dosen' => 'nullable|max:50',
            'tahun_sertifikasi' => 'nullable|integer|min:2000|max:' . date('Y'),
            'no_registrasi_dikti' => 'nullable|max:50',
            'provinsi_id' => 'nullable|exists:provinsi,id',
            'kabupaten_id' => 'nullable|exists:kabupaten,id',
            'kecamatan_id' => 'nullable|exists:kecamatan,id',
            'kelurahan_id' => 'nullable|exists:kelurahan,id',
            'rt' => 'nullable|max:5',
            'rw' => 'nullable|max:5',
            'kode_pos' => 'nullable|max:10',
            'no_hp' => 'nullable|max:20',
            'no_npwp' => 'nullable|max:30',
            'no_rekening' => 'nullable|max:50',
            'nama_bank' => 'nullable|max:50',
            'atas_nama_rekening' => 'nullable|max:100',
            'no_bpjs_kesehatan' => 'nullable|max:30',
            'no_bpjs_ketenagakerjaan' => 'nullable|max:30',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'nullable|in:Aktif,Cuti,Nonaktif',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->nidn),
                'role' => 'dosen',
            ]);
            
            // Auto-fill jabatan_struktural from nama_jabatan_id
            $jabatanStruktural = $request->jabatan_struktural;
            if ($request->filled('nama_jabatan_id') && empty($jabatanStruktural)) {
                $namaJabatan = NamaJabatan::find($request->nama_jabatan_id);
                $jabatanStruktural = $namaJabatan?->nama;
            }

            $data = [
                'user_id' => $user->id,
                'program_studi_id' => $request->program_studi_id,
                'unit_kerja_id' => $request->unit_kerja_id,
                'nama_jabatan_id' => $request->nama_jabatan_id,
                'jabatan_struktural' => $jabatanStruktural,
                'nidn' => $request->nidn,
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'email' => $request->email,
                'jabatan_fungsional' => $request->jabatan_fungsional,
                'golongan' => $request->golongan,
                'status' => $request->status ?? 'Aktif',
                // Data Akademik
                'gelar_depan' => $request->gelar_depan,
                'gelar_belakang' => $request->gelar_belakang,
                'pendidikan_terakhir' => $request->pendidikan_terakhir,
                'bidang_keahlian' => $request->bidang_keahlian,
                'rumpun_ilmu' => $request->rumpun_ilmu,
                // Data Publikasi
                'sinta_id' => $request->sinta_id,
                'scopus_id' => $request->scopus_id,
                'google_scholar_id' => $request->google_scholar_id,
                'orcid' => $request->orcid,
                // Data Sertifikasi
                'no_sertifikasi_dosen' => $request->no_sertifikasi_dosen,
                'tahun_sertifikasi' => $request->tahun_sertifikasi,
                'no_registrasi_dikti' => $request->no_registrasi_dikti,
                // Alamat Lengkap
                'provinsi_id' => $request->provinsi_id,
                'kabupaten_id' => $request->kabupaten_id,
                'kecamatan_id' => $request->kecamatan_id,
                'kelurahan_id' => $request->kelurahan_id,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'kode_pos' => $request->kode_pos,
                // Data Finansial
                'no_hp' => $request->no_hp,
                'no_npwp' => $request->no_npwp,
                'no_rekening' => $request->no_rekening,
                'nama_bank' => $request->nama_bank,
                'atas_nama_rekening' => $request->atas_nama_rekening,
                'no_bpjs_kesehatan' => $request->no_bpjs_kesehatan,
                'no_bpjs_ketenagakerjaan' => $request->no_bpjs_ketenagakerjaan,
            ];

            // Handle foto upload
            if ($request->hasFile('foto')) {
                $data['foto'] = $request->file('foto')->store('foto-dosen', 'public');
            }

            Dosen::create($data);

            DB::commit();
            return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Dosen $dosen)
    {
        $dosen->load(['programStudi.fakultas', 'mahasiswaWali', 'jadwalKuliah.mataKuliah', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan']);
        return view('dosen.show', compact('dosen'));
    }

    public function edit(Dosen $dosen)
    {
        $programStudi = ProgramStudi::with('fakultas')->get();
        $unitKerja = UnitKerja::where('is_active', true)->orderBy('nama')->get();
        $namaJabatans = NamaJabatan::aktif()->orderBy('level')->orderBy('urutan')->get();
        $provinsi = Provinsi::orderBy('nama')->get();
        
        // Load wilayah data for edit form
        $kabupaten = $dosen->provinsi_id ? Kabupaten::where('provinsi_id', $dosen->provinsi_id)->orderBy('nama')->get() : collect();
        $kecamatan = $dosen->kabupaten_id ? Kecamatan::where('kabupaten_id', $dosen->kabupaten_id)->orderBy('nama')->get() : collect();
        $kelurahan = $dosen->kecamatan_id ? Kelurahan::where('kecamatan_id', $dosen->kecamatan_id)->orderBy('nama')->get() : collect();
        
        return view('dosen.edit', compact('dosen', 'programStudi', 'unitKerja', 'namaJabatans', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan'));
    }

    public function update(Request $request, Dosen $dosen)
    {
        $request->validate([
            'nidn' => 'required|max:20|unique:dosen,nidn,' . $dosen->id,
            'nama' => 'required|max:255',
            'email' => 'required|email|unique:dosen,email,' . $dosen->id,
            'program_studi_id' => 'required|exists:program_studi,id',
            'unit_kerja_id' => 'nullable|exists:unit_kerja,id',
            'nama_jabatan_id' => 'nullable|exists:nama_jabatan,id',
            'jabatan_struktural' => 'nullable|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'status' => 'required|in:Aktif,Cuti,Nonaktif',
            'gelar_depan' => 'nullable|max:50',
            'gelar_belakang' => 'nullable|max:50',
            'pendidikan_terakhir' => 'nullable|in:S1,S2,S3',
            'bidang_keahlian' => 'nullable|max:255',
            'rumpun_ilmu' => 'nullable|max:255',
            'sinta_id' => 'nullable|max:50',
            'scopus_id' => 'nullable|max:50',
            'google_scholar_id' => 'nullable|max:50',
            'orcid' => 'nullable|max:50',
            'no_sertifikasi_dosen' => 'nullable|max:50',
            'tahun_sertifikasi' => 'nullable|integer|min:2000|max:' . date('Y'),
            'no_registrasi_dikti' => 'nullable|max:50',
            'provinsi_id' => 'nullable|exists:provinsi,id',
            'kabupaten_id' => 'nullable|exists:kabupaten,id',
            'kecamatan_id' => 'nullable|exists:kecamatan,id',
            'kelurahan_id' => 'nullable|exists:kelurahan,id',
            'rt' => 'nullable|max:5',
            'rw' => 'nullable|max:5',
            'kode_pos' => 'nullable|max:10',
            'no_hp' => 'nullable|max:20',
            'no_npwp' => 'nullable|max:30',
            'no_rekening' => 'nullable|max:50',
            'nama_bank' => 'nullable|max:50',
            'atas_nama_rekening' => 'nullable|max:100',
            'no_bpjs_kesehatan' => 'nullable|max:30',
            'no_bpjs_ketenagakerjaan' => 'nullable|max:30',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['_token', '_method', 'foto']);
        
        // Auto-fill jabatan_struktural from nama_jabatan_id
        if ($request->filled('nama_jabatan_id') && empty($request->jabatan_struktural)) {
            $namaJabatan = NamaJabatan::find($request->nama_jabatan_id);
            $data['jabatan_struktural'] = $namaJabatan?->nama;
        }

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Delete old foto if exists
            if ($dosen->foto) {
                Storage::disk('public')->delete($dosen->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto-dosen', 'public');
        }

        $dosen->update($data);
        $dosen->user->update(['email' => $request->email, 'name' => $request->nama]);

        return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil diperbarui!');
    }

    public function destroy(Dosen $dosen)
    {
        DB::beginTransaction();
        try {
            $user = $dosen->user;
            $dosen->delete();
            $user->delete();
            
            DB::commit();
            return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Search dosen for Select2 AJAX
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');
        
        $dosen = Dosen::where('status', 'Aktif')
            ->where(function($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                      ->orWhere('nidn', 'like', "%{$search}%");
            })
            ->orderBy('nama')
            ->limit(20)
            ->get(['id', 'nama', 'nidn']);
        
        $results = $dosen->map(function($d) {
            return [
                'id' => $d->nama,
                'text' => $d->nama . ' (' . $d->nidn . ')'
            ];
        });
        
        return response()->json(['results' => $results]);
    }
}
