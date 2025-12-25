<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class TransaksiPembayaran extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'transaksi_pembayaran';

    protected $fillable = [
        'no_transaksi',
        'tagihan_id',
        'mahasiswa_id',
        'jumlah',
        'tanggal_bayar',
        'metode_pembayaran',
        'bank',
        'no_referensi',
        'bukti_bayar',
        'status',
        'verified_by',
        'verified_at',
        'catatan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal_bayar' => 'date',
        'verified_at' => 'datetime',
    ];

    public const METODE = [
        'Tunai' => 'Tunai',
        'Transfer Bank' => 'Transfer Bank',
        'Virtual Account' => 'Virtual Account',
        'QRIS' => 'QRIS',
        'Kartu Kredit' => 'Kartu Kredit',
        'Lainnya' => 'Lainnya',
    ];

    public const STATUS = [
        'Pending' => 'Menunggu Verifikasi',
        'Verified' => 'Terverifikasi',
        'Rejected' => 'Ditolak',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->no_transaksi)) {
                $model->no_transaksi = self::generateNoTransaksi();
            }
        });

        static::created(function ($model) {
            if ($model->status === 'Verified') {
                $model->updateTagihanAmount();
            }
        });

        static::updated(function ($model) {
            if ($model->wasChanged('status')) {
                $model->updateTagihanAmount();
            }
        });
    }

    public static function generateNoTransaksi()
    {
        $prefix = 'TRX' . date('Ymd');
        $last = self::where('no_transaksi', 'like', $prefix . '%')
            ->orderBy('no_transaksi', 'desc')
            ->first();
        
        if ($last) {
            $lastNumber = intval(substr($last->no_transaksi, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }

    public function updateTagihanAmount()
    {
        $tagihan = $this->tagihan;
        if ($tagihan) {
            $totalDibayar = $tagihan->transaksi()
                ->where('status', 'Verified')
                ->sum('jumlah');
            
            $tagihan->update(['jumlah_dibayar' => $totalDibayar]);
        }
    }

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function mutasiBank()
    {
        return $this->hasOne(MutasiBank::class, 'transaksi_pembayaran_id');
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class, 'transaksi_pembayaran_id');
    }

    // Accessor for nomor_transaksi (alias for no_transaksi)
    public function getNomorTransaksiAttribute()
    {
        return $this->no_transaksi;
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'Verified' => '<span class="badge bg-success">Terverifikasi</span>',
            'Rejected' => '<span class="badge bg-danger">Ditolak</span>',
            default => '<span class="badge bg-warning">Pending</span>',
        };
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUS[$this->status] ?? $this->status;
    }
}
