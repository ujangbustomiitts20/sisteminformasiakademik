<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodePmb extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'periode_pmb';

    protected $fillable = [
        'nama',
        'tahun_akademik',
        'tanggal_mulai',
        'tanggal_selesai',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function gelombang()
    {
        return $this->hasMany(GelombangPmb::class, 'periode_pmb_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBerjalan($query)
    {
        return $query->where('tanggal_mulai', '<=', now())
                     ->where('tanggal_selesai', '>=', now());
    }

    // Helper Methods
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }

    public function isOpen()
    {
        $now = now();
        return $this->tanggal_mulai <= $now && $this->tanggal_selesai >= $now;
    }

    public function getStatusAttribute()
    {
        $now = now();
        if ($this->tanggal_mulai > $now) {
            return 'Akan Datang';
        } elseif ($this->tanggal_selesai < $now) {
            return 'Selesai';
        }
        return 'Berjalan';
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'Berjalan' => 'success',
            'Akan Datang' => 'info',
            'Selesai' => 'secondary',
            default => 'secondary',
        };
    }
}
