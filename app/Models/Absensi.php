<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Absensi extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'absensi';

    protected $fillable = [
        'krs_id',
        'pertemuan_id',
        'tanggal',
        'pertemuan',
        'status',
        'keterangan',
        'materi',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function krs()
    {
        return $this->belongsTo(Krs::class);
    }

    public function pertemuanData()
    {
        return $this->belongsTo(Pertemuan::class, 'pertemuan_id');
    }
}
