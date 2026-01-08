<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Role extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'roles';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'warna',
        'is_system',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Warna badge yang tersedia
    public const WARNA = [
        'primary' => 'Primary',
        'secondary' => 'Secondary',
        'success' => 'Success',
        'danger' => 'Danger',
        'warning' => 'Warning',
        'info' => 'Info',
        'dark' => 'Dark',
    ];

    // Role sistem bawaan
    public const SYSTEM_ROLES = [
        'admin' => 'Administrator',
        'dosen' => 'Dosen',
        'mahasiswa' => 'Mahasiswa',
        'kaprodi' => 'Kepala Program Studi',
        'dekan' => 'Dekan',
    ];

    /**
     * Get active roles
     */
    public static function getActive()
    {
        return static::where('is_active', true)->orderBy('urutan')->get();
    }

    /**
     * Get role by slug
     */
    public static function findBySlug(string $slug)
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Relationships
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission')
            ->withTimestamps();
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'role_menu')
            ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    /**
     * Check if role has permission
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    /**
     * Check if role has any of the given permissions
     */
    public function hasAnyPermission(array $permissionSlugs): bool
    {
        return $this->permissions()->whereIn('slug', $permissionSlugs)->exists();
    }

    /**
     * Get menu tree untuk role ini
     */
    public function getMenuTree()
    {
        return $this->menus()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('urutan')
            ->with(['children' => function ($query) {
                $query->whereIn('id', $this->menus()->pluck('menus.id'))
                    ->where('is_active', true)
                    ->orderBy('urutan');
            }])
            ->get();
    }

    /**
     * Accessors
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    public function getStatusTextAttribute(): string
    {
        return $this->is_active ? 'Aktif' : 'Nonaktif';
    }
}
