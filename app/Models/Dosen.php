<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Dosen extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'dosen';

    protected $fillable = [
        'user_id',
        'program_studi_id',
        'unit_kerja_id',
        'nidn',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'provinsi_id',
        'kabupaten_id',
        'kecamatan_id',
        'kelurahan_id',
        'rt',
        'rw',
        'kode_pos',
        'telepon',
        'no_hp',
        'email',
        'jabatan_fungsional',
        'nama_jabatan_id',
        'jabatan_struktural',
        'golongan',
        'status',
        'foto',
        // Data Akademik
        'pendidikan_terakhir',
        'gelar_depan',
        'gelar_belakang',
        'bidang_keahlian',
        'rumpun_ilmu',
        'sinta_id',
        'scopus_id',
        'google_scholar_id',
        'orcid',
        // Data Sertifikasi
        'no_sertifikasi_dosen',
        'tahun_sertifikasi',
        'no_registrasi_dikti',
        // Data Bank & BPJS
        'no_npwp',
        'no_rekening',
        'nama_bank',
        'atas_nama_rekening',
        'no_bpjs_kesehatan',
        'no_bpjs_ketenagakerjaan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tahun_sertifikasi' => 'integer',
    ];

    // Relasi Wilayah
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function namaJabatan()
    {
        return $this->belongsTo(NamaJabatan::class);
    }

    public function mahasiswaWali()
    {
        return $this->hasMany(Mahasiswa::class, 'dosen_wali_id');
    }

    /**
     * Alias for mahasiswaWali - for consistency
     */
    public function mahasiswaBimbingan()
    {
        return $this->hasMany(Mahasiswa::class, 'dosen_wali_id');
    }

    /**
     * Tugas Akhir yang dibimbing (sebagai pembimbing 1 atau 2)
     */
    public function tugasAkhirBimbingan()
    {
        return TugasAkhir::where('pembimbing_1_id', $this->id)
            ->orWhere('pembimbing_2_id', $this->id);
    }

    /**
     * Tugas Akhir sebagai Pembimbing 1
     */
    public function tugasAkhirPembimbing1()
    {
        return $this->hasMany(TugasAkhir::class, 'pembimbing_1_id');
    }

    /**
     * Tugas Akhir sebagai Pembimbing 2
     */
    public function tugasAkhirPembimbing2()
    {
        return $this->hasMany(TugasAkhir::class, 'pembimbing_2_id');
    }

    public function jadwalKuliah()
    {
        return $this->hasMany(JadwalKuliah::class);
    }

    public function rekapEdom()
    {
        return $this->hasMany(RekapEdom::class);
    }

    // Relasi Kepegawaian
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
        return $this->hasMany(DokumenKepegawaian::class)->orderBy('tanggal_terbit', 'desc');
    }

    public function aktivitasHarian()
    {
        return $this->hasMany(AktivitasHarian::class);
    }

    public function skpPegawai()
    {
        return $this->hasMany(SkpPegawai::class);
    }

    // Get pendidikan terakhir dari riwayat
    public function getPendidikanTerakhirAttribute($value)
    {
        if ($value) return $value;
        
        $pendidikan = $this->riwayatPendidikan()->first();
        return $pendidikan ? $pendidikan->jenjang : null;
    }

    // Get nama lengkap dengan gelar
    public function getNamaLengkapAttribute()
    {
        $nama = '';
        if ($this->gelar_depan) {
            $nama .= $this->gelar_depan . ' ';
        }
        $nama .= $this->nama;
        if ($this->gelar_belakang) {
            $nama .= ', ' . $this->gelar_belakang;
        }
        return $nama;
    }
}
