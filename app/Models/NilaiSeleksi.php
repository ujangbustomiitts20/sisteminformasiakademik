<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiSeleksi extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'nilai_seleksi';

    protected $fillable = [
        'calon_mahasiswa_id',
        'gelombang_pmb_id',
        'komponen_nilai',
        'nilai',
        'bobot',
        'nilai_akhir',
        'nilai_tpa',
        'nilai_bahasa',
        'nilai_matematika',
        'nilai_wawancara',
        'nilai_total',
        'input_by',
    ];

    protected $casts = [
        'komponen_nilai' => 'array',
        'nilai' => 'decimal:2',
        'bobot' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
        'nilai_tpa' => 'decimal:2',
        'nilai_bahasa' => 'decimal:2',
        'nilai_matematika' => 'decimal:2',
        'nilai_wawancara' => 'decimal:2',
        'nilai_total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->nilai_akhir = $model->nilai * $model->bobot;
        });

        static::updating(function ($model) {
            $model->nilai_akhir = $model->nilai * $model->bobot;
        });
    }

    // Relationships
    public function calonMahasiswa()
    {
        return $this->belongsTo(CalonMahasiswa::class, 'calon_mahasiswa_id');
    }

    public function gelombangPmb()
    {
        return $this->belongsTo(GelombangPmb::class, 'gelombang_pmb_id');
    }

    public function inputBy()
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    // Helper Methods
    public static function getKomponenNilai()
    {
        return [
            'tpa' => 'Tes Potensi Akademik',
            'bahasa_inggris' => 'Bahasa Inggris',
            'matematika' => 'Matematika',
            'wawancara' => 'Wawancara',
            'psikotes' => 'Psikotes',
            'nilai_rapor' => 'Nilai Rapor',
            'prestasi' => 'Prestasi',
        ];
    }

    public function getKomponenLabelAttribute()
    {
        $komponenList = self::getKomponenNilai();
        
        // Handle jika komponen_nilai adalah array/object (dari cast)
        if (is_array($this->komponen_nilai)) {
            // komponen_nilai berisi {komponen => nilai} jadi ambil keys-nya
            $labels = [];
            foreach (array_keys($this->komponen_nilai) as $komponen) {
                $labels[] = $komponenList[$komponen] ?? $komponen;
            }
            return implode(', ', $labels);
        }
        
        return $komponenList[$this->komponen_nilai] ?? $this->komponen_nilai;
    }

    /**
     * Get komponen nilai sebagai array dengan label dan nilai
     */
    public function getKomponenNilaiDetailAttribute()
    {
        $komponenList = self::getKomponenNilai();
        $details = [];
        
        if (is_array($this->komponen_nilai)) {
            foreach ($this->komponen_nilai as $komponen => $nilai) {
                $details[] = [
                    'komponen' => $komponen,
                    'label' => $komponenList[$komponen] ?? $komponen,
                    'nilai' => $nilai,
                ];
            }
        }
        
        return $details;
    }
}
