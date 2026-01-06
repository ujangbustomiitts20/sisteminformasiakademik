<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetSkp extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'target_skp';

    protected $fillable = [
        'skp_pegawai_id',
        'urutan',
        'uraian_kegiatan',
        'satuan',
        'target_kuantitas',
        'target_kualitas',
        'target_waktu',
        'target_biaya',
        'realisasi_kuantitas',
        'realisasi_kualitas',
        'realisasi_waktu',
        'realisasi_biaya',
        'nilai_capaian',
        'keterangan',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'target_kuantitas' => 'decimal:2',
        'target_kualitas' => 'decimal:2',
        'target_waktu' => 'decimal:2',
        'target_biaya' => 'decimal:2',
        'realisasi_kuantitas' => 'decimal:2',
        'realisasi_kualitas' => 'decimal:2',
        'realisasi_waktu' => 'decimal:2',
        'realisasi_biaya' => 'decimal:2',
        'nilai_capaian' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            self::hitungNilaiCapaian($model);
        });

        static::updating(function ($model) {
            self::hitungNilaiCapaian($model);
        });
    }

    protected static function hitungNilaiCapaian($model)
    {
        $capaian = [];

        // Kuantitas
        if ($model->target_kuantitas > 0 && $model->realisasi_kuantitas !== null) {
            $capaian[] = ($model->realisasi_kuantitas / $model->target_kuantitas) * 100;
        }

        // Kualitas
        if ($model->target_kualitas > 0 && $model->realisasi_kualitas !== null) {
            $capaian[] = ($model->realisasi_kualitas / $model->target_kualitas) * 100;
        }

        // Waktu (invers - semakin cepat semakin baik)
        if ($model->target_waktu > 0 && $model->realisasi_waktu !== null) {
            $capaianWaktu = (1.76 * $model->target_waktu - $model->realisasi_waktu) / $model->target_waktu * 100;
            $capaian[] = max(0, min(100, $capaianWaktu));
        }

        if (!empty($capaian)) {
            $model->nilai_capaian = array_sum($capaian) / count($capaian);
        }
    }

    // Relationships
    public function skpPegawai()
    {
        return $this->belongsTo(SkpPegawai::class);
    }

    // Accessors
    public function getCapaianKuantitasAttribute()
    {
        if ($this->target_kuantitas > 0 && $this->realisasi_kuantitas !== null) {
            return ($this->realisasi_kuantitas / $this->target_kuantitas) * 100;
        }
        return null;
    }

    public function getCapaianKualitasAttribute()
    {
        if ($this->target_kualitas > 0 && $this->realisasi_kualitas !== null) {
            return ($this->realisasi_kualitas / $this->target_kualitas) * 100;
        }
        return null;
    }

    public function getNilaiCapaianBadgeAttribute()
    {
        if ($this->nilai_capaian >= 91) return 'success';
        if ($this->nilai_capaian >= 76) return 'primary';
        if ($this->nilai_capaian >= 61) return 'warning';
        if ($this->nilai_capaian >= 51) return 'danger';
        return 'dark';
    }
}
