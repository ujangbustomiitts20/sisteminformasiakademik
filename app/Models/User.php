<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HashidsTrait;

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
        'role',
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
}
