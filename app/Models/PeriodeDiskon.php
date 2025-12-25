<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;
use Carbon\Carbon;

class PeriodeDiskon extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'periode_diskon';

    protected $fillable = [
        'kode',
        'nama',
        'jenis_potongan_id',
        'tipe_nilai',
        'nilai',
        'nilai_max',
        'min_transaksi',
        'tanggal_mulai',
        'tanggal_selesai',
        'kuota',
        'kuota_terpakai',
        'berlaku_untuk',
        'syarat_ketentuan',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'nilai_max' => 'decimal:2',
        'min_transaksi' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'kuota' => 'integer',
        'kuota_terpakai' => 'integer',
        'berlaku_untuk' => 'array',
        'is_active' => 'boolean',
    ];

    public const TIPE_NILAI = [
        'persen' => 'Persentase (%)',
        'nominal' => 'Nominal (Rp)',
    ];

    public function jenisPotongan()
    {
        return $this->belongsTo(JenisPotongan::class);
    }

    public function potonganMahasiswa()
    {
        return $this->hasMany(PotonganMahasiswa::class);
    }

    public function riwayatPotongan()
    {
        return $this->hasMany(RiwayatPotongan::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTipeNilaiLabelAttribute()
    {
        return self::TIPE_NILAI[$this->tipe_nilai] ?? $this->tipe_nilai;
    }

    public function getNilaiLabelAttribute()
    {
        if ($this->tipe_nilai === 'persen') {
            return $this->nilai . '%';
        }
        return 'Rp ' . number_format($this->nilai, 0, ',', '.');
    }

    public function getSisaKuotaAttribute()
    {
        if (!$this->kuota) {
            return null; // unlimited
        }
        return max(0, $this->kuota - $this->kuota_terpakai);
    }

    public function getIsValidAttribute()
    {
        $now = Carbon::now()->startOfDay();
        return $this->is_active 
            && $this->tanggal_mulai <= $now 
            && $this->tanggal_selesai >= $now
            && ($this->kuota === null || $this->sisa_kuota > 0);
    }

    public function getStatusLabelAttribute()
    {
        if (!$this->is_active) {
            return 'Nonaktif';
        }
        
        $now = Carbon::now()->startOfDay();
        if ($this->tanggal_mulai > $now) {
            return 'Belum Dimulai';
        }
        if ($this->tanggal_selesai < $now) {
            return 'Berakhir';
        }
        if ($this->kuota && $this->sisa_kuota <= 0) {
            return 'Kuota Habis';
        }
        return 'Aktif';
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status_label) {
            'Aktif' => 'success',
            'Belum Dimulai' => 'warning',
            'Berakhir' => 'secondary',
            'Kuota Habis' => 'danger',
            'Nonaktif' => 'secondary',
            default => 'secondary'
        };
    }

    public function hitungPotongan($nominal)
    {
        // Cek minimum transaksi
        if ($this->min_transaksi && $nominal < $this->min_transaksi) {
            return 0;
        }
        
        if ($this->tipe_nilai === 'persen') {
            $potongan = $nominal * $this->nilai / 100;
            if ($this->nilai_max) {
                return min($potongan, $this->nilai_max);
            }
            return $potongan;
        }
        return min($this->nilai, $nominal);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = Carbon::now()->startOfDay();
        return $query->where('is_active', true)
            ->where('tanggal_mulai', '<=', $now)
            ->where('tanggal_selesai', '>=', $now)
            ->where(function ($q) {
                $q->whereNull('kuota')
                    ->orWhereRaw('kuota > kuota_terpakai');
            });
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->kode)) {
                $model->kode = self::generateKode();
            }
        });
    }

    public static function generateKode()
    {
        $prefix = 'PRD' . date('Ym');
        $last = self::where('kode', 'like', $prefix . '%')
            ->orderBy('kode', 'desc')
            ->first();
        
        if ($last) {
            $lastNumber = intval(substr($last->kode, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }
}
