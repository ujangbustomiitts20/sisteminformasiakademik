<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'unit_kerja';

    protected $fillable = [
        'kode',
        'nama',
        'parent_id',
        'kepala_id',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(UnitKerja::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(UnitKerja::class, 'parent_id');
    }

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class);
    }

    public function kepala()
    {
        return $this->belongsTo(Pegawai::class, 'kepala_id');
    }
}
