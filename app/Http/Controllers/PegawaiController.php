<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\Provinsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pegawai::with(['unitKerja']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('unit_kerja')) {
            $query->where('unit_kerja_id', $request->unit_kerja);
        }

        if ($request->filled('jenis_pegawai')) {
            $query->where('jenis_pegawai', $request->jenis_pegawai);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pegawai = $query->orderBy('nama')->paginate(15);
        $unitKerja = UnitKerja::where('is_active', true)->orderBy('nama')->get();

        return view('kepegawaian.pegawai.index', compact('pegawai', 'unitKerja'));
    }

    public function create()
    {
        $unitKerja = UnitKerja::where('is_active', true)->orderBy('nama')->get();
        $provinsi = Provinsi::orderBy('nama')->get();
        
        return view('kepegawaian.pegawai.create', compact('unitKerja', 'provinsi'));
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
            'jabatan' => 'nullable|max:100',
            'jenis_pegawai' => 'required|in:PNS,PPPK,Honorer,Kontrak,Tetap Yayasan',
            'status' => 'required|in:Aktif,Cuti,Non-Aktif,Pensiun',
            'tmt_pegawai' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['foto']);

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
        $provinsi = Provinsi::orderBy('nama')->get();
        
        return view('kepegawaian.pegawai.edit', compact('pegawai', 'unitKerja', 'provinsi'));
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
            'jabatan' => 'nullable|max:100',
            'jenis_pegawai' => 'required|in:PNS,PPPK,Honorer,Kontrak,Tetap Yayasan',
            'status' => 'required|in:Aktif,Cuti,Non-Aktif,Pensiun',
            'tmt_pegawai' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['foto', '_token', '_method']);

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
