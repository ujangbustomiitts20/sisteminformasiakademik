<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\ProgramStudi;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Generate NIM otomatis via AJAX
     */
    public function generateNim(Request $request)
    {
        $request->validate([
            'program_studi_id' => 'required|exists:program_studi,id'
        ]);

        $nim = Mahasiswa::generateNim($request->program_studi_id);
        $email = Mahasiswa::generateEmail($nim);

        return response()->json([
            'success' => true,
            'nim' => $nim,
            'email' => $email,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_studi_id' => 'required|exists:program_studi,id',
            'nama' => 'required|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'angkatan' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'dosen_wali_id' => 'nullable|exists:dosen,id',
            'tempat_lahir' => 'nullable|max:100',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable',
            'telepon' => 'nullable|max:20',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // Data Wilayah Domisili
            'provinsi_id' => 'nullable|exists:provinsi,id',
            'kabupaten_id' => 'nullable|exists:kabupaten,id',
            'kecamatan_id' => 'nullable|exists:kecamatan,id',
            'kelurahan_id' => 'nullable|exists:kelurahan,id',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'kode_pos' => 'nullable|string|max:10',
            // Data Kependudukan
            'nik' => 'nullable|string|max:16',
            'no_kk' => 'nullable|string|max:16',
            'agama' => 'nullable|string|max:20',
            'kewarganegaraan' => 'nullable|string|max:10',
            'golongan_darah' => 'nullable|string|max:5',
            // Data Akademik Tambahan
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
            // Data Wilayah Orang Tua
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

        DB::beginTransaction();
        try {
            // Generate NIM otomatis
            $nim = Mahasiswa::generateNim($request->program_studi_id);
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

            // Buat user account
            $user = User::create([
                'name' => $request->nama,
                'email' => $email,
                'password' => Hash::make($nim), // Default password = NIM
                'role' => 'mahasiswa',
            ]);

            // Handle foto upload
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('mahasiswa/foto', 'public');
            }

            // Buat data mahasiswa
            Mahasiswa::create([
                'user_id' => $user->id,
                'program_studi_id' => $request->program_studi_id,
                'dosen_wali_id' => $request->dosen_wali_id,
                'nim' => $nim,
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                // Data Wilayah Domisili
                'provinsi_id' => $request->provinsi_id,
                'kabupaten_id' => $request->kabupaten_id,
                'kecamatan_id' => $request->kecamatan_id,
                'kelurahan_id' => $request->kelurahan_id,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'kode_pos' => $request->kode_pos,
                'telepon' => $request->telepon,
                'no_hp' => $request->no_hp,
                'email' => $email,
                'foto' => $fotoPath,
                // Data Kependudukan
                'nik' => $request->nik,
                'no_kk' => $request->no_kk,
                'agama' => $request->agama,
                'kewarganegaraan' => $request->kewarganegaraan ?? 'WNI',
                'golongan_darah' => $request->golongan_darah,
                // Data Akademik
                'angkatan' => $request->angkatan,
                'jalur_masuk' => $request->jalur_masuk,
                'sekolah_id' => $request->sekolah_id,
                'asal_sekolah' => $request->asal_sekolah,
                'jurusan_asal' => $request->jurusan_asal,
                'tahun_lulus_sekolah' => $request->tahun_lulus_sekolah,
                'nilai_un' => $request->nilai_un,
                'no_ijazah_sma' => $request->no_ijazah_sma,
                // Data Orang Tua
                'nama_ayah' => $request->nama_ayah,
                'nik_ayah' => $request->nik_ayah,
                'pekerjaan_ayah' => $request->pekerjaan_ayah,
                'pendidikan_ayah' => $request->pendidikan_ayah,
                'nama_ibu' => $request->nama_ibu,
                'nik_ibu' => $request->nik_ibu,
                'pekerjaan_ibu' => $request->pekerjaan_ibu,
                'pendidikan_ibu' => $request->pendidikan_ibu,
                'no_hp_ortu' => $request->no_hp_ortu,
                'email_ortu' => $request->email_ortu,
                'penghasilan_ortu' => $request->penghasilan_ortu,
                'alamat_ortu' => $request->alamat_ortu,
                // Data Wilayah Orang Tua
                'provinsi_ortu_id' => $request->provinsi_ortu_id,
                'kabupaten_ortu_id' => $request->kabupaten_ortu_id,
                'kecamatan_ortu_id' => $request->kecamatan_ortu_id,
                'kelurahan_ortu_id' => $request->kelurahan_ortu_id,
                // Data Wali
                'nama_wali' => $request->nama_wali,
                'hubungan_wali' => $request->hubungan_wali,
                'pekerjaan_wali' => $request->pekerjaan_wali,
                'no_hp_wali' => $request->no_hp_wali,
                'alamat_wali' => $request->alamat_wali,
                // Data Finansial
                'no_rekening' => $request->no_rekening,
                'nama_bank' => $request->nama_bank,
                'atas_nama_rekening' => $request->atas_nama_rekening,
                'penerima_kip' => $request->penerima_kip ?? false,
                'no_kip' => $request->no_kip,
            ]);

            DB::commit();
            return redirect()->route('mahasiswa.index')->with('success', "Data mahasiswa berhasil ditambahkan! NIM: {$nim}, Email: {$email}");
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
        $mahasiswa->load(['sekolah.kabupaten']);
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
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // Data Wilayah Domisili
            'provinsi_id' => 'nullable|exists:provinsi,id',
            'kabupaten_id' => 'nullable|exists:kabupaten,id',
            'kecamatan_id' => 'nullable|exists:kecamatan,id',
            'kelurahan_id' => 'nullable|exists:kelurahan,id',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'kode_pos' => 'nullable|string|max:10',
            // Data Kependudukan
            'nik' => 'nullable|string|max:16',
            'no_kk' => 'nullable|string|max:16',
            'agama' => 'nullable|string|max:20',
            'kewarganegaraan' => 'nullable|string|max:10',
            'golongan_darah' => 'nullable|string|max:5',
            // Data Akademik Tambahan
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
            // Data Wilayah Orang Tua
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
        
        // Update user email juga
        $mahasiswa->user->update(['email' => $request->email, 'name' => $request->nama]);

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui!');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        DB::beginTransaction();
        try {
            $user = $mahasiswa->user;
            
            // Hapus foto jika ada
            if ($mahasiswa->foto && Storage::disk('public')->exists($mahasiswa->foto)) {
                Storage::disk('public')->delete($mahasiswa->foto);
            }
            
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
