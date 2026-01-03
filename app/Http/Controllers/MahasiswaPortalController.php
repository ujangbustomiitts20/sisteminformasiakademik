<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\PotonganMahasiswa;
use App\Models\RiwayatPotongan;
use App\Models\PeriodeDiskon;
use App\Models\TransaksiPembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;

class MahasiswaPortalController extends Controller
{
    /**
     * Halaman Profil Mahasiswa
     */
    public function profil()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }
        
        $mahasiswa->load(['programStudi.fakultas', 'dosenWali', 'user', 'sekolah']);
        
        // Statistik
        $totalSks = $mahasiswa->totalSksLulus();
        $ipk = $mahasiswa->hitungIPK();
        $totalTagihan = Tagihan::where('mahasiswa_id', $mahasiswa->id)->sum('total_bayar');
        $totalDibayar = TransaksiPembayaran::where('mahasiswa_id', $mahasiswa->id)->sum('jumlah');
        
        // Hitung kelengkapan profil
        $fieldsToCheck = [
            'nama', 'nim', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 
            'agama', 'nik', 'no_hp', 'alamat', 'provinsi_id',
            'nama_ayah', 'nama_ibu', 'no_hp_ortu',
            'jalur_masuk', 'asal_sekolah', 'foto'
        ];
        // Sekolah bisa dari relasi atau field asal_sekolah
        $sekolahTerisi = $mahasiswa->sekolah_id || $mahasiswa->asal_sekolah;
        
        $filledCount = 0;
        foreach ($fieldsToCheck as $field) {
            if ($field === 'asal_sekolah') {
                if ($sekolahTerisi) $filledCount++;
            } elseif (!empty($mahasiswa->$field)) {
                $filledCount++;
            }
        }
        $kelengkapanProfil = round(($filledCount / count($fieldsToCheck)) * 100);
        
        return view('mahasiswa.profil', compact('mahasiswa', 'totalSks', 'ipk', 'totalTagihan', 'totalDibayar', 'kelengkapanProfil'));
    }

    /**
     * Form Edit Profil Mahasiswa
     */
    public function editProfil()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }
        
        $mahasiswa->load(['programStudi.fakultas', 'dosenWali', 'sekolah.kabupaten', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan']);
        
        return view('mahasiswa.edit-profil', compact('mahasiswa'));
    }

    /**
     * Update Profil Mahasiswa
     */
    public function updateProfil(Request $request)
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }

        $request->validate([
            // Data Pribadi (yang boleh diubah mahasiswa)
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'telepon' => 'nullable|string|max:20',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'provinsi_id' => 'nullable|exists:provinsi,id',
            'kabupaten_id' => 'nullable|exists:kabupaten,id',
            'kecamatan_id' => 'nullable|exists:kecamatan,id',
            'kelurahan_id' => 'nullable|exists:kelurahan,id',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'kode_pos' => 'nullable|string|max:10',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // Data Kependudukan
            'nik' => 'nullable|string|max:16',
            'no_kk' => 'nullable|string|max:16',
            'agama' => 'nullable|string|max:20',
            'kewarganegaraan' => 'nullable|string|max:10',
            'golongan_darah' => 'nullable|string|max:5',
            // Data Asal Sekolah & Jalur Masuk
            'jalur_masuk' => 'nullable|string|max:50',
            'sekolah_id' => 'nullable|exists:sekolah,id',
            'asal_sekolah' => 'nullable|string|max:255',
            'jurusan_asal' => 'nullable|string|max:100',
            'tahun_lulus_sekolah' => 'nullable|integer|min:1990|max:' . date('Y'),
            'nilai_un' => 'nullable|numeric|min:0|max:100',
            'no_ijazah_sma' => 'nullable|string|max:50',
            // Data Orang Tua
            'nama_ayah' => 'nullable|string|max:255',
            'nik_ayah' => 'nullable|string|max:16',
            'pekerjaan_ayah' => 'nullable|string|max:100',
            'pendidikan_ayah' => 'nullable|string|max:50',
            'nama_ibu' => 'nullable|string|max:255',
            'nik_ibu' => 'nullable|string|max:16',
            'pekerjaan_ibu' => 'nullable|string|max:100',
            'pendidikan_ibu' => 'nullable|string|max:50',
            'no_hp_ortu' => 'nullable|string|max:20',
            'email_ortu' => 'nullable|email|max:255',
            'penghasilan_ortu' => 'nullable|string|max:50',
            'alamat_ortu' => 'nullable|string',
            'provinsi_ortu_id' => 'nullable|exists:provinsi,id',
            'kabupaten_ortu_id' => 'nullable|exists:kabupaten,id',
            'kecamatan_ortu_id' => 'nullable|exists:kecamatan,id',
            'kelurahan_ortu_id' => 'nullable|exists:kelurahan,id',
            // Data Wali
            'nama_wali' => 'nullable|string|max:255',
            'hubungan_wali' => 'nullable|string|max:50',
            'pekerjaan_wali' => 'nullable|string|max:100',
            'no_hp_wali' => 'nullable|string|max:20',
            'alamat_wali' => 'nullable|string',
            // Data Finansial
            'no_rekening' => 'nullable|string|max:30',
            'nama_bank' => 'nullable|string|max:50',
            'atas_nama_rekening' => 'nullable|string|max:255',
            'penerima_kip' => 'nullable|boolean',
            'no_kip' => 'nullable|string|max:20',
        ]);

        // Handle foto upload
        $data = $request->except(['foto', 'hapus_foto']);
        
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($mahasiswa->foto && Storage::disk('public')->exists($mahasiswa->foto)) {
                Storage::disk('public')->delete($mahasiswa->foto);
            }
            $data['foto'] = $request->file('foto')->store('mahasiswa/foto', 'public');
        } elseif ($request->has('hapus_foto') && $request->hapus_foto) {
            // Hapus foto jika checkbox hapus_foto dicentang
            if ($mahasiswa->foto && Storage::disk('public')->exists($mahasiswa->foto)) {
                Storage::disk('public')->delete($mahasiswa->foto);
            }
            $data['foto'] = null;
        }

        $mahasiswa->update($data);

        return redirect()->route('mahasiswa.profil')->with('success', 'Data profil berhasil diperbarui!');
    }

    /**
     * Update Password Mahasiswa
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi',
            'password.required' => 'Password baru wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('mahasiswa.profil')->with('success', 'Password berhasil diubah!');
    }
    
    /**
     * Halaman Potongan & Diskon Mahasiswa
     */
    public function potongan()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }
        
        // Potongan Aktif (disetujui dan masih berlaku)
        $potonganAktif = PotonganMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'disetujui')
            ->where(function($q) {
                $q->whereNull('tanggal_selesai')
                  ->orWhere('tanggal_selesai', '>=', now());
            })
            ->with('jenisPotongan')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Potongan Pending
        $potonganPending = PotonganMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'pending')
            ->with('jenisPotongan')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Riwayat Penggunaan Potongan
        $riwayatPotongan = RiwayatPotongan::where('mahasiswa_id', $mahasiswa->id)
            ->with(['tagihan', 'potonganMahasiswa.jenisPotongan'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Total Potongan yang sudah digunakan
        $totalPotonganDigunakan = RiwayatPotongan::where('mahasiswa_id', $mahasiswa->id)
            ->sum('nominal_potongan');
        
        // Promo yang tersedia
        $promoTersedia = PeriodeDiskon::active()
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->where(function($q) {
                $q->whereNull('kuota')
                  ->orWhereRaw('kuota > kuota_terpakai');
            })
            ->with('jenisPotongan')
            ->get();
        
        return view('mahasiswa.potongan', compact(
            'potonganAktif',
            'potonganPending',
            'riwayatPotongan',
            'totalPotonganDigunakan',
            'promoTersedia'
        ));
    }
    
    /**
     * Halaman Kartu Tagihan (Preview)
     */
    public function kartuTagihan(Request $request)
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }
        
        $mahasiswa->load(['programStudi.fakultas']);
        
        // Get tahun akademik list for filter
        $tahunAkademiks = \App\Models\TahunAkademik::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();
        
        // Build query with filters
        $query = Tagihan::where('mahasiswa_id', $mahasiswa->id)
            ->with('tahunAkademik');
        
        // Filter by tahun akademik
        if ($request->filled('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by jenis tagihan
        if ($request->filled('jenis')) {
            $query->where('jenis_tagihan', $request->jenis);
        }
        
        $tagihan = $query->orderBy('created_at', 'desc')->get();
        
        // Get unique jenis tagihan for filter
        $jenisTagihans = Tagihan::where('mahasiswa_id', $mahasiswa->id)
            ->distinct()
            ->pluck('jenis_tagihan')
            ->filter();
        
        $totalTagihan = $tagihan->sum('total_bayar');
        $totalDibayar = $tagihan->sum('jumlah_dibayar');
        $sisaTagihan = max(0, $totalTagihan - $totalDibayar);
        
        return view('mahasiswa.kartu-tagihan', compact(
            'mahasiswa', 'tagihan', 'totalTagihan', 'totalDibayar', 'sisaTagihan',
            'tahunAkademiks', 'jenisTagihans'
        ));
    }
    
    /**
     * Download Kartu Tagihan
     */
    public function downloadKartuTagihan()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }
        
        $mahasiswa->load(['programStudi.fakultas']);
        
        $tagihan = Tagihan::where('mahasiswa_id', $mahasiswa->id)
            ->with('tahunAkademik')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $pdf = Pdf::loadView('mahasiswa.pdf.kartu-tagihan', compact('mahasiswa', 'tagihan'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('kartu-tagihan-' . $mahasiswa->nim . '.pdf');
    }
    
    /**
     * Halaman Riwayat Pembayaran (Preview)
     */
    public function riwayatPembayaran(Request $request)
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }
        
        $mahasiswa->load(['programStudi.fakultas']);
        
        // Get tahun akademik list for filter
        $tahunAkademiks = \App\Models\TahunAkademik::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();
        
        // Build query with filters
        $query = TransaksiPembayaran::where('mahasiswa_id', $mahasiswa->id)
            ->with(['tagihan.tahunAkademik']);
        
        // Filter by tahun akademik (via tagihan)
        if ($request->filled('tahun_akademik_id')) {
            $query->whereHas('tagihan', function($q) use ($request) {
                $q->where('tahun_akademik_id', $request->tahun_akademik_id);
            });
        }
        
        // Filter by metode pembayaran
        if ($request->filled('metode')) {
            $query->where('metode_pembayaran', $request->metode);
        }
        
        // Filter by tanggal
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_bayar', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_bayar', '<=', $request->tanggal_sampai);
        }
        
        $transaksi = $query->orderBy('tanggal_bayar', 'desc')->get();
        
        // Get unique metode pembayaran for filter
        $metodePembayarans = TransaksiPembayaran::where('mahasiswa_id', $mahasiswa->id)
            ->distinct()
            ->pluck('metode_pembayaran')
            ->filter();
        
        $totalDibayar = $transaksi->sum('jumlah');
        
        return view('mahasiswa.riwayat-pembayaran', compact(
            'mahasiswa', 'transaksi', 'totalDibayar',
            'tahunAkademiks', 'metodePembayarans'
        ));
    }
    
    /**
     * Download Riwayat Pembayaran
     */
    public function downloadRiwayatPembayaran()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }
        
        $mahasiswa->load(['programStudi.fakultas']);
        
        $transaksi = TransaksiPembayaran::where('mahasiswa_id', $mahasiswa->id)
            ->with(['tagihan.tahunAkademik'])
            ->orderBy('tanggal_bayar', 'desc')
            ->get();
        
        $totalDibayar = $transaksi->sum('jumlah');
        
        $pdf = Pdf::loadView('mahasiswa.pdf.riwayat-pembayaran', compact('mahasiswa', 'transaksi', 'totalDibayar'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('riwayat-pembayaran-' . $mahasiswa->nim . '.pdf');
    }
    
    /**
     * Halaman Kartu Mahasiswa (Preview)
     */
    public function kartuMahasiswa()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        
        if ($mahasiswa) {
            $mahasiswa->load(['programStudi.fakultas']);
        }
        
        return view('mahasiswa.kartu-mahasiswa', compact('mahasiswa'));
    }
    
    /**
     * Download Kartu Mahasiswa Digital
     */
    public function downloadKartuMahasiswa()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.kartu')->with('error', 'Data mahasiswa tidak ditemukan');
        }
        
        $mahasiswa->load(['programStudi.fakultas']);
        
        $pdf = Pdf::loadView('mahasiswa.pdf.kartu-mahasiswa', compact('mahasiswa'));
        // ID Card size: 323x204 points (approx 114mm x 72mm for better print)
        $pdf->setPaper([0, 0, 323, 204]);
        
        return $pdf->download('kartu-mahasiswa-' . $mahasiswa->nim . '.pdf');
    }
}
