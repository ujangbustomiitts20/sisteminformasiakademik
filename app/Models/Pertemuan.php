<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Pertemuan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'pertemuan';

    protected $fillable = [
        'jadwal_kuliah_id',
        'pertemuan_ke',
        'judul',
        'jenis',
        'deskripsi',
        'tanggal',
        'file_materi',
        'link_materi',
        'is_published',
        'is_approved',
        'approved_by',
        'approved_at',
        'rejection_note',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_published' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public const JENIS = [
        'Materi' => 'Materi Pembelajaran',
        'Tugas' => 'Tugas',
        'Quiz' => 'Quiz/Kuis',
        'UTS' => 'Ujian Tengah Semester',
        'UAS' => 'Ujian Akhir Semester',
        'Praktikum' => 'Praktikum',
        'Diskusi' => 'Diskusi',
        'Presentasi' => 'Presentasi',
        'Forum' => 'Forum Diskusi',
        'Lainnya' => 'Lainnya',
    ];

    public function jadwalKuliah()
    {
        return $this->belongsTo(JadwalKuliah::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function getJenisLabelAttribute()
    {
        return self::JENIS[$this->jenis] ?? $this->jenis;
    }

    public function getJenisBadgeClassAttribute()
    {
        return match($this->jenis) {
            'Materi' => 'bg-primary',
            'Tugas' => 'bg-warning',
            'Quiz' => 'bg-info',
            'UTS' => 'bg-danger',
            'UAS' => 'bg-danger',
            'Praktikum' => 'bg-success',
            'Diskusi' => 'bg-secondary',
            'Presentasi' => 'bg-purple',
            'Forum' => 'bg-dark',
            default => 'bg-secondary',
        };
    }

    public function getApprovalStatusAttribute()
    {
        if ($this->is_approved) {
            return 'approved';
        } elseif ($this->rejection_note) {
            return 'rejected';
        } elseif ($this->tanggal) {
            return 'pending';
        }
        return 'draft';
    }

    public function getApprovalBadgeAttribute()
    {
        return match($this->approval_status) {
            'approved' => '<span class="badge bg-success">Disetujui</span>',
            'rejected' => '<span class="badge bg-danger">Ditolak</span>',
            'pending' => '<span class="badge bg-warning">Menunggu Persetujuan</span>',
            default => '<span class="badge bg-secondary">Draft</span>',
        };
    }
}
