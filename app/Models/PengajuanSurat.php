<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengajuanSurat extends Model
{
    use HasFactory;
    
    protected $table = 'pengajuan_surat';
    
    protected $fillable = [
        'mahasiswa_id',
        'jenis_surat',
        'keperluan',
        'ditujukan_kepada',
        'keterangan_tambahan',
        'status',
        'catatan_admin',
        'diproses_oleh',
        'tanggal_diproses',
        'nomor_surat',
        'file_surat',
    ];
    
    protected $casts = [
        'tanggal_diproses' => 'datetime',
    ];
    
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
    
    public function diproses_oleh_user()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
    
    public function getStatusBadgeAttribute()
    {
        return [
            'pending' => 'warning',
            'diproses' => 'info',
            'disetujui' => 'success',
            'ditolak' => 'danger',
        ][$this->status] ?? 'secondary';
    }
    
    public function getStatusTextAttribute()
    {
        return [
            'pending' => 'Menunggu',
            'diproses' => 'Sedang Diproses',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
        ][$this->status] ?? 'Unknown';
    }
}
