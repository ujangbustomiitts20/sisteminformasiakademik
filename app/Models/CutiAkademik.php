<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CutiAkademik extends Model
{
    use HashidsTrait;
    protected $table = 'cuti_akademik';

    protected $fillable = [
        'mahasiswa_id',
        'tahun_akademik_id',
        'nomor_surat',
        'alasan',
        'keterangan',
        'dokumen_pendukung',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_semester',
        'status',
        'disetujui_kaprodi_oleh',
        'tanggal_persetujuan_kaprodi',
        'catatan_kaprodi',
        'disetujui_dekan_oleh',
        'tanggal_persetujuan_dekan',
        'catatan_dekan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_persetujuan_kaprodi' => 'datetime',
        'tanggal_persetujuan_dekan' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function disetujuiKaprodiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_kaprodi_oleh');
    }

    public function disetujuiDekanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_dekan_oleh');
    }

    // Accessor untuk badge status
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'Pending' => 'warning',
            'Disetujui Kaprodi' => 'info',
            'Disetujui Dekan' => 'success',
            'Ditolak' => 'danger',
            'Selesai' => 'secondary',
            default => 'light',
        };
    }

    // Accessor untuk badge alasan
    public function getAlasanBadgeAttribute(): string
    {
        return match($this->alasan) {
            'Keuangan' => 'warning',
            'Kesehatan' => 'danger',
            'Keluarga' => 'info',
            'Pekerjaan' => 'primary',
            'Lainnya' => 'secondary',
            default => 'light',
        };
    }

    // Generate nomor surat
    public static function generateNomorSurat(): string
    {
        $tahun = date('Y');
        $bulan = date('m');
        $count = self::whereYear('created_at', $tahun)->count() + 1;
        
        return sprintf('CUTI/%s/%s/%04d', $bulan, $tahun, $count);
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

    // Scope aktif (belum selesai)
    public function scopeAktif($query)
    {
        return $query->whereIn('status', ['Pending', 'Disetujui Kaprodi', 'Disetujui Dekan']);
    }

    // Check apakah sedang dalam masa cuti
    public function isSedangCuti(): bool
    {
        $today = now()->toDateString();
        return $this->status === 'Disetujui Dekan' 
            && $this->tanggal_mulai <= $today 
            && $this->tanggal_selesai >= $today;
    }
}
