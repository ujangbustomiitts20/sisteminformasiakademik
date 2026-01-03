<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KuotaPmb extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'kuota_pmb';

    protected $fillable = [
        'gelombang_pmb_id',
        'program_studi_id',
        'jalur_seleksi_id',
        'kuota',
        'terisi',
        'passing_grade',
    ];

    protected $casts = [
        'kuota' => 'integer',
        'terisi' => 'integer',
        'passing_grade' => 'decimal:2',
    ];

    // Relationships
    public function gelombangPmb()
    {
        return $this->belongsTo(GelombangPmb::class, 'gelombang_pmb_id');
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'program_studi_id');
    }

    public function jalurSeleksi()
    {
        return $this->belongsTo(JalurSeleksi::class, 'jalur_seleksi_id');
    }

    // Helper Methods
    public function getSisaKuotaAttribute()
    {
        return max(0, $this->kuota - $this->terisi);
    }

    public function getPersentaseTerisiAttribute()
    {
        if ($this->kuota == 0) return 0;
        return round(($this->terisi / $this->kuota) * 100, 1);
    }

    public function isKuotaPenuh()
    {
        return $this->terisi >= $this->kuota;
    }

    public static function getKuota($gelombangId, $prodiId, $jalurId)
    {
        return self::where('gelombang_pmb_id', $gelombangId)
                   ->where('program_studi_id', $prodiId)
                   ->where('jalur_seleksi_id', $jalurId)
                   ->first();
    }

    public static function incrementTerisi($gelombangId, $prodiId, $jalurId)
    {
        $kuota = self::getKuota($gelombangId, $prodiId, $jalurId);
        if ($kuota) {
            $kuota->increment('terisi');
        }
    }

    public static function decrementTerisi($gelombangId, $prodiId, $jalurId)
    {
        $kuota = self::getKuota($gelombangId, $prodiId, $jalurId);
        if ($kuota && $kuota->terisi > 0) {
            $kuota->decrement('terisi');
        }
    }
}
