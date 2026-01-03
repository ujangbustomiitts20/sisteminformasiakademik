<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KenaikanGajiBerkala extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'kenaikan_gaji_berkala';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'no_sk',
        'tanggal_sk',
        'tmt_kgb',
        'tmt_kgb_berikutnya',
        'golongan_ruang',
        'masa_kerja_golongan_tahun',
        'masa_kerja_golongan_bulan',
        'gaji_pokok_lama',
        'gaji_pokok_baru',
        'masa_kerja_total_tahun',
        'masa_kerja_total_bulan',
        'status',
        'dokumen_sk',
        'catatan',
        'is_otomatis',
        'diproses_oleh',
        'tanggal_diproses',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'tmt_kgb' => 'date',
        'tmt_kgb_berikutnya' => 'date',
        'tanggal_diproses' => 'date',
        'gaji_pokok_lama' => 'decimal:2',
        'gaji_pokok_baru' => 'decimal:2',
        'is_otomatis' => 'boolean',
    ];

    const STATUS = [
        'pending' => 'Pending',
        'diproses' => 'Diproses',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Auto set TMT KGB berikutnya (2 tahun dari TMT KGB saat ini)
            if ($model->tmt_kgb && empty($model->tmt_kgb_berikutnya)) {
                $model->tmt_kgb_berikutnya = $model->tmt_kgb->copy()->addYears(2);
            }
        });
    }

    // Relationships
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function diprosesOleh()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
            'diproses' => '<span class="badge bg-info">Diproses</span>',
            'disetujui' => '<span class="badge bg-success">Disetujui</span>',
            'ditolak' => '<span class="badge bg-danger">Ditolak</span>',
        ];
        return $badges[$this->status] ?? '<span class="badge bg-secondary">' . $this->status . '</span>';
    }

    public function getNamaPegawaiAttribute()
    {
        if ($this->dosen_id) {
            return $this->dosen->nama ?? '-';
        }
        return $this->pegawai->nama ?? '-';
    }

    public function getMasaKerjaGolonganAttribute()
    {
        return $this->masa_kerja_golongan_tahun . ' tahun ' . $this->masa_kerja_golongan_bulan . ' bulan';
    }

    public function getMasaKerjaTotalAttribute()
    {
        return $this->masa_kerja_total_tahun . ' tahun ' . $this->masa_kerja_total_bulan . ' bulan';
    }

    public function getKenaikanGajiAttribute()
    {
        if ($this->gaji_pokok_lama && $this->gaji_pokok_baru) {
            return $this->gaji_pokok_baru - $this->gaji_pokok_lama;
        }
        return 0;
    }

    public function getPersentaseKenaikanAttribute()
    {
        if ($this->gaji_pokok_lama > 0) {
            return round(($this->kenaikan_gaji / $this->gaji_pokok_lama) * 100, 2);
        }
        return 0;
    }

    public function getSisaHariKgbAttribute()
    {
        if ($this->tmt_kgb_berikutnya) {
            return now()->diffInDays($this->tmt_kgb_berikutnya, false);
        }
        return null;
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAkanJatuhTempo($query, $bulan = 3)
    {
        return $query->whereBetween('tmt_kgb_berikutnya', [now(), now()->addMonths($bulan)]);
    }

    public function scopeSudahJatuhTempo($query)
    {
        return $query->where('tmt_kgb_berikutnya', '<=', now())
                     ->where('status', 'pending');
    }
}
