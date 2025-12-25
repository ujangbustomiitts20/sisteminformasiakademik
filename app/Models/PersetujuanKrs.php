<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersetujuanKrs extends Model
{
    protected $table = 'persetujuan_krs';

    protected $fillable = [
        'mahasiswa_id',
        'dosen_id',
        'tahun_akademik_id',
        'status',
        'catatan_mahasiswa',
        'catatan_dosen',
        'total_sks',
        'tanggal_pengajuan',
        'tanggal_persetujuan',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'datetime',
        'tanggal_persetujuan' => 'datetime',
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
            'Pending' => 'warning',
            'Disetujui' => 'success',
            'Ditolak' => 'danger',
            'Revisi' => 'info',
            default => 'secondary',
        };
    }

    // Scope by status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope pending
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    // Scope disetujui
    public function scopeDisetujui($query)
    {
        return $query->where('status', 'Disetujui');
    }
}
