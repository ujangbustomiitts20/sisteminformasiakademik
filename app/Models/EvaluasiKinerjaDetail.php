<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiKinerjaDetail extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'evaluasi_kinerja_detail';

    protected $fillable = [
        'evaluasi_kinerja_id',
        'kriteria_evaluasi_id',
        'nilai',
        'keterangan',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    public function evaluasiKinerja()
    {
        return $this->belongsTo(EvaluasiKinerja::class);
    }

    public function kriteriaEvaluasi()
    {
        return $this->belongsTo(KriteriaEvaluasi::class);
    }

    public function getNilaiTerbobotAttribute(): float
    {
        $bobot = $this->kriteriaEvaluasi->bobot ?? 0;
        return $this->nilai * $bobot / 100;
    }
}
