<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifLembur extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'tarif_lembur';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'jenis_hari',
        'jenis_jam',
        'persentase',
        'nominal_tetap',
        'is_active',
    ];

    protected $casts = [
        'persentase' => 'decimal:2',
        'nominal_tetap' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    const JENIS_HARI = [
        'kerja' => 'Hari Kerja',
        'libur' => 'Hari Libur',
        'libur_nasional' => 'Hari Libur Nasional',
    ];

    const JENIS_JAM = [
        'jam_pertama' => 'Jam Pertama',
        'jam_kedua_dst' => 'Jam Kedua dst',
        'semua' => 'Semua Jam',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->kode)) {
                $model->kode = self::generateKode();
            }
        });
    }

    public static function generateKode(): string
    {
        $prefix = 'TL';
        $lastTarif = self::orderBy('id', 'desc')->first();
        $number = $lastTarif ? (int)substr($lastTarif->kode, -4) + 1 : 1;
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function pengajuanLembur()
    {
        return $this->hasMany(PengajuanLembur::class);
    }

    public function getJenisHariLabelAttribute(): string
    {
        return self::JENIS_HARI[$this->jenis_hari] ?? $this->jenis_hari;
    }

    public function getJenisJamLabelAttribute(): string
    {
        return self::JENIS_JAM[$this->jenis_jam] ?? $this->jenis_jam;
    }

    public function getJenisHariColorAttribute(): string
    {
        return match($this->jenis_hari) {
            'kerja' => 'primary',
            'libur' => 'warning',
            'libur_nasional' => 'danger',
            default => 'secondary',
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
