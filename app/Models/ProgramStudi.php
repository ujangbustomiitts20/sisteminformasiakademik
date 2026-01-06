<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class ProgramStudi extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'program_studi';

    protected $fillable = [
        'fakultas_id',
        'kode',
        'nama',
        'singkatan',
        'jenjang',
        'kaprodi',
        'total_sks',
        'akreditasi',
        'tanggal_akreditasi',
        'no_sk_akreditasi',
        'tanggal_berdiri',
        'sk_pendirian',
        'gelar_lulusan',
        'visi',
        'misi',
        'kompetensi',
        'kuota',
        'alamat',
        'telepon',
        'email',
        'website',
        'logo',
    ];

    protected $casts = [
        'tanggal_akreditasi' => 'date',
        'tanggal_berdiri' => 'date',
    ];

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class);
    }

    public function dosen()
    {
        return $this->hasMany(Dosen::class);
    }

    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class);
    }

    public function mataKuliah()
    {
        return $this->hasMany(MataKuliah::class);
    }

    /**
     * Get akreditasi badge color
     */
    public function getAkreditasiBadgeAttribute()
    {
        return match($this->akreditasi) {
            'A', 'Unggul' => 'success',
            'B', 'Baik Sekali' => 'primary',
            'C', 'Baik' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Get full name with jenjang
     */
    public function getNamaLengkapAttribute()
    {
        return $this->jenjang . ' ' . $this->nama;
    }
}
