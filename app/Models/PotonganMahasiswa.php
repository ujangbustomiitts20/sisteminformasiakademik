<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;
use Carbon\Carbon;

class PotonganMahasiswa extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'potongan_mahasiswa';

    protected $fillable = [
        'kode',
        'mahasiswa_id',
        'jenis_potongan_id',
        'periode_diskon_id',
        'tipe_nilai',
        'nilai',
        'nilai_max',
        'alasan',
        'catatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'berlaku_untuk_tagihan',
        'status',
        'disetujui_oleh',
        'disetujui_at',
        'alasan_penolakan',
        'created_by',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'nilai_max' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'berlaku_untuk_tagihan' => 'array',
        'disetujui_at' => 'datetime',
    ];

    public const STATUS = [
        'pending' => 'Menunggu Persetujuan',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
        'kadaluarsa' => 'Kadaluarsa',
        'dibatalkan' => 'Dibatalkan',
    ];

    public const STATUS_BADGE = [
        'pending' => 'warning',
        'disetujui' => 'success',
        'ditolak' => 'danger',
        'kadaluarsa' => 'secondary',
        'dibatalkan' => 'secondary',
    ];

    public const TIPE_NILAI = [
        'persen' => 'Persentase (%)',
        'nominal' => 'Nominal (Rp)',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function jenisPotongan()
    {
        return $this->belongsTo(JenisPotongan::class);
    }

    public function periodeDiskon()
    {
        return $this->belongsTo(PeriodeDiskon::class);
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function riwayatPotongan()
    {
        return $this->hasMany(RiwayatPotongan::class);
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        return self::STATUS_BADGE[$this->status] ?? 'secondary';
    }

    public function getTipeNilaiLabelAttribute()
    {
        return self::TIPE_NILAI[$this->tipe_nilai] ?? $this->tipe_nilai;
    }

    public function getNilaiLabelAttribute()
    {
        if ($this->tipe_nilai === 'persen') {
            return $this->nilai . '%';
        }
        return 'Rp ' . number_format($this->nilai, 0, ',', '.');
    }

    public function getIsValidAttribute()
    {
        if ($this->status !== 'disetujui') {
            return false;
        }
        
        $now = Carbon::now()->startOfDay();
        
        if ($this->tanggal_mulai && $this->tanggal_mulai > $now) {
            return false;
        }
        
        if ($this->tanggal_selesai && $this->tanggal_selesai < $now) {
            return false;
        }
        
        return true;
    }

    public function hitungPotongan($nominal)
    {
        if ($this->tipe_nilai === 'persen') {
            $potongan = $nominal * $this->nilai / 100;
            if ($this->nilai_max) {
                return min($potongan, $this->nilai_max);
            }
            return $potongan;
        }
        return min($this->nilai, $nominal);
    }

    public function approve($userId, $catatan = null)
    {
        $this->update([
            'status' => 'disetujui',
            'disetujui_oleh' => $userId,
            'disetujui_at' => now(),
            'catatan' => $catatan ?? $this->catatan,
        ]);
    }

    public function reject($userId, $alasan)
    {
        $this->update([
            'status' => 'ditolak',
            'disetujui_oleh' => $userId,
            'disetujui_at' => now(),
            'alasan_penolakan' => $alasan,
        ]);
    }

    public function cancel()
    {
        $this->update([
            'status' => 'dibatalkan',
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeValid($query)
    {
        $now = Carbon::now()->startOfDay();
        return $query->where('status', 'disetujui')
            ->where(function ($q) use ($now) {
                $q->whereNull('tanggal_mulai')
                    ->orWhere('tanggal_mulai', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('tanggal_selesai')
                    ->orWhere('tanggal_selesai', '>=', $now);
            });
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->kode)) {
                $model->kode = self::generateKode();
            }
        });
    }

    public static function generateKode()
    {
        $prefix = 'PTM' . date('Ym');
        $last = self::where('kode', 'like', $prefix . '%')
            ->orderBy('kode', 'desc')
            ->first();
        
        if ($last) {
            $lastNumber = intval(substr($last->kode, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }
}
