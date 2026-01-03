<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKonversi extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'detail_konversi';

    protected $fillable = [
        'pengajuan_konversi_id',
        'kode_mk_asal',
        'nama_mk_asal',
        'sks_asal',
        'nilai_asal',
        'bobot_asal',
        'mata_kuliah_id',
        'nilai_konversi',
        'bobot_konversi',
        'status',
        'alasan',
        'disetujui_oleh',
    ];

    protected $casts = [
        'sks_asal' => 'integer',
        'bobot_asal' => 'decimal:2',
        'bobot_konversi' => 'decimal:2',
    ];

    public function pengajuanKonversi()
    {
        return $this->belongsTo(PengajuanKonversi::class);
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public static function getStatusOptions()
    {
        return [
            'pending' => 'Pending',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'tidak_dikonversi' => 'Tidak Dikonversi',
        ];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'disetujui' => 'success',
            'ditolak' => 'danger',
            'tidak_dikonversi' => 'secondary',
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    // Konversi nilai huruf ke bobot
    public static function nilaiKeBobot($nilai)
    {
        $konversi = [
            'A' => 4.00, 'A-' => 3.75,
            'B+' => 3.50, 'B' => 3.00, 'B-' => 2.75,
            'C+' => 2.50, 'C' => 2.00, 'C-' => 1.75,
            'D+' => 1.50, 'D' => 1.00,
            'E' => 0.00,
        ];

        return $konversi[strtoupper($nilai)] ?? null;
    }

    // Konversi bobot ke nilai huruf
    public static function bobotKeNilai($bobot)
    {
        if ($bobot >= 3.75) return 'A';
        if ($bobot >= 3.50) return 'A-';
        if ($bobot >= 3.00) return 'B+';
        if ($bobot >= 2.75) return 'B';
        if ($bobot >= 2.50) return 'B-';
        if ($bobot >= 2.00) return 'C+';
        if ($bobot >= 1.75) return 'C';
        if ($bobot >= 1.50) return 'C-';
        if ($bobot >= 1.00) return 'D';
        return 'E';
    }
}
