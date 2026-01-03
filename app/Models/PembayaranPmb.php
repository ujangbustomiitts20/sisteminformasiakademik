<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranPmb extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'pembayaran_pmb';

    protected $fillable = [
        'calon_mahasiswa_id',
        'no_pembayaran',
        'jenis_pembayaran',
        'jumlah',
        'metode_pembayaran',
        'no_va',
        'bank',
        'bukti_bayar',
        'status',
        'tanggal_bayar',
        'tanggal_expired',
        'verified_by',
        'verified_at',
        'catatan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal_bayar' => 'datetime',
        'tanggal_expired' => 'datetime',
        'verified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->no_pembayaran)) {
                $model->no_pembayaran = self::generateNoPembayaran();
            }
        });
    }

    // Generate Nomor Pembayaran
    public static function generateNoPembayaran()
    {
        $prefix = 'PAY' . date('Ymd');
        
        $lastNo = self::where('no_pembayaran', 'like', $prefix . '%')
                      ->orderBy('no_pembayaran', 'desc')
                      ->first();

        if ($lastNo) {
            $lastNumber = intval(substr($lastNo->no_pembayaran, -4));
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

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeMenungguVerifikasi($query)
    {
        return $query->where('status', 'menunggu_verifikasi');
    }

    public function scopeTerverifikasi($query)
    {
        return $query->where('status', 'terverifikasi');
    }

    // Helper Methods
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'terverifikasi' => 'Terverifikasi',
            'ditolak' => 'Ditolak',
            'expired' => 'Expired',
            default => $this->status,
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'menunggu_verifikasi' => 'info',
            'terverifikasi' => 'success',
            'ditolak' => 'danger',
            'expired' => 'secondary',
            default => 'secondary',
        };
    }

    public function getJenisPembayaranLabelAttribute()
    {
        return match($this->jenis_pembayaran) {
            'pendaftaran' => 'Biaya Pendaftaran',
            'daftar_ulang' => 'Biaya Daftar Ulang',
            default => $this->jenis_pembayaran,
        };
    }

    public function isExpired()
    {
        return $this->tanggal_expired && $this->tanggal_expired < now();
    }
}
