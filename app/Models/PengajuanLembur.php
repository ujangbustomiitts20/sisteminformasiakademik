<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanLembur extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'pengajuan_lembur';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'tarif_lembur_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'durasi_jam',
        'alasan',
        'pekerjaan_yang_dilakukan',
        'status',
        'approval_level',
        'status_kaprodi',
        'tanggal_approval_kaprodi',
        'catatan_kaprodi',
        'kaprodi_id',
        'disetujui_oleh',
        'tanggal_disetujui',
        'catatan_approval',
        'tarif_per_jam',
        'total_bayar',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'durasi_jam' => 'decimal:2',
        'tanggal_disetujui' => 'datetime',
        'tanggal_approval_kaprodi' => 'datetime',
        'tarif_per_jam' => 'decimal:2',
        'total_bayar' => 'decimal:2',
    ];

    const STATUS = [
        'diajukan' => 'Diajukan',
        'menunggu_admin' => 'Menunggu Admin',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
        'selesai' => 'Selesai',
    ];

    const STATUS_KAPRODI = [
        'pending' => 'Menunggu',
        'disetujui' => 'Disetujui Kaprodi',
        'ditolak' => 'Ditolak Kaprodi',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->durasi_jam = $model->hitungDurasi();
        });
        
        static::updating(function ($model) {
            $model->durasi_jam = $model->hitungDurasi();
            if ($model->status === 'disetujui') {
                $model->total_bayar = $model->durasi_jam * $model->tarif_per_jam;
            }
        });
    }

    public function hitungDurasi(): float
    {
        if (!$this->jam_mulai || !$this->jam_selesai) return 0;
        
        $mulai = \Carbon\Carbon::parse($this->jam_mulai);
        $selesai = \Carbon\Carbon::parse($this->jam_selesai);
        
        return $mulai->floatDiffInHours($selesai);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function kaprodi()
    {
        return $this->belongsTo(Dosen::class, 'kaprodi_id');
    }

    public function tarifLembur()
    {
        return $this->belongsTo(TarifLembur::class);
    }

    public function getNamaPegawaiAttribute(): string
    {
        if ($this->dosen_id && $this->dosen) {
            return $this->dosen->nama;
        }
        if ($this->pegawai_id && $this->pegawai) {
            return $this->pegawai->nama;
        }
        return '-';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'diajukan' => 'warning',
            'menunggu_admin' => 'info',
            'disetujui' => 'success',
            'ditolak' => 'danger',
            'selesai' => 'primary',
            default => 'secondary',
        };
    }

    public function getStatusKaprodiLabelAttribute(): string
    {
        return self::STATUS_KAPRODI[$this->status_kaprodi] ?? '-';
    }

    public function getStatusKaprodiColorAttribute(): string
    {
        return match($this->status_kaprodi) {
            'pending' => 'warning',
            'disetujui' => 'success',
            'ditolak' => 'danger',
            default => 'secondary',
        };
    }

    public function getFullStatusLabelAttribute(): string
    {
        if ($this->status === 'diajukan' && $this->status_kaprodi === 'pending') {
            return 'Menunggu Kaprodi';
        }
        if ($this->status === 'menunggu_admin') {
            return 'Menunggu Admin';
        }
        return $this->status_label;
    }

    public function getDurasiFormatAttribute(): string
    {
        $jam = floor($this->durasi_jam);
        $menit = ($this->durasi_jam - $jam) * 60;
        return sprintf('%d jam %d menit', $jam, $menit);
    }

    // Alias untuk view compatibility
    public function getTotalJamAttribute(): float
    {
        return $this->durasi_jam ?? 0;
    }

    public function getTotalUpahAttribute(): float
    {
        return $this->total_bayar ?? 0;
    }

    public function getJenisHariLabelAttribute(): string
    {
        if ($this->tarifLembur) {
            return $this->tarifLembur->jenis_hari_label ?? ucfirst($this->tarifLembur->jenis_hari ?? 'Biasa');
        }
        
        // Detect weekend
        if ($this->tanggal && in_array($this->tanggal->dayOfWeek, [0, 6])) {
            return 'Akhir Pekan';
        }
        return 'Hari Kerja';
    }

    public function getJenisHariColorAttribute(): string
    {
        if ($this->tarifLembur) {
            return match($this->tarifLembur->jenis_hari ?? '') {
                'libur', 'libur_nasional' => 'danger',
                'weekend', 'akhir_pekan' => 'warning',
                default => 'secondary',
            };
        }
        
        if ($this->tanggal && in_array($this->tanggal->dayOfWeek, [0, 6])) {
            return 'warning';
        }
        return 'secondary';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'diajukan');
    }

    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year);
    }
}
