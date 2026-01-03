<?php

namespace App\Http\Controllers;

use App\Models\DaftarUlang;
use App\Models\CalonMahasiswa;
use App\Models\GelombangPmb;
use App\Models\HasilSeleksi;
use App\Models\Mahasiswa;
use App\Models\PembayaranPmb;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DaftarUlangController extends Controller
{
    public function index(Request $request)
    {
        $gelombangs = GelombangPmb::with('periodePmb')->orderBy('id', 'desc')->get();
        $prodis = ProgramStudi::orderBy('nama')->get();

        $query = DaftarUlang::with(['calonMahasiswa.programStudi', 'calonMahasiswa.gelombangPmb', 'programStudi']);

        if ($request->filled('gelombang')) {
            $query->whereHas('calonMahasiswa', function($q) use ($request) {
                $q->where('gelombang_pmb_id', $request->gelombang);
            });
        }

        if ($request->filled('prodi')) {
            $query->where('program_studi_id', $request->prodi);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_daftar_ulang', 'like', "%{$search}%")
                  ->orWhereHas('calonMahasiswa', function($q2) use ($search) {
                    $q2->where('nama_lengkap', 'like', "%{$search}%")
                       ->orWhere('no_pendaftaran', 'like', "%{$search}%");
                });
            });
        }

        $daftarUlangs = $query->orderBy('created_at', 'desc')->paginate(15);

        // Summary - menggunakan status ENUM yang valid
        $summary = [
            'total' => DaftarUlang::count(),
            'menunggu_bayar' => DaftarUlang::where('status', 'pending')->count(),
            'sudah_bayar' => DaftarUlang::where('status', 'lunas')->count(),
            'menjadi_mahasiswa' => DaftarUlang::where('status', 'selesai')->count(),
        ];

        return view('pmb.daftar-ulang.index', compact('daftarUlangs', 'gelombangs', 'prodis', 'summary'));
    }

    /**
     * Tampilkan form generate daftar ulang
     */
    public function showGenerate()
    {
        $gelombangs = GelombangPmb::with(['periodePmb', 'hasilSeleksi'])
            ->whereHas('hasilSeleksi', function($q) {
                $q->where('status', 'lulus');
            })
            ->orderBy('id', 'desc')
            ->get();

        return view('pmb.daftar-ulang.generate', compact('gelombangs'));
    }

    /**
     * Generate daftar ulang untuk peserta lulus
     */
    public function storeGenerate(Request $request)
    {
        $validated = $request->validate([
            'gelombang_id' => 'required|exists:gelombang_pmb,id',
            'biaya' => 'required|numeric|min:0',
            'biaya_ukt' => 'nullable|numeric|min:0',
            'tanggal_expired' => 'required|date|after:today',
        ]);

        DB::beginTransaction();
        try {
            // Ambil semua yang lulus dan belum punya daftar ulang
            $hasilLulus = HasilSeleksi::where('gelombang_pmb_id', $validated['gelombang_id'])
                ->where('status', 'lulus')
                ->whereDoesntHave('calonMahasiswa.daftarUlang')
                ->with('calonMahasiswa')
                ->get();

            $count = 0;
            $totalBiaya = $validated['biaya'] + ($validated['biaya_ukt'] ?? 0);
            
            foreach ($hasilLulus as $hasil) {
                // Generate nomor daftar ulang
                $noDaftarUlang = 'DU' . date('Ymd') . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

                DaftarUlang::create([
                    'no_daftar_ulang' => $noDaftarUlang,
                    'calon_mahasiswa_id' => $hasil->calon_mahasiswa_id,
                    'program_studi_id' => $hasil->program_studi_diterima_id,
                    'biaya' => $validated['biaya'],
                    'biaya_daftar_ulang' => $validated['biaya'],
                    'biaya_ukt' => $validated['biaya_ukt'] ?? 0,
                    'tanggal_expired' => $validated['tanggal_expired'],
                    'status' => 'pending',
                ]);

                // Buat record pembayaran daftar ulang
                $noPembayaran = 'PAY' . date('Ymd') . str_pad(PembayaranPmb::whereDate('created_at', today())->count() + 1, 5, '0', STR_PAD_LEFT);
                PembayaranPmb::create([
                    'calon_mahasiswa_id' => $hasil->calon_mahasiswa_id,
                    'no_pembayaran' => $noPembayaran,
                    'jenis_pembayaran' => 'daftar_ulang',
                    'jumlah' => $totalBiaya,
                    'status' => 'pending',
                    'tanggal_expired' => $validated['tanggal_expired'],
                ]);

                $hasil->calonMahasiswa->update(['status_pendaftaran' => 'daftar_ulang']);
                $count++;
            }

            DB::commit();

            return redirect()->route('pmb.daftar-ulang.index')
                ->with('success', "Berhasil generate {$count} data daftar ulang.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal generate daftar ulang: ' . $e->getMessage());
        }
    }

    public function show(string $hashid)
    {
        $daftarUlang = DaftarUlang::findByHashidOrFail($hashid);
        $daftarUlang->load(['calonMahasiswa.programStudi', 'calonMahasiswa.hasilSeleksi', 'calonMahasiswa.dokumen', 'mahasiswa', 'programStudi']);

        // Cari pembayaran terkait
        $pembayaran = PembayaranPmb::where('calon_mahasiswa_id', $daftarUlang->calon_mahasiswa_id)
            ->where('jenis_pembayaran', 'daftar_ulang')
            ->first();

        return view('pmb.daftar-ulang.show', compact('daftarUlang', 'pembayaran'));
    }

    /**
     * Verifikasi pembayaran daftar ulang
     */
    public function verifikasiPembayaran(Request $request, string $hashid)
    {
        $daftarUlang = DaftarUlang::findByHashidOrFail($hashid);

        $daftarUlang->update([
            'status' => 'lunas',
            'tanggal_bayar' => now(),
            'diproses_oleh' => auth()->id(),
        ]);

        return back()->with('success', 'Pembayaran daftar ulang berhasil diverifikasi.');
    }

    /**
     * Proses menjadi mahasiswa
     */
    public function prosesMahasiswa(string $hashid)
    {
        $daftarUlang = DaftarUlang::findByHashidOrFail($hashid);
        $camaba = $daftarUlang->calonMahasiswa;

        if (!in_array($daftarUlang->status, ['lunas', 'sudah_bayar'])) {
            return back()->with('error', 'Pembayaran daftar ulang belum lunas.');
        }

        DB::beginTransaction();
        try {
            // Ambil program studi diterima
            $prodiId = $daftarUlang->program_studi_id ?? $camaba->hasilSeleksi->program_studi_diterima_id ?? $camaba->program_studi_id;

            // Generate NIM menggunakan method yang sama dengan MahasiswaController
            $nim = Mahasiswa::generateNim($prodiId);
            if (!$nim) {
                throw new \Exception('Gagal generate NIM. Pastikan kode fakultas dan prodi sudah diatur.');
            }

            // Generate email dari NIM
            $email = Mahasiswa::generateEmail($nim);

            // Cek duplikat
            if (Mahasiswa::where('nim', $nim)->exists()) {
                throw new \Exception('NIM sudah terdaftar. Silakan coba lagi.');
            }
            if (User::where('email', $email)->exists()) {
                throw new \Exception('Email sudah terdaftar. Silakan coba lagi.');
            }

            // Buat user account untuk mahasiswa
            $user = User::create([
                'name' => $camaba->nama_lengkap,
                'email' => $email,
                'password' => Hash::make($nim), // Password default = NIM
                'role' => 'mahasiswa',
            ]);

            // Buat data mahasiswa dengan mapping lengkap dari calon mahasiswa
            $mahasiswa = Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $nim,
                'nama' => $camaba->nama_lengkap,
                'email' => $email,
                'program_studi_id' => $prodiId,
                'jenis_kelamin' => $camaba->jenis_kelamin,
                'tempat_lahir' => $camaba->tempat_lahir,
                'tanggal_lahir' => $camaba->tanggal_lahir,
                'agama' => $camaba->agama,
                'kewarganegaraan' => $camaba->kewarganegaraan ?? 'WNI',
                'alamat' => $camaba->alamat,
                'rt' => $camaba->rt,
                'rw' => $camaba->rw,
                'kode_pos' => $camaba->kode_pos,
                'no_hp' => $camaba->no_hp,
                'telepon' => $camaba->no_hp,
                // Data Kependudukan
                'nik' => $camaba->nik,
                // Data Akademik
                'angkatan' => date('Y'),
                'tahun_masuk' => date('Y'),
                'semester' => 1,
                'jalur_masuk' => $camaba->jalurSeleksi->nama ?? null,
                'asal_sekolah' => $camaba->asal_sekolah,
                'jurusan_asal' => $camaba->jurusan_sekolah,
                'tahun_lulus_sekolah' => $camaba->tahun_lulus,
                // Data Orang Tua
                'nama_ayah' => $camaba->nama_ayah,
                'pekerjaan_ayah' => $camaba->pekerjaan_ayah,
                'no_hp_ortu' => $camaba->no_hp_ayah,
                'nama_ibu' => $camaba->nama_ibu,
                'pekerjaan_ibu' => $camaba->pekerjaan_ibu,
                'penghasilan_ortu' => $camaba->penghasilan_ortu,
                // Status & Foto
                'status' => 'aktif',
                'foto' => $camaba->foto,
            ]);

            // Update daftar ulang
            $daftarUlang->update([
                'status' => 'selesai',
                'nim_generated' => $nim,
                'mahasiswa_id' => $mahasiswa->id,
                'tanggal_verifikasi' => now(),
                'diproses_oleh' => auth()->id(),
            ]);

            // Update status calon mahasiswa
            $camaba->update([
                'status' => 'menjadi_mahasiswa',
                'status_pendaftaran' => 'menjadi_mahasiswa'
            ]);

            DB::commit();

            return back()->with('success', "Berhasil memproses menjadi mahasiswa dengan NIM: {$nim}. Password default: {$nim}");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    /**
     * Proses batch menjadi mahasiswa
     */
    public function batchProsesMahasiswa(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        // Parse comma-separated hashids
        $hashids = array_filter(explode(',', $request->ids));
        
        if (empty($hashids)) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $success = 0;
        $failed = 0;

        foreach ($hashids as $hashid) {
            $daftarUlang = DaftarUlang::findByHashid($hashid);
            
            if ($daftarUlang && in_array($daftarUlang->status, ['lunas', 'sudah_bayar'])) {
                try {
                    DB::beginTransaction();
                    
                    $camaba = $daftarUlang->calonMahasiswa;
                    $prodiId = $daftarUlang->program_studi_id ?? $camaba->hasilSeleksi->program_studi_diterima_id ?? $camaba->program_studi_id;

                    // Generate NIM dan Email
                    $nim = Mahasiswa::generateNim($prodiId);
                    if (!$nim) {
                        throw new \Exception('Gagal generate NIM');
                    }
                    $email = Mahasiswa::generateEmail($nim);

                    // Cek duplikat
                    if (Mahasiswa::where('nim', $nim)->exists() || User::where('email', $email)->exists()) {
                        throw new \Exception('NIM/Email sudah terdaftar');
                    }

                    // Buat user account untuk mahasiswa
                    $user = User::create([
                        'name' => $camaba->nama_lengkap,
                        'email' => $email,
                        'password' => Hash::make($nim),
                        'role' => 'mahasiswa',
                    ]);

                    $mahasiswa = Mahasiswa::create([
                        'user_id' => $user->id,
                        'nim' => $nim,
                        'nama' => $camaba->nama_lengkap,
                        'email' => $email,
                        'program_studi_id' => $prodiId,
                        'jenis_kelamin' => $camaba->jenis_kelamin,
                        'tempat_lahir' => $camaba->tempat_lahir,
                        'tanggal_lahir' => $camaba->tanggal_lahir,
                        'agama' => $camaba->agama,
                        'kewarganegaraan' => $camaba->kewarganegaraan ?? 'WNI',
                        'alamat' => $camaba->alamat,
                        'rt' => $camaba->rt,
                        'rw' => $camaba->rw,
                        'kode_pos' => $camaba->kode_pos,
                        'no_hp' => $camaba->no_hp,
                        'telepon' => $camaba->no_hp,
                        'nik' => $camaba->nik,
                        'angkatan' => date('Y'),
                        'tahun_masuk' => date('Y'),
                        'semester' => 1,
                        'jalur_masuk' => $camaba->jalurSeleksi->nama ?? null,
                        'asal_sekolah' => $camaba->asal_sekolah,
                        'jurusan_asal' => $camaba->jurusan_sekolah,
                        'tahun_lulus_sekolah' => $camaba->tahun_lulus,
                        'nama_ayah' => $camaba->nama_ayah,
                        'pekerjaan_ayah' => $camaba->pekerjaan_ayah,
                        'no_hp_ortu' => $camaba->no_hp_ayah,
                        'nama_ibu' => $camaba->nama_ibu,
                        'pekerjaan_ibu' => $camaba->pekerjaan_ibu,
                        'penghasilan_ortu' => $camaba->penghasilan_ortu,
                        'status' => 'aktif',
                        'foto' => $camaba->foto,
                    ]);

                    $daftarUlang->update([
                        'status' => 'selesai',
                        'nim_generated' => $nim,
                        'mahasiswa_id' => $mahasiswa->id,
                        'tanggal_verifikasi' => now(),
                        'diproses_oleh' => auth()->id(),
                    ]);

                    $camaba->update([
                        'status' => 'menjadi_mahasiswa',
                        'status_pendaftaran' => 'menjadi_mahasiswa'
                    ]);

                    DB::commit();
                    $success++;
                } catch (\Exception $e) {
                    DB::rollback();
                    $failed++;
                }
            } else {
                $failed++;
            }
        }

        return back()->with('success', "Proses selesai. Berhasil: {$success}, Gagal: {$failed}");
    }
}
