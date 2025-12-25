<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Pembayaran extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'pembayaran';

    protected $fillable = [
        'mahasiswa_id',
        'tahun_akademik_id',
        'jenis',
        'jumlah',
        'tanggal_bayar',
        'metode_bayar',
        'bukti_bayar',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }
}
