<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeKegiatanLapangan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'periode_kegiatan_lapangan';

    protected $fillable = [
        'jenis_kegiatan_id',
        'tahun_akademik_id',
        'nama',
        'tanggal_mulai_daftar',
        'tanggal_selesai_daftar',
        'tanggal_mulai_kegiatan',
        'tanggal_selesai_kegiatan',
        'kuota',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai_daftar' => 'date',
        'tanggal_selesai_daftar' => 'date',
        'tanggal_mulai_kegiatan' => 'date',
        'tanggal_selesai_kegiatan' => 'date',
        'kuota' => 'integer',
    ];

    public function jenisKegiatan()
    {
        return $this->belongsTo(JenisKegiatanLapangan::class, 'jenis_kegiatan_id');
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function pendaftaran()
    {
        return $this->hasMany(PendaftaranKegiatanLapangan::class, 'periode_id');
    }

    public static function getStatusOptions()
    {
        return [
            'draft' => 'Draft',
            'dibuka' => 'Pendaftaran Dibuka',
            'ditutup' => 'Pendaftaran Ditutup',
            'selesai' => 'Selesai',
        ];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'secondary',
            'dibuka' => 'success',
            'ditutup' => 'warning',
            'selesai' => 'info',
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function scopeDibuka($query)
    {
        return $query->where('status', 'dibuka')
            ->whereDate('tanggal_mulai_daftar', '<=', now())
            ->whereDate('tanggal_selesai_daftar', '>=', now());
    }

    public function getJumlahPendaftarAttribute()
    {
        return $this->pendaftaran()->count();
    }

    public function getSisaKuotaAttribute()
    {
        if (!$this->kuota) return null;
        return $this->kuota - $this->jumlah_pendaftar;
    }
}
