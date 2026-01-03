<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class PengajuanKonversiKegiatan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'pengajuan_konversi_kegiatan';

    protected $fillable = [
        'mahasiswa_id',
        'tahun_akademik_id',
        'no_pengajuan',
        'tanggal_pengajuan',
        'status',
        'catatan_mahasiswa',
        'catatan_admin',
        'diproses_oleh',
        'tanggal_diproses',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_diproses' => 'datetime',
    ];

    /**
     * Boot method untuk auto-generate nomor pengajuan
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->no_pengajuan)) {
                $model->no_pengajuan = self::generateNoPengajuan();
            }
            if (empty($model->tanggal_pengajuan)) {
                $model->tanggal_pengajuan = now();
            }
        });
    }

    /**
     * Generate nomor pengajuan: RPL-YYYYMM-XXXXX
     */
    public static function generateNoPengajuan()
    {
        $prefix = 'RPL';
        $yearMonth = date('Ym');
        
        $lastRecord = self::where('no_pengajuan', 'like', "{$prefix}{$yearMonth}%")
            ->orderBy('no_pengajuan', 'desc')
            ->first();
        
        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->no_pengajuan, -5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $yearMonth . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    // ========== RELATIONSHIPS ==========

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function diprosesOleh()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function details()
    {
        return $this->hasMany(DetailKonversiKegiatan::class);
    }

    // ========== HELPERS ==========

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'Draft' => '<span class="badge bg-secondary">Draft</span>',
            'Diajukan' => '<span class="badge bg-info">Diajukan</span>',
            'Diproses' => '<span class="badge bg-warning">Diproses</span>',
            'Disetujui' => '<span class="badge bg-success">Disetujui</span>',
            'Ditolak' => '<span class="badge bg-danger">Ditolak</span>',
            default => '<span class="badge bg-secondary">' . $this->status . '</span>',
        };
    }

    public function getTotalSksAttribute()
    {
        return $this->details()->where('status_detail', 'Disetujui')->sum('sks_diakui');
    }

    public function canEdit()
    {
        return in_array($this->status, ['Draft']);
    }

    public function canSubmit()
    {
        return $this->status === 'Draft' && $this->details()->count() > 0;
    }

    public function canProcess()
    {
        return $this->status === 'Diajukan';
    }
}
