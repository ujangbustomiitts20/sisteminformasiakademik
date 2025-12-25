<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranWisuda extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'pendaftaran_wisuda';

    protected $fillable = [
        'no_pendaftaran',
        'mahasiswa_id',
        'periode_wisuda_id',
        'tanggal_daftar',
        'ipk',
        'total_sks',
        'judul_skripsi',
        'tanggal_lulus_sidang',
        'status',
        'foto_formal',
        'bukti_bebas_pustaka',
        'bukti_bebas_keuangan',
        'bukti_pembayaran_wisuda',
        'catatan_verifikasi',
        'diverifikasi_oleh',
        'tanggal_verifikasi',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
        'tanggal_lulus_sidang' => 'date',
        'tanggal_verifikasi' => 'datetime',
        'ipk' => 'decimal:2',
    ];

    const STATUS_PENDING = 'Pending';
    const STATUS_VERIFIKASI = 'Verifikasi Berkas';
    const STATUS_LOLOS_YUDISIUM = 'Lolos Yudisium';
    const STATUS_DITOLAK = 'Ditolak';
    const STATUS_LULUS = 'Lulus';
    const STATUS_BATAL = 'Batal';

    const STATUS_LIST = [
        self::STATUS_PENDING => 'Menunggu Verifikasi',
        self::STATUS_VERIFIKASI => 'Verifikasi Berkas',
        self::STATUS_LOLOS_YUDISIUM => 'Lolos Yudisium',
        self::STATUS_DITOLAK => 'Ditolak',
        self::STATUS_LULUS => 'Lulus Wisuda',
        self::STATUS_BATAL => 'Dibatalkan',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->no_pendaftaran)) {
                $model->no_pendaftaran = self::generateNoPendaftaran();
            }
            if (empty($model->tanggal_daftar)) {
                $model->tanggal_daftar = now();
            }
        });
    }

    public static function generateNoPendaftaran()
    {
        $prefix = 'WSD';
        $date = date('Ymd');
        $lastRecord = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = 1;
        if ($lastRecord && preg_match('/(\d{4})$/', $lastRecord->no_pendaftaran, $matches)) {
            $sequence = intval($matches[1]) + 1;
        }

        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function periodeWisuda()
    {
        return $this->belongsTo(PeriodeWisuda::class);
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function yudisium()
    {
        return $this->hasOne(Yudisium::class);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Pending' => 'warning',
            'Verifikasi Berkas' => 'info',
            'Lolos Yudisium' => 'primary',
            'Ditolak' => 'danger',
            'Lulus' => 'success',
            'Batal' => 'secondary',
        ];

        $class = $badges[$this->status] ?? 'secondary';
        $label = self::STATUS_LIST[$this->status] ?? $this->status;
        return "<span class=\"badge bg-{$class}\">{$label}</span>";
    }

    public function getKelengkapanBerkasAttribute()
    {
        $required = ['foto_formal', 'bukti_bebas_pustaka', 'bukti_bebas_keuangan', 'bukti_pembayaran_wisuda'];
        $completed = 0;
        
        foreach ($required as $field) {
            if (!empty($this->$field)) {
                $completed++;
            }
        }

        return [
            'completed' => $completed,
            'total' => count($required),
            'percent' => round(($completed / count($required)) * 100),
        ];
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeLolosYudisium($query)
    {
        return $query->where('status', self::STATUS_LOLOS_YUDISIUM);
    }
}
