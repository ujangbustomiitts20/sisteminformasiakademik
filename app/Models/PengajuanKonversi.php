<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanKonversi extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'pengajuan_konversi';

    protected $fillable = [
        'nomor_pengajuan',
        'mahasiswa_id',
        // Data calon mahasiswa (input manual)
        'nama_calon_mahasiswa',
        'email_calon',
        'no_hp_calon',
        'program_studi_tujuan_id',
        // Data kampus asal
        'universitas_asal',
        'program_studi_asal',
        'nim_asal',
        'tahun_masuk_asal',
        'dokumen_transkrip',
        'dokumen_silabus',
        'dokumen_pendukung',
        'status',
        'catatan',
        'catatan_kaprodi',
        'diproses_oleh',
        'tanggal_diproses',
        'diproses_kaprodi_oleh',
        'tanggal_diproses_kaprodi',
    ];

    protected $casts = [
        'tahun_masuk_asal' => 'integer',
        'tanggal_diproses' => 'datetime',
        'tanggal_diproses_kaprodi' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function programStudiTujuan()
    {
        return $this->belongsTo(ProgramStudi::class, 'program_studi_tujuan_id');
    }

    public function prosesOleh()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function prosesKaprodiOleh()
    {
        return $this->belongsTo(User::class, 'diproses_kaprodi_oleh');
    }

    public function detailKonversi()
    {
        return $this->hasMany(DetailKonversi::class, 'pengajuan_konversi_id');
    }

    // Alias for view compatibility
    public function details()
    {
        return $this->detailKonversi();
    }

    public static function getStatusOptions()
    {
        return [
            'draft' => 'Draft',
            'menunggu_kaprodi' => 'Menunggu Persetujuan Kaprodi',
            'diproses_kaprodi' => 'Sedang Diproses Kaprodi',
            'disetujui_kaprodi' => 'Disetujui Kaprodi',
            'ditolak_kaprodi' => 'Ditolak Kaprodi',
            'disetujui' => 'Disetujui (Final)',
            'ditolak' => 'Ditolak',
        ];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'secondary',
            'menunggu_kaprodi' => 'info',
            'diproses_kaprodi' => 'warning',
            'disetujui_kaprodi' => 'primary',
            'ditolak_kaprodi' => 'danger',
            'disetujui' => 'success',
            'ditolak' => 'danger',
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    // Ambil nama untuk ditampilkan (dari mahasiswa jika sudah terdaftar, atau dari input manual)
    public function getNamaAttribute()
    {
        if ($this->mahasiswa) {
            return $this->mahasiswa->nama;
        }
        return $this->nama_calon_mahasiswa;
    }

    // Ambil NIM untuk ditampilkan
    public function getNimAttribute()
    {
        if ($this->mahasiswa) {
            return $this->mahasiswa->nim;
        }
        return null; // Belum punya NIM
    }

    public static function generateNomorPengajuan()
    {
        $tahun = date('Y');
        $bulan = date('m');
        $prefix = "KNV/{$tahun}/{$bulan}/";
        
        $lastNumber = self::where('nomor_pengajuan', 'like', $prefix . '%')
            ->orderBy('nomor_pengajuan', 'desc')
            ->first();

        if ($lastNumber) {
            $lastSeq = (int) substr($lastNumber->nomor_pengajuan, -4);
            $newSeq = $lastSeq + 1;
        } else {
            $newSeq = 1;
        }

        return $prefix . str_pad($newSeq, 4, '0', STR_PAD_LEFT);
    }

    // Hitung total SKS yang dikonversi
    public function getTotalSksKonversiAttribute()
    {
        return $this->detailKonversi()
            ->where('status', 'disetujui')
            ->whereNotNull('mata_kuliah_id')
            ->join('mata_kuliah', 'detail_konversi.mata_kuliah_id', '=', 'mata_kuliah.id')
            ->sum('mata_kuliah.sks');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_pengajuan)) {
                $model->nomor_pengajuan = self::generateNomorPengajuan();
            }
        });
    }
}
