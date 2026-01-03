<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Mahasiswa extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'mahasiswa';

    protected $fillable = [
        'user_id',
        'program_studi_id',
        'dosen_wali_id',
        'nim',
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
        // Data Kependudukan
        'nik',
        'no_kk',
        'agama',
        'kewarganegaraan',
        'golongan_darah',
        // Data Akademik
        'angkatan',
        'jalur_masuk',
        'sekolah_id',
        'asal_sekolah',
        'jurusan_asal',
        'tahun_lulus_sekolah',
        'nilai_un',
        'no_ijazah_sma',
        'semester_aktif',
        'status',
        'foto',
        // Data Orang Tua
        'nama_ayah',
        'nik_ayah',
        'pekerjaan_ayah',
        'pendidikan_ayah',
        'nama_ibu',
        'nik_ibu',
        'pekerjaan_ibu',
        'pendidikan_ibu',
        'no_hp_ortu',
        'email_ortu',
        'penghasilan_ortu',
        'alamat_ortu',
        'provinsi_ortu_id',
        'kabupaten_ortu_id',
        'kecamatan_ortu_id',
        'kelurahan_ortu_id',
        // Data Wali
        'nama_wali',
        'hubungan_wali',
        'pekerjaan_wali',
        'no_hp_wali',
        'alamat_wali',
        // Data Finansial
        'no_rekening',
        'nama_bank',
        'atas_nama_rekening',
        'penerima_kip',
        'no_kip',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'penerima_kip' => 'boolean',
        'nilai_un' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function dosenWali()
    {
        return $this->belongsTo(Dosen::class, 'dosen_wali_id');
    }

    // Relasi Sekolah Asal
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    // Relasi Wilayah Mahasiswa
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

    // Relasi Wilayah Orang Tua
    public function provinsiOrtu()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_ortu_id');
    }

    public function kabupatenOrtu()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_ortu_id');
    }

    public function kecamatanOrtu()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_ortu_id');
    }

    public function kelurahanOrtu()
    {
        return $this->belongsTo(Kelurahan::class, 'kelurahan_ortu_id');
    }

    public function krs()
    {
        return $this->hasMany(Krs::class);
    }

    // Relasi ke nilai melalui KRS menggunakan hasManyThrough
    public function nilai()
    {
        return $this->hasManyThrough(Nilai::class, Krs::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function pendaftaranWisuda()
    {
        return $this->hasMany(PendaftaranWisuda::class);
    }

    public function yudisium()
    {
        return $this->hasMany(Yudisium::class);
    }

    public function tugasAkhir()
    {
        return $this->hasOne(TugasAkhir::class);
    }

    public function cutiAkademik()
    {
        return $this->hasMany(CutiAkademik::class);
    }

    // Hitung IPK
    public function hitungIPK()
    {
        $krs = $this->krs()->whereHas('nilai', function($q) {
            $q->whereNotNull('bobot');
        })->with(['nilai', 'jadwalKuliah.mataKuliah'])->get();

        $totalBobot = 0;
        $totalSks = 0;

        foreach ($krs as $k) {
            if ($k->nilai && $k->jadwalKuliah && $k->jadwalKuliah->mataKuliah) {
                $sks = $k->jadwalKuliah->mataKuliah->sks;
                $totalBobot += $k->nilai->bobot * $sks;
                $totalSks += $sks;
            }
        }

        return $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;
    }

    // Hitung total SKS yang sudah ditempuh
    public function totalSksLulus()
    {
        return $this->krs()
            ->whereHas('nilai', function($q) {
                $q->where('huruf', '!=', 'E');
            })
            ->with('jadwalKuliah.mataKuliah')
            ->get()
            ->sum(function($krs) {
                return $krs->jadwalKuliah->mataKuliah->sks ?? 0;
            });
    }

    /**
     * Generate NIM otomatis
     * Format: [2 digit kode fakultas][2 digit kode prodi][2 digit bulan][2 digit tahun][4 digit nomor urut]
     * Contoh: 01011225001 (Fakultas 01, Prodi 01, Desember 2025, urut 0001)
     */
    public static function generateNim($programStudiId)
    {
        $programStudi = ProgramStudi::with('fakultas')->find($programStudiId);
        
        if (!$programStudi || !$programStudi->fakultas) {
            return null;
        }

        // Extract angka dari kode fakultas, atau gunakan id jika tidak ada angka
        $kodeFakultasRaw = preg_replace('/[^0-9]/', '', $programStudi->fakultas->kode);
        if (empty($kodeFakultasRaw)) {
            // Jika tidak ada angka, gunakan id fakultas
            $kodeFakultasRaw = $programStudi->fakultas->id;
        }
        $kodeFakultas = str_pad($kodeFakultasRaw, 2, '0', STR_PAD_LEFT);
        $kodeFakultas = substr($kodeFakultas, -2); // Ambil 2 digit terakhir
        
        // Extract angka dari kode prodi, atau gunakan id jika tidak ada angka
        $kodeProdiRaw = preg_replace('/[^0-9]/', '', $programStudi->kode);
        if (empty($kodeProdiRaw)) {
            // Jika tidak ada angka, gunakan id prodi
            $kodeProdiRaw = $programStudi->id;
        }
        $kodeProdi = str_pad($kodeProdiRaw, 2, '0', STR_PAD_LEFT);
        $kodeProdi = substr($kodeProdi, -2); // Ambil 2 digit terakhir

        // 2 digit bulan dan 2 digit tahun
        $bulan = date('m');
        $tahun = date('y'); // 2 digit tahun

        // Prefix untuk mencari nomor urut
        $prefix = $kodeFakultas . $kodeProdi . $bulan . $tahun;

        // Cari nomor urut terakhir dengan prefix ini
        $lastNim = self::where('nim', 'like', $prefix . '%')
            ->orderBy('nim', 'desc')
            ->value('nim');

        if ($lastNim) {
            // Ambil 4 digit terakhir dan tambah 1
            $lastUrut = (int) substr($lastNim, -4);
            $newUrut = $lastUrut + 1;
        } else {
            $newUrut = 1;
        }

        // Format nomor urut 4 digit
        $nomorUrut = str_pad($newUrut, 4, '0', STR_PAD_LEFT);

        return $prefix . $nomorUrut;
    }

    /**
     * Generate email dari NIM
     * Format: nim@student.siakad.ac.id
     */
    public static function generateEmail($nim)
    {
        // Ambil domain dari setting atau gunakan default
        $domain = setting('email_domain_mahasiswa', 'student.siakad.ac.id');
        return strtolower($nim) . '@' . $domain;
    }
}
