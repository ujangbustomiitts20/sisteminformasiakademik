<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SanksiPegawai extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'sanksi_pegawai';

    protected $fillable = [
        'pelanggaran_pegawai_id',
        'jenis_sanksi',
        'nomor_sk',
        'tanggal_sk',
        'tanggal_mulai',
        'tanggal_berakhir',
        'keterangan',
        'file_sk',
        'status',
        'ditetapkan_oleh',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
    ];

    const JENIS_SANKSI = [
        'teguran_lisan' => 'Teguran Lisan',
        'teguran_tertulis' => 'Teguran Tertulis',
        'penundaan_kgb' => 'Penundaan Kenaikan Gaji Berkala',
        'penundaan_pangkat' => 'Penundaan Kenaikan Pangkat',
        'penurunan_pangkat' => 'Penurunan Pangkat',
        'pembebasan_jabatan' => 'Pembebasan dari Jabatan',
        'pemberhentian_hormat' => 'Pemberhentian dengan Hormat',
        'pemberhentian_tidak_hormat' => 'Pemberhentian Tidak dengan Hormat',
    ];

    const STATUS = [
        'draft' => 'Draft',
        'aktif' => 'Aktif',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    public function pelanggaranPegawai()
    {
        return $this->belongsTo(PelanggaranPegawai::class);
    }

    public function ditetapkanOleh()
    {
        return $this->belongsTo(User::class, 'ditetapkan_oleh');
    }

    public function getJenisSanksiLabelAttribute(): string
    {
        return self::JENIS_SANKSI[$this->jenis_sanksi] ?? $this->jenis_sanksi;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'secondary',
            'aktif' => 'danger',
            'selesai' => 'success',
            'dibatalkan' => 'warning',
            default => 'secondary',
        };
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
