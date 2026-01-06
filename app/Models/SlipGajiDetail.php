<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlipGajiDetail extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'slip_gaji_detail';

    protected $fillable = [
        'slip_gaji_id',
        'komponen_gaji_id',
        'nama_komponen',
        'jenis',
        'nilai',
        'keterangan',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    /**
     * Relasi ke slip gaji
     */
    public function slipGaji()
    {
        return $this->belongsTo(SlipGaji::class);
    }

    /**
     * Relasi ke komponen gaji
     */
    public function komponenGaji()
    {
        return $this->belongsTo(KomponenGaji::class);
    }

    /**
     * Scope untuk pendapatan
     */
    public function scopePendapatan($query)
    {
        return $query->where('jenis', 'pendapatan');
    }

    /**
     * Scope untuk potongan
     */
    public function scopePotongan($query)
    {
        return $query->where('jenis', 'potongan');
    }
}
