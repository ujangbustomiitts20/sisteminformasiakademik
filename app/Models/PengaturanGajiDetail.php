<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanGajiDetail extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'pengaturan_gaji_detail';

    protected $fillable = [
        'pengaturan_gaji_id',
        'komponen_gaji_id',
        'nilai',
        'aktif',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'aktif' => 'boolean',
    ];

    /**
     * Relasi ke pengaturan gaji
     */
    public function pengaturanGaji()
    {
        return $this->belongsTo(PengaturanGaji::class);
    }

    /**
     * Relasi ke komponen gaji
     */
    public function komponenGaji()
    {
        return $this->belongsTo(KomponenGaji::class);
    }
}
