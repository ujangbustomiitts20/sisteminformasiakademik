<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PejabatAkademikController extends Controller
{
    /**
     * Display list of Kaprodi and Dekan
     */
    public function index()
    {
        // Get all kaprodi
        $kaprodis = User::where('role', 'kaprodi')
            ->with(['dosen.programStudi.fakultas'])
            ->get();

        // Get all dekan
        $dekans = User::where('role', 'dekan')
            ->with(['dosen.programStudi.fakultas'])
            ->get();

        // Get program studi without kaprodi
        $prodiTanpaKaprodi = ProgramStudi::whereDoesntHave('dosen', function($q) {
            $q->whereHas('user', fn($u) => $u->where('role', 'kaprodi'));
        })->with('fakultas')->get();

        // Get fakultas without dekan
        $fakultasTanpaDekan = Fakultas::whereDoesntHave('programStudi.dosen', function($q) {
            $q->whereHas('user', fn($u) => $u->where('role', 'dekan'));
        })->get();

        // Get fakultas list with info apakah sudah ada dekan
        $fakultasList = Fakultas::with(['programStudi'])->get()->map(function($fak) use ($dekans) {
            $fak->has_dekan = $dekans->contains(function($dekan) use ($fak) {
                return $dekan->dosen && $dekan->dosen->programStudi && 
                       $dekan->dosen->programStudi->fakultas_id == $fak->id;
            });
            return $fak;
        });

        // Get prodi list with info apakah sudah ada kaprodi
        $prodiList = ProgramStudi::with('fakultas')->get()->map(function($prodi) use ($kaprodis) {
            $prodi->has_kaprodi = $kaprodis->contains(function($kaprodi) use ($prodi) {
                return $kaprodi->dosen && $kaprodi->dosen->program_studi_id == $prodi->id;
            });
            return $prodi;
        });

        // Get dosen yang bisa dijadikan kaprodi/dekan (belum punya role khusus)
        $dosenAvailable = Dosen::whereHas('user', function($q) {
            $q->where('role', 'dosen');
        })->with(['user', 'programStudi.fakultas'])->get();

        return view('admin.pejabat-akademik.index', compact(
            'kaprodis', 
            'dekans', 
            'prodiTanpaKaprodi',
            'fakultasTanpaDekan',
            'fakultasList',
            'prodiList',
            'dosenAvailable'
        ));
    }

    /**
     * Form to assign Kaprodi
     */
    public function createKaprodi()
    {
        $programStudis = ProgramStudi::with('fakultas')->orderBy('nama')->get();
        $dosens = Dosen::whereHas('user', function($q) {
            $q->whereIn('role', ['dosen']);
        })->with(['user', 'programStudi'])->orderBy('nama')->get();

        return view('admin.pejabat-akademik.create-kaprodi', compact('programStudis', 'dosens'));
    }

    /**
     * Store new Kaprodi assignment
     */
    public function storeKaprodi(Request $request)
    {
        $request->validate([
            'dosen_id' => 'required|exists:dosen,id',
            'program_studi_id' => 'required|exists:program_studi,id',
        ]);

        // Check if prodi already has kaprodi
        $existingKaprodi = Dosen::where('program_studi_id', $request->program_studi_id)
            ->whereHas('user', fn($q) => $q->where('role', 'kaprodi'))
            ->first();

        if ($existingKaprodi) {
            $prodi = ProgramStudi::find($request->program_studi_id);
            return back()->with('error', 'Program Studi ' . $prodi->nama . ' sudah memiliki Kaprodi (' . $existingKaprodi->nama . ')!')->withInput();
        }

        DB::beginTransaction();
        try {
            $dosen = Dosen::findOrFail($request->dosen_id);
            
            // Update dosen's program studi if different
            if ($dosen->program_studi_id != $request->program_studi_id) {
                $dosen->program_studi_id = $request->program_studi_id;
                $dosen->save();
            }

            // Update user role to kaprodi
            $dosen->user->update(['role' => 'kaprodi']);

            // Update program studi kaprodi field
            $prodi = ProgramStudi::find($request->program_studi_id);
            $prodi->update(['kaprodi' => $dosen->nama]);

            DB::commit();
            return redirect()->route('admin.pejabat-akademik.index')
                ->with('success', 'Kaprodi berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan kaprodi: ' . $e->getMessage());
        }
    }

    /**
     * Form to assign Dekan
     */
    public function createDekan()
    {
        $fakultas = Fakultas::orderBy('nama')->get();
        $dosens = Dosen::whereHas('user', function($q) {
            $q->whereIn('role', ['dosen']);
        })->with(['user', 'programStudi.fakultas'])->orderBy('nama')->get();

        return view('admin.pejabat-akademik.create-dekan', compact('fakultas', 'dosens'));
    }

    /**
     * Store new Dekan assignment
     */
    public function storeDekan(Request $request)
    {
        $request->validate([
            'dosen_id' => 'required|exists:dosen,id',
            'fakultas_id' => 'required|exists:fakultas,id',
        ]);

        // Check if fakultas already has dekan
        $prodiIds = ProgramStudi::where('fakultas_id', $request->fakultas_id)->pluck('id');
        $existingDekan = Dosen::whereIn('program_studi_id', $prodiIds)
            ->whereHas('user', fn($q) => $q->where('role', 'dekan'))
            ->first();

        if ($existingDekan) {
            $fakultas = Fakultas::find($request->fakultas_id);
            return back()->with('error', 'Fakultas ' . $fakultas->nama . ' sudah memiliki Dekan (' . $existingDekan->nama . ')!')->withInput();
        }

        DB::beginTransaction();
        try {
            $dosen = Dosen::findOrFail($request->dosen_id);
            $fakultas = Fakultas::findOrFail($request->fakultas_id);

            // Ensure dosen's prodi is within the fakultas
            $prodiInFakultas = ProgramStudi::where('fakultas_id', $fakultas->id)->first();
            if ($prodiInFakultas && $dosen->program_studi_id != $prodiInFakultas->id) {
                $dosen->program_studi_id = $prodiInFakultas->id;
                $dosen->save();
            }

            // Update user role to dekan
            $dosen->user->update(['role' => 'dekan']);

            // Update fakultas dekan field
            $fakultas->update(['dekan' => $dosen->nama]);

            DB::commit();
            return redirect()->route('admin.pejabat-akademik.index')
                ->with('success', 'Dekan berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan dekan: ' . $e->getMessage());
        }
    }

    /**
     * Remove Kaprodi assignment
     */
    public function removeKaprodi(User $user)
    {
        if ($user->role !== 'kaprodi') {
            return back()->with('error', 'User bukan kaprodi!');
        }

        DB::beginTransaction();
        try {
            // Get program studi
            if ($user->dosen) {
                $prodi = $user->dosen->programStudi;
                if ($prodi) {
                    $prodi->update(['kaprodi' => null]);
                }
            }

            // Revert role to dosen
            $user->update(['role' => 'dosen']);

            DB::commit();
            return back()->with('success', 'Kaprodi berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus kaprodi: ' . $e->getMessage());
        }
    }

    /**
     * Remove Dekan assignment
     */
    public function removeDekan(User $user)
    {
        if ($user->role !== 'dekan') {
            return back()->with('error', 'User bukan dekan!');
        }

        DB::beginTransaction();
        try {
            // Get fakultas
            if ($user->dosen && $user->dosen->programStudi) {
                $fakultas = $user->dosen->programStudi->fakultas;
                if ($fakultas) {
                    $fakultas->update(['dekan' => null]);
                }
            }

            // Revert role to dosen
            $user->update(['role' => 'dosen']);

            DB::commit();
            return back()->with('success', 'Dekan berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus dekan: ' . $e->getMessage());
        }
    }

    /**
     * Quick create/assign dosen as kaprodi/dekan
     */
    public function quickCreate(Request $request)
    {
        $request->validate([
            'dosen_id' => 'required|exists:dosen,id',
            'role' => 'required|in:kaprodi,dekan',
            'program_studi_id' => 'required_if:role,kaprodi|nullable|exists:program_studi,id',
            'fakultas_id' => 'required_if:role,dekan|nullable|exists:fakultas,id',
        ]);

        // Check duplicate for kaprodi
        if ($request->role == 'kaprodi' && $request->program_studi_id) {
            $existingKaprodi = Dosen::where('program_studi_id', $request->program_studi_id)
                ->whereHas('user', fn($q) => $q->where('role', 'kaprodi'))
                ->first();

            if ($existingKaprodi) {
                $prodi = ProgramStudi::find($request->program_studi_id);
                return back()->with('error', 'Program Studi ' . $prodi->nama . ' sudah memiliki Kaprodi (' . $existingKaprodi->nama . ')! Hapus kaprodi lama terlebih dahulu.')->withInput();
            }
        }

        // Check duplicate for dekan
        if ($request->role == 'dekan' && $request->fakultas_id) {
            $prodiIds = ProgramStudi::where('fakultas_id', $request->fakultas_id)->pluck('id');
            $existingDekan = Dosen::whereIn('program_studi_id', $prodiIds)
                ->whereHas('user', fn($q) => $q->where('role', 'dekan'))
                ->first();

            if ($existingDekan) {
                $fakultas = Fakultas::find($request->fakultas_id);
                return back()->with('error', 'Fakultas ' . $fakultas->nama . ' sudah memiliki Dekan (' . $existingDekan->nama . ')! Hapus dekan lama terlebih dahulu.')->withInput();
            }
        }

        DB::beginTransaction();
        try {
            // Get existing dosen
            $dosen = Dosen::with('user')->findOrFail($request->dosen_id);
            
            if (!$dosen->user) {
                throw new \Exception('Dosen tidak memiliki user account!');
            }

            // Update user role
            $dosen->user->update(['role' => $request->role]);

            // Update prodi/fakultas based on role
            if ($request->role == 'kaprodi') {
                // Update dosen's program studi if different
                if ($request->program_studi_id && $dosen->program_studi_id != $request->program_studi_id) {
                    $dosen->update(['program_studi_id' => $request->program_studi_id]);
                }
                
                // Update prodi kaprodi field
                $programStudiId = $request->program_studi_id ?? $dosen->program_studi_id;
                if ($programStudiId) {
                    ProgramStudi::find($programStudiId)->update(['kaprodi' => $dosen->nama]);
                }
            } else {
                // For dekan
                if ($request->fakultas_id) {
                    Fakultas::find($request->fakultas_id)->update(['dekan' => $dosen->nama]);
                    
                    // If dosen's prodi not in this fakultas, update to first prodi in fakultas
                    $prodiInFakultas = ProgramStudi::where('fakultas_id', $request->fakultas_id)->first();
                    if ($prodiInFakultas && (!$dosen->programStudi || $dosen->programStudi->fakultas_id != $request->fakultas_id)) {
                        $dosen->update(['program_studi_id' => $prodiInFakultas->id]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.pejabat-akademik.index')
                ->with('success', $dosen->nama . ' berhasil diangkat sebagai ' . ucfirst($request->role) . '!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengangkat ' . $request->role . ': ' . $e->getMessage())
                ->withInput();
        }
    }
}
