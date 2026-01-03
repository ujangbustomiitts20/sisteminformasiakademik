<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeUjian extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'periode_ujian';

    protected $fillable = [
        'nama',
        'tahun_akademik_id',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal_cetak_kartu',
        'minimal_kehadiran',
        'cek_pembayaran',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_cetak_kartu' => 'date',
        'minimal_kehadiran' => 'integer',
        'cek_pembayaran' => 'boolean',
    ];

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function jadwalUjian()
    {
        return $this->hasMany(JadwalUjian::class);
    }

    public function kartuUjian()
    {
        return $this->hasMany(KartuUjian::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function isAktif()
    {
        return $this->status === 'aktif';
    }

    public function isBisaCetakKartu()
    {
        if (!$this->tanggal_cetak_kartu) {
            return $this->status === 'aktif';
        }
        return now()->gte($this->tanggal_cetak_kartu) && $this->status === 'aktif';
    }

    public static function getJenisOptions()
    {
        return [
            'UTS' => 'Ujian Tengah Semester',
            'UAS' => 'Ujian Akhir Semester',
            'Susulan' => 'Ujian Susulan',
            'Remedial' => 'Ujian Remedial',
        ];
    }
}
