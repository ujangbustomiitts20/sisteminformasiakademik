<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class JadwalKuliah extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'jadwal_kuliah';

    protected $fillable = [
        'tahun_akademik_id',
        'mata_kuliah_id',
        'dosen_id',
        'ruangan_id',
        'kelas',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'kuota',
    ];

    protected $casts = [
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
    ];

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function krs()
    {
        return $this->hasMany(Krs::class);
    }

    public function pertemuan()
    {
        return $this->hasMany(Pertemuan::class)->orderBy('pertemuan_ke');
    }

    // Hitung jumlah peserta yang sudah mendaftar
    public function jumlahPeserta()
    {
        return $this->krs()->where('status', 'Disetujui')->count();
    }

    // Check apakah masih ada kuota
    public function sisaKuota()
    {
        return $this->kuota - $this->jumlahPeserta();
    }

    // Get jadwal lengkap string
    public function getJadwalLengkapAttribute()
    {
        return $this->hari . ', ' . date('H:i', strtotime($this->jam_mulai)) . ' - ' . date('H:i', strtotime($this->jam_selesai));
    }
}
