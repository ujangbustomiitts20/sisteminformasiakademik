<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalUjian extends Model
{
    use HashidsTrait;
    
    protected $table = 'jadwal_ujian';

    protected $fillable = [
        'tahun_akademik_id',
        'mata_kuliah_id',
        'jadwal_kuliah_id',
        'dosen_id',
        'jenis_ujian',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'ruangan',
        'durasi_menit',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
    ];

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function jadwalKuliah(): BelongsTo
    {
        return $this->belongsTo(JadwalKuliah::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    // Accessor untuk badge jenis ujian
    public function getJenisBadgeAttribute(): string
    {
        return match($this->jenis_ujian) {
            'UTS' => 'primary',
            'UAS' => 'danger',
            'Quiz' => 'info',
            'Remedial' => 'warning',
            'Susulan' => 'secondary',
            default => 'secondary',
        };
    }

    // Accessor untuk badge status
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'Terjadwal' => 'info',
            'Berlangsung' => 'warning',
            'Selesai' => 'success',
            'Ditunda' => 'secondary',
            'Dibatalkan' => 'danger',
            default => 'secondary',
        };
    }

    // Scope untuk filter by tahun akademik aktif
    public function scopeAktif($query)
    {
        return $query->whereHas('tahunAkademik', fn($q) => $q->where('is_aktif', true));
    }

    // Scope untuk filter by jenis ujian
    public function scopeJenis($query, $jenis)
    {
        return $query->where('jenis_ujian', $jenis);
    }

    // Scope untuk ujian yang akan datang
    public function scopeUpcoming($query)
    {
        return $query->where('tanggal', '>=', now()->toDateString())
                     ->where('status', 'Terjadwal')
                     ->orderBy('tanggal')
                     ->orderBy('jam_mulai');
    }
}
