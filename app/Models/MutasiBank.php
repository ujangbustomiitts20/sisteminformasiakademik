<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiBank extends Model
{
    use HasFactory;

    protected $table = 'mutasi_bank';

    protected $fillable = [
        'akun_bank_id',
        'tanggal',
        'nomor_referensi',
        'tipe',
        'nominal',
        'saldo',
        'keterangan',
        'nama_pengirim',
        'status',
        'transaksi_pembayaran_id',
        'matched_by',
        'matched_at',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
        'saldo' => 'decimal:2',
        'matched_at' => 'datetime',
    ];

    const TIPE_KREDIT = 'kredit';
    const TIPE_DEBIT = 'debit';

    const STATUS_PENDING = 'pending';
    const STATUS_MATCHED = 'matched';
    const STATUS_UNMATCHED = 'unmatched';
    const STATUS_MANUAL = 'manual';

    // Accessor for referensi (alias for nomor_referensi)
    public function getReferensiAttribute()
    {
        return $this->nomor_referensi;
    }

    // Relations
    public function akunBank()
    {
        return $this->belongsTo(AkunBank::class, 'akun_bank_id');
    }

    public function transaksiPembayaran()
    {
        return $this->belongsTo(TransaksiPembayaran::class, 'transaksi_pembayaran_id');
    }

    public function matchedBy()
    {
        return $this->belongsTo(User::class, 'matched_by');
    }

    public function detailRekonsiliasi()
    {
        return $this->hasMany(DetailRekonsiliasi::class, 'mutasi_bank_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeMatched($query)
    {
        return $query->where('status', self::STATUS_MATCHED);
    }

    public function scopeUnmatched($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_UNMATCHED]);
    }

    public function scopeKredit($query)
    {
        return $query->where('tipe', self::TIPE_KREDIT);
    }

    public function scopeDebit($query)
    {
        return $query->where('tipe', self::TIPE_DEBIT);
    }

    public function scopePeriode($query, $dari, $sampai)
    {
        return $query->whereBetween('tanggal', [$dari, $sampai]);
    }

    // Methods
    public function match(TransaksiPembayaran $transaksi, $userId = null)
    {
        $this->transaksi_pembayaran_id = $transaksi->id;
        $this->status = self::STATUS_MATCHED;
        $this->matched_by = $userId ?? auth()->id();
        $this->matched_at = now();
        $this->save();

        return $this;
    }

    public function unmatch()
    {
        $this->transaksi_pembayaran_id = null;
        $this->status = self::STATUS_UNMATCHED;
        $this->matched_by = null;
        $this->matched_at = null;
        $this->save();

        return $this;
    }

    public function markAsManual($catatan = null)
    {
        $this->status = self::STATUS_MANUAL;
        $this->matched_by = auth()->id();
        $this->matched_at = now();
        if ($catatan) {
            $this->catatan = $catatan;
        }
        $this->save();

        return $this;
    }

    public function isMatched()
    {
        return $this->status === self::STATUS_MATCHED;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }
}
