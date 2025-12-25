<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Tarif extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'tarif';

    protected $fillable = [
        'program_studi_id',
        'angkatan',
        'jenis',
        'nama_tarif',
        'nominal',
        'periode',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public const JENIS = [
        'SPP' => 'SPP (Sumbangan Pembinaan Pendidikan)',
        'Herregistrasi' => 'Herregistrasi',
        'Praktikum' => 'Biaya Praktikum',
        'SKS' => 'Biaya Per SKS',
        'Wisuda' => 'Biaya Wisuda',
        'Almamater' => 'Biaya Almamater',
        'KKN' => 'Biaya KKN',
        'PKL' => 'Biaya PKL/Magang',
        'Lainnya' => 'Lainnya',
    ];

    public const PERIODE = [
        'Semester' => 'Per Semester',
        'Tahunan' => 'Per Tahun',
        'Sekali' => 'Sekali Bayar',
    ];

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class);
    }

    public function getJenisLabelAttribute()
    {
        return self::JENIS[$this->jenis] ?? $this->jenis;
    }

    public function getPeriodeLabelAttribute()
    {
        return self::PERIODE[$this->periode] ?? $this->periode;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForProdi($query, $prodiId)
    {
        return $query->where(function($q) use ($prodiId) {
            $q->where('program_studi_id', $prodiId)
              ->orWhereNull('program_studi_id');
        });
    }

    public function scopeForAngkatan($query, $angkatan)
    {
        return $query->where(function($q) use ($angkatan) {
            $q->where('angkatan', $angkatan)
              ->orWhereNull('angkatan');
        });
    }
}
