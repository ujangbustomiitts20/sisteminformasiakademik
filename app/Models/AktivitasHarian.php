<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AktivitasHarian extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'aktivitas_harian';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'uraian_kegiatan_skp_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'uraian_kegiatan',
        'output_hasil',
        'volume',
        'satuan',
        'lokasi',
        'keterangan',
        'status',
        'disetujui_oleh',
        'tanggal_disetujui',
        'catatan_atasan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'volume' => 'decimal:2',
        'tanggal_disetujui' => 'datetime',
    ];

    const STATUS = [
        'draft' => 'Draft',
        'diajukan' => 'Diajukan',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
    ];

    // ==================== RELATIONSHIPS ====================

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function uraianKegiatanSkp()
    {
        return $this->belongsTo(UraianKegiatanSkp::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    // ==================== ACCESSORS ====================

    public function getNamaPegawaiAttribute(): string
    {
        return $this->dosen?->nama ?? $this->pegawai?->nama ?? '-';
    }

    public function getNidnNipAttribute(): string
    {
        if ($this->dosen) {
            return $this->dosen->nidn ?? $this->dosen->nip ?? '-';
        }
        return $this->pegawai?->nip ?? '-';
    }

    public function getDurasiAttribute(): ?string
    {
        if ($this->jam_mulai && $this->jam_selesai) {
            $mulai = \Carbon\Carbon::parse($this->jam_mulai);
            $selesai = \Carbon\Carbon::parse($this->jam_selesai);
            $diff = $mulai->diff($selesai);
            
            $jam = $diff->h;
            $menit = $diff->i;
            
            if ($jam > 0 && $menit > 0) {
                return "{$jam} jam {$menit} menit";
            } elseif ($jam > 0) {
                return "{$jam} jam";
            } elseif ($menit > 0) {
                return "{$menit} menit";
            }
        }
        return null;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft' => 'secondary',
            'diajukan' => 'warning',
            'disetujui' => 'success',
            'ditolak' => 'danger',
            default => 'secondary',
        };
    }

    public function getJamMulaiFormatAttribute(): ?string
    {
        return $this->jam_mulai ? \Carbon\Carbon::parse($this->jam_mulai)->format('H:i') : null;
    }

    public function getJamSelesaiFormatAttribute(): ?string
    {
        return $this->jam_selesai ? \Carbon\Carbon::parse($this->jam_selesai)->format('H:i') : null;
    }

    // ==================== SCOPES ====================

    public function scopeForDosen($query, $dosenId)
    {
        return $query->where('dosen_id', $dosenId);
    }

    public function scopeForPegawai($query, $pegawaiId)
    {
        return $query->where('pegawai_id', $pegawaiId);
    }

    public function scopeTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    public function scopeBulan($query, $bulan, $tahun)
    {
        return $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // ==================== STATIC METHODS ====================

    /**
     * Get rekap aktivitas per bulan untuk dosen
     */
    public static function getRekapBulanan($dosenId, $bulan, $tahun)
    {
        return self::where('dosen_id', $dosenId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->selectRaw('
                COUNT(*) as total_aktivitas,
                COUNT(CASE WHEN status = "disetujui" THEN 1 END) as disetujui,
                COUNT(CASE WHEN status = "diajukan" THEN 1 END) as diajukan,
                COUNT(CASE WHEN status = "draft" THEN 1 END) as draft,
                SUM(volume) as total_volume
            ')
            ->first();
    }

    /**
     * Check if aktivitas can be edited
     */
    public function canEdit(): bool
    {
        return in_array($this->status, ['draft', 'ditolak']);
    }

    /**
     * Check if aktivitas can be deleted
     */
    public function canDelete(): bool
    {
        return in_array($this->status, ['draft', 'ditolak']);
    }
}
