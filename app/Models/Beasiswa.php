<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Beasiswa extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'beasiswa';

    protected $fillable = [
        'kode',
        'nama',
        'jenis',
        'tipe_potongan',
        'nilai_potongan',
        'sumber_dana',
        'kuota',
        'persyaratan',
        'is_active',
    ];

    protected $casts = [
        'nilai_potongan' => 'decimal:2',
        'kuota' => 'integer',
        'is_active' => 'boolean',
    ];

    public const JENIS = [
        'Beasiswa' => 'Beasiswa',
        'Potongan' => 'Potongan',
        'Keringanan' => 'Keringanan',
    ];

    public const TIPE_POTONGAN = [
        'Persen' => 'Persentase (%)',
        'Nominal' => 'Nominal (Rp)',
    ];

    public function penerima()
    {
        return $this->hasMany(PenerimaBeasiswa::class);
    }

    public function penerimaAktif()
    {
        return $this->hasMany(PenerimaBeasiswa::class)->where('status', 'Disetujui');
    }

    public function getJumlahPenerimaAttribute()
    {
        return $this->penerimaAktif()->count();
    }

    public function getSisaKuotaAttribute()
    {
        if (!$this->kuota) {
            return null; // unlimited
        }
        return max(0, $this->kuota - $this->jumlah_penerima);
    }

    public function getNilaiPotonganLabelAttribute()
    {
        if ($this->tipe_potongan === 'Persen') {
            return $this->nilai_potongan . '%';
        }
        return 'Rp ' . number_format($this->nilai_potongan, 0, ',', '.');
    }

    public function hitungPotongan($nominal)
    {
        if ($this->tipe_potongan === 'Persen') {
            return $nominal * $this->nilai_potongan / 100;
        }
        return min($this->nilai_potongan, $nominal);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
