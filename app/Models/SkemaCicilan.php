<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkemaCicilan extends Model
{
    use HasFactory;

    protected $table = 'skema_cicilan';

    protected $fillable = [
        'nama',
        'jumlah_cicilan',
        'biaya_admin',
        'persentase_bunga',
        'minimal_tagihan',
        'interval_hari',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'biaya_admin' => 'decimal:2',
        'persentase_bunga' => 'decimal:2',
        'minimal_tagihan' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relations
    public function cicilan()
    {
        return $this->hasMany(Cicilan::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Methods
    public function hitungTotalBayar($nominal)
    {
        $bunga = $nominal * ($this->persentase_bunga / 100) * $this->jumlah_cicilan;
        return $nominal + $this->biaya_admin + $bunga;
    }

    public function hitungPerCicilan($nominal)
    {
        $total = $this->hitungTotalBayar($nominal);
        return ceil($total / $this->jumlah_cicilan);
    }

    // Accessor
    public function getStatusBadgeAttribute()
    {
        return $this->is_active 
            ? '<span class="badge bg-success">Aktif</span>'
            : '<span class="badge bg-secondary">Nonaktif</span>';
    }
}
