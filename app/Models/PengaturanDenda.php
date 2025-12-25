<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class PengaturanDenda extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'pengaturan_denda';

    protected $fillable = [
        'nama',
        'tipe',
        'nilai',
        'periode',
        'maksimal_denda',
        'grace_period',
        'is_active',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'maksimal_denda' => 'decimal:2',
        'grace_period' => 'integer',
        'is_active' => 'boolean',
    ];

    public const TIPE = [
        'Persen' => 'Persentase (%)',
        'Nominal' => 'Nominal (Rp)',
    ];

    public const PERIODE = [
        'Harian' => 'Per Hari',
        'Mingguan' => 'Per Minggu',
        'Bulanan' => 'Per Bulan',
    ];

    public function getNilaiLabelAttribute()
    {
        if ($this->tipe === 'Persen') {
            return $this->nilai . '% per ' . strtolower($this->periode);
        }
        return 'Rp ' . number_format($this->nilai, 0, ',', '.') . ' per ' . strtolower($this->periode);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
