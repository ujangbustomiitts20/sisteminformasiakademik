<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\NamaJabatan;
use App\Models\Provinsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $pegawai = Pegawai::with(['unitKerja', 'namaJabatan'])->orderBy('nama')->get();

        return view('kepegawaian.pegawai.index', compact('pegawai'));
    }

    public function create()
    {
        $unitKerja = UnitKerja::where('is_active', true)->orderBy('nama')->get();
        $namaJabatans = NamaJabatan::aktif()->orderBy('level')->orderBy('urutan')->get();
        $provinsi = Provinsi::orderBy('nama')->get();
        
        return view('kepegawaian.pegawai.create', compact('unitKerja', 'namaJabatans', 'provinsi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'nullable|unique:pegawai,nip|max:30',
            'nik' => 'nullable|unique:pegawai,nik|max:20',
            'nama' => 'required|max:255',
            'tempat_lahir' => 'nullable|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'agama' => 'nullable|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'status_pernikahan' => 'nullable|in:Belum Menikah,Menikah,Cerai Hidup,Cerai Mati',
            'email' => 'nullable|email|max:255',
            'no_hp' => 'nullable|max:20',
            'unit_kerja_id' => 'nullable|exists:unit_kerja,id',
            'nama_jabatan_id' => 'nullable|exists:nama_jabatan,id',
            'jenis_pegawai' => 'required|in:PNS,PPPK,Honorer,Kontrak,Tetap Yayasan',
            'status' => 'required|in:Aktif,Cuti,Non-Aktif,Pensiun',
            'tmt_pegawai' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['foto']);
        
        // Auto-fill jabatan from nama_jabatan_id
        if ($request->filled('nama_jabatan_id')) {
            $namaJabatan = NamaJabatan::find($request->nama_jabatan_id);
            $data['jabatan'] = $namaJabatan?->nama;
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('pegawai/foto', 'public');
        }

        Pegawai::create($data);

        return redirect()->route('kepegawaian.pegawai.index')
            ->with('success', 'Data pegawai berhasil ditambahkan!');
    }

    public function show(Pegawai $pegawai)
    {
        $pegawai->load(['unitKerja', 'riwayatPendidikan', 'riwayatJabatan', 'riwayatPangkat', 'riwayatPelatihan', 'dokumenKepegawaian']);
        
        return view('kepegawaian.pegawai.show', compact('pegawai'));
    }

    public function edit(Pegawai $pegawai)
    {
        $unitKerja = UnitKerja::where('is_active', true)->orderBy('nama')->get();
        $namaJabatans = NamaJabatan::aktif()->orderBy('level')->orderBy('urutan')->get();
        $provinsi = Provinsi::orderBy('nama')->get();
        
        return view('kepegawaian.pegawai.edit', compact('pegawai', 'unitKerja', 'namaJabatans', 'provinsi'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $request->validate([
            'nip' => 'nullable|max:30|unique:pegawai,nip,' . $pegawai->id,
            'nik' => 'nullable|max:20|unique:pegawai,nik,' . $pegawai->id,
            'nama' => 'required|max:255',
            'tempat_lahir' => 'nullable|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'agama' => 'nullable|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'status_pernikahan' => 'nullable|in:Belum Menikah,Menikah,Cerai Hidup,Cerai Mati',
            'email' => 'nullable|email|max:255',
            'no_hp' => 'nullable|max:20',
            'unit_kerja_id' => 'nullable|exists:unit_kerja,id',
            'nama_jabatan_id' => 'nullable|exists:nama_jabatan,id',
            'jenis_pegawai' => 'required|in:PNS,PPPK,Honorer,Kontrak,Tetap Yayasan',
            'status' => 'required|in:Aktif,Cuti,Non-Aktif,Pensiun',
            'tmt_pegawai' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['foto', '_token', '_method']);
        
        // Auto-fill jabatan from nama_jabatan_id
        if ($request->filled('nama_jabatan_id')) {
            $namaJabatan = NamaJabatan::find($request->nama_jabatan_id);
            $data['jabatan'] = $namaJabatan?->nama;
        } else {
            $data['jabatan'] = null;
        }

        if ($request->hasFile('foto')) {
            if ($pegawai->foto) {
                Storage::disk('public')->delete($pegawai->foto);
            }
            $data['foto'] = $request->file('foto')->store('pegawai/foto', 'public');
        }

        $pegawai->update($data);

        return redirect()->route('kepegawaian.pegawai.index')
            ->with('success', 'Data pegawai berhasil diperbarui!');
    }

    public function destroy(Pegawai $pegawai)
    {
        if ($pegawai->foto) {
            Storage::disk('public')->delete($pegawai->foto);
        }

        $pegawai->delete();

        return redirect()->route('kepegawaian.pegawai.index')
            ->with('success', 'Data pegawai berhasil dihapus!');
    }
}
