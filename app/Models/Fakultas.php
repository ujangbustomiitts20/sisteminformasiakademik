<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Fakultas extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'fakultas';

    protected $fillable = [
        'kode',
        'nama',
        'dekan',
    ];

    public function programStudi()
    {
        return $this->hasMany(ProgramStudi::class);
    }
}
