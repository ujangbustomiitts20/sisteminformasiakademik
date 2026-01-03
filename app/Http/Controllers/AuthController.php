<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            // Log login activity
            ActivityLog::logLogin(Auth::user());
            
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Log logout activity
        if (Auth::check()) {
            ActivityLog::logLogout(Auth::user());
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function showProfile()
    {
        $user = auth()->user();
        return view('auth.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
        
        // Tambahan validasi untuk mahasiswa
        if ($user->isMahasiswa()) {
            $rules['no_hp'] = 'nullable|string|max:15';
            $rules['jenis_kelamin'] = 'nullable|in:Laki-laki,Perempuan';
            $rules['alamat'] = 'nullable|string';
            $rules['nama_ayah'] = 'nullable|string|max:255';
            $rules['nama_ibu'] = 'nullable|string|max:255';
            $rules['pekerjaan_ayah'] = 'nullable|string|max:255';
            $rules['pekerjaan_ibu'] = 'nullable|string|max:255';
            $rules['no_hp_ortu'] = 'nullable|string|max:15';
            $rules['penghasilan_ortu'] = 'nullable|string';
            $rules['alamat_ortu'] = 'nullable|string';
        }
        
        // Tambahan validasi untuk dosen
        if ($user->isDosen()) {
            $rules['telepon'] = 'nullable|string|max:15';
            $rules['email_pribadi'] = 'nullable|email|max:255';
            $rules['jenis_kelamin'] = 'nullable|in:Laki-laki,Perempuan';
            $rules['tempat_lahir'] = 'nullable|string|max:100';
            $rules['tanggal_lahir'] = 'nullable|date';
            $rules['alamat'] = 'nullable|string';
        }
        
        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Delete old foto if exists
            if ($user->isMahasiswa() && $user->mahasiswa?->foto) {
                Storage::disk('public')->delete($user->mahasiswa->foto);
            } elseif ($user->isDosen() && $user->dosen?->foto) {
                Storage::disk('public')->delete($user->dosen->foto);
            }

            $foto = $request->file('foto');
            $filename = 'foto_' . $user->id . '_' . time() . '.' . $foto->getClientOriginalExtension();
            $path = $foto->storeAs('foto', $filename, 'public');

            // Update foto di tabel mahasiswa/dosen
            if ($user->isMahasiswa() && $user->mahasiswa) {
                $user->mahasiswa->update(['foto' => $path]);
            } elseif ($user->isDosen() && $user->dosen) {
                $user->dosen->update(['foto' => $path]);
            }
        }
        
        // Update data mahasiswa
        if ($user->isMahasiswa() && $user->mahasiswa) {
            $user->mahasiswa->update([
                'no_hp' => $request->no_hp,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
                'nama_ayah' => $request->nama_ayah,
                'nama_ibu' => $request->nama_ibu,
                'pekerjaan_ayah' => $request->pekerjaan_ayah,
                'pekerjaan_ibu' => $request->pekerjaan_ibu,
                'no_hp_ortu' => $request->no_hp_ortu,
                'penghasilan_ortu' => $request->penghasilan_ortu,
                'alamat_ortu' => $request->alamat_ortu,
            ]);
        }
        
        // Update data dosen
        if ($user->isDosen() && $user->dosen) {
            // Konversi jenis_kelamin ke format database (L/P)
            $jenisKelamin = null;
            if ($request->jenis_kelamin == 'Laki-laki') {
                $jenisKelamin = 'L';
            } elseif ($request->jenis_kelamin == 'Perempuan') {
                $jenisKelamin = 'P';
            }
            
            $dosenData = [
                'nama' => $request->name,
                'telepon' => $request->telepon,
                'email' => $request->email_pribadi,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
            ];
            
            // Hanya update jenis_kelamin jika ada nilai
            if ($jenisKelamin) {
                $dosenData['jenis_kelamin'] = $jenisKelamin;
            }
            
            // Handle foto upload untuk dosen
            if ($request->hasFile('foto')) {
                // Delete old foto if exists
                if ($user->dosen->foto) {
                    Storage::disk('public')->delete($user->dosen->foto);
                }
                
                $foto = $request->file('foto');
                $filename = 'foto_dosen_' . $user->dosen->id . '_' . time() . '.' . $foto->getClientOriginalExtension();
                $path = $foto->storeAs('foto/dosen', $filename, 'public');
                $dosenData['foto'] = $path;
            }
            
            $user->dosen->update($dosenData);
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui!');
    }
}
