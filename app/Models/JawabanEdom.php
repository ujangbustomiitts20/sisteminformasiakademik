<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanEdom extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'jawaban_edom';

    protected $fillable = [
        'periode_edom_id',
        'mahasiswa_id',
        'jadwal_kuliah_id',
        'dosen_id',
        'pertanyaan_edom_id',
        'nilai',
    ];

    protected $casts = [
        'nilai' => 'integer',
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

    public function pertanyaanEdom()
    {
        return $this->belongsTo(PertanyaanEdom::class);
    }

    public static function getNilaiOptions()
    {
        return [
            1 => 'Sangat Tidak Setuju',
            2 => 'Tidak Setuju',
            3 => 'Cukup',
            4 => 'Setuju',
            5 => 'Sangat Setuju',
        ];
    }
}
