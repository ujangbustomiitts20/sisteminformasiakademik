<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Dosen extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'dosen';

    protected $fillable = [
        'user_id',
        'program_studi_id',
        'nidn',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'telepon',
        'email',
        'jabatan_fungsional',
        'golongan',
        'status',
        'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function mahasiswaWali()
    {
        return $this->hasMany(Mahasiswa::class, 'dosen_wali_id');
    }

    public function jadwalKuliah()
    {
        return $this->hasMany(JadwalKuliah::class);
    }
}
