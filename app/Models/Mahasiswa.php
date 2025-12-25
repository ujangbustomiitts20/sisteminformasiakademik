<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Mahasiswa extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'mahasiswa';

    protected $fillable = [
        'user_id',
        'program_studi_id',
        'dosen_wali_id',
        'nim',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'telepon',
        'no_hp',
        'email',
        'angkatan',
        'semester_aktif',
        'status',
        'foto',
        'nama_ayah',
        'nama_ibu',
        'pekerjaan_ayah',
        'pekerjaan_ibu',
        'no_hp_ortu',
        'penghasilan_ortu',
        'alamat_ortu',
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

    public function dosenWali()
    {
        return $this->belongsTo(Dosen::class, 'dosen_wali_id');
    }

    public function krs()
    {
        return $this->hasMany(Krs::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    // Hitung IPK
    public function hitungIPK()
    {
        $krs = $this->krs()->whereHas('nilai', function($q) {
            $q->whereNotNull('bobot');
        })->with(['nilai', 'jadwalKuliah.mataKuliah'])->get();

        $totalBobot = 0;
        $totalSks = 0;

        foreach ($krs as $k) {
            if ($k->nilai && $k->jadwalKuliah && $k->jadwalKuliah->mataKuliah) {
                $sks = $k->jadwalKuliah->mataKuliah->sks;
                $totalBobot += $k->nilai->bobot * $sks;
                $totalSks += $sks;
            }
        }

        return $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;
    }

    // Hitung total SKS yang sudah ditempuh
    public function totalSksLulus()
    {
        return $this->krs()
            ->whereHas('nilai', function($q) {
                $q->where('huruf', '!=', 'E');
            })
            ->with('jadwalKuliah.mataKuliah')
            ->get()
            ->sum(function($krs) {
                return $krs->jadwalKuliah->mataKuliah->sks ?? 0;
            });
    }
}
