<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $provinsi = Provinsi::orderBy('nama')->get(['id', 'kode', 'nama']);
        return response()->json($provinsi);
    }

    /**
     * Get kabupaten by provinsi_id
     */
    public function kabupaten(Request $request)
    {
        $provinsiId = $request->get('provinsi_id');
        
        $kabupaten = Kabupaten::when($provinsiId, function ($query) use ($provinsiId) {
                return $query->where('provinsi_id', $provinsiId);
            })
            ->orderBy('nama')
            ->get(['id', 'provinsi_id', 'kode', 'nama']);
            
        return response()->json($kabupaten);
    }

    /**
     * Get kecamatan by kabupaten_id
     */
    public function kecamatan(Request $request)
    {
        $kabupatenId = $request->get('kabupaten_id');
        
        $kecamatan = Kecamatan::when($kabupatenId, function ($query) use ($kabupatenId) {
                return $query->where('kabupaten_id', $kabupatenId);
            })
            ->orderBy('nama')
            ->get(['id', 'kabupaten_id', 'kode', 'nama']);
            
        return response()->json($kecamatan);
    }

    /**
     * Get kelurahan by kecamatan_id
     */
    public function kelurahan(Request $request)
    {
        $kecamatanId = $request->get('kecamatan_id');
        
        $kelurahan = Kelurahan::when($kecamatanId, function ($query) use ($kecamatanId) {
                return $query->where('kecamatan_id', $kecamatanId);
            })
            ->orderBy('nama')
            ->get(['id', 'kecamatan_id', 'kode', 'nama', 'kode_pos']);
            
        return response()->json($kelurahan);
    }

    /**
     * Get sekolah dengan filter
     */
    public function sekolah(Request $request)
    {
        $query = Sekolah::active();
        
        if ($request->filled('provinsi_id')) {
            $query->where('provinsi_id', $request->provinsi_id);
        }
        
        if ($request->filled('kabupaten_id')) {
            $query->where('kabupaten_id', $request->kabupaten_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('npsn', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }
        
        $sekolah = $query->with('kabupaten:id,nama')
            ->orderBy('nama')
            ->limit(50)
            ->get(['id', 'npsn', 'nama', 'jenjang', 'status', 'kabupaten_id']);
            
        return response()->json($sekolah);
    }

    /**
     * Get single sekolah detail
     */
    public function sekolahDetail($id)
    {
        $sekolah = Sekolah::with(['provinsi:id,nama', 'kabupaten:id,nama', 'kecamatan:id,nama'])
            ->find($id);
            
        if (!$sekolah) {
            return response()->json(['message' => 'Sekolah tidak ditemukan'], 404);
        }
        
        return response()->json($sekolah);
    }
}
