<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiKinerja extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'evaluasi_kinerja';

    protected $fillable = [
        'periode_evaluasi_id',
        'dosen_id',
        'pegawai_id',
        'penilai_id',
        'nilai_total',
        'predikat',
        'catatan',
        'rekomendasi',
        'status',
        'tanggal_penilaian',
    ];

    protected $casts = [
        'nilai_total' => 'decimal:2',
        'tanggal_penilaian' => 'datetime',
    ];

    const PREDIKAT = [
        'sangat_baik' => 'Sangat Baik',
        'baik' => 'Baik',
        'cukup' => 'Cukup',
        'kurang' => 'Kurang',
        'sangat_kurang' => 'Sangat Kurang',
    ];

    const STATUS = [
        'draft' => 'Draft',
        'diajukan' => 'Diajukan',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            // Auto calculate predikat based on nilai_total
            $model->predikat = self::calculatePredikat($model->nilai_total);
        });
    }

    public static function calculatePredikat($nilai): string
    {
        if ($nilai >= 90) return 'sangat_baik';
        if ($nilai >= 75) return 'baik';
        if ($nilai >= 60) return 'cukup';
        if ($nilai >= 40) return 'kurang';
        return 'sangat_kurang';
    }

    public function periodeEvaluasi()
    {
        return $this->belongsTo(PeriodeEvaluasi::class);
    }

    // Alias untuk kompatibilitas view
    public function periode()
    {
        return $this->periodeEvaluasi();
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function penilai()
    {
        return $this->belongsTo(User::class, 'penilai_id');
    }

    public function details()
    {
        return $this->hasMany(EvaluasiKinerjaDetail::class);
    }

    public function getNamaPegawaiAttribute(): string
    {
        if ($this->dosen_id && $this->dosen) {
            return $this->dosen->nama;
        }
        if ($this->pegawai_id && $this->pegawai) {
            return $this->pegawai->nama;
        }
        return '-';
    }

    public function getPredikatLabelAttribute(): string
    {
        return self::PREDIKAT[$this->predikat] ?? $this->predikat ?? '-';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'secondary',
            'diajukan' => 'warning',
            'disetujui' => 'success',
            'ditolak' => 'danger',
            default => 'secondary',
        };
    }

    public function getPredikatColorAttribute(): string
    {
        return match($this->predikat) {
            'sangat_baik' => 'success',
            'baik' => 'info',
            'cukup' => 'warning',
            'kurang' => 'danger',
            'sangat_kurang' => 'dark',
            default => 'secondary',
        };
    }

    public function hitungNilaiTotal()
    {
        $total = 0;
        foreach ($this->details as $detail) {
            $bobot = $detail->kriteriaEvaluasi->bobot ?? 0;
            $total += ($detail->nilai * $bobot / 100);
        }
        $this->nilai_total = $total;
        $this->save();
        
        return $total;
    }
}
