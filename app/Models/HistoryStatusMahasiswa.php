<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoryStatusMahasiswa extends Model
{
    protected $table = 'history_status_mahasiswa';

    protected $fillable = [
        'mahasiswa_id',
        'tahun_akademik_id',
        'status_lama',
        'status_baru',
        'keterangan',
        'diubah_oleh',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function diubahOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }

    // Static method untuk catat perubahan
    public static function catat($mahasiswaId, $tahunAkademikId, $statusLama, $statusBaru, $keterangan = null, $userId = null)
    {
        return self::create([
            'mahasiswa_id' => $mahasiswaId,
            'tahun_akademik_id' => $tahunAkademikId,
            'status_lama' => $statusLama,
            'status_baru' => $statusBaru,
            'keterangan' => $keterangan,
            'diubah_oleh' => $userId ?? auth()->id(),
        ]);
    }
}
