<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeEvaluasi extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'periode_evaluasi';

    protected $fillable = [
        'nama',
        'tahun',
        'semester',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    const STATUS = [
        'draft' => 'Draft',
        'aktif' => 'Aktif',
        'selesai' => 'Selesai',
    ];

    public function evaluasiKinerja()
    {
        return $this->hasMany(EvaluasiKinerja::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'secondary',
            'aktif' => 'success',
            'selesai' => 'info',
            default => 'secondary',
        };
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public static function getAktif()
    {
        return self::where('status', 'aktif')->first();
    }
}
