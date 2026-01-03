<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiPegawai extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'presensi_pegawai';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',
        'lokasi_masuk',
        'lokasi_keluar',
        'latitude_masuk',
        'longitude_masuk',
        'latitude_keluar',
        'longitude_keluar',
        'foto_masuk',
        'foto_keluar',
        'keterangan',
        'device_info',
        'ip_address',
        'is_manual',
        'diinput_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_manual' => 'boolean',
        'latitude_masuk' => 'decimal:8',
        'longitude_masuk' => 'decimal:8',
        'latitude_keluar' => 'decimal:8',
        'longitude_keluar' => 'decimal:8',
    ];

    const STATUS = [
        'hadir' => 'Hadir',
        'terlambat' => 'Terlambat',
        'sakit' => 'Sakit',
        'izin' => 'Izin',
        'cuti' => 'Cuti',
        'alpha' => 'Alpha',
        'dinas_luar' => 'Dinas Luar',
    ];

    // Relationships
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function diinputOleh()
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'hadir' => '<span class="badge bg-success">Hadir</span>',
            'terlambat' => '<span class="badge bg-warning text-dark">Terlambat</span>',
            'sakit' => '<span class="badge bg-info">Sakit</span>',
            'izin' => '<span class="badge bg-primary">Izin</span>',
            'cuti' => '<span class="badge bg-secondary">Cuti</span>',
            'alpha' => '<span class="badge bg-danger">Alpha</span>',
            'dinas_luar' => '<span class="badge bg-dark">Dinas Luar</span>',
        ];
        return $badges[$this->status] ?? '<span class="badge bg-secondary">' . $this->status . '</span>';
    }

    public function getNamaPegawaiAttribute()
    {
        if ($this->dosen_id) {
            return $this->dosen->nama ?? '-';
        }
        return $this->pegawai->nama ?? '-';
    }

    public function getDurasiKerjaAttribute()
    {
        if ($this->jam_masuk && $this->jam_keluar) {
            $masuk = \Carbon\Carbon::parse($this->jam_masuk);
            $keluar = \Carbon\Carbon::parse($this->jam_keluar);
            $diff = $masuk->diff($keluar);
            return $diff->format('%H:%I');
        }
        return '-';
    }

    // Scopes
    public function scopeByTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByBulan($query, $bulan, $tahun)
    {
        return $query->whereMonth('tanggal', $bulan)
                     ->whereYear('tanggal', $tahun);
    }

    // Helper untuk cek keterlambatan
    public function checkTerlambat($jamKerja = '08:00')
    {
        if (!$this->jam_masuk) return false;
        
        $jamMasuk = \Carbon\Carbon::parse($this->jam_masuk);
        $jamKerja = \Carbon\Carbon::parse($jamKerja);
        $toleransi = SettingJamKerja::getActive()->toleransi_terlambat ?? 15;
        
        return $jamMasuk->gt($jamKerja->addMinutes($toleransi));
    }

    // Alias method isTerlambat untuk view
    public function isTerlambat()
    {
        return $this->status === 'terlambat' || $this->checkTerlambat();
    }

    // Helper untuk hitung jam kerja
    public function hitungJamKerja()
    {
        if ($this->jam_masuk && $this->jam_keluar) {
            $masuk = \Carbon\Carbon::parse($this->jam_masuk);
            $keluar = \Carbon\Carbon::parse($this->jam_keluar);
            $diff = $masuk->diff($keluar);
            return $diff->format('%H:%I');
        }
        return '-';
    }
}
