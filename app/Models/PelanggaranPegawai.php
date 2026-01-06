<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelanggaranPegawai extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'pelanggaran_pegawai';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'jenis_pelanggaran_id',
        'tanggal_pelanggaran',
        'deskripsi',
        'bukti',
        'file_bukti',
        'status',
        'dilaporkan_oleh',
    ];

    protected $casts = [
        'tanggal_pelanggaran' => 'date',
    ];

    const STATUS = [
        'dilaporkan' => 'Dilaporkan',
        'investigasi' => 'Dalam Investigasi',
        'terbukti' => 'Terbukti',
        'tidak_terbukti' => 'Tidak Terbukti',
        'selesai' => 'Selesai',
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jenisPelanggaran()
    {
        return $this->belongsTo(JenisPelanggaran::class);
    }

    public function dilaporkanOleh()
    {
        return $this->belongsTo(User::class, 'dilaporkan_oleh');
    }

    public function sanksi()
    {
        return $this->hasOne(SanksiPegawai::class);
    }

    public function sanksiList()
    {
        return $this->hasMany(SanksiPegawai::class);
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

    // Alias untuk kompatibilitas view
    public function getTanggalKejadianAttribute()
    {
        return $this->tanggal_pelanggaran;
    }

    // Tingkat diambil dari jenis pelanggaran
    public function getTingkatAttribute(): string
    {
        return $this->jenisPelanggaran->tingkat ?? 'ringan';
    }

    public function getTingkatLabelAttribute(): string
    {
        return match($this->tingkat) {
            'ringan' => 'Ringan',
            'sedang' => 'Sedang',
            'berat' => 'Berat',
            'sangat_berat' => 'Sangat Berat',
            default => ucfirst($this->tingkat),
        };
    }

    public function getTingkatColorAttribute(): string
    {
        return match($this->tingkat) {
            'ringan' => 'success',
            'sedang' => 'warning',
            'berat' => 'danger',
            'sangat_berat' => 'dark',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'dilaporkan' => 'warning',
            'investigasi' => 'info',
            'terbukti' => 'danger',
            'tidak_terbukti' => 'success',
            'selesai' => 'secondary',
            default => 'secondary',
        };
    }
}
