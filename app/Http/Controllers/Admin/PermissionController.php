<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index(Request $request)
    {
        $query = Permission::withCount('roles');

        // Filter by grup
        if ($request->filled('grup')) {
            $query->where('grup', $request->grup);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('slug', 'like', "%{$request->search}%")
                  ->orWhere('deskripsi', 'like', "%{$request->search}%");
            });
        }

        $permissions = $query->orderBy('grup')->orderBy('nama')->paginate(20);

        // Get grouped permissions for summary
        $groupedPermissions = Permission::getAllGrouped();

        // Stats
        $stats = [
            'total' => Permission::count(),
            'groups' => Permission::distinct('grup')->count('grup'),
        ];

        return view('admin.permissions.index', compact('permissions', 'groupedPermissions', 'stats'));
    }

    /**
     * Show form for creating a new permission.
     */
    public function create()
    {
        $grups = Permission::GRUP;

        return view('admin.permissions.create', compact('grups'));
    }

    /**
     * Store a newly created permission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:permissions,slug',
            'grup' => 'required|string|max:50',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        // Generate slug if not provided
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['nama']);

        $permission = Permission::create($validated);

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission '{$permission->nama}' berhasil dibuat.");
    }

    /**
     * Show form for editing the permission.
     */
    public function edit(Permission $permission)
    {
        $grups = Permission::GRUP;
        $permission->load('roles');

        return view('admin.permissions.edit', compact('permission', 'grups'));
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('permissions')->ignore($permission->id)],
            'grup' => 'required|string|max:50',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['nama']);

        $permission->update($validated);

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission '{$permission->nama}' berhasil diperbarui.");
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(Permission $permission)
    {
        if ($permission->roles()->count() > 0) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'Permission tidak dapat dihapus karena masih digunakan oleh role.');
        }

        $permissionName = $permission->nama;
        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission '{$permissionName}' berhasil dihapus.");
    }

    /**
     * Generate common permissions for a module.
     */
    public function generateForModule(Request $request)
    {
        $request->validate([
            'module' => 'required|string|max:50',
            'grup' => 'required|string|max:50',
        ]);

        $module = $request->module;
        $grup = $request->grup;

        $actions = [
            'lihat' => 'Melihat daftar',
            'tambah' => 'Menambah data',
            'edit' => 'Mengubah data',
            'hapus' => 'Menghapus data',
            'export' => 'Export data',
            'import' => 'Import data',
        ];

        $created = 0;
        foreach ($actions as $action => $desc) {
            $slug = "{$module}.{$action}";
            $nama = ucfirst($action) . ' ' . ucfirst(str_replace('_', ' ', $module));

            Permission::firstOrCreate(
                ['slug' => $slug],
                [
                    'nama' => $nama,
                    'grup' => $grup,
                    'deskripsi' => "{$desc} {$module}",
                ]
            );
            $created++;
        }

        return redirect()->route('admin.permissions.index')
            ->with('success', "{$created} permission untuk modul '{$module}' berhasil dibuat.");
    }
}
