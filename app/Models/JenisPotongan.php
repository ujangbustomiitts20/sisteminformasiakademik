<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class JenisPotongan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'jenis_potongan';

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'deskripsi',
        'tipe_nilai',
        'nilai_default',
        'nilai_max',
        'is_stackable',
        'prioritas',
        'is_active',
    ];

    protected $casts = [
        'nilai_default' => 'decimal:2',
        'nilai_max' => 'decimal:2',
        'is_stackable' => 'boolean',
        'is_active' => 'boolean',
        'prioritas' => 'integer',
    ];

    public const KATEGORI = [
        'diskon' => 'Diskon',
        'potongan_khusus' => 'Potongan Khusus',
        'promo' => 'Promo',
        'keringanan' => 'Keringanan',
    ];

    public const TIPE_NILAI = [
        'persen' => 'Persentase (%)',
        'nominal' => 'Nominal (Rp)',
    ];

    public function periodeDiskon()
    {
        return $this->hasMany(PeriodeDiskon::class);
    }

    public function potonganMahasiswa()
    {
        return $this->hasMany(PotonganMahasiswa::class);
    }

    public function getKategoriLabelAttribute()
    {
        return self::KATEGORI[$this->kategori] ?? $this->kategori;
    }

    public function getTipeNilaiLabelAttribute()
    {
        return self::TIPE_NILAI[$this->tipe_nilai] ?? $this->tipe_nilai;
    }

    public function getNilaiDefaultLabelAttribute()
    {
        if ($this->tipe_nilai === 'persen') {
            return $this->nilai_default . '%';
        }
        return 'Rp ' . number_format($this->nilai_default, 0, ',', '.');
    }

    public function hitungPotongan($nominal)
    {
        if ($this->tipe_nilai === 'persen') {
            $potongan = $nominal * $this->nilai_default / 100;
            if ($this->nilai_max) {
                return min($potongan, $this->nilai_max);
            }
            return $potongan;
        }
        return min($this->nilai_default, $nominal);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->kode)) {
                $model->kode = self::generateKode($model->kategori);
            }
        });
    }

    public static function generateKode($kategori = 'diskon')
    {
        $prefix = match($kategori) {
            'diskon' => 'DSK',
            'potongan_khusus' => 'PTK',
            'promo' => 'PRM',
            'keringanan' => 'KRG',
            default => 'POT'
        };
        
        $last = self::where('kode', 'like', $prefix . '%')
            ->orderBy('kode', 'desc')
            ->first();
        
        if ($last) {
            $lastNumber = intval(substr($last->kode, 3));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }
}
