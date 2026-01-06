<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlipGaji extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'slip_gaji';

    protected $fillable = [
        'no_slip',
        'dosen_id',
        'pegawai_id',
        'tahun',
        'bulan',
        'tanggal_slip',
        'gaji_pokok',
        'total_tunjangan',
        'total_potongan',
        'gaji_kotor',
        'gaji_bersih',
        'status',
        'tanggal_bayar',
        'metode_pembayaran',
        'no_referensi',
        'disetujui_oleh',
        'tanggal_disetujui',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_slip' => 'date',
        'tanggal_bayar' => 'date',
        'tanggal_disetujui' => 'datetime',
        'gaji_pokok' => 'decimal:2',
        'total_tunjangan' => 'decimal:2',
        'total_potongan' => 'decimal:2',
        'gaji_kotor' => 'decimal:2',
        'gaji_bersih' => 'decimal:2',
    ];

    /**
     * Boot method untuk auto-generate nomor slip dan kalkulasi
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->no_slip)) {
                $model->no_slip = self::generateNoSlip($model->tahun, $model->bulan);
            }
            if (empty($model->tanggal_slip)) {
                $model->tanggal_slip = now();
            }
            self::hitungTotal($model);
        });

        static::updating(function ($model) {
            self::hitungTotal($model);
        });
    }

    /**
     * Generate nomor slip
     */
    public static function generateNoSlip($tahun, $bulan)
    {
        $prefix = 'SLP' . $tahun . str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $lastSlip = self::where('no_slip', 'like', $prefix . '%')
            ->orderBy('no_slip', 'desc')
            ->first();

        if ($lastSlip) {
            $lastNumber = (int) substr($lastSlip->no_slip, -5);
            return $prefix . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        }

        return $prefix . '00001';
    }

    /**
     * Hitung total
     */
    protected static function hitungTotal($model)
    {
        $model->gaji_kotor = $model->gaji_pokok + $model->total_tunjangan;
        $model->gaji_bersih = $model->gaji_kotor - $model->total_potongan;
    }

    /**
     * Relasi ke Dosen
     */
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    /**
     * Relasi ke Pegawai
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    /**
     * Relasi ke detail slip gaji
     */
    public function details()
    {
        return $this->hasMany(SlipGajiDetail::class);
    }

    /**
     * Relasi ke user yang menyetujui
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    /**
     * Relasi ke user pembuat
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get nama pegawai (dosen atau tendik)
     */
    public function getNamaPegawaiAttribute()
    {
        if ($this->dosen) {
            return $this->dosen->nama;
        }
        if ($this->pegawai) {
            return $this->pegawai->nama;
        }
        return '-';
    }

    /**
     * Get tipe pegawai
     */
    public function getTipePegawaiAttribute()
    {
        if ($this->dosen_id) {
            return 'Dosen';
        }
        if ($this->pegawai_id) {
            return 'Tendik';
        }
        return '-';
    }

    /**
     * Get nama bulan dalam bahasa Indonesia
     */
    public function getNamaBulanAttribute()
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulan[$this->bulan] ?? '';
    }

    /**
     * Get periode (Bulan Tahun)
     */
    public function getPeriodeAttribute()
    {
        return $this->nama_bulan . ' ' . $this->tahun;
    }

    /**
     * Scope untuk filter status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter periode
     */
    public function scopePeriode($query, $tahun, $bulan = null)
    {
        $query->where('tahun', $tahun);
        if ($bulan) {
            $query->where('bulan', $bulan);
        }
        return $query;
    }

    /**
     * Hitung ulang total dari detail
     */
    public function recalculateFromDetails()
    {
        $this->total_tunjangan = $this->details()->where('jenis', 'pendapatan')->sum('nilai');
        $this->total_potongan = $this->details()->where('jenis', 'potongan')->sum('nilai');
        $this->gaji_kotor = $this->gaji_pokok + $this->total_tunjangan;
        $this->gaji_bersih = $this->gaji_kotor - $this->total_potongan;
        $this->save();
    }
}
