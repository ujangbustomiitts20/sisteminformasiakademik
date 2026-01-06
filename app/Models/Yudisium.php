<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Yudisium extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'yudisium';

    protected $fillable = [
        'no_yudisium',
        'pendaftaran_wisuda_id',
        'mahasiswa_id',
        'tanggal_yudisium',
        'ipk_akhir',
        'total_sks_lulus',
        'predikat',
        'tanggal_masuk',
        'tanggal_lulus',
        'masa_studi_bulan',
        'no_ijazah',
        'no_transkrip',
        'status',
        'catatan',
        'diproses_oleh',
        'tanggal_proses',
        // Legacy fields
        'no_sk_yudisium',
        'tanggal_sk_yudisium',
        'no_sk_rektor',
        'tanggal_sk_rektor',
        'no_blanko',
        'no_pin',
        'no_nirl',
        'url_pddikti',
        'feeder_aktivitas',
        'status_keluar',
        'tahun_semester',
        'nilai_kompre',
        'nilai_uap_tulis',
        'nilai_uap_praktek',
        'simbol_uap_tulis',
        'simbol_uap_praktek',
        'peminatan',
        'legacy_id',
    ];

    protected $casts = [
        'tanggal_yudisium' => 'date',
        'tanggal_masuk' => 'date',
        'tanggal_lulus' => 'date',
        'tanggal_proses' => 'datetime',
        'tanggal_sk_yudisium' => 'date',
        'tanggal_sk_rektor' => 'date',
        'ipk_akhir' => 'decimal:2',
        'nilai_kompre' => 'decimal:2',
        'nilai_uap_tulis' => 'decimal:2',
        'nilai_uap_praktek' => 'decimal:2',
    ];

    const STATUS_PENDING = 'Pending';
    const STATUS_DISETUJUI = 'Disetujui';
    const STATUS_DITOLAK = 'Ditolak';

    const PREDIKAT_CUM_LAUDE = 'Cum Laude';
    const PREDIKAT_SANGAT_MEMUASKAN = 'Sangat Memuaskan';
    const PREDIKAT_MEMUASKAN = 'Memuaskan';
    const PREDIKAT_CUKUP = 'Cukup';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->no_yudisium)) {
                $model->no_yudisium = self::generateNoYudisium();
            }
        });
    }

    public static function generateNoYudisium()
    {
        $prefix = 'YDS';
        $year = date('Y');
        $lastRecord = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = 1;
        if ($lastRecord && preg_match('/(\d{5})$/', $lastRecord->no_yudisium, $matches)) {
            $sequence = intval($matches[1]) + 1;
        }

        return $prefix . $year . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    public static function hitungPredikat($ipk, $masaStudiBulan, $totalSks)
    {
        // Cum Laude: IPK >= 3.51, masa studi <= 48 bulan (4 tahun)
        if ($ipk >= 3.51 && $masaStudiBulan <= 48) {
            return self::PREDIKAT_CUM_LAUDE;
        }
        // Sangat Memuaskan: IPK 3.01 - 3.50
        if ($ipk >= 3.01) {
            return self::PREDIKAT_SANGAT_MEMUASKAN;
        }
        // Memuaskan: IPK 2.76 - 3.00
        if ($ipk >= 2.76) {
            return self::PREDIKAT_MEMUASKAN;
        }
        // Cukup: IPK < 2.76
        return self::PREDIKAT_CUKUP;
    }

    // Relationships
    public function pendaftaranWisuda()
    {
        return $this->belongsTo(PendaftaranWisuda::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function prosesOleh()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Pending' => 'warning',
            'Disetujui' => 'success',
            'Ditolak' => 'danger',
        ];

        $class = $badges[$this->status] ?? 'secondary';
        return "<span class=\"badge bg-{$class}\">{$this->status}</span>";
    }

    public function getPredikatBadgeAttribute()
    {
        $badges = [
            'Cum Laude' => 'success',
            'Sangat Memuaskan' => 'primary',
            'Memuaskan' => 'info',
            'Cukup' => 'secondary',
        ];

        $class = $badges[$this->predikat] ?? 'secondary';
        return "<span class=\"badge bg-{$class}\">{$this->predikat}</span>";
    }

    public function getMasaStudiFormatAttribute()
    {
        $tahun = floor($this->masa_studi_bulan / 12);
        $bulan = $this->masa_studi_bulan % 12;
        
        $result = '';
        if ($tahun > 0) $result .= "{$tahun} tahun ";
        if ($bulan > 0) $result .= "{$bulan} bulan";
        
        return trim($result) ?: '0 bulan';
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', self::STATUS_DISETUJUI);
    }
}
