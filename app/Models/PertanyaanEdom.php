<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PertanyaanEdom extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'pertanyaan_edom';

    protected $fillable = [
        'kode',
        'pertanyaan',
        'kategori',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function jawabanEdom()
    {
        return $this->hasMany(JawabanEdom::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public static function getKategoriOptions()
    {
        return [
            'kompetensi_pedagogik' => 'Kompetensi Pedagogik',
            'kompetensi_profesional' => 'Kompetensi Profesional',
            'kompetensi_kepribadian' => 'Kompetensi Kepribadian',
            'kompetensi_sosial' => 'Kompetensi Sosial',
        ];
    }
}
