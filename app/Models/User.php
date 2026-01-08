<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HashidsTrait;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HashidsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Legacy role field - kept for backward compatibility
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function dosen()
    {
        return $this->hasOne(Dosen::class);
    }

    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a Dosen (has dosen data attached)
     * This returns true for any user with dosen data, regardless of their role
     */
    public function isDosen()
    {
        return $this->role === 'dosen' || $this->dosen()->exists();
    }

    /**
     * Check if user has 'dosen' as their primary role
     */
    public function hasDosenRole()
    {
        return $this->role === 'dosen';
    }

    public function isMahasiswa()
    {
        return $this->role === 'mahasiswa';
    }

    /**
     * Check if user is Kaprodi (either by role or by being assigned as kaprodi in program_studi)
     */
    public function isKaprodi()
    {
        if ($this->role === 'kaprodi') {
            return true;
        }
        
        // Check if this user's dosen is assigned as kaprodi
        if ($this->dosen) {
            return ProgramStudi::where('kaprodi', $this->dosen->nama)->exists();
        }
        
        return false;
    }

    /**
     * Check if user is Dekan (either by role or by being assigned as dekan in fakultas)
     */
    public function isDekan()
    {
        if ($this->role === 'dekan') {
            return true;
        }
        
        // Check if this user's dosen is assigned as dekan
        if ($this->dosen) {
            return Fakultas::where('dekan', $this->dosen->nama)->exists();
        }
        
        return false;
    }

    /**
     * Get the fakultas where this user is dekan
     */
    public function getFakultasDekan()
    {
        if (!$this->dosen) {
            return null;
        }
        return Fakultas::where('dekan', $this->dosen->nama)->first();
    }

    /**
     * Get the program studi where this user is kaprodi
     */
    public function getProdiKaprodi()
    {
        if (!$this->dosen) {
            return null;
        }
        return ProgramStudi::where('kaprodi', $this->dosen->nama)->first();
    }

    /**
     * Check if user has any additional role (dekan/kaprodi) besides their main role
     */
    public function hasAdditionalRoles()
    {
        return $this->isDekan() || $this->isKaprodi();
    }

    /**
     * Check if user can access dosen features (is dosen or has dosen data)
     */
    public function canAccessDosenFeatures()
    {
        return $this->dosen()->exists();
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Dynamic Roles Relationship
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    /**
     * Get primary role
     */
    public function getPrimaryRole()
    {
        return $this->roles()->wherePivot('is_primary', true)->first()
            ?? $this->roles()->first();
    }

    /**
     * Get all role slugs
     */
    public function getRoleSlugs(): array
    {
        return Cache::remember("user_{$this->id}_role_slugs", 300, function () {
            $slugs = $this->roles()->pluck('slug')->toArray();
            
            // Include legacy role if set
            if ($this->role && !in_array($this->role, $slugs)) {
                $slugs[] = $this->role;
            }
            
            return $slugs;
        });
    }

    /**
     * Check if user has specific role (dynamic or legacy)
     */
    public function hasRole(string $roleSlug): bool
    {
        // Check legacy role first
        if ($this->role === $roleSlug) {
            return true;
        }
        
        // Check dynamic roles
        return in_array($roleSlug, $this->getRoleSlugs());
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roleSlugs): bool
    {
        foreach ($roleSlugs as $slug) {
            if ($this->hasRole($slug)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Assign role to user
     */
    public function assignRole(string $roleSlug, bool $isPrimary = false): void
    {
        $role = Role::findBySlug($roleSlug);
        
        if (!$role) {
            return;
        }

        // If setting as primary, remove primary from other roles
        if ($isPrimary) {
            $this->roles()->updateExistingPivot(
                $this->roles()->pluck('id')->toArray(),
                ['is_primary' => false]
            );
        }

        $this->roles()->syncWithoutDetaching([
            $role->id => ['is_primary' => $isPrimary]
        ]);

        // Clear cache
        Cache::forget("user_{$this->id}_role_slugs");
        Cache::forget("user_{$this->id}_permissions");
        Cache::forget("user_{$this->id}_menus");
    }

    /**
     * Remove role from user
     */
    public function removeRole(string $roleSlug): void
    {
        $role = Role::findBySlug($roleSlug);
        
        if ($role) {
            $this->roles()->detach($role->id);
            Cache::forget("user_{$this->id}_role_slugs");
            Cache::forget("user_{$this->id}_permissions");
            Cache::forget("user_{$this->id}_menus");
        }
    }

    /**
     * Sync roles for user
     */
    public function syncRoles(array $roleSlugs, ?string $primarySlug = null): void
    {
        $roleIds = Role::whereIn('slug', $roleSlugs)->pluck('id');
        
        $syncData = [];
        foreach ($roleIds as $roleId) {
            $role = Role::find($roleId);
            $syncData[$roleId] = ['is_primary' => $role->slug === $primarySlug];
        }
        
        $this->roles()->sync($syncData);
        
        Cache::forget("user_{$this->id}_role_slugs");
        Cache::forget("user_{$this->id}_permissions");
        Cache::forget("user_{$this->id}_menus");
    }

    /**
     * Get all permissions for user (from all roles)
     */
    public function getAllPermissions()
    {
        return Cache::remember("user_{$this->id}_permissions", 300, function () {
            $permissions = collect();
            
            foreach ($this->roles as $role) {
                $permissions = $permissions->merge($role->permissions);
            }
            
            return $permissions->unique('id');
        });
    }

    /**
     * Get all permission slugs from user's menus
     * This derives permissions from menu access
     */
    public function getMenuPermissions(): array
    {
        return Cache::remember("user_{$this->id}_menu_permissions", 300, function () {
            $permissions = [];
            $menus = $this->getMenus();
            
            foreach ($menus as $menu) {
                // Add permission from menu's permission_slug
                if ($menu->permission_slug) {
                    $permissions[] = $menu->permission_slug;
                }
                
                // Derive permission from route_name (e.g., pmb.dashboard -> pmb.*)
                if ($menu->route_name) {
                    $prefix = explode('.', $menu->route_name)[0];
                    $permissions[] = $prefix . '.*';  // wildcard access
                    $permissions[] = $menu->route_name;
                }
                
                // Check children menus
                if ($menu->children) {
                    foreach ($menu->children as $child) {
                        if ($child->permission_slug) {
                            $permissions[] = $child->permission_slug;
                        }
                        if ($child->route_name) {
                            $prefix = explode('.', $child->route_name)[0];
                            $permissions[] = $prefix . '.*';
                            $permissions[] = $child->route_name;
                        }
                    }
                }
            }
            
            return array_unique($permissions);
        });
    }

    /**
     * Check if user has permission
     * Checks both explicit permissions AND menu-derived permissions
     */
    public function hasPermission(string $permissionSlug): bool
    {
        // Admin has all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Check explicit permissions first
        if ($this->getAllPermissions()->contains('slug', $permissionSlug)) {
            return true;
        }

        // Check menu-derived permissions
        $menuPermissions = $this->getMenuPermissions();
        
        // Direct match
        if (in_array($permissionSlug, $menuPermissions)) {
            return true;
        }
        
        // Wildcard match (e.g., pmb.* matches pmb.gelombang)
        $prefix = explode('.', $permissionSlug)[0];
        if (in_array($prefix . '.*', $menuPermissions)) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissionSlugs): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $userPermissions = $this->getAllPermissions()->pluck('slug')->toArray();
        return !empty(array_intersect($permissionSlugs, $userPermissions));
    }

    /**
     * Check if user is super admin (has all access)
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'admin' || $this->hasRole('admin');
    }

    /**
     * Get menus for user based on roles
     */
    public function getMenus()
    {
        return Cache::remember("user_{$this->id}_menus", 300, function () {
            $menuIds = collect();
            
            foreach ($this->roles as $role) {
                $menuIds = $menuIds->merge($role->menus()->pluck('menus.id'));
            }
            
            return Menu::whereIn('id', $menuIds->unique())
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('urutan')
                ->with(['children' => function ($query) use ($menuIds) {
                    $query->whereIn('id', $menuIds->unique())
                        ->where('is_active', true)
                        ->orderBy('urutan');
                }])
                ->get();
        });
    }

    /**
     * Clear user's permission cache
     */
    public function clearPermissionCache(): void
    {
        Cache::forget("user_{$this->id}_role_slugs");
        Cache::forget("user_{$this->id}_permissions");
        Cache::forget("user_{$this->id}_menus");
        Cache::forget("user_{$this->id}_menu_permissions");
    }
}
