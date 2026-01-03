<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BimbinganTA extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'bimbingan_ta';

    protected $fillable = [
        'tugas_akhir_id',
        'dosen_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'tempat',
        'materi_bimbingan',
        'hasil_bimbingan',
        'catatan_dosen',
        'rencana_selanjutnya',
        'status',
        'persentase_progress',
        'dokumen_pendukung',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'persentase_progress' => 'integer',
    ];

    public function tugasAkhir()
    {
        return $this->belongsTo(TugasAkhir::class, 'tugas_akhir_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public static function getStatusOptions()
    {
        return [
            'dijadwalkan' => 'Dijadwalkan',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'dijadwalkan' => 'info',
            'selesai' => 'success',
            'dibatalkan' => 'danger',
        ];

        return $badges[$this->status] ?? 'secondary';
    }
}
