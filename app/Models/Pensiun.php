<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pensiun extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'pensiun';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'jenis_pensiun',
        'usia_bup',
        'tanggal_lahir',
        'tanggal_pensiun',
        'tanggal_bup',
        'pangkat_terakhir',
        'golongan_terakhir',
        'jabatan_terakhir',
        'masa_kerja_tahun',
        'masa_kerja_bulan',
        'no_sk',
        'tanggal_sk',
        'pejabat_penandatangan',
        'gaji_pokok_terakhir',
        'dana_pensiun',
        'no_taspen',
        'alamat_pensiun',
        'no_telepon_pensiun',
        'no_rekening_pensiun',
        'nama_bank',
        'dokumen_sk',
        'dokumen_karpeg',
        'dokumen_taspen',
        'status',
        'catatan',
        'sudah_serah_terima',
        'tanggal_serah_terima',
        'diproses_oleh',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_pensiun' => 'date',
        'tanggal_bup' => 'date',
        'tanggal_sk' => 'date',
        'tanggal_serah_terima' => 'date',
        'gaji_pokok_terakhir' => 'decimal:2',
        'dana_pensiun' => 'decimal:2',
        'sudah_serah_terima' => 'boolean',
        'usia_bup' => 'integer',
    ];

    const JENIS_PENSIUN = [
        'bup' => 'Batas Usia Pensiun (BUP)',
        'atas_permintaan_sendiri' => 'Atas Permintaan Sendiri',
        'uzur' => 'Uzur (Tidak Cakap Jasmani/Rohani)',
        'meninggal' => 'Meninggal Dunia',
        'penyederhanaan_organisasi' => 'Penyederhanaan Organisasi',
        'hukuman_disiplin' => 'Hukuman Disiplin',
        'dini' => 'Pensiun Dini',
    ];

    const STATUS = [
        'prediksi' => 'Prediksi',
        'proses' => 'Dalam Proses',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    const BUP_JABATAN = [
        'guru_besar' => 70,
        'lektor_kepala' => 65,
        'dosen_biasa' => 60,
        'pns_biasa' => 58,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Auto calculate tanggal BUP dari tanggal lahir
            if ($model->tanggal_lahir && $model->usia_bup) {
                $model->tanggal_bup = $model->tanggal_lahir->copy()->addYears($model->usia_bup);
            }
        });
    }

    // Relationships
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function diprosesOleh()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    // Accessors
    public function getJenisPensiunLabelAttribute()
    {
        return self::JENIS_PENSIUN[$this->jenis_pensiun] ?? $this->jenis_pensiun;
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'prediksi' => '<span class="badge bg-info">Prediksi</span>',
            'proses' => '<span class="badge bg-warning text-dark">Proses</span>',
            'selesai' => '<span class="badge bg-success">Selesai</span>',
            'dibatalkan' => '<span class="badge bg-danger">Dibatalkan</span>',
        ];
        return $badges[$this->status] ?? '<span class="badge bg-secondary">' . $this->status . '</span>';
    }

    public function getNamaPegawaiAttribute()
    {
        if ($this->dosen_id) {
            return $this->dosen->nama ?? '-';
        }
        return $this->pegawai->nama ?? '-';
    }

    public function getMasaKerjaAttribute()
    {
        return $this->masa_kerja_tahun . ' tahun ' . $this->masa_kerja_bulan . ' bulan';
    }

    public function getUsiaSekarangAttribute()
    {
        if ($this->tanggal_lahir) {
            return $this->tanggal_lahir->age;
        }
        return null;
    }

    public function getSisaHariPensiunAttribute()
    {
        if ($this->tanggal_bup) {
            return now()->diffInDays($this->tanggal_bup, false);
        }
        return null;
    }

    public function getSisaBulanPensiunAttribute()
    {
        if ($this->tanggal_bup) {
            return now()->diffInMonths($this->tanggal_bup, false);
        }
        return null;
    }

    public function getSisaTahunPensiunAttribute()
    {
        if ($this->tanggal_bup) {
            return now()->diffInYears($this->tanggal_bup, false);
        }
        return null;
    }

    public function getSisaWaktuPensiunAttribute()
    {
        if ($this->tanggal_bup) {
            $diff = now()->diff($this->tanggal_bup);
            if ($diff->invert) {
                return 'Sudah pensiun';
            }
            return $diff->y . ' tahun ' . $diff->m . ' bulan ' . $diff->d . ' hari';
        }
        return null;
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAkanPensiun($query, $bulan = 12)
    {
        return $query->whereBetween('tanggal_bup', [now(), now()->addMonths($bulan)]);
    }

    public function scopePensiunTahunIni($query)
    {
        return $query->whereYear('tanggal_bup', now()->year);
    }

    public function scopePrediksi($query)
    {
        return $query->where('status', 'prediksi');
    }

    // Helper untuk generate prediksi pensiun
    public static function generatePrediksiPensiun()
    {
        // Generate untuk Dosen
        $dosens = \App\Models\Dosen::whereNotNull('tanggal_lahir')
                                   ->where('status_aktif', 'Aktif')
                                   ->get();

        foreach ($dosens as $dosen) {
            // Tentukan BUP berdasarkan jabatan fungsional
            $usiaBup = 60; // Default
            if (str_contains(strtolower($dosen->jabatan_fungsional ?? ''), 'guru besar')) {
                $usiaBup = 70;
            } elseif (str_contains(strtolower($dosen->jabatan_fungsional ?? ''), 'lektor kepala')) {
                $usiaBup = 65;
            }

            $tanggalBup = $dosen->tanggal_lahir->copy()->addYears($usiaBup);

            // Skip jika sudah melewati BUP
            if ($tanggalBup->lt(now())) {
                continue;
            }

            self::updateOrCreate(
                ['dosen_id' => $dosen->id],
                [
                    'jenis_pensiun' => 'bup',
                    'usia_bup' => $usiaBup,
                    'tanggal_lahir' => $dosen->tanggal_lahir,
                    'tanggal_pensiun' => $tanggalBup,
                    'tanggal_bup' => $tanggalBup,
                    'pangkat_terakhir' => $dosen->pangkat ?? null,
                    'golongan_terakhir' => $dosen->golongan ?? null,
                    'jabatan_terakhir' => $dosen->jabatan_fungsional ?? null,
                    'status' => 'prediksi',
                ]
            );
        }

        // Generate untuk Pegawai
        $pegawais = \App\Models\Pegawai::whereNotNull('tanggal_lahir')
                                        ->where('status', 'Aktif')
                                        ->get();

        foreach ($pegawais as $pegawai) {
            $usiaBup = 58; // Default untuk PNS biasa
            $tanggalBup = $pegawai->tanggal_lahir->copy()->addYears($usiaBup);

            if ($tanggalBup->lt(now())) {
                continue;
            }

            self::updateOrCreate(
                ['pegawai_id' => $pegawai->id],
                [
                    'jenis_pensiun' => 'bup',
                    'usia_bup' => $usiaBup,
                    'tanggal_lahir' => $pegawai->tanggal_lahir,
                    'tanggal_pensiun' => $tanggalBup,
                    'tanggal_bup' => $tanggalBup,
                    'pangkat_terakhir' => $pegawai->pangkat ?? null,
                    'golongan_terakhir' => $pegawai->golongan ?? null,
                    'jabatan_terakhir' => $pegawai->jabatan ?? null,
                    'status' => 'prediksi',
                ]
            );
        }
    }
}
