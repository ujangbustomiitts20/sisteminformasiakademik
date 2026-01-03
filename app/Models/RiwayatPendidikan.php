<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPendidikan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'riwayat_pendidikan';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'jenjang',
        'nama_institusi',
        'program_studi',
        'jurusan',
        'no_ijazah',
        'tanggal_ijazah',
        'tahun_masuk',
        'tahun_lulus',
        'judul_tugas_akhir',
        'ipk',
        'file_ijazah',
        'file_transkrip',
    ];

    protected $casts = [
        'tanggal_ijazah' => 'date',
        'tahun_masuk' => 'integer',
        'tahun_lulus' => 'integer',
        'ipk' => 'decimal:2',
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
