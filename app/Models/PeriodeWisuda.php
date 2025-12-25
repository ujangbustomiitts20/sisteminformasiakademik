<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeWisuda extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'periode_wisuda';

    protected $fillable = [
        'tahun_akademik_id',
        'nama',
        'tanggal_wisuda',
        'tanggal_buka_pendaftaran',
        'tanggal_tutup_pendaftaran',
        'tanggal_yudisium',
        'lokasi',
        'kuota',
        'biaya_wisuda',
        'persyaratan',
        'status',
    ];

    protected $casts = [
        'tanggal_wisuda' => 'date',
        'tanggal_buka_pendaftaran' => 'date',
        'tanggal_tutup_pendaftaran' => 'date',
        'tanggal_yudisium' => 'date',
        'biaya_wisuda' => 'decimal:2',
    ];

    const STATUS_DRAFT = 'Draft';
    const STATUS_DIBUKA = 'Dibuka';
    const STATUS_DITUTUP = 'Ditutup';
    const STATUS_SELESAI = 'Selesai';

    // Relationships
    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function pendaftaran()
    {
        return $this->hasMany(PendaftaranWisuda::class);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Draft' => 'secondary',
            'Dibuka' => 'success',
            'Ditutup' => 'warning',
            'Selesai' => 'primary',
        ];

        $class = $badges[$this->status] ?? 'secondary';
        return "<span class=\"badge bg-{$class}\">{$this->status}</span>";
    }

    public function getJumlahPendaftarAttribute()
    {
        return $this->pendaftaran()->count();
    }

    public function getSisaKuotaAttribute()
    {
        if (!$this->kuota) return null;
        return max(0, $this->kuota - $this->jumlah_pendaftar);
    }

    public function getIsPendaftaranOpenAttribute()
    {
        $now = now()->toDateString();
        return $this->status === self::STATUS_DIBUKA
            && $now >= $this->tanggal_buka_pendaftaran->toDateString()
            && $now <= $this->tanggal_tutup_pendaftaran->toDateString()
            && ($this->sisa_kuota === null || $this->sisa_kuota > 0);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_DIBUKA, self::STATUS_DITUTUP]);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_DIBUKA)
            ->where('tanggal_buka_pendaftaran', '<=', now())
            ->where('tanggal_tutup_pendaftaran', '>=', now());
    }
}
