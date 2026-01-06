<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UraianKegiatanSkp extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'uraian_kegiatan_skp';

    protected $fillable = [
        'kode',
        'kategori',
        'sub_kategori',
        'uraian_kegiatan',
        'satuan',
        'target_default',
        'bobot',
        'keterangan',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'target_default' => 'decimal:2',
        'bobot' => 'decimal:2',
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    const KATEGORI = [
        'tri_dharma' => 'Tri Dharma Perguruan Tinggi',
        'penunjang' => 'Penunjang',
        'tambahan' => 'Tugas Tambahan',
    ];

    const SUB_KATEGORI = [
        'pendidikan' => 'Pendidikan & Pengajaran',
        'penelitian' => 'Penelitian',
        'pengabdian' => 'Pengabdian Masyarakat',
        'bimbingan' => 'Bimbingan',
        'administrasi' => 'Administrasi',
        'pengembangan' => 'Pengembangan Diri',
        'struktural' => 'Jabatan Struktural',
        'lainnya' => 'Lainnya',
    ];

    const SATUAN = [
        'sks' => 'SKS',
        'dokumen' => 'Dokumen',
        'kegiatan' => 'Kegiatan',
        'mahasiswa' => 'Mahasiswa',
        'artikel' => 'Artikel',
        'buku' => 'Buku',
        'modul' => 'Modul',
        'proposal' => 'Proposal',
        'laporan' => 'Laporan',
        'sertifikat' => 'Sertifikat',
        'persen' => 'Persen',
        'bulan' => 'Bulan',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeBySubKategori($query, $subKategori)
    {
        return $query->where('sub_kategori', $subKategori);
    }

    // Accessors
    public function getKategoriLabelAttribute()
    {
        return self::KATEGORI[$this->kategori] ?? $this->kategori;
    }

    public function getSubKategoriLabelAttribute()
    {
        return self::SUB_KATEGORI[$this->sub_kategori] ?? $this->sub_kategori;
    }

    public function getStatusBadgeAttribute()
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Nonaktif';
    }

    // Static methods
    public static function generateKode($kategori)
    {
        $prefix = match($kategori) {
            'tri_dharma' => 'TD',
            'penunjang' => 'PJ',
            'tambahan' => 'TM',
            default => 'UK',
        };

        $count = self::where('kode', 'like', $prefix . '%')->count() + 1;
        return $prefix . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    // Get grouped by kategori for dropdown
    public static function getGroupedForSelect()
    {
        return self::active()
            ->orderBy('kategori')
            ->orderBy('sub_kategori')
            ->orderBy('urutan')
            ->get()
            ->groupBy('kategori_label');
    }
}
