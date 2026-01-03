<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Sekolah extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'sekolah';

    protected $fillable = [
        'npsn',
        'nama',
        'jenjang',
        'status',
        'provinsi_id',
        'kabupaten_id',
        'kecamatan_id',
        'alamat',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class);
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class);
    }

    // Scope untuk sekolah aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessor untuk nama lengkap dengan lokasi
    public function getNamaLengkapAttribute()
    {
        $parts = [$this->nama];
        if ($this->kabupaten) {
            $parts[] = $this->kabupaten->nama;
        }
        return implode(' - ', $parts);
    }
}
