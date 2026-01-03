<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeEdom extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'periode_edom';

    protected $fillable = [
        'nama',
        'tahun_akademik_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'deskripsi',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function jawabanEdom()
    {
        return $this->hasMany(JawabanEdom::class);
    }

    public function rekapEdom()
    {
        return $this->hasMany(RekapEdom::class);
    }

    public function komentarEdom()
    {
        return $this->hasMany(KomentarEdom::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function isAktif()
    {
        return $this->status === 'aktif' 
            && now()->between($this->tanggal_mulai, $this->tanggal_selesai);
    }
}
