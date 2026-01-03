<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasAkhir extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'tugas_akhir';

    protected $fillable = [
        'nomor_ta',
        'mahasiswa_id',
        'tahun_akademik_id',
        'judul',
        'abstrak',
        'bidang_kajian',
        'latar_belakang',
        'rumusan_masalah',
        'metodologi',
        'pembimbing_1_id',
        'pembimbing_2_id',
        'status',
        'catatan_pembimbing',
        'dokumen_proposal',
        'dokumen_skripsi',
        'dokumen_final',
        'tanggal_pengajuan',
        'tanggal_approval_judul',
        'tanggal_lulus',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'datetime',
        'tanggal_approval_judul' => 'datetime',
        'tanggal_lulus' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function pembimbing1()
    {
        return $this->belongsTo(Dosen::class, 'pembimbing_1_id');
    }

    public function pembimbing2()
    {
        return $this->belongsTo(Dosen::class, 'pembimbing_2_id');
    }

    public function bimbingan()
    {
        return $this->hasMany(BimbinganTA::class, 'tugas_akhir_id');
    }

    public function seminarProposal()
    {
        return $this->hasOne(SeminarProposal::class, 'tugas_akhir_id');
    }

    public function sidang()
    {
        return $this->hasOne(SidangTA::class, 'tugas_akhir_id');
    }

    public static function getStatusOptions()
    {
        return [
            'draft' => 'Draft',
            'diajukan' => 'Menunggu Persetujuan Judul',
            'judul_disetujui' => 'Judul Disetujui',
            'judul_ditolak' => 'Judul Ditolak',
            'proposal_diajukan' => 'Proposal Diajukan',
            'proposal_disetujui' => 'Proposal Disetujui',
            'proposal_revisi' => 'Proposal Perlu Revisi',
            'penelitian' => 'Sedang Penelitian',
            'sidang_diajukan' => 'Pengajuan Sidang',
            'sidang_dijadwalkan' => 'Sidang Dijadwalkan',
            'lulus' => 'Lulus Sidang',
            'lulus_revisi' => 'Lulus dengan Revisi',
            'tidak_lulus' => 'Tidak Lulus',
            'selesai' => 'Selesai',
        ];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'secondary',
            'diajukan' => 'info',
            'judul_disetujui' => 'primary',
            'judul_ditolak' => 'danger',
            'proposal_diajukan' => 'info',
            'proposal_disetujui' => 'primary',
            'proposal_revisi' => 'warning',
            'penelitian' => 'primary',
            'sidang_diajukan' => 'info',
            'sidang_dijadwalkan' => 'warning',
            'lulus' => 'success',
            'lulus_revisi' => 'warning',
            'tidak_lulus' => 'danger',
            'selesai' => 'success',
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getStatusLabel()
    {
        $options = self::getStatusOptions();
        return $options[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public static function generateNomorTA()
    {
        $tahun = date('Y');
        $prefix = "TA/{$tahun}/";
        
        $lastNumber = self::where('nomor_ta', 'like', $prefix . '%')
            ->orderBy('nomor_ta', 'desc')
            ->first();

        if ($lastNumber) {
            $lastSeq = (int) substr($lastNumber->nomor_ta, -4);
            $newSeq = $lastSeq + 1;
        } else {
            $newSeq = 1;
        }

        return $prefix . str_pad($newSeq, 4, '0', STR_PAD_LEFT);
    }

    // Hitung persentase progress
    public function getProgressAttribute()
    {
        $progress = [
            'draft' => 5,
            'diajukan' => 10,
            'judul_disetujui' => 20,
            'judul_ditolak' => 10,
            'proposal_diajukan' => 30,
            'proposal_disetujui' => 40,
            'proposal_revisi' => 35,
            'penelitian' => 60,
            'sidang_diajukan' => 75,
            'sidang_dijadwalkan' => 80,
            'lulus' => 95,
            'lulus_revisi' => 90,
            'tidak_lulus' => 80,
            'selesai' => 100,
        ];

        return $progress[$this->status] ?? 0;
    }

    // Hitung jumlah bimbingan
    public function getJumlahBimbinganAttribute()
    {
        return $this->bimbingan()->where('status', 'selesai')->count();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_ta)) {
                $model->nomor_ta = self::generateNomorTA();
            }
        });
    }
}
