<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarUlang extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'daftar_ulang';

    protected $fillable = [
        'calon_mahasiswa_id',
        'program_studi_id',
        'no_daftar_ulang',
        'biaya_daftar_ulang',
        'biaya_ukt',
        'biaya',
        'total_biaya',
        'status',
        'tanggal_bayar',
        'tanggal_expired',
        'tanggal_verifikasi',
        'nim_generated',
        'mahasiswa_id',
        'diproses_oleh',
    ];

    protected $casts = [
        'biaya_daftar_ulang' => 'decimal:2',
        'biaya_ukt' => 'decimal:2',
        'biaya' => 'decimal:2',
        'total_biaya' => 'decimal:2',
        'tanggal_bayar' => 'datetime',
        'tanggal_expired' => 'date',
        'tanggal_verifikasi' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->no_daftar_ulang)) {
                $model->no_daftar_ulang = self::generateNoDaftarUlang();
            }
            $model->total_biaya = ($model->biaya_daftar_ulang ?? 0) + ($model->biaya_ukt ?? 0);
        });

        static::updating(function ($model) {
            $model->total_biaya = ($model->biaya_daftar_ulang ?? 0) + ($model->biaya_ukt ?? 0);
        });
    }

    // Generate Nomor Daftar Ulang
    public static function generateNoDaftarUlang()
    {
        $prefix = 'DU' . date('Ymd');
        
        $lastNo = self::where('no_daftar_ulang', 'like', $prefix . '%')
                      ->orderBy('no_daftar_ulang', 'desc')
                      ->first();

        if ($lastNo) {
            $lastNumber = intval(substr($lastNo->no_daftar_ulang, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function calonMahasiswa()
    {
        return $this->belongsTo(CalonMahasiswa::class, 'calon_mahasiswa_id');
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'program_studi_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function diprosesOleh()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeMenungguBayar($query)
    {
        return $query->where('status', 'menunggu_bayar');
    }

    public function scopeSudahBayar($query)
    {
        return $query->where('status', 'sudah_bayar');
    }

    public function scopeLunas($query)
    {
        return $query->where('status', 'lunas');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    // Helper Methods
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'menunggu_bayar' => 'Menunggu Pembayaran',
            'sudah_bayar' => 'Sudah Bayar',
            'lunas' => 'Pembayaran Lunas',
            'verifikasi_dokumen' => 'Verifikasi Dokumen',
            'selesai' => 'Selesai',
            'menjadi_mahasiswa' => 'Menjadi Mahasiswa',
            'batal' => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'menunggu_bayar' => 'warning',
            'sudah_bayar' => 'info',
            'lunas' => 'info',
            'verifikasi_dokumen' => 'primary',
            'selesai' => 'success',
            'menjadi_mahasiswa' => 'success',
            'batal' => 'danger',
            default => 'secondary',
        };
    }

    public function getBiayaFormatAttribute()
    {
        $biaya = $this->biaya ?? $this->biaya_daftar_ulang ?? 0;
        return 'Rp ' . number_format($biaya, 0, ',', '.');
    }

    public function isSelesai()
    {
        return in_array($this->status, ['selesai', 'menjadi_mahasiswa']);
    }

    public function isSudahBayar()
    {
        return in_array($this->status, ['sudah_bayar', 'lunas']);
    }
}
