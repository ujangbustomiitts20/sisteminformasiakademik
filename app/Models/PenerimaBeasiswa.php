<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;
use App\Models\User;

class PenerimaBeasiswa extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'penerima_beasiswa';

    protected $fillable = [
        'beasiswa_id',
        'mahasiswa_id',
        'tahun_akademik_id',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'approved_by',
        'approved_at',
        'catatan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'approved_at' => 'datetime',
    ];

    public const STATUS = [
        'Diajukan' => 'Diajukan',
        'Disetujui' => 'Disetujui',
        'Ditolak' => 'Ditolak',
        'Dicabut' => 'Dicabut',
    ];

    public function beasiswa()
    {
        return $this->belongsTo(Beasiswa::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'Disetujui' => '<span class="badge bg-success">Disetujui</span>',
            'Ditolak' => '<span class="badge bg-danger">Ditolak</span>',
            'Dicabut' => '<span class="badge bg-secondary">Dicabut</span>',
            default => '<span class="badge bg-warning">Diajukan</span>',
        };
    }

    public function getIsActiveAttribute()
    {
        if ($this->status !== 'Disetujui') {
            return false;
        }
        
        $now = now();
        if ($this->tanggal_selesai && $this->tanggal_selesai < $now) {
            return false;
        }
        if ($this->tanggal_mulai && $this->tanggal_mulai > $now) {
            return false;
        }
        
        return true;
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'Disetujui')
            ->where(function($q) {
                $q->whereNull('tanggal_selesai')
                  ->orWhere('tanggal_selesai', '>=', now());
            });
    }
}
