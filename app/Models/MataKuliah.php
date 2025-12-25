<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class MataKuliah extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'mata_kuliah';

    protected $fillable = [
        'program_studi_id',
        'kode',
        'nama',
        'sks',
        'jumlah_pertemuan',
        'semester',
        'jenis',
        'deskripsi',
    ];

    protected $attributes = [
        'jumlah_pertemuan' => 16,
    ];

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function jadwalKuliah()
    {
        return $this->hasMany(JadwalKuliah::class);
    }

    /**
     * Mata kuliah yang menjadi prasyarat untuk MK ini
     */
    public function prasyarat()
    {
        return $this->belongsToMany(
            MataKuliah::class,
            'prasyarat_mata_kuliah',
            'mata_kuliah_id',
            'mata_kuliah_prasyarat_id'
        )->withPivot('jenis_prasyarat', 'nilai_minimal')->withTimestamps();
    }

    /**
     * Mata kuliah yang membutuhkan MK ini sebagai prasyarat
     */
    public function mataKuliahYangMembutuhkan()
    {
        return $this->belongsToMany(
            MataKuliah::class,
            'prasyarat_mata_kuliah',
            'mata_kuliah_prasyarat_id',
            'mata_kuliah_id'
        )->withPivot('jenis_prasyarat', 'nilai_minimal')->withTimestamps();
    }

    /**
     * Detail prasyarat dengan relasi model
     */
    public function prasyaratDetail()
    {
        return $this->hasMany(PrasyaratMataKuliah::class, 'mata_kuliah_id');
    }

    /**
     * Cek apakah mahasiswa memenuhi prasyarat untuk MK ini
     */
    public function cekPrasyarat($mahasiswaId)
    {
        $prasyaratList = $this->prasyaratDetail()->with('mataKuliahPrasyarat')->get();
        
        if ($prasyaratList->isEmpty()) {
            return [
                'terpenuhi' => true,
                'pesan' => 'Tidak ada prasyarat',
                'detail' => []
            ];
        }

        $hasil = [];
        $semuaTerpenuhi = true;

        foreach ($prasyaratList as $prasyarat) {
            $mkPrasyarat = $prasyarat->mataKuliahPrasyarat;
            
            // Cari nilai mahasiswa untuk MK prasyarat
            $nilaiMhs = Nilai::whereHas('krs', function($q) use ($mahasiswaId) {
                $q->where('mahasiswa_id', $mahasiswaId)
                  ->where('status', 'Disetujui');
            })->whereHas('krs.jadwalKuliah', function($q) use ($mkPrasyarat) {
                $q->where('mata_kuliah_id', $mkPrasyarat->id);
            })->first();

            $status = 'belum_ambil';
            $terpenuhi = false;
            $nilaiHuruf = null;

            if ($nilaiMhs) {
                $nilaiHuruf = $nilaiMhs->huruf;
                
                if ($prasyarat->jenis_prasyarat === 'wajib') {
                    // Harus lulus dengan nilai minimal
                    $nilaiMinimal = $prasyarat->nilai_minimal ?? 'D';
                    $terpenuhi = $this->bandingkanNilai($nilaiHuruf, $nilaiMinimal);
                    $status = $terpenuhi ? 'lulus' : 'tidak_memenuhi_nilai';
                } else {
                    // Pilihan: cukup pernah ambil saja
                    $terpenuhi = true;
                    $status = 'sudah_ambil';
                }
            }

            if (!$terpenuhi) {
                $semuaTerpenuhi = false;
            }

            $hasil[] = [
                'mata_kuliah' => $mkPrasyarat->kode . ' - ' . $mkPrasyarat->nama,
                'jenis' => $prasyarat->jenis_prasyarat,
                'nilai_minimal' => $prasyarat->nilai_minimal,
                'nilai_mahasiswa' => $nilaiHuruf,
                'status' => $status,
                'terpenuhi' => $terpenuhi,
            ];
        }

        return [
            'terpenuhi' => $semuaTerpenuhi,
            'pesan' => $semuaTerpenuhi ? 'Semua prasyarat terpenuhi' : 'Ada prasyarat yang belum terpenuhi',
            'detail' => $hasil
        ];
    }

    /**
     * Bandingkan nilai huruf
     */
    private function bandingkanNilai($nilaiMhs, $nilaiMinimal)
    {
        $urutanNilai = ['A' => 8, 'A-' => 7, 'B+' => 6, 'B' => 5, 'B-' => 4, 'C+' => 3, 'C' => 2, 'D' => 1, 'E' => 0];
        
        $skorMhs = $urutanNilai[$nilaiMhs] ?? 0;
        $skorMinimal = $urutanNilai[$nilaiMinimal] ?? 0;
        
        return $skorMhs >= $skorMinimal;
    }

    /**
     * Apakah MK ini memiliki prasyarat
     */
    public function hasPrasyarat()
    {
        return $this->prasyaratDetail()->exists();
    }

    /**
     * Get daftar prasyarat dalam format string
     */
    public function getPrasyaratStringAttribute()
    {
        $prasyarat = $this->prasyarat;
        
        if ($prasyarat->isEmpty()) {
            return '-';
        }

        return $prasyarat->map(function($mk) {
            $jenis = $mk->pivot->jenis_prasyarat === 'wajib' ? '(Wajib)' : '(Pilihan)';
            return $mk->kode . ' ' . $jenis;
        })->implode(', ');
    }
}

