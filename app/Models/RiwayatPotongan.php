<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class RiwayatPotongan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'riwayat_potongan';

    protected $fillable = [
        'potongan_mahasiswa_id',
        'periode_diskon_id',
        'tagihan_id',
        'mahasiswa_id',
        'kode_potongan',
        'nama_potongan',
        'tipe_nilai',
        'nilai',
        'nominal_tagihan',
        'nominal_potongan',
        'keterangan',
        'applied_by',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'nominal_tagihan' => 'decimal:2',
        'nominal_potongan' => 'decimal:2',
    ];

    public function potonganMahasiswa()
    {
        return $this->belongsTo(PotonganMahasiswa::class);
    }

    public function periodeDiskon()
    {
        return $this->belongsTo(PeriodeDiskon::class);
    }

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function appliedBy()
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    public function getNilaiLabelAttribute()
    {
        if ($this->tipe_nilai === 'persen') {
            return $this->nilai . '%';
        }
        return 'Rp ' . number_format($this->nilai, 0, ',', '.');
    }
}
