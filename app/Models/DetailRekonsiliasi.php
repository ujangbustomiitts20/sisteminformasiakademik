<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailRekonsiliasi extends Model
{
    use HasFactory;

    protected $table = 'detail_rekonsiliasi';

    protected $fillable = [
        'rekonsiliasi_id',
        'mutasi_bank_id',
        'transaksi_pembayaran_id',
        'status',
        'keterangan',
    ];

    const STATUS_MATCHED = 'matched';
    const STATUS_UNMATCHED = 'unmatched';
    const STATUS_MANUAL = 'manual';
    const STATUS_IGNORED = 'ignored';

    // Relations
    public function rekonsiliasi()
    {
        return $this->belongsTo(Rekonsiliasi::class, 'rekonsiliasi_id');
    }

    public function mutasiBank()
    {
        return $this->belongsTo(MutasiBank::class, 'mutasi_bank_id');
    }

    public function transaksiPembayaran()
    {
        return $this->belongsTo(TransaksiPembayaran::class, 'transaksi_pembayaran_id');
    }

    // Scopes
    public function scopeMatched($query)
    {
        return $query->where('status', self::STATUS_MATCHED);
    }

    public function scopeUnmatched($query)
    {
        return $query->where('status', self::STATUS_UNMATCHED);
    }

    // Methods
    public function match(TransaksiPembayaran $transaksi)
    {
        $this->transaksi_pembayaran_id = $transaksi->id;
        $this->status = self::STATUS_MATCHED;
        $this->save();

        // Update mutasi bank juga
        $this->mutasiBank->match($transaksi);

        return $this;
    }

    public function unmatch()
    {
        $this->transaksi_pembayaran_id = null;
        $this->status = self::STATUS_UNMATCHED;
        $this->save();

        // Update mutasi bank juga
        $this->mutasiBank->unmatch();

        return $this;
    }

    public function ignore($keterangan = null)
    {
        $this->status = self::STATUS_IGNORED;
        $this->keterangan = $keterangan ?? 'Diabaikan oleh admin';
        $this->save();

        return $this;
    }
}
