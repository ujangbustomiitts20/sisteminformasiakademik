<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomponenGaji extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'komponen_gaji';

    protected $fillable = [
        'kode',
        'nama',
        'jenis',
        'tipe_nilai',
        'nilai_default',
        'wajib',
        'keterangan',
        'aktif',
        'urutan',
    ];

    protected $casts = [
        'nilai_default' => 'decimal:2',
        'wajib' => 'boolean',
        'aktif' => 'boolean',
    ];

    /**
     * Scope untuk komponen aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Scope untuk komponen pendapatan
     */
    public function scopePendapatan($query)
    {
        return $query->where('jenis', 'pendapatan');
    }

    /**
     * Scope untuk komponen potongan
     */
    public function scopePotongan($query)
    {
        return $query->where('jenis', 'potongan');
    }

    /**
     * Generate kode komponen
     */
    public static function generateKode($jenis = 'pendapatan')
    {
        $prefix = $jenis === 'pendapatan' ? 'TJ' : 'PT';
        $lastKode = self::where('kode', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(kode, 3) AS UNSIGNED) DESC')
            ->first();
        
        if ($lastKode) {
            $lastNumber = (int) substr($lastKode->kode, 2);
            return $prefix . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        }
        
        return $prefix . '001';
    }
}
