<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cicilan extends Model
{
    use HasFactory;

    protected $table = 'cicilan';

    protected $fillable = [
        'tagihan_id',
        'skema_cicilan_id',
        'total_tagihan_awal',
        'biaya_admin',
        'total_bunga',
        'total_harus_dibayar',
        'nominal_per_cicilan',
        'jumlah_cicilan',
        'cicilan_terbayar',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    protected $casts = [
        'total_tagihan_awal' => 'decimal:2',
        'biaya_admin' => 'decimal:2',
        'total_bunga' => 'decimal:2',
        'total_harus_dibayar' => 'decimal:2',
        'nominal_per_cicilan' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    const STATUS_AKTIF = 'Aktif';
    const STATUS_LUNAS = 'Lunas';
    const STATUS_GAGAL = 'Gagal';
    const STATUS_BATAL = 'Batal';

    // Relations
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function skemaCicilan()
    {
        return $this->belongsTo(SkemaCicilan::class);
    }

    public function detailCicilan()
    {
        return $this->hasMany(DetailCicilan::class)->orderBy('cicilan_ke');
    }

    public function mahasiswa()
    {
        return $this->hasOneThrough(Mahasiswa::class, Tagihan::class, 'id', 'id', 'tagihan_id', 'mahasiswa_id');
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }

    public function scopeByMahasiswa($query, $mahasiswaId)
    {
        return $query->whereHas('tagihan', fn($q) => $q->where('mahasiswa_id', $mahasiswaId));
    }

    // Methods
    public function updateStatus()
    {
        $terbayar = $this->detailCicilan()->whereIn('status', ['Dibayar', 'Dibayar Terlambat'])->count();
        $this->cicilan_terbayar = $terbayar;

        if ($terbayar >= $this->jumlah_cicilan) {
            $this->status = self::STATUS_LUNAS;
            $this->tanggal_selesai = now();
        }

        $this->save();
    }

    public function getCicilanSelanjutnya()
    {
        return $this->detailCicilan()
            ->where('status', 'Belum Bayar')
            ->orderBy('cicilan_ke')
            ->first();
    }

    public function getSisaTagihan()
    {
        return $this->detailCicilan()
            ->whereIn('status', ['Belum Bayar', 'Terlambat'])
            ->sum('nominal');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Aktif' => '<span class="badge bg-primary">Aktif</span>',
            'Lunas' => '<span class="badge bg-success">Lunas</span>',
            'Gagal' => '<span class="badge bg-danger">Gagal</span>',
            'Batal' => '<span class="badge bg-secondary">Batal</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">'.$this->status.'</span>';
    }

    public function getProgressPersenAttribute()
    {
        if ($this->jumlah_cicilan == 0) return 0;
        return round(($this->cicilan_terbayar / $this->jumlah_cicilan) * 100);
    }
}
