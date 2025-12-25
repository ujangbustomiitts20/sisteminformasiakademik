<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Pengumuman extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'pengumuman';

    protected $fillable = [
        'user_id',
        'judul',
        'isi',
        'kategori',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_aktif',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_aktif' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk pengumuman yang aktif
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true)
            ->where('tanggal_mulai', '<=', now())
            ->where(function($q) {
                $q->whereNull('tanggal_selesai')
                    ->orWhere('tanggal_selesai', '>=', now());
            });
    }
}
