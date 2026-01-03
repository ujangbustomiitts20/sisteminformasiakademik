<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Kelurahan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'kelurahan';

    protected $fillable = [
        'kecamatan_id',
        'kode',
        'nama',
        'kode_pos',
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}
