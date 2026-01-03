<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeminarProposal extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'seminar_proposal';

    protected $fillable = [
        'nomor_seminar',
        'tugas_akhir_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'ruangan',
        'penguji_1_id',
        'penguji_2_id',
        'status',
        'nilai_penguji_1',
        'nilai_penguji_2',
        'nilai_pembimbing_1',
        'nilai_pembimbing_2',
        'nilai_akhir',
        'hasil',
        'catatan_revisi',
        'deadline_revisi',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'deadline_revisi' => 'date',
        'nilai_penguji_1' => 'decimal:2',
        'nilai_penguji_2' => 'decimal:2',
        'nilai_pembimbing_1' => 'decimal:2',
        'nilai_pembimbing_2' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
    ];

    public function tugasAkhir()
    {
        return $this->belongsTo(TugasAkhir::class, 'tugas_akhir_id');
    }

    public function penguji1()
    {
        return $this->belongsTo(Dosen::class, 'penguji_1_id');
    }

    public function penguji2()
    {
        return $this->belongsTo(Dosen::class, 'penguji_2_id');
    }

    public static function getStatusOptions()
    {
        return [
            'diajukan' => 'Diajukan',
            'dijadwalkan' => 'Dijadwalkan',
            'berlangsung' => 'Berlangsung',
            'selesai' => 'Selesai',
            'ditunda' => 'Ditunda',
            'dibatalkan' => 'Dibatalkan',
        ];
    }

    public static function getHasilOptions()
    {
        return [
            'lulus' => 'Lulus',
            'lulus_revisi' => 'Lulus dengan Revisi',
            'tidak_lulus' => 'Tidak Lulus',
        ];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'diajukan' => 'info',
            'dijadwalkan' => 'primary',
            'berlangsung' => 'warning',
            'selesai' => 'success',
            'ditunda' => 'secondary',
            'dibatalkan' => 'danger',
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getStatusLabelAttribute()
    {
        $options = self::getStatusOptions();
        return $options[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getHasilBadgeAttribute()
    {
        $badges = [
            'lulus' => 'success',
            'lulus_revisi' => 'warning',
            'tidak_lulus' => 'danger',
        ];

        return $badges[$this->hasil] ?? 'secondary';
    }

    // Hitung nilai akhir
    public function hitungNilaiAkhir()
    {
        // Bobot: Pembimbing 1 (30%), Pembimbing 2 (20%), Penguji 1 (25%), Penguji 2 (25%)
        $nilai = 0;
        $nilai += ($this->nilai_pembimbing_1 ?? 0) * 0.30;
        $nilai += ($this->nilai_pembimbing_2 ?? 0) * 0.20;
        $nilai += ($this->nilai_penguji_1 ?? 0) * 0.25;
        $nilai += ($this->nilai_penguji_2 ?? 0) * 0.25;

        $this->nilai_akhir = round($nilai, 2);
        
        return $this->nilai_akhir;
    }

    public static function generateNomorSeminar()
    {
        $tahun = date('Y');
        $bulan = date('m');
        $prefix = "SEM/{$tahun}/{$bulan}/";
        
        $lastNumber = self::where('nomor_seminar', 'like', $prefix . '%')
            ->orderBy('nomor_seminar', 'desc')
            ->first();

        if ($lastNumber) {
            $lastSeq = (int) substr($lastNumber->nomor_seminar, -4);
            $newSeq = $lastSeq + 1;
        } else {
            $newSeq = 1;
        }

        return $prefix . str_pad($newSeq, 4, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_seminar)) {
                $model->nomor_seminar = self::generateNomorSeminar();
            }
        });
    }
}
