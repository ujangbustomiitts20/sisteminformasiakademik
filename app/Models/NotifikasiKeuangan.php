<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifikasiKeuangan extends Model
{
    use HasFactory;

    protected $table = 'notifikasi_keuangan';

    protected $fillable = [
        'mahasiswa_id',
        'jenis',
        'judul',
        'pesan',
        'reference_type',
        'reference_id',
        'channel',
        'status',
        'sent_at',
        'read_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    const JENIS_TAGIHAN_JATUH_TEMPO = 'tagihan_jatuh_tempo';
    const JENIS_CICILAN_JATUH_TEMPO = 'cicilan_jatuh_tempo';
    const JENIS_DENDA = 'denda';
    const JENIS_REMINDER = 'reminder';
    const JENIS_PEMBAYARAN_BERHASIL = 'pembayaran_berhasil';

    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';
    const STATUS_READ = 'read';

    const CHANNEL_DATABASE = 'database';
    const CHANNEL_EMAIL = 'email';
    const CHANNEL_WHATSAPP = 'whatsapp';

    // Relations
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeUnread($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_SENT]);
    }

    public function scopeByChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }

    // Methods
    public function markAsSent()
    {
        $this->status = self::STATUS_SENT;
        $this->sent_at = now();
        $this->save();
    }

    public function markAsRead()
    {
        $this->status = self::STATUS_READ;
        $this->read_at = now();
        $this->save();
    }

    public function markAsFailed()
    {
        $this->status = self::STATUS_FAILED;
        $this->save();
    }

    // Static Methods
    public static function buatNotifikasi($mahasiswaId, $jenis, $judul, $pesan, $refType = null, $refId = null, $channel = 'database')
    {
        return self::create([
            'mahasiswa_id' => $mahasiswaId,
            'jenis' => $jenis,
            'judul' => $judul,
            'pesan' => $pesan,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'channel' => $channel,
            'status' => self::STATUS_PENDING,
        ]);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'sent' => '<span class="badge bg-success">Terkirim</span>',
            'failed' => '<span class="badge bg-danger">Gagal</span>',
            'read' => '<span class="badge bg-secondary">Dibaca</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">'.$this->status.'</span>';
    }

    public function getJenisLabelAttribute()
    {
        $labels = [
            'tagihan_jatuh_tempo' => 'Tagihan Jatuh Tempo',
            'cicilan_jatuh_tempo' => 'Cicilan Jatuh Tempo',
            'denda' => 'Pemberitahuan Denda',
            'reminder' => 'Pengingat Pembayaran',
            'pembayaran_berhasil' => 'Pembayaran Berhasil',
        ];

        return $labels[$this->jenis] ?? $this->jenis;
    }
}
