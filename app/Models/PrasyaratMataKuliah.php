<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrasyaratMataKuliah extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'prasyarat_mata_kuliah';

    protected $fillable = [
        'mata_kuliah_id',
        'mata_kuliah_prasyarat_id',
        'jenis_prasyarat',
        'nilai_minimal',
    ];

    /**
     * Mata kuliah yang memiliki prasyarat ini
     */
    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    /**
     * Mata kuliah yang menjadi prasyarat
     */
    public function mataKuliahPrasyarat()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_prasyarat_id');
    }

    /**
     * Get badge color for jenis prasyarat
     */
    public function getJenisBadgeAttribute()
    {
        return $this->jenis_prasyarat === 'wajib' ? 'danger' : 'warning';
    }

    /**
     * Get label jenis prasyarat
     */
    public function getJenisLabelAttribute()
    {
        return $this->jenis_prasyarat === 'wajib' ? 'Wajib Lulus' : 'Pernah Ambil';
    }
}
