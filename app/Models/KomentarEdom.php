<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomentarEdom extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'komentar_edom';

    protected $fillable = [
        'periode_edom_id',
        'mahasiswa_id',
        'jadwal_kuliah_id',
        'dosen_id',
        'komentar',
        'saran',
    ];

    public function periodeEdom()
    {
        return $this->belongsTo(PeriodeEdom::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function jadwalKuliah()
    {
        return $this->belongsTo(JadwalKuliah::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }
}
