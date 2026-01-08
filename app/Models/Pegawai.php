<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'pegawai';

    protected $fillable = [
        'user_id',
        'nip',
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_pernikahan',
        'email',
        'no_hp',
        'telepon',
        'alamat',
        'rt',
        'rw',
        'provinsi_id',
        'kabupaten_id',
        'kecamatan_id',
        'kelurahan_id',
        'kode_pos',
        'unit_kerja_id',
        'jabatan',
        'nama_jabatan_id',
        'jenis_pegawai',
        'golongan',
        'pangkat',
        'tmt_pegawai',
        'tmt_jabatan',
        'status',
        'no_rekening',
        'nama_bank',
        'npwp',
        'no_bpjs_kesehatan',
        'no_bpjs_ketenagakerjaan',
        'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tmt_pegawai' => 'date',
        'tmt_jabatan' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function namaJabatan()
    {
        return $this->belongsTo(NamaJabatan::class);
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class);
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    // Kepegawaian Relations
    public function riwayatPendidikan()
    {
        return $this->hasMany(RiwayatPendidikan::class)->orderBy('tahun_lulus', 'desc');
    }

    public function riwayatJabatan()
    {
        return $this->hasMany(RiwayatJabatan::class)->orderBy('tmt_jabatan', 'desc');
    }

    public function riwayatPangkat()
    {
        return $this->hasMany(RiwayatPangkat::class)->orderBy('tmt_pangkat', 'desc');
    }

    public function riwayatPelatihan()
    {
        return $this->hasMany(RiwayatPelatihan::class)->orderBy('tanggal_mulai', 'desc');
    }

    public function dokumenKepegawaian()
    {
        return $this->hasMany(DokumenKepegawaian::class)->orderBy('created_at', 'desc');
    }

    // Accessors
    public function getNamaLengkapAttribute()
    {
        return $this->nama;
    }

    public function getUsiaAttribute()
    {
        if ($this->tanggal_lahir) {
            return $this->tanggal_lahir->age;
        }
        return null;
    }

    public function getMasaKerjaAttribute()
    {
        if ($this->tmt_pegawai) {
            $diff = $this->tmt_pegawai->diff(now());
            return $diff->y . ' tahun ' . $diff->m . ' bulan';
        }
        return null;
    }
}
