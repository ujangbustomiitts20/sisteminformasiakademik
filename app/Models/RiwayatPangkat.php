<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPangkat extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'riwayat_pangkat';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'pangkat',
        'golongan',
        'no_sk',
        'tanggal_sk',
        'tmt_pangkat',
        'pejabat_penetap',
        'masa_kerja_tahun',
        'masa_kerja_bulan',
        'file_sk',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'tmt_pangkat' => 'date',
        'masa_kerja_tahun' => 'decimal:2',
        'masa_kerja_bulan' => 'decimal:2',
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    // Helper untuk mendapatkan nama pangkat berdasarkan golongan
    public static function getPangkatByGolongan($golongan)
    {
        $mapping = [
            'I/a' => 'Juru Muda',
            'I/b' => 'Juru Muda Tk. I',
            'I/c' => 'Juru',
            'I/d' => 'Juru Tk. I',
            'II/a' => 'Pengatur Muda',
            'II/b' => 'Pengatur Muda Tk. I',
            'II/c' => 'Pengatur',
            'II/d' => 'Pengatur Tk. I',
            'III/a' => 'Penata Muda',
            'III/b' => 'Penata Muda Tk. I',
            'III/c' => 'Penata',
            'III/d' => 'Penata Tk. I',
            'IV/a' => 'Pembina',
            'IV/b' => 'Pembina Tk. I',
            'IV/c' => 'Pembina Utama Muda',
            'IV/d' => 'Pembina Utama Madya',
            'IV/e' => 'Pembina Utama',
        ];

        return $mapping[$golongan] ?? '-';
    }
}
