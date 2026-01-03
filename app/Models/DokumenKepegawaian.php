<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenKepegawaian extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'dokumen_kepegawaian';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'jenis_dokumen',
        'nama_dokumen',
        'no_dokumen',
        'tanggal_terbit',
        'tanggal_berlaku',
        'penerbit',
        'file_dokumen',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'tanggal_berlaku' => 'date',
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    // Check if document is still valid
    public function isValid()
    {
        if (!$this->tanggal_berlaku) {
            return true;
        }
        return $this->tanggal_berlaku >= now();
    }
}
