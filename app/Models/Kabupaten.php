<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Kabupaten extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'kabupaten';

    protected $fillable = [
        'provinsi_id',
        'kode',
        'nama',
    ];

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class);
    }

    public function kecamatan()
    {
        return $this->hasMany(Kecamatan::class);
    }

    public function sekolah()
    {
        return $this->hasMany(Sekolah::class);
    }
}
