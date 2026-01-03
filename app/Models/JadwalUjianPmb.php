<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalUjianPmb extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'jadwal_ujian_pmb';

    protected $fillable = [
        'gelombang_pmb_id',
        'nama_ujian',
        'tanggal_ujian',
        'jam_mulai',
        'jam_selesai',
        'lokasi',
        'ruangan',
        'kapasitas',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_ujian' => 'date',
    ];

    // Relationships
    public function gelombangPmb()
    {
        return $this->belongsTo(GelombangPmb::class, 'gelombang_pmb_id');
    }

    public function peserta()
    {
        return $this->hasMany(PesertaUjianPmb::class, 'jadwal_ujian_pmb_id');
    }

    // Helper Methods
    public function getTotalPesertaAttribute()
    {
        return $this->peserta()->count();
    }

    public function getTotalHadirAttribute()
    {
        return $this->peserta()->where('hadir', true)->count();
    }

    public function getSisaKapasitasAttribute()
    {
        if (!$this->kapasitas) return null;
        return max(0, $this->kapasitas - $this->total_peserta);
    }

    public function getWaktuUjianAttribute()
    {
        return $this->jam_mulai . ' - ' . $this->jam_selesai;
    }

    public function isSelesai()
    {
        return $this->tanggal_ujian < now()->toDateString();
    }

    public function isBerlangsung()
    {
        $today = now()->toDateString();
        $currentTime = now()->format('H:i:s');
        return $this->tanggal_ujian == $today 
            && $this->jam_mulai <= $currentTime 
            && $this->jam_selesai >= $currentTime;
    }
}
