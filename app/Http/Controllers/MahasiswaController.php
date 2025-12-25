<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\ProgramStudi;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mahasiswa::with(['programStudi.fakultas', 'dosenWali']);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nim', 'like', "%{$request->search}%")
                    ->orWhere('nama', 'like', "%{$request->search}%");
            });
        }

        if ($request->program_studi_id) {
            $query->where('program_studi_id', $request->program_studi_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->angkatan) {
            $query->where('angkatan', $request->angkatan);
        }

        $mahasiswa = $query->orderBy('nim')->paginate(15);
        $programStudi = ProgramStudi::all();
        $angkatanList = Mahasiswa::distinct()->pluck('angkatan')->sort()->reverse();

        return view('mahasiswa.index', compact('mahasiswa', 'programStudi', 'angkatanList'));
    }

    public function create()
    {
        $programStudi = ProgramStudi::with('fakultas')->get();
        $dosen = Dosen::where('status', 'Aktif')->get();
        return view('mahasiswa.create', compact('programStudi', 'dosen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required|unique:mahasiswa,nim|max:20',
            'nama' => 'required|max:255',
            'email' => 'required|email|unique:mahasiswa,email|unique:users,email',
            'program_studi_id' => 'required|exists:program_studi,id',
            'jenis_kelamin' => 'required|in:L,P',
            'angkatan' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'dosen_wali_id' => 'nullable|exists:dosen,id',
            'tempat_lahir' => 'nullable|max:100',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable',
            'telepon' => 'nullable|max:20',
        ]);

        DB::beginTransaction();
        try {
            // Buat user account
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->nim), // Default password = NIM
                'role' => 'mahasiswa',
            ]);

            // Buat data mahasiswa
            Mahasiswa::create([
                'user_id' => $user->id,
                'program_studi_id' => $request->program_studi_id,
                'dosen_wali_id' => $request->dosen_wali_id,
                'nim' => $request->nim,
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'email' => $request->email,
                'angkatan' => $request->angkatan,
            ]);

            DB::commit();
            return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load(['programStudi.fakultas', 'dosenWali', 'krs.jadwalKuliah.mataKuliah', 'krs.nilai', 'pembayaran']);
        $ipk = $mahasiswa->hitungIPK();
        $totalSks = $mahasiswa->totalSksLulus();
        
        return view('mahasiswa.show', compact('mahasiswa', 'ipk', 'totalSks'));
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        $programStudi = ProgramStudi::with('fakultas')->get();
        $dosen = Dosen::where('status', 'Aktif')->get();
        return view('mahasiswa.edit', compact('mahasiswa', 'programStudi', 'dosen'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $request->validate([
            'nim' => 'required|max:20|unique:mahasiswa,nim,' . $mahasiswa->id,
            'nama' => 'required|max:255',
            'email' => 'required|email|unique:mahasiswa,email,' . $mahasiswa->id,
            'program_studi_id' => 'required|exists:program_studi,id',
            'jenis_kelamin' => 'required|in:L,P',
            'angkatan' => 'required|integer',
            'status' => 'required|in:Aktif,Cuti,Lulus,DO',
            'semester_aktif' => 'required|integer|min:1',
            'dosen_wali_id' => 'nullable|exists:dosen,id',
        ]);

        $mahasiswa->update($request->all());
        
        // Update user email juga
        $mahasiswa->user->update(['email' => $request->email, 'name' => $request->nama]);

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui!');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        DB::beginTransaction();
        try {
            $user = $mahasiswa->user;
            $mahasiswa->delete();
            $user->delete();
            
            DB::commit();
            return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
