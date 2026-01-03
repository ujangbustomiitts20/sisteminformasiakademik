<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->role) {
            $query->where('role', $request->role);
        }

        $users = $query->with(['dosen', 'mahasiswa'])
            ->orderBy('name')
            ->paginate(15);

        return view('user.index', compact('users'));
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,dosen,mahasiswa,kaprodi,dekan',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function show(User $user)
    {
        $user->load(['dosen.programStudi.fakultas', 'mahasiswa.programStudi.fakultas']);
        return view('user.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('user.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6|confirmed',
            'role' => 'required|in:admin,dosen,mahasiswa,kaprodi,dekan',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('user.index')->with('success', 'User berhasil diupdate!');
    }

    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri!');
        }

        // Check if user has related data
        if ($user->role === 'dosen' && $user->dosen) {
            return back()->with('error', 'User ini terhubung dengan data dosen. Hapus data dosen terlebih dahulu!');
        }

        if ($user->role === 'mahasiswa' && $user->mahasiswa) {
            return back()->with('error', 'User ini terhubung dengan data mahasiswa. Hapus data mahasiswa terlebih dahulu!');
        }

        $user->delete();

        return redirect()->route('user.index')->with('success', 'User berhasil dihapus!');
    }

    public function resetPassword(User $user)
    {
        // Reset password to default based on role
        $defaultPassword = match($user->role) {
            'admin' => 'admin123',
            'dosen' => $user->dosen?->nidn ?? 'dosen123',
            'mahasiswa' => $user->mahasiswa?->nim ?? 'mahasiswa123',
            default => 'password123',
        };

        $user->update([
            'password' => Hash::make($defaultPassword),
        ]);

        return back()->with('success', "Password berhasil direset ke: {$defaultPassword}");
    }
}
