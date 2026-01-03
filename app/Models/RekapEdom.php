<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapEdom extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'rekap_edom';

    protected $fillable = [
        'periode_edom_id',
        'dosen_id',
        'jadwal_kuliah_id',
        'rata_rata_pedagogik',
        'rata_rata_profesional',
        'rata_rata_kepribadian',
        'rata_rata_sosial',
        'rata_rata_total',
        'jumlah_responden',
    ];

    protected $casts = [
        'rata_rata_pedagogik' => 'decimal:2',
        'rata_rata_profesional' => 'decimal:2',
        'rata_rata_kepribadian' => 'decimal:2',
        'rata_rata_sosial' => 'decimal:2',
        'rata_rata_total' => 'decimal:2',
        'jumlah_responden' => 'integer',
    ];

    public function periodeEdom()
    {
        return $this->belongsTo(PeriodeEdom::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function jadwalKuliah()
    {
        return $this->belongsTo(JadwalKuliah::class);
    }

    public function getKategoriAttribute()
    {
        $avg = $this->rata_rata_total;
        
        if ($avg >= 4.5) return 'Sangat Baik';
        if ($avg >= 3.5) return 'Baik';
        if ($avg >= 2.5) return 'Cukup';
        if ($avg >= 1.5) return 'Kurang';
        return 'Sangat Kurang';
    }

    public function getKategoriBadgeAttribute()
    {
        $kategori = $this->kategori;
        
        $badges = [
            'Sangat Baik' => 'success',
            'Baik' => 'primary',
            'Cukup' => 'warning',
            'Kurang' => 'danger',
            'Sangat Kurang' => 'dark',
        ];

        return $badges[$kategori] ?? 'secondary';
    }

    /**
     * Hitung rekap EDOM untuk dosen/jadwal tertentu
     */
    public static function hitungRekap($periodeEdomId, $dosenId, $jadwalKuliahId)
    {
        $jawaban = JawabanEdom::where('periode_edom_id', $periodeEdomId)
            ->where('dosen_id', $dosenId)
            ->where('jadwal_kuliah_id', $jadwalKuliahId)
            ->get();

        if ($jawaban->isEmpty()) {
            return null;
        }

        // Group by kategori
        $grouped = $jawaban->groupBy(function ($item) {
            return $item->pertanyaanEdom->kategori;
        });

        $rataRata = [];
        foreach (['pedagogik', 'profesional', 'kepribadian', 'sosial'] as $kategori) {
            $items = $grouped->get($kategori, collect());
            $rataRata[$kategori] = $items->count() > 0 ? $items->avg('nilai') : 0;
        }

        $jumlahResponden = $jawaban->unique('mahasiswa_id')->count();

        return self::updateOrCreate(
            [
                'periode_edom_id' => $periodeEdomId,
                'dosen_id' => $dosenId,
                'jadwal_kuliah_id' => $jadwalKuliahId,
            ],
            [
                'rata_rata_pedagogik' => $rataRata['pedagogik'],
                'rata_rata_profesional' => $rataRata['profesional'],
                'rata_rata_kepribadian' => $rataRata['kepribadian'],
                'rata_rata_sosial' => $rataRata['sosial'],
                'rata_rata_total' => array_sum($rataRata) / 4,
                'jumlah_responden' => $jumlahResponden,
            ]
        );
    }
}
