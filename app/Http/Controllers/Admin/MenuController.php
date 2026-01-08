<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    /**
     * Display a listing of menus.
     */
    public function index(Request $request)
    {
        $query = Menu::with(['parent', 'children', 'roles']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        // Filter by parent (root or specific parent)
        if ($request->filled('parent')) {
            if ($request->parent === 'root') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', Menu::decodeHashid($request->parent));
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('route_name', 'like', "%{$request->search}%");
            });
        }

        $menus = $query->orderBy('urutan')->orderBy('nama')->paginate(20);

        // Get menu tree for sidebar preview
        $menuTree = Menu::getMenuTree();

        // Get parent menus for filter
        $parentMenus = Menu::whereNull('parent_id')->orderBy('urutan')->get();

        // Stats
        $stats = [
            'total' => Menu::count(),
            'aktif' => Menu::where('is_active', true)->count(),
            'parent' => Menu::whereNull('parent_id')->count(),
            'child' => Menu::whereNotNull('parent_id')->count(),
        ];

        return view('admin.menus.index', compact('menus', 'menuTree', 'parentMenus', 'stats'));
    }

    /**
     * Show form for creating a new menu.
     */
    public function create()
    {
        $parentMenus = Menu::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();
        $permissions = Permission::orderBy('grup')->orderBy('nama')->get();
        $roles = Role::where('is_active', true)->orderBy('urutan')->get();
        $icons = Menu::COMMON_ICONS;
        $badgeColors = Menu::BADGE_COLORS;

        return view('admin.menus.create', compact('parentMenus', 'permissions', 'roles', 'icons', 'badgeColors'));
    }

    /**
     * Store a newly created menu.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:menus,id',
            'icon' => 'nullable|string|max:50',
            'route_name' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'permission_slug' => 'nullable|exists:permissions,slug',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_divider' => 'boolean',
            'badge_text' => 'nullable|string|max:20',
            'badge_color' => 'nullable|string|max:20',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_divider'] = $request->has('is_divider');

        // Set default urutan
        if (empty($validated['urutan'])) {
            $maxUrutan = Menu::where('parent_id', $validated['parent_id'] ?? null)->max('urutan');
            $validated['urutan'] = ($maxUrutan ?? 0) + 1;
        }

        $menu = Menu::create($validated);

        // Sync roles
        if ($request->has('roles')) {
            $menu->roles()->sync($request->roles);
        }

        return redirect()->route('admin.menus.index')
            ->with('success', "Menu '{$menu->nama}' berhasil dibuat.");
    }

    /**
     * Display the specified menu.
     */
    public function show(Menu $menu)
    {
        $menu->load(['parent', 'children', 'roles']);
        
        return view('admin.menus.show', compact('menu'));
    }

    /**
     * Show form for editing the menu.
     */
    public function edit(Menu $menu)
    {
        $parentMenus = Menu::whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();
        $permissions = Permission::orderBy('grup')->orderBy('nama')->get();
        $roles = Role::where('is_active', true)->orderBy('urutan')->get();
        $icons = Menu::COMMON_ICONS;
        $badgeColors = Menu::BADGE_COLORS;
        $menuRoles = $menu->roles->pluck('id')->toArray();

        return view('admin.menus.edit', compact('menu', 'parentMenus', 'permissions', 'roles', 'icons', 'badgeColors', 'menuRoles'));
    }

    /**
     * Update the specified menu.
     */
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'parent_id' => ['nullable', 'exists:menus,id', Rule::notIn([$menu->id])],
            'icon' => 'nullable|string|max:50',
            'route_name' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'permission_slug' => 'nullable|exists:permissions,slug',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_divider' => 'boolean',
            'badge_text' => 'nullable|string|max:20',
            'badge_color' => 'nullable|string|max:20',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_divider'] = $request->has('is_divider');

        // Prevent circular parent reference
        if (!empty($validated['parent_id'])) {
            $childIds = $menu->children->pluck('id')->toArray();
            if (in_array($validated['parent_id'], $childIds)) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menjadikan child menu sebagai parent.');
            }
        }

        $menu->update($validated);

        // Sync roles
        $menu->roles()->sync($request->roles ?? []);

        return redirect()->route('admin.menus.index')
            ->with('success', "Menu '{$menu->nama}' berhasil diperbarui.");
    }

    /**
     * Remove the specified menu.
     */
    public function destroy(Menu $menu)
    {
        // Check if has children
        if ($menu->children()->count() > 0) {
            return redirect()->route('admin.menus.index')
                ->with('error', 'Menu tidak dapat dihapus karena memiliki submenu.');
        }

        $menuName = $menu->nama;
        $menu->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', "Menu '{$menuName}' berhasil dihapus.");
    }

    /**
     * Toggle menu status.
     */
    public function toggleStatus(Menu $menu)
    {
        $menu->update(['is_active' => !$menu->is_active]);

        $status = $menu->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()
            ->with('success', "Menu '{$menu->nama}' berhasil {$status}.");
    }

    /**
     * Reorder menus via AJAX.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menus,id',
            'items.*.urutan' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            Menu::where('id', $item['id'])->update(['urutan' => $item['urutan']]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan menu berhasil diperbarui.']);
    }

    /**
     * Get menus for a specific role (AJAX).
     */
    public function getByRole(Role $role)
    {
        $menus = $role->getMenuTree();
        
        return response()->json([
            'success' => true,
            'menus' => $menus,
        ]);
    }

    /**
     * Preview menu structure.
     */
    public function preview(Request $request)
    {
        $roleId = $request->role_id;
        
        if ($roleId) {
            $role = Role::findByHashid($roleId);
            $menus = $role ? $role->getMenuTree() : collect();
        } else {
            $menus = Menu::getMenuTree();
        }

        return view('admin.menus.preview', compact('menus'));
    }
}
