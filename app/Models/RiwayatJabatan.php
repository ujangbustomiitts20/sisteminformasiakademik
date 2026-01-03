<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatJabatan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'riwayat_jabatan';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'nama_jabatan',
        'jenis_jabatan',
        'unit_kerja_jabatan',
        'jabatan_fungsional',
        'no_sk',
        'tanggal_sk',
        'tmt_jabatan',
        'tmt_selesai',
        'pejabat_penetap',
        'pejabat_sk',
        'angka_kredit',
        'file_sk',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'tmt_jabatan' => 'date',
        'tmt_selesai' => 'date',
        'angka_kredit' => 'decimal:2',
    ];

    // Accessor untuk kompatibilitas - jika nama_jabatan kosong, gunakan jabatan_fungsional
    public function getNamaJabatanAttribute($value)
    {
        return $value ?: $this->attributes['jabatan_fungsional'] ?? null;
    }

    // Accessor untuk unit_kerja (alias)
    public function getUnitKerjaAttribute()
    {
        return $this->unit_kerja_jabatan;
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
