<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontrakKerja extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'kontrak_kerja';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'nomor_kontrak',
        'jenis_kontrak',
        'tanggal_mulai',
        'tanggal_berakhir',
        'gaji_pokok',
        'keterangan',
        'file_kontrak',
        'status',
        'created_by',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
        'gaji_pokok' => 'decimal:2',
    ];

    const JENIS_KONTRAK = [
        'tetap' => 'Pegawai Tetap',
        'kontrak' => 'Pegawai Kontrak',
        'honorer' => 'Tenaga Honorer',
        'paruh_waktu' => 'Paruh Waktu',
    ];

    const STATUS = [
        'draft' => 'Draft',
        'aktif' => 'Aktif',
        'berakhir' => 'Berakhir',
        'diperpanjang' => 'Diperpanjang',
        'dibatalkan' => 'Dibatalkan',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->nomor_kontrak)) {
                $model->nomor_kontrak = self::generateNomorKontrak();
            }
        });
    }

    public static function generateNomorKontrak(): string
    {
        $prefix = 'KTR';
        $year = date('Y');
        $month = date('m');
        
        $lastKontrak = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastKontrak ? (int)substr($lastKontrak->nomor_kontrak, -5) + 1 : 1;
        
        return $prefix . $year . $month . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getPegawaiAttribute()
    {
        if ($this->dosen_id) {
            return $this->dosen;
        }
        return $this->getRelationValue('pegawai');
    }

    public function getNamaPegawaiAttribute(): string
    {
        if ($this->dosen_id && $this->dosen) {
            return $this->dosen->nama;
        }
        if ($this->pegawai_id && $this->relationLoaded('pegawai')) {
            return $this->getRelationValue('pegawai')->nama ?? '-';
        }
        return '-';
    }

    public function getJenisKontrakLabelAttribute(): string
    {
        return self::JENIS_KONTRAK[$this->jenis_kontrak] ?? $this->jenis_kontrak;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'secondary',
            'aktif' => 'success',
            'berakhir' => 'warning',
            'diperpanjang' => 'info',
            'dibatalkan' => 'danger',
            default => 'secondary',
        };
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        if (!$this->tanggal_berakhir) return false;
        return $this->tanggal_berakhir->diffInDays(now()) <= $days && $this->tanggal_berakhir->isFuture();
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeAkanBerakhir($query, $days = 30)
    {
        return $query->where('status', 'aktif')
            ->whereNotNull('tanggal_berakhir')
            ->whereBetween('tanggal_berakhir', [now(), now()->addDays($days)]);
    }
}
