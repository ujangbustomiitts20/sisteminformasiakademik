<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IzinKeluar extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'izin_keluar';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'tanggal',
        'jam_keluar',
        'jam_kembali',
        'jam_kembali_aktual',
        'keperluan',
        'keterangan',
        'tujuan',
        'status',
        'approval_level',
        'status_kaprodi',
        'tanggal_approval_kaprodi',
        'catatan_kaprodi',
        'kaprodi_id',
        'disetujui_oleh',
        'tanggal_disetujui',
        'catatan_approval',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_keluar' => 'datetime:H:i',
        'jam_kembali' => 'datetime:H:i',
        'jam_kembali_aktual' => 'datetime:H:i',
        'tanggal_disetujui' => 'datetime',
        'tanggal_approval_kaprodi' => 'datetime',
    ];

    const KEPERLUAN = [
        'dinas' => 'Keperluan Dinas',
        'pribadi' => 'Keperluan Pribadi',
        'kesehatan' => 'Kesehatan',
        'keluarga' => 'Keperluan Keluarga',
        'lainnya' => 'Lainnya',
    ];

    const STATUS = [
        'diajukan' => 'Diajukan',
        'menunggu_admin' => 'Menunggu Admin',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
        'selesai' => 'Selesai',
    ];

    const STATUS_KAPRODI = [
        'pending' => 'Menunggu',
        'disetujui' => 'Disetujui Kaprodi',
        'ditolak' => 'Ditolak Kaprodi',
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function kaprodi()
    {
        return $this->belongsTo(Dosen::class, 'kaprodi_id');
    }

    public function getNamaPegawaiAttribute(): string
    {
        if ($this->dosen_id && $this->dosen) {
            return $this->dosen->nama;
        }
        if ($this->pegawai_id && $this->pegawai) {
            return $this->pegawai->nama;
        }
        return '-';
    }

    public function getKeperluanLabelAttribute(): string
    {
        return self::KEPERLUAN[$this->keperluan] ?? $this->keperluan;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'diajukan' => 'warning',
            'menunggu_admin' => 'info',
            'disetujui' => 'success',
            'ditolak' => 'danger',
            'selesai' => 'primary',
            default => 'secondary',
        };
    }

    public function getStatusKaprodiLabelAttribute(): string
    {
        return self::STATUS_KAPRODI[$this->status_kaprodi] ?? '-';
    }

    public function getStatusKaprodiColorAttribute(): string
    {
        return match($this->status_kaprodi) {
            'pending' => 'warning',
            'disetujui' => 'success',
            'ditolak' => 'danger',
            default => 'secondary',
        };
    }

    public function getFullStatusLabelAttribute(): string
    {
        if ($this->status === 'diajukan' && $this->status_kaprodi === 'pending') {
            return 'Menunggu Kaprodi';
        }
        if ($this->status === 'menunggu_admin') {
            return 'Menunggu Admin';
        }
        return $this->status_label;
    }

    public function getDurasiAttribute(): ?string
    {
        if (!$this->jam_keluar || !$this->jam_kembali) return null;
        
        $keluar = \Carbon\Carbon::parse($this->jam_keluar);
        $kembali = \Carbon\Carbon::parse($this->jam_kembali);
        
        $diff = $keluar->diff($kembali);
        return $diff->format('%H:%I');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'diajukan');
    }

    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', today());
    }
}
