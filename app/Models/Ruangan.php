<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Ruangan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'ruangan';

    protected $fillable = [
        'kode',
        'nama',
        'kapasitas',
        'gedung',
        'lantai',
        'jenis',
    ];

    public function jadwalKuliah()
    {
        return $this->hasMany(JadwalKuliah::class);
    }
}
