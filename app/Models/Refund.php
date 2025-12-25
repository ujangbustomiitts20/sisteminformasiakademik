<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $table = 'refund';

    protected $fillable = [
        'nomor_refund',
        'mahasiswa_id',
        'tagihan_id',
        'transaksi_pembayaran_id',
        'jenis',
        'jumlah_pengajuan',
        'jumlah_disetujui',
        'alasan',
        'metode_refund',
        'nama_bank',
        'nomor_rekening',
        'nama_pemilik_rekening',
        'dokumen_pendukung',
        'status',
        'diproses_oleh',
        'diproses_at',
        'disetujui_oleh',
        'disetujui_at',
        'diselesaikan_oleh',
        'diselesaikan_at',
        'catatan_admin',
        'alasan_penolakan',
        'bukti_refund',
        'nomor_referensi_refund',
        'tanggal_refund',
        'created_by',
    ];

    protected $casts = [
        'jumlah_pengajuan' => 'decimal:2',
        'jumlah_disetujui' => 'decimal:2',
        'diproses_at' => 'datetime',
        'disetujui_at' => 'datetime',
        'diselesaikan_at' => 'datetime',
        'tanggal_refund' => 'datetime',
    ];

    const JENIS_KELEBIHAN_BAYAR = 'kelebihan_bayar';
    const JENIS_PEMBATALAN = 'pembatalan';
    const JENIS_CUTI = 'cuti';
    const JENIS_DO = 'do';
    const JENIS_PINDAH = 'pindah';
    const JENIS_LAINNYA = 'lainnya';

    const JENIS_LIST = [
        self::JENIS_KELEBIHAN_BAYAR => 'Kelebihan Bayar',
        self::JENIS_PEMBATALAN => 'Pembatalan Registrasi',
        self::JENIS_CUTI => 'Cuti Akademik',
        self::JENIS_DO => 'Drop Out (DO)',
        self::JENIS_PINDAH => 'Pindah Universitas',
        self::JENIS_LAINNYA => 'Lainnya',
    ];

    const METODE_TRANSFER = 'transfer';
    const METODE_TUNAI = 'tunai';
    const METODE_POTONG_TAGIHAN = 'potong_tagihan';

    const METODE_LIST = [
        self::METODE_TRANSFER => 'Transfer Bank',
        self::METODE_TUNAI => 'Tunai',
        self::METODE_POTONG_TAGIHAN => 'Potong Tagihan Berikutnya',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_DIPROSES = 'diproses';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_DITOLAK = 'ditolak';
    const STATUS_SELESAI = 'selesai';
    const STATUS_DIBATALKAN = 'dibatalkan';

    const STATUS_LIST = [
        self::STATUS_PENDING => 'Menunggu',
        self::STATUS_DIPROSES => 'Sedang Diproses',
        self::STATUS_DISETUJUI => 'Disetujui',
        self::STATUS_DITOLAK => 'Ditolak',
        self::STATUS_SELESAI => 'Selesai',
        self::STATUS_DIBATALKAN => 'Dibatalkan',
    ];

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_refund)) {
                $model->nomor_refund = self::generateNomorRefund();
            }
            if (empty($model->created_by)) {
                $model->created_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('status')) {
                RefundHistory::create([
                    'refund_id' => $model->id,
                    'status_lama' => $model->getOriginal('status'),
                    'status_baru' => $model->status,
                    'user_id' => auth()->id(),
                ]);
            }
        });
    }

    // Generate nomor refund
    public static function generateNomorRefund()
    {
        $prefix = 'REF-' . date('Ym') . '-';
        $last = self::where('nomor_refund', 'like', $prefix . '%')
            ->orderBy('nomor_refund', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->nomor_refund, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    // Relations
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function transaksiPembayaran()
    {
        return $this->belongsTo(TransaksiPembayaran::class);
    }

    public function diprosesOleh()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function diselesaikanOleh()
    {
        return $this->belongsTo(User::class, 'diselesaikan_oleh');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function histories()
    {
        return $this->hasMany(RefundHistory::class)->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeDiproses($query)
    {
        return $query->where('status', self::STATUS_DIPROSES);
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', self::STATUS_DISETUJUI);
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', self::STATUS_SELESAI);
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', self::STATUS_DITOLAK);
    }

    public function scopeByMahasiswa($query, $mahasiswaId)
    {
        return $query->where('mahasiswa_id', $mahasiswaId);
    }

    // Accessors
    public function getJenisLabelAttribute()
    {
        return self::JENIS_LIST[$this->jenis] ?? $this->jenis;
    }

    public function getMetodeLabelAttribute()
    {
        return self::METODE_LIST[$this->metode_refund] ?? $this->metode_refund;
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUS_LIST[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => 'warning',
            self::STATUS_DIPROSES => 'info',
            self::STATUS_DISETUJUI => 'primary',
            self::STATUS_DITOLAK => 'danger',
            self::STATUS_SELESAI => 'success',
            self::STATUS_DIBATALKAN => 'secondary',
        ];

        $class = $badges[$this->status] ?? 'secondary';
        return "<span class=\"badge bg-{$class}\">{$this->status_label}</span>";
    }

    // Methods
    public function canProcess()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canApprove()
    {
        return $this->status === self::STATUS_DIPROSES;
    }

    public function canReject()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_DIPROSES]);
    }

    public function canComplete()
    {
        return $this->status === self::STATUS_DISETUJUI;
    }

    public function canCancel()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_DIPROSES]);
    }

    public function process($catatan = null)
    {
        $this->status = self::STATUS_DIPROSES;
        $this->diproses_oleh = auth()->id();
        $this->diproses_at = now();
        if ($catatan) {
            $this->catatan_admin = $catatan;
        }
        $this->save();

        return $this;
    }

    public function approve($jumlahDisetujui, $catatan = null)
    {
        $this->status = self::STATUS_DISETUJUI;
        $this->jumlah_disetujui = $jumlahDisetujui;
        $this->disetujui_oleh = auth()->id();
        $this->disetujui_at = now();
        if ($catatan) {
            $this->catatan_admin = $catatan;
        }
        $this->save();

        return $this;
    }

    public function reject($alasan)
    {
        $this->status = self::STATUS_DITOLAK;
        $this->alasan_penolakan = $alasan;
        $this->diproses_oleh = auth()->id();
        $this->diproses_at = now();
        $this->save();

        return $this;
    }

    public function complete($buktiRefund = null, $nomorReferensi = null)
    {
        $this->status = self::STATUS_SELESAI;
        $this->diselesaikan_oleh = auth()->id();
        $this->diselesaikan_at = now();
        $this->tanggal_refund = now();
        
        if ($buktiRefund) {
            $this->bukti_refund = $buktiRefund;
        }
        if ($nomorReferensi) {
            $this->nomor_referensi_refund = $nomorReferensi;
        }
        
        $this->save();

        return $this;
    }

    public function cancel()
    {
        $this->status = self::STATUS_DIBATALKAN;
        $this->save();

        return $this;
    }
}
