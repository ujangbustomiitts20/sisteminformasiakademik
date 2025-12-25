<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KalenderAkademik extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_akademik_id',
        'judul',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
        'warna',
        'jenis',
        'is_active'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean'
    ];

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('tanggal_mulai', '>=', now()->format('Y-m-d'));
    }

    public function scopeCurrent($query)
    {
        return $query->where('tanggal_mulai', '<=', now()->format('Y-m-d'))
            ->where(function($q) {
                $q->where('tanggal_selesai', '>=', now()->format('Y-m-d'))
                  ->orWhereNull('tanggal_selesai');
            });
    }
}
