<?php

namespace App\Http\Controllers;

use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    /**
     * Get all provinsi
     */
    public function provinsi()
    {
        $data = Provinsi::orderBy('nama')->get(['id', 'kode', 'nama']);
        return response()->json($data);
    }

    /**
     * Get kabupaten by provinsi_id
     */
    public function kabupaten(Request $request)
    {
        $query = Kabupaten::orderBy('nama');
        
        if ($request->filled('provinsi_id')) {
            $query->where('provinsi_id', $request->provinsi_id);
        }
        
        $data = $query->get(['id', 'provinsi_id', 'kode', 'nama']);
        return response()->json($data);
    }

    /**
     * Get kecamatan by kabupaten_id
     */
    public function kecamatan(Request $request)
    {
        $query = Kecamatan::orderBy('nama');
        
        if ($request->filled('kabupaten_id')) {
            $query->where('kabupaten_id', $request->kabupaten_id);
        }
        
        $data = $query->get(['id', 'kabupaten_id', 'kode', 'nama']);
        return response()->json($data);
    }

    /**
     * Get kelurahan by kecamatan_id
     */
    public function kelurahan(Request $request)
    {
        $query = Kelurahan::orderBy('nama');
        
        if ($request->filled('kecamatan_id')) {
            $query->where('kecamatan_id', $request->kecamatan_id);
        }
        
        $data = $query->get(['id', 'kecamatan_id', 'kode', 'nama', 'kode_pos']);
        return response()->json($data);
    }

    /**
     * Search sekolah
     */
    public function sekolah(Request $request)
    {
        $query = Sekolah::query();
        
        // Filter by provinsi
        if ($request->filled('provinsi_id')) {
            $query->where('provinsi_id', $request->provinsi_id);
        }
        
        // Filter by kabupaten
        if ($request->filled('kabupaten_id')) {
            $query->where('kabupaten_id', $request->kabupaten_id);
        }
        
        // Filter by jenjang (SMA, SMK, MA)
        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }
        
        // Search by nama or npsn
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('npsn', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('nama')
            ->limit(50)
            ->get(['id', 'npsn', 'nama', 'jenjang', 'status', 'provinsi_id', 'kabupaten_id', 'alamat']);
            
        return response()->json($data);
    }

    /**
     * Get sekolah detail
     */
    public function sekolahDetail($id)
    {
        $sekolah = Sekolah::with(['provinsi', 'kabupaten'])->find($id);
        
        if (!$sekolah) {
            return response()->json(['error' => 'Sekolah tidak ditemukan'], 404);
        }
        
        return response()->json($sekolah);
    }
}
