<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Menu extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'menus';

    protected $fillable = [
        'parent_id',
        'nama',
        'icon',
        'route_name',
        'url',
        'permission_slug',
        'urutan',
        'is_active',
        'is_divider',
        'badge_text',
        'badge_color',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_divider' => 'boolean',
    ];

    // Warna badge
    public const BADGE_COLORS = [
        'primary' => 'Primary',
        'secondary' => 'Secondary',
        'success' => 'Success',
        'danger' => 'Danger',
        'warning' => 'Warning',
        'info' => 'Info',
    ];

    // Icon yang sering digunakan
    public const COMMON_ICONS = [
        'bi-speedometer2' => 'Dashboard',
        'bi-people' => 'Users/Mahasiswa',
        'bi-person-badge' => 'Dosen/Pegawai',
        'bi-book' => 'Akademik/Buku',
        'bi-calendar' => 'Kalender/Jadwal',
        'bi-cash-stack' => 'Keuangan',
        'bi-briefcase' => 'Kepegawaian',
        'bi-mortarboard' => 'PMB/Wisuda',
        'bi-gear' => 'Pengaturan',
        'bi-file-earmark-text' => 'Laporan/Dokumen',
        'bi-building' => 'Gedung/Ruangan',
        'bi-grid' => 'Master Data',
        'bi-person-lines-fill' => 'Profil',
        'bi-clipboard-data' => 'Nilai/Data',
        'bi-journal-text' => 'KRS',
        'bi-card-checklist' => 'Absensi',
        'bi-graph-up' => 'Statistik',
        'bi-shield-check' => 'Role/Permission',
        'bi-list' => 'Menu',
        'bi-folder' => 'Folder',
        'bi-house' => 'Home',
        'bi-box-arrow-right' => 'Logout',
    ];

    /**
     * Get active menus
     */
    public static function getActive()
    {
        return static::where('is_active', true)->orderBy('urutan')->get();
    }

    /**
     * Get root menus (without parent)
     */
    public static function getRootMenus()
    {
        return static::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();
    }

    /**
     * Get full menu tree
     */
    public static function getMenuTree()
    {
        return static::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('urutan')
            ->with(['children' => function ($query) {
                $query->where('is_active', true)->orderBy('urutan');
            }])
            ->get();
    }

    /**
     * Relationships
     */
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('urutan');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_menu')
            ->withTimestamps();
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_slug', 'slug');
    }

    /**
     * Get URL for menu item
     */
    public function getUrlAttribute(): ?string
    {
        // Check for custom URL first
        $rawUrl = $this->getRawOriginal('url');
        if (!empty($rawUrl)) {
            return $rawUrl;
        }

        // Generate URL from route name
        if ($this->route_name && \Route::has($this->route_name)) {
            try {
                return route($this->route_name);
            } catch (\Exception $e) {
                return '#';
            }
        }

        return '#';
    }

    /**
     * Check if menu is active based on current route
     */
    public function isActive(): bool
    {
        if ($this->route_name && request()->routeIs($this->route_name . '*')) {
            return true;
        }

        // Check if any child is active
        foreach ($this->children as $child) {
            if ($child->isActive()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can access this menu
     */
    public function canAccess($user = null): bool
    {
        $user = $user ?? auth()->user();
        
        if (!$user) {
            return false;
        }

        // Admin can access everything
        if ($user->role === 'admin' || $user->isSuperAdmin()) {
            return true;
        }

        // If no permission required, allow access
        if (empty($this->permission_slug)) {
            return true;
        }

        // Check user permissions
        return $user->hasPermission($this->permission_slug);
    }

    /**
     * Accessors
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    public function getFullPathAttribute(): string
    {
        $path = $this->nama;
        $parent = $this->parent;
        
        while ($parent) {
            $path = $parent->nama . ' > ' . $path;
            $parent = $parent->parent;
        }
        
        return $path;
    }
}
