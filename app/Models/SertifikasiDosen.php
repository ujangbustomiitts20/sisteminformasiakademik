<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SertifikasiDosen extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'sertifikasi_dosen';

    protected $fillable = [
        'dosen_id',
        'jenis_sertifikasi',
        'nama_sertifikasi',
        'nomor_sertifikat',
        'penerbit',
        'tanggal_terbit',
        'tanggal_berlaku',
        'tanggal_expired',
        'bidang_studi',
        'file_sertifikat',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'tanggal_berlaku' => 'date',
        'tanggal_expired' => 'date',
    ];

    const JENIS_SERTIFIKASI = [
        'serdos' => 'Sertifikasi Dosen (Serdos)',
        'kompetensi' => 'Sertifikat Kompetensi',
        'profesi' => 'Sertifikat Profesi',
        'keahlian' => 'Sertifikat Keahlian',
        'lainnya' => 'Lainnya',
    ];

    const STATUS = [
        'aktif' => 'Aktif',
        'expired' => 'Expired',
        'dicabut' => 'Dicabut',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            // Auto update status if expired
            if ($model->tanggal_expired && $model->tanggal_expired->isPast()) {
                $model->status = 'expired';
            }
        });
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function getJenisSertifikasiLabelAttribute(): string
    {
        return self::JENIS_SERTIFIKASI[$this->jenis_sertifikasi] ?? $this->jenis_sertifikasi;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'aktif' => 'success',
            'expired' => 'warning',
            'dicabut' => 'danger',
            default => 'secondary',
        };
    }

    public function isExpiringSoon(int $days = 90): bool
    {
        if (!$this->tanggal_expired) return false;
        return $this->tanggal_expired->diffInDays(now()) <= $days && $this->tanggal_expired->isFuture();
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeSerdos($query)
    {
        return $query->where('jenis_sertifikasi', 'serdos');
    }

    public function scopeAkanExpired($query, $days = 90)
    {
        return $query->where('status', 'aktif')
            ->whereNotNull('tanggal_expired')
            ->whereBetween('tanggal_expired', [now(), now()->addDays($days)]);
    }
}
