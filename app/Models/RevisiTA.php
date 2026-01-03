<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevisiTA extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'revisi_ta';

    protected $fillable = [
        'sidang_ta_id',
        'dosen_id',
        'catatan_revisi',
        'sudah_diperbaiki',
        'tanggal_perbaikan',
        'catatan_perbaikan',
    ];

    protected $casts = [
        'sudah_diperbaiki' => 'boolean',
        'tanggal_perbaikan' => 'datetime',
    ];

    public function sidangTA()
    {
        return $this->belongsTo(SidangTA::class, 'sidang_ta_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }
}
