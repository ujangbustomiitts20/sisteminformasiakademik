<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoCuti extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'saldo_cuti';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'tahun',
        'jatah_cuti',
        'cuti_digunakan',
        'sisa_cuti',
        'cuti_hangus',
        'keterangan',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'jatah_cuti' => 'integer',
        'cuti_digunakan' => 'integer',
        'sisa_cuti' => 'integer',
        'cuti_hangus' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->sisa_cuti = $model->jatah_cuti - $model->cuti_digunakan;
        });

        static::updating(function ($model) {
            if ($model->isDirty(['jatah_cuti', 'cuti_digunakan'])) {
                $model->sisa_cuti = $model->jatah_cuti - $model->cuti_digunakan;
            }
        });
    }

    // Relationships
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    // Accessors
    public function getNamaPegawaiAttribute()
    {
        if ($this->dosen_id) {
            return $this->dosen->nama_lengkap ?? $this->dosen->nama ?? '-';
        }
        return $this->pegawai->nama ?? '-';
    }

    // Hitung cuti pending (yang sedang diajukan)
    public function getCutiPendingAttribute()
    {
        $query = CutiPegawai::whereIn('status', ['diajukan', 'disetujui_atasan'])
            ->whereYear('tanggal_mulai', $this->tahun);
        
        if ($this->dosen_id) {
            $query->where('dosen_id', $this->dosen_id);
        } else {
            $query->where('pegawai_id', $this->pegawai_id);
        }
        
        return $query->sum('jumlah_hari') ?? 0;
    }

    // Scopes
    public function scopeByTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    // Helper untuk mendapatkan atau membuat saldo cuti
    public static function getOrCreate($dosenId = null, $pegawaiId = null, $tahun = null)
    {
        $tahun = $tahun ?? date('Y');
        
        $query = self::where('tahun', $tahun);
        
        if ($dosenId) {
            $query->where('dosen_id', $dosenId);
        } else {
            $query->where('pegawai_id', $pegawaiId);
        }
        
        $saldo = $query->first();
        
        if (!$saldo) {
            $saldo = self::create([
                'dosen_id' => $dosenId,
                'pegawai_id' => $pegawaiId,
                'tahun' => $tahun,
                'jatah_cuti' => 12, // Default 12 hari
                'cuti_digunakan' => 0,
                'sisa_cuti' => 12,
            ]);
        }
        
        return $saldo;
    }
}
