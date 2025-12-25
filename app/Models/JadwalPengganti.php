<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPengganti extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'jadwal_pengganti';

    protected $fillable = [
        'jadwal_kuliah_id',
        'tanggal_asli',
        'tanggal_pengganti',
        'jam_mulai',
        'jam_selesai',
        'ruangan_id',
        'alasan',
        'keterangan',
        'status',
        'diajukan_oleh',
        'disetujui_oleh',
        'tanggal_persetujuan',
    ];

    protected $casts = [
        'tanggal_asli' => 'date',
        'tanggal_pengganti' => 'date',
        'tanggal_persetujuan' => 'datetime',
    ];

    const ALASAN_LIST = [
        'Libur Nasional' => 'Libur Nasional',
        'Dosen Berhalangan' => 'Dosen Berhalangan',
        'Kegiatan Kampus' => 'Kegiatan Kampus',
        'Force Majeure' => 'Force Majeure',
        'Lainnya' => 'Lainnya',
    ];

    const STATUS_PENDING = 'Pending';
    const STATUS_DISETUJUI = 'Disetujui';
    const STATUS_DITOLAK = 'Ditolak';
    const STATUS_SELESAI = 'Selesai';

    // Relationships
    public function jadwalKuliah()
    {
        return $this->belongsTo(JadwalKuliah::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Pending' => 'warning',
            'Disetujui' => 'success',
            'Ditolak' => 'danger',
            'Selesai' => 'secondary',
        ];

        $class = $badges[$this->status] ?? 'secondary';
        return "<span class=\"badge bg-{$class}\">{$this->status}</span>";
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

    public function scopeUpcoming($query)
    {
        return $query->where('tanggal_pengganti', '>=', now()->toDateString());
    }
}
