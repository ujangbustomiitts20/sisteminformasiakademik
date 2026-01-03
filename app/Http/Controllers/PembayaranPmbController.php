<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPmb;
use App\Models\CalonMahasiswa;
use App\Models\BiayaPendaftaran;
use App\Models\DaftarUlang;
use App\Models\GelombangPmb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembayaranPmbController extends Controller
{
    public function index(Request $request)
    {
        $gelombangs = GelombangPmb::with('periodePmb')->orderBy('id', 'desc')->get();

        $query = PembayaranPmb::with(['calonMahasiswa.gelombangPmb', 'calonMahasiswa.programStudi']);

        if ($request->filled('gelombang')) {
            $query->whereHas('calonMahasiswa', function($q) use ($request) {
                $q->where('gelombang_pmb_id', $request->gelombang);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_pembayaran', $request->jenis);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_pembayaran', 'like', "%{$search}%")
                  ->orWhereHas('calonMahasiswa', function($q2) use ($search) {
                      $q2->where('nama_lengkap', 'like', "%{$search}%")
                         ->orWhere('no_pendaftaran', 'like', "%{$search}%");
                  });
            });
        }

        $pembayarans = $query->orderBy('created_at', 'desc')->paginate(15);

        // Summary
        $totalTagihan = PembayaranPmb::count();
        $menungguVerifikasi = PembayaranPmb::where('status', 'menunggu_verifikasi')->count();
        $terverifikasi = PembayaranPmb::where('status', 'terverifikasi')->count();
        $totalPendapatan = PembayaranPmb::where('status', 'terverifikasi')->sum('jumlah');

        return view('pmb.pembayaran.index', compact(
            'pembayarans', 
            'gelombangs', 
            'totalTagihan', 
            'menungguVerifikasi', 
            'terverifikasi', 
            'totalPendapatan'
        ));
    }

    public function show(string $hashid)
    {
        $pembayaran = PembayaranPmb::findByHashidOrFail($hashid);
        $pembayaran->load(['calonMahasiswa.gelombangPmb', 'calonMahasiswa.jalurSeleksi', 'calonMahasiswa.programStudi', 'verifiedBy']);

        // Ambil riwayat pembayaran calon mahasiswa ini
        $riwayatPembayaran = PembayaranPmb::where('calon_mahasiswa_id', $pembayaran->calon_mahasiswa_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pmb.pembayaran.show', compact('pembayaran', 'riwayatPembayaran'));
    }

    public function verifikasi(Request $request, string $hashid)
    {
        $pembayaran = PembayaranPmb::findByHashidOrFail($hashid);

        $validated = $request->validate([
            'status' => 'nullable|in:terverifikasi,ditolak',
            'catatan' => 'nullable|string',
        ]);

        $status = $validated['status'] ?? 'terverifikasi';

        $pembayaran->update([
            'status' => $status,
            'catatan' => $validated['catatan'] ?? null,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        // Update status calon mahasiswa berdasarkan jenis pembayaran
        if ($status === 'terverifikasi') {
            $updateData = [];
            
            if ($pembayaran->jenis_pembayaran === 'pendaftaran') {
                $updateData = [
                    'status' => 'terdaftar',
                    'status_pendaftaran' => 'terdaftar',
                    'is_bayar_pendaftaran' => true,
                    'tanggal_bayar' => now(),
                ];
                
                if (!empty($updateData)) {
                    $pembayaran->calonMahasiswa->update($updateData);
                }
            } elseif ($pembayaran->jenis_pembayaran === 'daftar_ulang') {
                // Update status DaftarUlang menjadi lunas
                $daftarUlang = DaftarUlang::where('calon_mahasiswa_id', $pembayaran->calon_mahasiswa_id)->first();
                if ($daftarUlang) {
                    $daftarUlang->update([
                        'status' => 'lunas',
                        'tanggal_bayar' => now(),
                        'diproses_oleh' => auth()->id(),
                    ]);
                }
            }
        }

        $statusText = $status === 'terverifikasi' ? 'diverifikasi' : 'ditolak';
        return back()->with('success', "Pembayaran berhasil {$statusText}.");
    }

    /**
     * Konfirmasi pembayaran (dari status pending ke menunggu_verifikasi atau langsung terverifikasi)
     */
    public function konfirmasi(Request $request, string $hashid)
    {
        $pembayaran = PembayaranPmb::findByHashidOrFail($hashid);

        if ($pembayaran->status !== 'pending') {
            return back()->with('error', 'Pembayaran tidak dalam status pending.');
        }

        $validated = $request->validate([
            'metode_pembayaran' => 'required|string',
            'bank' => 'nullable|string|max:50',
            'catatan' => 'nullable|string',
        ]);

        // Update pembayaran - langsung terverifikasi karena admin yang konfirmasi
        $pembayaran->update([
            'status' => 'terverifikasi',
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'bank' => $validated['bank'],
            'catatan' => $validated['catatan'],
            'tanggal_bayar' => now(),
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        // Update status calon mahasiswa berdasarkan jenis pembayaran
        $message = 'Pembayaran berhasil dikonfirmasi.';
        
        if ($pembayaran->jenis_pembayaran === 'pendaftaran') {
            $pembayaran->calonMahasiswa->update([
                'status' => 'terdaftar',
                'status_pendaftaran' => 'terdaftar',
                'is_bayar_pendaftaran' => true,
                'tanggal_bayar' => now(),
            ]);
            $message .= ' Status calon mahasiswa diupdate menjadi Terdaftar.';
        } elseif ($pembayaran->jenis_pembayaran === 'daftar_ulang') {
            // Update status DaftarUlang menjadi lunas
            $daftarUlang = DaftarUlang::where('calon_mahasiswa_id', $pembayaran->calon_mahasiswa_id)->first();
            if ($daftarUlang) {
                $daftarUlang->update([
                    'status' => 'lunas',
                    'tanggal_bayar' => now(),
                    'diproses_oleh' => auth()->id(),
                ]);
            }
            $message .= ' Status daftar ulang diupdate menjadi Lunas. Silakan proses menjadi mahasiswa di menu Daftar Ulang.';
        }

        return back()->with('success', $message);
    }

    /**
     * Batch verifikasi pembayaran
     */
    public function batchVerifikasi(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
            'status' => 'required|in:terverifikasi,ditolak',
        ]);

        // Parse comma-separated hashids
        $hashids = array_filter(explode(',', $request->ids));
        
        if (empty($hashids)) {
            return back()->with('error', 'Tidak ada pembayaran yang dipilih.');
        }

        $count = 0;
        foreach ($hashids as $hashid) {
            $pembayaran = PembayaranPmb::findByHashid($hashid);
            if ($pembayaran && $pembayaran->status === 'menunggu_verifikasi') {
                $pembayaran->update([
                    'status' => $request->status,
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);

                // Update status calon mahasiswa berdasarkan jenis pembayaran
                if ($request->status === 'terverifikasi') {
                    if ($pembayaran->jenis_pembayaran === 'pendaftaran') {
                        $pembayaran->calonMahasiswa->update([
                            'status' => 'terdaftar',
                            'status_pendaftaran' => 'terdaftar',
                            'is_bayar_pendaftaran' => true,
                            'tanggal_bayar' => now(),
                        ]);
                    } elseif ($pembayaran->jenis_pembayaran === 'daftar_ulang') {
                        $pembayaran->calonMahasiswa->update([
                            'status' => 'menjadi_mahasiswa',
                            'status_pendaftaran' => 'menjadi_mahasiswa',
                        ]);
                    }
                }

                $count++;
            }
        }

        $statusText = $request->status === 'terverifikasi' ? 'diverifikasi' : 'ditolak';
        return back()->with('success', "{$count} pembayaran berhasil {$statusText}.");
    }

    /**
     * Generate tagihan pembayaran untuk calon mahasiswa
     */
    public function generateTagihan(Request $request)
    {
        $validated = $request->validate([
            'calon_mahasiswa_id' => 'required|exists:calon_mahasiswa,id',
            'jenis_pembayaran' => 'required|in:pendaftaran,daftar_ulang',
        ]);

        $camaba = CalonMahasiswa::findOrFail($validated['calon_mahasiswa_id']);

        // Cek apakah sudah ada tagihan
        $existingTagihan = PembayaranPmb::where('calon_mahasiswa_id', $camaba->id)
            ->where('jenis_pembayaran', $validated['jenis_pembayaran'])
            ->whereNotIn('status', ['ditolak', 'expired'])
            ->first();

        if ($existingTagihan) {
            return back()->with('error', 'Tagihan sudah ada untuk jenis pembayaran ini.');
        }

        // Ambil biaya
        if ($validated['jenis_pembayaran'] === 'pendaftaran') {
            $biaya = BiayaPendaftaran::getBiaya(
                $camaba->gelombang_pmb_id,
                $camaba->jalur_seleksi_id,
                $camaba->program_studi_id
            );
            $jumlah = $biaya ? $biaya->total_biaya : 0;
        } else {
            $jumlah = $camaba->daftarUlang?->total_biaya ?? 0;
        }

        $pembayaran = PembayaranPmb::create([
            'calon_mahasiswa_id' => $camaba->id,
            'jenis_pembayaran' => $validated['jenis_pembayaran'],
            'jumlah' => $jumlah,
            'status' => 'pending',
            'tanggal_expired' => now()->addDays(3),
        ]);

        return back()->with('success', 'Tagihan berhasil dibuat dengan No. ' . $pembayaran->no_pembayaran);
    }
}
