<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\User;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        $query = Dosen::with(['programStudi.fakultas']);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nidn', 'like', "%{$request->search}%")
                    ->orWhere('nama', 'like', "%{$request->search}%");
            });
        }

        if ($request->program_studi_id) {
            $query->where('program_studi_id', $request->program_studi_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $dosen = $query->orderBy('nama')->paginate(15);
        $programStudi = ProgramStudi::all();

        return view('dosen.index', compact('dosen', 'programStudi'));
    }

    public function create()
    {
        $programStudi = ProgramStudi::with('fakultas')->get();
        return view('dosen.create', compact('programStudi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nidn' => 'required|unique:dosen,nidn|max:20',
            'nama' => 'required|max:255',
            'email' => 'required|email|unique:dosen,email|unique:users,email',
            'program_studi_id' => 'required|exists:program_studi,id',
            'jenis_kelamin' => 'required|in:L,P',
            'jabatan_fungsional' => 'nullable|max:100',
            'golongan' => 'nullable|max:20',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->nidn),
                'role' => 'dosen',
            ]);

            Dosen::create([
                'user_id' => $user->id,
                'program_studi_id' => $request->program_studi_id,
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
            ]);

            DB::commit();
            return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Dosen $dosen)
    {
        $dosen->load(['programStudi.fakultas', 'mahasiswaWali', 'jadwalKuliah.mataKuliah']);
        return view('dosen.show', compact('dosen'));
    }

    public function edit(Dosen $dosen)
    {
        $programStudi = ProgramStudi::with('fakultas')->get();
        return view('dosen.edit', compact('dosen', 'programStudi'));
    }

    public function update(Request $request, Dosen $dosen)
    {
        $request->validate([
            'nidn' => 'required|max:20|unique:dosen,nidn,' . $dosen->id,
            'nama' => 'required|max:255',
            'email' => 'required|email|unique:dosen,email,' . $dosen->id,
            'program_studi_id' => 'required|exists:program_studi,id',
            'jenis_kelamin' => 'required|in:L,P',
            'status' => 'required|in:Aktif,Cuti,Nonaktif',
        ]);

        $dosen->update($request->all());
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
}
