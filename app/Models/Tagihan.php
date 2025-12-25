<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Tagihan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'tagihan';

    protected $fillable = [
        'no_tagihan',
        'mahasiswa_id',
        'tahun_akademik_id',
        'tarif_id',
        'jenis_tagihan',
        'keterangan_tagihan',
        'nominal',
        'diskon',
        'denda',
        'total_bayar',
        'jumlah_dibayar',
        'sisa_tagihan',
        'tanggal_jatuh_tempo',
        'status',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'diskon' => 'decimal:2',
        'denda' => 'decimal:2',
        'total_bayar' => 'decimal:2',
        'jumlah_dibayar' => 'decimal:2',
        'sisa_tagihan' => 'decimal:2',
        'tanggal_jatuh_tempo' => 'date',
    ];

    public const STATUS = [
        'Belum Bayar' => 'Belum Bayar',
        'Cicilan' => 'Cicilan',
        'Lunas' => 'Lunas',
        'Batal' => 'Dibatalkan',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->no_tagihan)) {
                $model->no_tagihan = self::generateNoTagihan();
            }
            // Calculate totals
            $model->total_bayar = $model->nominal - $model->diskon + $model->denda;
            $model->sisa_tagihan = $model->total_bayar - $model->jumlah_dibayar;
        });

        static::updating(function ($model) {
            $model->total_bayar = $model->nominal - $model->diskon + $model->denda;
            $model->sisa_tagihan = $model->total_bayar - $model->jumlah_dibayar;
            
            // Auto update status
            if ($model->sisa_tagihan <= 0) {
                $model->status = 'Lunas';
            } elseif ($model->jumlah_dibayar > 0) {
                $model->status = 'Cicilan';
            }
        });
    }

    public static function generateNoTagihan()
    {
        $prefix = 'INV' . date('Ym');
        $last = self::where('no_tagihan', 'like', $prefix . '%')
            ->orderBy('no_tagihan', 'desc')
            ->first();
        
        if ($last) {
            $lastNumber = intval(substr($last->no_tagihan, -5));
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '00001';
        }
        
        return $prefix . $newNumber;
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function tarif()
    {
        return $this->belongsTo(Tarif::class);
    }

    public function transaksi()
    {
        return $this->hasMany(TransaksiPembayaran::class);
    }

    public function cicilan()
    {
        return $this->hasMany(Cicilan::class);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'Lunas' => '<span class="badge bg-success">Lunas</span>',
            'Cicilan' => '<span class="badge bg-warning">Cicilan</span>',
            'Batal' => '<span class="badge bg-secondary">Dibatalkan</span>',
            default => '<span class="badge bg-danger">Belum Bayar</span>',
        };
    }

    public function getIsOverdueAttribute()
    {
        return $this->status !== 'Lunas' && $this->tanggal_jatuh_tempo < now();
    }

    public function getHariTerlambatAttribute()
    {
        if (!$this->is_overdue) {
            return 0;
        }
        return now()->diffInDays($this->tanggal_jatuh_tempo);
    }

    public function calculateDenda()
    {
        if (!$this->is_overdue) {
            return 0;
        }

        $pengaturanDenda = PengaturanDenda::where('is_active', true)->first();
        if (!$pengaturanDenda) {
            return 0;
        }

        $hariTerlambat = $this->hari_terlambat - $pengaturanDenda->grace_period;
        if ($hariTerlambat <= 0) {
            return 0;
        }

        // Convert to days based on period
        $multiplier = match($pengaturanDenda->periode) {
            'Mingguan' => ceil($hariTerlambat / 7),
            'Bulanan' => ceil($hariTerlambat / 30),
            default => $hariTerlambat,
        };

        if ($pengaturanDenda->tipe === 'Persen') {
            $denda = ($this->nominal * $pengaturanDenda->nilai / 100) * $multiplier;
        } else {
            $denda = $pengaturanDenda->nilai * $multiplier;
        }

        // Apply max limit
        if ($pengaturanDenda->maksimal_denda && $denda > $pengaturanDenda->maksimal_denda) {
            $denda = $pengaturanDenda->maksimal_denda;
        }

        return $denda;
    }

    public function scopeBelumLunas($query)
    {
        return $query->whereIn('status', ['Belum Bayar', 'Cicilan']);
    }

    public function scopeOverdue($query)
    {
        return $query->belumLunas()->where('tanggal_jatuh_tempo', '<', now());
    }
}
