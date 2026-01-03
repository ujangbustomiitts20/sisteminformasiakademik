<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Nilai extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'nilai';

    protected $fillable = [
        'krs_id',
        'tugas',
        'kehadiran',
        'uts',
        'uas',
        'nilai_akhir',
        'huruf',
        'bobot',
    ];

    public function krs()
    {
        return $this->belongsTo(Krs::class);
    }

    // Hitung nilai akhir otomatis dengan bobot komponen
    // Default: 20% Kehadiran, 20% Tugas, 25% UTS, 35% UAS (jika kehadiran ada)
    // Atau: 30% Tugas, 30% UTS, 40% UAS (jika kehadiran tidak ada)
    public function hitungNilaiAkhir()
    {
        if ($this->uts !== null && $this->uas !== null) {
            if ($this->kehadiran !== null) {
                // Dengan kehadiran
                $this->nilai_akhir = 
                    ($this->kehadiran * 0.20) + 
                    (($this->tugas ?? 0) * 0.20) + 
                    ($this->uts * 0.25) + 
                    ($this->uas * 0.35);
            } else {
                // Tanpa kehadiran (formula lama)
                $this->nilai_akhir = 
                    (($this->tugas ?? 0) * 0.30) + 
                    ($this->uts * 0.30) + 
                    ($this->uas * 0.40);
            }
            $this->huruf = $this->konversiHuruf($this->nilai_akhir);
            $this->bobot = $this->konversiBobot($this->huruf);
            $this->save();
        }
    }

    // Konversi nilai ke huruf
    public function konversiHuruf($nilai)
    {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 80) return 'A-';
        if ($nilai >= 75) return 'B+';
        if ($nilai >= 70) return 'B';
        if ($nilai >= 65) return 'B-';
        if ($nilai >= 60) return 'C+';
        if ($nilai >= 55) return 'C';
        if ($nilai >= 50) return 'D';
        return 'E';
    }

    // Konversi huruf ke bobot
    public function konversiBobot($huruf)
    {
        $bobotMap = [
            'A' => 4.00,
            'A-' => 3.75,
            'B+' => 3.50,
            'B' => 3.00,
            'B-' => 2.75,
            'C+' => 2.50,
            'C' => 2.00,
            'D' => 1.00,
            'E' => 0.00,
        ];

        return $bobotMap[$huruf] ?? 0;
    }
}
