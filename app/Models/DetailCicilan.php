<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailCicilan extends Model
{
    use HasFactory;

    protected $table = 'detail_cicilan';

    protected $fillable = [
        'cicilan_id',
        'cicilan_ke',
        'nominal',
        'jatuh_tempo',
        'tanggal_bayar',
        'transaksi_pembayaran_id',
        'denda',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'denda' => 'decimal:2',
        'jatuh_tempo' => 'date',
        'tanggal_bayar' => 'date',
    ];

    const STATUS_BELUM_BAYAR = 'Belum Bayar';
    const STATUS_DIBAYAR = 'Dibayar';
    const STATUS_TERLAMBAT = 'Terlambat';
    const STATUS_DIBAYAR_TERLAMBAT = 'Dibayar Terlambat';

    // Relations
    public function cicilan()
    {
        return $this->belongsTo(Cicilan::class);
    }

    public function transaksiPembayaran()
    {
        return $this->belongsTo(TransaksiPembayaran::class);
    }

    // Scopes
    public function scopeBelumBayar($query)
    {
        return $query->whereIn('status', [self::STATUS_BELUM_BAYAR, self::STATUS_TERLAMBAT]);
    }

    public function scopeSudahBayar($query)
    {
        return $query->whereIn('status', [self::STATUS_DIBAYAR, self::STATUS_DIBAYAR_TERLAMBAT]);
    }

    public function scopeJatuhTempoHariIni($query)
    {
        return $query->whereDate('jatuh_tempo', today());
    }

    public function scopeTerlambat($query)
    {
        return $query->where('status', self::STATUS_BELUM_BAYAR)
            ->whereDate('jatuh_tempo', '<', today());
    }

    public function scopeAkanJatuhTempo($query, $hari = 7)
    {
        return $query->where('status', self::STATUS_BELUM_BAYAR)
            ->whereDate('jatuh_tempo', '>=', today())
            ->whereDate('jatuh_tempo', '<=', today()->addDays($hari));
    }

    // Methods
    public function bayar($transaksiId = null, $denda = 0)
    {
        $this->tanggal_bayar = now();
        $this->transaksi_pembayaran_id = $transaksiId;
        $this->denda = $denda;
        
        if ($this->jatuh_tempo < now()->startOfDay()) {
            $this->status = self::STATUS_DIBAYAR_TERLAMBAT;
        } else {
            $this->status = self::STATUS_DIBAYAR;
        }

        $this->save();

        // Update status cicilan parent
        $this->cicilan->updateStatus();
    }

    public function getTotalHarusBayar()
    {
        return $this->nominal + $this->denda;
    }

    public function isTerlambat()
    {
        return $this->status === self::STATUS_BELUM_BAYAR && $this->jatuh_tempo < now()->startOfDay();
    }

    public function getHariTerlambat()
    {
        if (!$this->isTerlambat()) return 0;
        return $this->jatuh_tempo->diffInDays(now());
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Belum Bayar' => '<span class="badge bg-warning">Belum Bayar</span>',
            'Dibayar' => '<span class="badge bg-success">Dibayar</span>',
            'Terlambat' => '<span class="badge bg-danger">Terlambat</span>',
            'Dibayar Terlambat' => '<span class="badge bg-info">Dibayar Terlambat</span>',
        ];

        // Check if terlambat but status masih Belum Bayar
        if ($this->status === self::STATUS_BELUM_BAYAR && $this->jatuh_tempo < now()->startOfDay()) {
            return '<span class="badge bg-danger">Terlambat</span>';
        }

        return $badges[$this->status] ?? '<span class="badge bg-secondary">'.$this->status.'</span>';
    }
}
