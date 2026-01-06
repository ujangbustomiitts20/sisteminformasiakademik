<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Pegawai;
use App\Models\ProgramStudi;
use App\Models\UnitKerja;
use Illuminate\Http\Request;

class DataPegawaiController extends Controller
{
    /**
     * Display a listing of all pegawai (Dosen + Tenaga Kependidikan).
     */
    public function index(Request $request)
    {
        // Get filter options
        $prodiList = ProgramStudi::orderBy('nama')->get();
        $unitKerjaList = UnitKerja::orderBy('nama')->get();
        
        // Query Dosen
        $dosenQuery = Dosen::with(['programStudi', 'user']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $dosenQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nidn', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('prodi')) {
            $dosenQuery->where('program_studi_id', $request->prodi);
        }
        
        if ($request->filled('status')) {
            $dosenQuery->where('status', $request->status);
        }
        
        $dosens = $dosenQuery->orderBy('nama')->get();
        
        // Query Pegawai (Tendik)
        $pegawaiQuery = Pegawai::with(['unitKerja', 'user']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $pegawaiQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('unit_kerja')) {
            $pegawaiQuery->where('unit_kerja_id', $request->unit_kerja);
        }
        
        if ($request->filled('status')) {
            $pegawaiQuery->where('status', $request->status);
        }
        
        $pegawais = $pegawaiQuery->orderBy('nama')->get();
        
        // Combine data based on tab filter
        $tab = $request->get('tab', 'semua');
        
        // Stats
        $stats = [
            'total_dosen' => Dosen::count(),
            'total_tendik' => Pegawai::count(),
            'dosen_aktif' => Dosen::where('status', 'Aktif')->count(),
            'tendik_aktif' => Pegawai::where('status', 'aktif')->count(),
            'total' => Dosen::count() + Pegawai::count(),
        ];
        
        return view('kepegawaian.data-pegawai.index', compact(
            'dosens', 
            'pegawais', 
            'prodiList', 
            'unitKerjaList', 
            'stats',
            'tab'
        ));
    }
}
