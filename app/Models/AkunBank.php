<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AkunBank extends Model
{
    use HasFactory;

    protected $table = 'akun_bank';

    protected $fillable = [
        'nama_bank',
        'nomor_rekening',
        'nama_rekening',
        'cabang',
        'kode_bank',
        'tipe',
        'saldo_awal',
        'saldo_sistem',
        'is_active',
    ];

    protected $casts = [
        'saldo_awal' => 'decimal:2',
        'saldo_sistem' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    const TIPE_PENAMPUNGAN = 'penampungan';
    const TIPE_OPERASIONAL = 'operasional';
    const TIPE_BEASISWA = 'beasiswa';

    // Relations
    public function mutasiBank()
    {
        return $this->hasMany(MutasiBank::class, 'akun_bank_id');
    }

    // Alias for mutasiBank
    public function mutasi()
    {
        return $this->mutasiBank();
    }

    public function rekonsiliasi()
    {
        return $this->hasMany(Rekonsiliasi::class, 'akun_bank_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByTipe($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    // Methods
    public function getNamaLengkapAttribute()
    {
        return "{$this->nama_bank} - {$this->nomor_rekening} ({$this->nama_rekening})";
    }

    public function updateSaldoSistem()
    {
        $totalKredit = $this->mutasiBank()->where('tipe', 'kredit')->sum('nominal');
        $totalDebit = $this->mutasiBank()->where('tipe', 'debit')->sum('nominal');
        
        $this->saldo_sistem = $this->saldo_awal + $totalKredit - $totalDebit;
        $this->save();
        
        return $this->saldo_sistem;
    }
}
