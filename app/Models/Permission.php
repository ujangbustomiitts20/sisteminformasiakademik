<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Permission extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'permissions';

    protected $fillable = [
        'nama',
        'slug',
        'grup',
        'deskripsi',
    ];

    // Grup permission
    public const GRUP = [
        'dashboard' => 'Dashboard',
        'akademik' => 'Akademik',
        'mahasiswa' => 'Mahasiswa',
        'dosen' => 'Dosen',
        'keuangan' => 'Keuangan',
        'kepegawaian' => 'Kepegawaian',
        'pmb' => 'PMB',
        'master' => 'Master Data',
        'pengaturan' => 'Pengaturan',
        'laporan' => 'Laporan',
    ];

    /**
     * Get permissions by grup
     */
    public static function getByGrup(string $grup)
    {
        return static::where('grup', $grup)->orderBy('nama')->get();
    }

    /**
     * Get all permissions grouped
     */
    public static function getAllGrouped()
    {
        return static::orderBy('grup')->orderBy('nama')->get()->groupBy('grup');
    }

    /**
     * Relationships
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission')
            ->withTimestamps();
    }
}
