<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Provinsi extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'provinsi';

    protected $fillable = [
        'kode',
        'nama',
    ];

    public function kabupaten()
    {
        return $this->hasMany(Kabupaten::class);
    }

    public function sekolah()
    {
        return $this->hasMany(Sekolah::class);
    }
}
