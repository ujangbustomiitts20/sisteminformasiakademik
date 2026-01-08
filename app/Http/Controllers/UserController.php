<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
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

        $users = $query->with(['dosen', 'mahasiswa', 'roles'])
            ->orderBy('name')
            ->paginate(15);
        
        $roles = Role::where('is_active', true)->orderBy('urutan')->get();

        return view('user.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::where('is_active', true)->orderBy('urutan')->get();
        return view('user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'nullable|in:admin,dosen,mahasiswa,kaprodi,dekan',
            'dynamic_roles' => 'nullable|array',
            'dynamic_roles.*' => 'exists:roles,id',
            'primary_role' => 'nullable|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Sync dynamic roles
        if ($request->has('dynamic_roles')) {
            $syncData = [];
            foreach ($request->dynamic_roles as $roleId) {
                $syncData[$roleId] = ['is_primary' => $roleId == $request->primary_role];
            }
            $user->roles()->sync($syncData);
        }

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function show(User $user)
    {
        $user->load(['dosen.programStudi.fakultas', 'mahasiswa.programStudi.fakultas', 'roles']);
        return view('user.show', compact('user'));
    }

    public function edit(User $user)
    {
        $user->load('roles');
        $roles = Role::where('is_active', true)->orderBy('urutan')->get();
        $userRoles = $user->roles->pluck('id')->toArray();
        $primaryRoleId = $user->roles()->wherePivot('is_primary', true)->first()?->id;
        
        return view('user.edit', compact('user', 'roles', 'userRoles', 'primaryRoleId'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6|confirmed',
            'role' => 'nullable|in:admin,dosen,mahasiswa,kaprodi,dekan',
            'dynamic_roles' => 'nullable|array',
            'dynamic_roles.*' => 'exists:roles,id',
            'primary_role' => 'nullable|exists:roles,id',
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

        // Sync dynamic roles
        if ($request->has('dynamic_roles')) {
            $syncData = [];
            foreach ($request->dynamic_roles as $roleId) {
                $syncData[$roleId] = ['is_primary' => $roleId == $request->primary_role];
            }
            $user->roles()->sync($syncData);
        } else {
            $user->roles()->detach();
        }

        // Clear permission cache
        $user->clearPermissionCache();

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
