<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Kecamatan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'kecamatan';

    protected $fillable = [
        'kabupaten_id',
        'kode',
        'nama',
    ];

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }

    public function kelurahan()
    {
        return $this->hasMany(Kelurahan::class);
    }

    public function sekolah()
    {
        return $this->hasMany(Sekolah::class);
    }
}
