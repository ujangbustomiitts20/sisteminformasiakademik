<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiayaPendaftaran extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'biaya_pendaftaran';

    protected $fillable = [
        'gelombang_pmb_id',
        'jalur_seleksi_id',
        'program_studi_id',
        'biaya_formulir',
        'biaya_ujian',
        'total_biaya',
    ];

    protected $casts = [
        'biaya_formulir' => 'decimal:2',
        'biaya_ujian' => 'decimal:2',
        'total_biaya' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->total_biaya = $model->biaya_formulir + $model->biaya_ujian;
        });

        static::updating(function ($model) {
            $model->total_biaya = $model->biaya_formulir + $model->biaya_ujian;
        });
    }

    // Relationships
    public function gelombangPmb()
    {
        return $this->belongsTo(GelombangPmb::class, 'gelombang_pmb_id');
    }

    public function jalurSeleksi()
    {
        return $this->belongsTo(JalurSeleksi::class, 'jalur_seleksi_id');
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'program_studi_id');
    }

    // Helper Methods
    public static function getBiaya($gelombangId, $jalurId, $prodiId = null)
    {
        // Cari biaya spesifik untuk prodi tertentu
        if ($prodiId) {
            $biaya = self::where('gelombang_pmb_id', $gelombangId)
                         ->where('jalur_seleksi_id', $jalurId)
                         ->where('program_studi_id', $prodiId)
                         ->first();
            if ($biaya) return $biaya;
        }

        // Fallback: cari biaya umum (tanpa prodi spesifik)
        return self::where('gelombang_pmb_id', $gelombangId)
                   ->where('jalur_seleksi_id', $jalurId)
                   ->whereNull('program_studi_id')
                   ->first();
    }
}
