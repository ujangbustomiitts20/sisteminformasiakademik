<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogKegiatanLapangan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'log_kegiatan_lapangan';

    protected $fillable = [
        'pendaftaran_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'kegiatan',
        'hasil',
        'kendala',
        'dokumentasi',
        'status',
        'komentar_pembimbing',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranKegiatanLapangan::class, 'pendaftaran_id');
    }

    public static function getStatusOptions()
    {
        return [
            'draft' => 'Draft',
            'diajukan' => 'Diajukan',
            'disetujui' => 'Disetujui',
            'revisi' => 'Perlu Revisi',
        ];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'secondary',
            'diajukan' => 'info',
            'disetujui' => 'success',
            'revisi' => 'warning',
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getDurasiAttribute()
    {
        if (!$this->jam_mulai || !$this->jam_selesai) return null;
        
        $mulai = \Carbon\Carbon::parse($this->jam_mulai);
        $selesai = \Carbon\Carbon::parse($this->jam_selesai);
        
        return $mulai->diffInMinutes($selesai);
    }
}
