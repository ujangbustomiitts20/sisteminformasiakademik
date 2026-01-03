<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CalonMahasiswa extends Authenticatable
{
    use HasFactory, HashidsTrait;

    protected $table = 'calon_mahasiswa';

    protected $fillable = [
        'no_pendaftaran',
        'gelombang_pmb_id',
        'jalur_seleksi_id',
        'program_studi_id',
        'program_studi_2_id',
        // Data Pribadi
        'nama_lengkap',
        'nik',
        'nisn',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'kewarganegaraan',
        // Kontak
        'email',
        'no_hp',
        'no_wa',
        // Alamat
        'alamat',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
        // Asal Sekolah
        'asal_sekolah',
        'npsn_sekolah',
        'jurusan_sekolah',
        'tahun_lulus',
        'nilai_rata_rata',
        // Orang Tua
        'nama_ayah',
        'pekerjaan_ayah',
        'no_hp_ayah',
        'nama_ibu',
        'pekerjaan_ibu',
        'no_hp_ibu',
        'penghasilan_ortu',
        // Foto & Login
        'foto',
        'password',
        // Status
        'status',
        'status_pendaftaran',
        'is_dokumen_lengkap',
        'is_bayar_pendaftaran',
        'tanggal_bayar',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'nilai_rata_rata' => 'decimal:2',
        'penghasilan_ortu' => 'decimal:2',
        'is_dokumen_lengkap' => 'boolean',
        'is_bayar_pendaftaran' => 'boolean',
        'tanggal_bayar' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->no_pendaftaran)) {
                $model->no_pendaftaran = self::generateNoPendaftaran($model->gelombang_pmb_id);
            }
        });
    }

    // Generate Nomor Pendaftaran
    public static function generateNoPendaftaran($gelombangId)
    {
        $gelombang = GelombangPmb::with('periodePmb')->find($gelombangId);
        $tahun = date('Y');
        $prefix = 'PMB' . $tahun;
        
        $lastNo = self::where('no_pendaftaran', 'like', $prefix . '%')
                      ->orderBy('no_pendaftaran', 'desc')
                      ->first();

        if ($lastNo) {
            $lastNumber = intval(substr($lastNo->no_pendaftaran, -5));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function gelombangPmb()
    {
        return $this->belongsTo(GelombangPmb::class, 'gelombang_pmb_id');
    }

    public function jalurSeleksi()
    {
        return $this->belongsTo(JalurSeleksi::class, 'jalur_seleksi_id');
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'program_studi_id');
    }

    public function programStudi2()
    {
        return $this->belongsTo(ProgramStudi::class, 'program_studi_2_id');
    }

    public function dokumen()
    {
        return $this->hasMany(DokumenCamaba::class, 'calon_mahasiswa_id');
    }

    public function pembayaran()
    {
        return $this->hasMany(PembayaranPmb::class, 'calon_mahasiswa_id');
    }

    public function pesertaUjian()
    {
        return $this->hasMany(PesertaUjianPmb::class, 'calon_mahasiswa_id');
    }

    public function nilaiSeleksi()
    {
        return $this->hasMany(NilaiSeleksi::class, 'calon_mahasiswa_id');
    }

    public function hasilSeleksi()
    {
        return $this->hasOne(HasilSeleksi::class, 'calon_mahasiswa_id');
    }

    public function daftarUlang()
    {
        return $this->hasOne(DaftarUlang::class, 'calon_mahasiswa_id');
    }

    // Status Helper
    public function getStatusLabelAttribute()
    {
        $status = $this->status ?? $this->status_pendaftaran;
        return match($status) {
            'draft' => 'Draft',
            'mendaftar' => 'Mendaftar',
            'menunggu_bayar' => 'Menunggu Pembayaran',
            'terdaftar' => 'Terdaftar',
            'verifikasi_dokumen' => 'Verifikasi Dokumen',
            'lulus_administrasi' => 'Lulus Administrasi',
            'mengikuti_ujian' => 'Mengikuti Ujian',
            'lulus' => 'Lulus Seleksi',
            'tidak_lulus' => 'Tidak Lulus',
            'daftar_ulang' => 'Proses Daftar Ulang',
            'menjadi_mahasiswa' => 'Menjadi Mahasiswa',
            'batal' => 'Dibatalkan',
            default => $status,
        };
    }

    public function getStatusBadgeAttribute()
    {
        $status = $this->status ?? $this->status_pendaftaran;
        return match($status) {
            'draft' => 'secondary',
            'mendaftar' => 'secondary',
            'menunggu_bayar' => 'warning',
            'terdaftar' => 'info',
            'verifikasi_dokumen' => 'info',
            'lulus_administrasi' => 'primary',
            'mengikuti_ujian' => 'primary',
            'lulus' => 'success',
            'tidak_lulus' => 'danger',
            'daftar_ulang' => 'info',
            'menjadi_mahasiswa' => 'success',
            'batal' => 'dark',
            default => 'secondary',
        };
    }

    // Scopes
    public function scopeByGelombang($query, $gelombangId)
    {
        return $query->where('gelombang_pmb_id', $gelombangId);
    }

    public function scopeByJalur($query, $jalurId)
    {
        return $query->where('jalur_seleksi_id', $jalurId);
    }

    public function scopeByProdi($query, $prodiId)
    {
        return $query->where('program_studi_id', $prodiId);
    }

    public function scopeTerdaftar($query)
    {
        return $query->whereNotIn('status_pendaftaran', ['draft', 'batal']);
    }

    public function scopeLulus($query)
    {
        return $query->where('status_pendaftaran', 'lulus');
    }

    // Helper Methods
    public function getTotalNilaiAttribute()
    {
        return $this->nilaiSeleksi()->sum('nilai_akhir');
    }

    public function getAlamatLengkapAttribute()
    {
        $parts = array_filter([
            $this->alamat,
            $this->rt ? 'RT ' . $this->rt : null,
            $this->rw ? 'RW ' . $this->rw : null,
            $this->kelurahan,
            $this->kecamatan,
            $this->kabupaten,
            $this->provinsi,
            $this->kode_pos,
        ]);
        return implode(', ', $parts);
    }
}
