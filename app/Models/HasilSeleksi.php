<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilSeleksi extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'hasil_seleksi';

    protected $fillable = [
        'calon_mahasiswa_id',
        'gelombang_pmb_id',
        'nilai_total',
        'ranking',
        'status',
        'program_studi_diterima_id',
        'catatan',
        'tanggal_pengumuman',
        'diproses_oleh',
    ];

    protected $casts = [
        'nilai_total' => 'decimal:2',
        'tanggal_pengumuman' => 'datetime',
    ];

    // Relationships
    public function calonMahasiswa()
    {
        return $this->belongsTo(CalonMahasiswa::class, 'calon_mahasiswa_id');
    }

    public function gelombangPmb()
    {
        return $this->belongsTo(GelombangPmb::class, 'gelombang_pmb_id');
    }

    public function programStudiDiterima()
    {
        return $this->belongsTo(ProgramStudi::class, 'program_studi_diterima_id');
    }

    public function diprosesOleh()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    // Scopes
    public function scopeLulus($query)
    {
        return $query->where('status', 'lulus');
    }

    public function scopeTidakLulus($query)
    {
        return $query->where('status', 'tidak_lulus');
    }

    public function scopeCadangan($query)
    {
        return $query->where('status', 'cadangan');
    }

    // Helper Methods
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'lulus' => 'Lulus',
            'tidak_lulus' => 'Tidak Lulus',
            'cadangan' => 'Cadangan',
            default => 'Belum Diproses',
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'lulus' => 'success',
            'tidak_lulus' => 'danger',
            'cadangan' => 'warning',
            default => 'secondary',
        };
    }

    public function isLulus()
    {
        return $this->status === 'lulus';
    }
}
