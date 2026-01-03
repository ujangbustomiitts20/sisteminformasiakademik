<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenugasanMutasi extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'penugasan_mutasi';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'jenis',
        'no_sk',
        'tanggal_sk',
        'tmt',
        'tanggal_selesai',
        'unit_kerja_asal_id',
        'unit_kerja_tujuan_id',
        'jabatan_asal',
        'jabatan_tujuan',
        'nama_tugas',
        'deskripsi_tugas',
        'lokasi_penugasan',
        'alasan',
        'dokumen_sk',
        'status',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'tmt' => 'date',
        'tanggal_selesai' => 'date',
    ];

    const JENIS = [
        'penugasan' => 'Penugasan',
        'mutasi' => 'Mutasi',
        'promosi' => 'Promosi',
        'demosi' => 'Demosi',
        'rotasi' => 'Rotasi',
    ];

    const STATUS = [
        'draft' => 'Draft',
        'aktif' => 'Aktif',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    // Relationships
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function unitKerjaAsal()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_asal_id');
    }

    public function unitKerjaTujuan()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_tujuan_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getJenisLabelAttribute()
    {
        return self::JENIS[$this->jenis] ?? $this->jenis;
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'aktif' => '<span class="badge bg-success">Aktif</span>',
            'selesai' => '<span class="badge bg-info">Selesai</span>',
            'dibatalkan' => '<span class="badge bg-danger">Dibatalkan</span>',
        ];
        return $badges[$this->status] ?? '<span class="badge bg-secondary">' . $this->status . '</span>';
    }

    public function getJenisBadgeAttribute()
    {
        $badges = [
            'penugasan' => 'primary',
            'mutasi' => 'info',
            'promosi' => 'success',
            'demosi' => 'warning',
            'rotasi' => 'secondary',
        ];
        return $badges[$this->jenis] ?? 'secondary';
    }

    public function getNamaPegawaiAttribute()
    {
        if ($this->dosen_id) {
            return $this->dosen->nama ?? '-';
        }
        return $this->pegawai->nama ?? '-';
    }

    public function getDurasiAttribute()
    {
        if ($this->tmt && $this->tanggal_selesai) {
            return $this->tmt->diffForHumans($this->tanggal_selesai, true);
        }
        return '-';
    }

    // Scopes
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeMasaBerlaku($query)
    {
        return $query->where('tmt', '<=', now())
                     ->where(function($q) {
                         $q->whereNull('tanggal_selesai')
                           ->orWhere('tanggal_selesai', '>=', now());
                     });
    }
}
