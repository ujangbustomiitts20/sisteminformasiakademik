<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        $query = Role::withCount(['users', 'permissions', 'menus']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('slug', 'like', "%{$request->search}%")
                  ->orWhere('deskripsi', 'like', "%{$request->search}%");
            });
        }

        $roles = $query->orderBy('urutan')->orderBy('nama')->paginate(15);

        // Stats
        $stats = [
            'total' => Role::count(),
            'aktif' => Role::where('is_active', true)->count(),
            'system' => Role::where('is_system', true)->count(),
        ];

        return view('admin.roles.index', compact('roles', 'stats'));
    }

    /**
     * Show form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::getAllGrouped();
        $menus = Menu::getMenuTree();
        $warna = Role::WARNA;

        return view('admin.roles.create', compact('permissions', 'menus', 'warna'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50|unique:roles,nama',
            'slug' => 'nullable|string|max:50|unique:roles,slug',
            'deskripsi' => 'nullable|string|max:255',
            'warna' => 'required|string|max:20',
            'urutan' => 'nullable|integer|min:0',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'menus' => 'nullable|array',
            'menus.*' => 'exists:menus,id',
        ]);

        try {
            // Generate slug if not provided
            $validated['slug'] = $validated['slug'] ?? Str::slug($validated['nama']);
            $validated['is_active'] = $request->has('is_active');
            $validated['is_system'] = false;

            $role = Role::create($validated);

            // Sync permissions
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            }

            // Sync menus
            if ($request->has('menus')) {
                $role->menus()->sync($request->menus);
            }

            return redirect()->route('admin.roles.index')
                ->with('success', "Role '{$role->nama}' berhasil dibuat.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan role: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'menus', 'users']);
        
        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show form for editing the role.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::getAllGrouped();
        $menus = Menu::getMenuTree();
        $warna = Role::WARNA;
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        $roleMenus = $role->menus->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'menus', 'warna', 'rolePermissions', 'roleMenus'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:50', Rule::unique('roles')->ignore($role->id)],
            'slug' => ['nullable', 'string', 'max:50', Rule::unique('roles')->ignore($role->id)],
            'deskripsi' => 'nullable|string|max:255',
            'warna' => 'required|string|max:20',
            'urutan' => 'nullable|integer|min:0',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'menus' => 'nullable|array',
            'menus.*' => 'exists:menus,id',
        ]);

        try {
            // Don't allow changing slug for system roles
            if ($role->is_system) {
                unset($validated['slug']);
            } else {
                $validated['slug'] = $validated['slug'] ?? Str::slug($validated['nama']);
            }
            
            $validated['is_active'] = $request->has('is_active');

            $role->update($validated);

            // Sync permissions
            $role->permissions()->sync($request->permissions ?? []);

            // Sync menus
            $role->menus()->sync($request->menus ?? []);

            // Clear cache for all users with this role
            foreach ($role->users as $user) {
                $user->clearPermissionCache();
            }

            return redirect()->route('admin.roles.index')
                ->with('success', "Role '{$role->nama}' berhasil diperbarui.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui role: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Role sistem tidak dapat dihapus.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh user.');
        }

        $roleName = $role->nama;
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$roleName}' berhasil dihapus.");
    }

    /**
     * Duplicate a role.
     */
    public function duplicate(Role $role)
    {
        $newRole = $role->replicate();
        $newRole->nama = $role->nama . ' (Copy)';
        $newRole->slug = Str::slug($newRole->nama);
        $newRole->is_system = false;
        $newRole->save();

        // Copy permissions
        $newRole->permissions()->sync($role->permissions->pluck('id'));

        // Copy menus
        $newRole->menus()->sync($role->menus->pluck('id'));

        return redirect()->route('admin.roles.edit', $newRole)
            ->with('success', "Role berhasil diduplikasi sebagai '{$newRole->nama}'.");
    }

    /**
     * Toggle role status.
     */
    public function toggleStatus(Role $role)
    {
        $role->update(['is_active' => !$role->is_active]);

        $status = $role->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()
            ->with('success', "Role '{$role->nama}' berhasil {$status}.");
    }
}
