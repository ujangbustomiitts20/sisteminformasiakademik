<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BimbinganAkademik extends Model
{
    use HashidsTrait;
    protected $table = 'bimbingan_akademik';

    protected $fillable = [
        'mahasiswa_id',
        'dosen_id',
        'tahun_akademik_id',
        'tanggal_bimbingan',
        'jenis',
        'topik',
        'catatan_mahasiswa',
        'catatan_dosen',
        'rekomendasi',
        'status',
    ];

    protected $casts = [
        'tanggal_bimbingan' => 'date',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    // Accessor untuk badge status
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'Dijadwalkan' => 'warning',
            'Selesai' => 'success',
            'Dibatalkan' => 'danger',
            default => 'secondary',
        };
    }

    // Accessor untuk badge jenis
    public function getJenisBadgeAttribute(): string
    {
        return match($this->jenis) {
            'KRS' => 'primary',
            'Akademik' => 'info',
            'Pribadi' => 'secondary',
            'Karir' => 'success',
            'Lainnya' => 'light',
            default => 'secondary',
        };
    }

    // Scope by status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope by dosen
    public function scopeByDosen($query, $dosenId)
    {
        return $query->where('dosen_id', $dosenId);
    }

    // Scope by mahasiswa
    public function scopeByMahasiswa($query, $mahasiswaId)
    {
        return $query->where('mahasiswa_id', $mahasiswaId);
    }

    // Scope dijadwalkan
    public function scopeDijadwalkan($query)
    {
        return $query->where('status', 'Dijadwalkan');
    }

    // Scope selesai
    public function scopeSelesai($query)
    {
        return $query->where('status', 'Selesai');
    }
}
