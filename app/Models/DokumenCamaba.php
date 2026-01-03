<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenCamaba extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'dokumen_camaba';

    protected $fillable = [
        'calon_mahasiswa_id',
        'jenis_dokumen',
        'nama_file',
        'path_file',
        'status_verifikasi',
        'catatan_verifikasi',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    // Relationships
    public function calonMahasiswa()
    {
        return $this->belongsTo(CalonMahasiswa::class, 'calon_mahasiswa_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status_verifikasi', 'pending');
    }

    public function scopeValid($query)
    {
        return $query->where('status_verifikasi', 'valid');
    }

    public function scopeTidakValid($query)
    {
        return $query->where('status_verifikasi', 'tidak_valid');
    }

    // Helper Methods
    public function getStatusLabelAttribute()
    {
        return match($this->status_verifikasi) {
            'pending' => 'Menunggu Verifikasi',
            'valid' => 'Valid',
            'tidak_valid' => 'Tidak Valid',
            default => $this->status_verifikasi,
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status_verifikasi) {
            'pending' => 'warning',
            'valid' => 'success',
            'tidak_valid' => 'danger',
            default => 'secondary',
        };
    }

    public function getJenisDokumenLabelAttribute()
    {
        $labels = [
            'ktp' => 'KTP',
            'kk' => 'Kartu Keluarga',
            'akta' => 'Akta Kelahiran',
            'ijazah' => 'Ijazah',
            'skhun' => 'SKHUN',
            'rapor' => 'Rapor',
            'foto' => 'Pas Foto',
            'surat_sehat' => 'Surat Keterangan Sehat',
            'surat_kelakuan_baik' => 'SKCK',
            'sertifikat' => 'Sertifikat Prestasi',
            'lainnya' => 'Lainnya',
        ];
        return $labels[$this->jenis_dokumen] ?? $this->jenis_dokumen;
    }
}
