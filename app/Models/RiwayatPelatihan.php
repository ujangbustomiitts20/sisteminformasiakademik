<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPelatihan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'riwayat_pelatihan';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'jenis',
        'nama_pelatihan',
        'penyelenggara',
        'tempat',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_jam',
        'no_sertifikat',
        'tahun',
        'file_sertifikat',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'jumlah_jam' => 'integer',
        'tahun' => 'integer',
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
