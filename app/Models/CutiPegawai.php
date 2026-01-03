<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CutiPegawai extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'cuti_pegawai';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'no_pengajuan',
        'jenis_cuti',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_hari',
        'alasan',
        'alamat_selama_cuti',
        'no_telepon_selama_cuti',
        'dokumen_pendukung',
        'status',
        'atasan_langsung_id',
        'catatan_atasan',
        'tanggal_persetujuan_atasan',
        'disetujui_oleh',
        'tanggal_disetujui',
        'catatan_admin',
        'sisa_cuti_sebelum',
        'sisa_cuti_sesudah',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_persetujuan_atasan' => 'date',
        'tanggal_disetujui' => 'date',
    ];

    const JENIS_CUTI = [
        'tahunan' => 'Cuti Tahunan',
        'sakit' => 'Cuti Sakit',
        'melahirkan' => 'Cuti Melahirkan',
        'besar' => 'Cuti Besar',
        'alasan_penting' => 'Cuti Alasan Penting',
        'di_luar_tanggungan' => 'Cuti Di Luar Tanggungan Negara',
    ];

    const STATUS = [
        'draft' => 'Draft',
        'diajukan' => 'Diajukan',
        'disetujui_atasan' => 'Disetujui Atasan',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
        'dibatalkan' => 'Dibatalkan',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->no_pengajuan)) {
                $model->no_pengajuan = self::generateNoPengajuan();
            }
            // Hitung jumlah hari cuti
            if ($model->tanggal_mulai && $model->tanggal_selesai) {
                $model->jumlah_hari = $model->tanggal_mulai->diffInDays($model->tanggal_selesai) + 1;
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty(['tanggal_mulai', 'tanggal_selesai'])) {
                $model->jumlah_hari = $model->tanggal_mulai->diffInDays($model->tanggal_selesai) + 1;
            }
        });
    }

    public static function generateNoPengajuan()
    {
        $prefix = 'CUTI';
        $date = date('Ymd');
        $count = self::whereDate('created_at', today())->count() + 1;
        return $prefix . $date . str_pad($count, 4, '0', STR_PAD_LEFT);
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

    public function atasanLangsung()
    {
        return $this->belongsTo(Dosen::class, 'atasan_langsung_id');
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    // Accessors
    public function getJenisCutiLabelAttribute()
    {
        return self::JENIS_CUTI[$this->jenis_cuti] ?? $this->jenis_cuti;
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'diajukan' => '<span class="badge bg-info">Diajukan</span>',
            'disetujui_atasan' => '<span class="badge bg-primary">Disetujui Atasan</span>',
            'disetujui' => '<span class="badge bg-success">Disetujui</span>',
            'ditolak' => '<span class="badge bg-danger">Ditolak</span>',
            'dibatalkan' => '<span class="badge bg-warning text-dark">Dibatalkan</span>',
        ];
        return $badges[$this->status] ?? '<span class="badge bg-secondary">' . $this->status . '</span>';
    }

    public function getNamaPegawaiAttribute()
    {
        if ($this->dosen_id) {
            return $this->dosen->nama ?? '-';
        }
        return $this->pegawai->nama ?? '-';
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_cuti', $jenis);
    }

    public function scopeByPeriode($query, $mulai, $selesai)
    {
        return $query->whereBetween('tanggal_mulai', [$mulai, $selesai]);
    }
}
