<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestimoniPmb extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'testimoni_pmb';

    protected $fillable = [
        'nama',
        'angkatan',
        'program_studi',
        'testimoni',
        'foto',
        'pekerjaan',
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

    public function getFotoUrlAttribute()
    {
        if ($this->foto && file_exists(public_path('storage/' . $this->foto))) {
            return asset('storage/' . $this->foto);
        }
        return asset('images/testimoni-default.jpg');
    }
}
