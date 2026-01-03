<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GaleriPmb extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'galeri_pmb';

    protected $fillable = [
        'judul',
        'deskripsi',
        'gambar',
        'kategori',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }

    public function scopeKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function getGambarUrlAttribute()
    {
        if ($this->gambar && file_exists(public_path('storage/' . $this->gambar))) {
            return asset('storage/' . $this->gambar);
        }
        return asset('images/galeri-default.jpg');
    }

    public static function getByKategori()
    {
        return self::active()->get()->groupBy('kategori');
    }
}
