<?php

namespace App\Http\Controllers;

use App\Models\JadwalKuliah;
use App\Models\TahunAkademik;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class JadwalKuliahController extends Controller
{
    public function index(Request $request)
    {
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->orderBy('semester', 'desc')->get();
        $dosen = Dosen::where('status', 'Aktif')->orderBy('nama')->get();
        
        $tahunAkademikAktif = $request->tahun_akademik 
            ? TahunAkademik::find($request->tahun_akademik) 
            : TahunAkademik::getAktif();

        $query = JadwalKuliah::with(['mataKuliah.programStudi', 'dosen', 'ruangan', 'tahunAkademik']);

        if ($tahunAkademikAktif) {
            $query->where('tahun_akademik_id', $tahunAkademikAktif->id);
        }

        if ($request->hari) {
            $query->where('hari', $request->hari);
        }

        if ($request->dosen) {
            $query->where('dosen_id', $request->dosen);
        }

        $jadwalKuliah = $query->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam_mulai')
            ->paginate(20);

        return view('jadwal-kuliah.index', compact('jadwalKuliah', 'tahunAkademik', 'tahunAkademikAktif', 'dosen'));
    }

    public function create()
    {
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();
        $mataKuliah = MataKuliah::with('programStudi')->orderBy('nama')->get();
        $dosen = Dosen::where('status', 'Aktif')->orderBy('nama')->get();
        $ruangan = Ruangan::orderBy('nama')->get();

        return view('jadwal-kuliah.create', compact('tahunAkademik', 'mataKuliah', 'dosen', 'ruangan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'dosen_id' => 'required|exists:dosen,id',
            'ruangan_id' => 'required|exists:ruangan,id',
            'kelas' => 'required|max:5',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'kuota' => 'required|integer|min:1',
        ]);

        // Check konflik jadwal ruangan
        $konflilRuangan = JadwalKuliah::where('tahun_akademik_id', $request->tahun_akademik_id)
            ->where('ruangan_id', $request->ruangan_id)
            ->where('hari', $request->hari)
            ->where(function($q) use ($request) {
                $q->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai]);
            })
            ->exists();

        if ($konflilRuangan) {
            return back()->with('error', 'Ruangan sudah digunakan pada waktu tersebut!')->withInput();
        }

        // Check konflik jadwal dosen
        $konflikDosen = JadwalKuliah::where('tahun_akademik_id', $request->tahun_akademik_id)
            ->where('dosen_id', $request->dosen_id)
            ->where('hari', $request->hari)
            ->where(function($q) use ($request) {
                $q->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai]);
            })
            ->exists();

        if ($konflikDosen) {
            return back()->with('error', 'Dosen sudah memiliki jadwal pada waktu tersebut!')->withInput();
        }

        JadwalKuliah::create($request->all());

        return redirect()->route('jadwal-kuliah.index')->with('success', 'Jadwal kuliah berhasil ditambahkan!');
    }

    public function show(JadwalKuliah $jadwalKuliah)
    {
        $jadwalKuliah->load(['mataKuliah.programStudi', 'dosen', 'ruangan', 'tahunAkademik', 'krs.mahasiswa']);
        return view('jadwal-kuliah.show', compact('jadwalKuliah'));
    }

    public function edit(JadwalKuliah $jadwalKuliah)
    {
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();
        $mataKuliah = MataKuliah::with('programStudi')->orderBy('nama')->get();
        $dosen = Dosen::where('status', 'Aktif')->orderBy('nama')->get();
        $ruangan = Ruangan::orderBy('nama')->get();

        return view('jadwal-kuliah.edit', compact('jadwalKuliah', 'tahunAkademik', 'mataKuliah', 'dosen', 'ruangan'));
    }

    public function update(Request $request, JadwalKuliah $jadwalKuliah)
    {
        $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'dosen_id' => 'required|exists:dosen,id',
            'ruangan_id' => 'required|exists:ruangan,id',
            'kelas' => 'required|max:5',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'kuota' => 'required|integer|min:1',
        ]);

        $jadwalKuliah->update($request->all());

        return redirect()->route('jadwal-kuliah.index')->with('success', 'Jadwal kuliah berhasil diperbarui!');
    }

    public function destroy(JadwalKuliah $jadwalKuliah)
    {
        try {
            $jadwalKuliah->delete();
            return redirect()->route('jadwal-kuliah.index')->with('success', 'Jadwal kuliah berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Tidak dapat menghapus jadwal yang sudah memiliki KRS!');
        }
    }
}
