<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkpPegawai extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'skp_pegawai';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'tahun',
        'periode',
        'no_skp',
        'tanggal_skp',
        'jabatan',
        'unit_kerja',
        'atasan_penilai',
        'jabatan_penilai',
        'pejabat_penilai_id',
        'atasan_penilai_id',
        'nilai_skp',
        'nilai_perilaku',
        'orientasi_pelayanan',
        'integritas',
        'komitmen',
        'disiplin',
        'kerjasama',
        'kepemimpinan',
        'nilai_akhir',
        'predikat',
        'status',
        'catatan',
        'tanggal_penilaian',
        'file_skp',
    ];

    protected $casts = [
        'tanggal_skp' => 'date',
        'tanggal_penilaian' => 'date',
        'nilai_skp' => 'decimal:2',
        'nilai_perilaku' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
    ];

    const STATUS = [
        'draft' => 'Draft',
        'diajukan' => 'Diajukan',
        'disetujui' => 'Target Disetujui',
        'revisi' => 'Perlu Revisi',
        'realisasi' => 'Input Realisasi',
        'dinilai' => 'Dinilai',
        'final' => 'Final',
    ];

    const PREDIKAT = [
        'sangat_baik' => 'Sangat Baik',
        'baik' => 'Baik',
        'cukup' => 'Cukup',
        'kurang' => 'Kurang',
        'buruk' => 'Buruk',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->no_skp)) {
                $model->no_skp = self::generateNoSkp($model->tahun);
            }
            self::hitungNilaiAkhir($model);
        });

        static::updating(function ($model) {
            if ($model->isDirty(['nilai_skp', 'nilai_perilaku'])) {
                self::hitungNilaiAkhir($model);
            }
        });
    }

    public static function generateNoSkp($tahun)
    {
        $prefix = 'SKP/' . $tahun . '/';
        $count = self::where('tahun', $tahun)->count() + 1;
        return $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    protected static function hitungNilaiAkhir($model)
    {
        if ($model->nilai_skp && $model->nilai_perilaku) {
            // Bobot: SKP 60%, Perilaku 40%
            $model->nilai_akhir = ($model->nilai_skp * 0.6) + ($model->nilai_perilaku * 0.4);
            
            // Tentukan predikat
            if ($model->nilai_akhir >= 91) {
                $model->predikat = 'sangat_baik';
            } elseif ($model->nilai_akhir >= 76) {
                $model->predikat = 'baik';
            } elseif ($model->nilai_akhir >= 61) {
                $model->predikat = 'cukup';
            } elseif ($model->nilai_akhir >= 51) {
                $model->predikat = 'kurang';
            } else {
                $model->predikat = 'buruk';
            }
        }
    }

    // Relationships
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function pejabatPenilai()
    {
        return $this->belongsTo(Dosen::class, 'pejabat_penilai_id');
    }

    public function atasanPenilai()
    {
        return $this->belongsTo(Dosen::class, 'atasan_penilai_id');
    }

    public function targetSkp()
    {
        return $this->hasMany(TargetSkp::class);
    }

    // Accessors
    public function getNamaPegawaiAttribute()
    {
        if ($this->dosen_id) {
            return $this->dosen->nama_lengkap ?? $this->dosen->nama ?? '-';
        }
        return $this->pegawai->nama ?? '-';
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'draft' => 'secondary',
            'diajukan' => 'warning',
            'disetujui' => 'primary',
            'revisi' => 'danger',
            'realisasi' => 'info',
            'dinilai' => 'success',
            'final' => 'success',
            default => 'secondary',
        };
    }

    public function getPredikatBadgeAttribute()
    {
        return match ($this->predikat) {
            'sangat_baik' => 'success',
            'baik' => 'primary',
            'cukup' => 'warning',
            'kurang' => 'danger',
            'buruk' => 'dark',
            default => 'secondary',
        };
    }

    public function getPredikatLabelAttribute()
    {
        return self::PREDIKAT[$this->predikat] ?? '-';
    }

    public function getPeriodeFormatAttribute()
    {
        return $this->periode ?? ('Tahun ' . $this->tahun);
    }
}
