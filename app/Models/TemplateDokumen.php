<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateDokumen extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'template_dokumen';

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'deskripsi',
        'template_judul',
        'template_nomor',
        'template_isi',
        'template_penutup',
        'tampilkan_kop',
        'tampilkan_logo',
        'pejabat_1_id',
        'pejabat_1_label',
        'pejabat_2_id',
        'pejabat_2_label',
        'tampilkan_ttd_digital',
        'tampilkan_stempel',
        'ukuran_kertas',
        'orientasi',
        'catatan_bawah',
        'aktif',
    ];

    protected $casts = [
        'tampilkan_kop' => 'boolean',
        'tampilkan_logo' => 'boolean',
        'tampilkan_ttd_digital' => 'boolean',
        'tampilkan_stempel' => 'boolean',
        'aktif' => 'boolean',
    ];

    /**
     * Kategori dokumen
     */
    public const KATEGORI = [
        'akademik' => 'Akademik',
        'keuangan' => 'Keuangan',
        'kepegawaian' => 'Kepegawaian/SDM',
        'kemahasiswaan' => 'Kemahasiswaan',
        'pmb' => 'Penerimaan Mahasiswa Baru',
        'umum' => 'Umum',
    ];

    /**
     * Relasi ke fields
     */
    public function fields()
    {
        return $this->hasMany(TemplateDokumenField::class)->orderBy('urutan');
    }

    /**
     * Relasi ke Pejabat 1
     */
    public function pejabat1()
    {
        return $this->belongsTo(PejabatPenandatangan::class, 'pejabat_1_id');
    }

    /**
     * Relasi ke Pejabat 2
     */
    public function pejabat2()
    {
        return $this->belongsTo(PejabatPenandatangan::class, 'pejabat_2_id');
    }

    /**
     * Scope aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Get by kode
     */
    public static function getByKode($kode)
    {
        return self::with('fields')->where('kode', $kode)->first();
    }

    /**
     * Render template dengan data
     * Mengganti placeholder {field} dengan nilai dari $data
     */
    public function render($data = [])
    {
        $result = [
            'judul' => $this->replacePlaceholders($this->template_judul, $data),
            'nomor' => $this->replacePlaceholders($this->template_nomor, $data),
            'isi' => $this->replacePlaceholders($this->template_isi, $data),
            'penutup' => $this->replacePlaceholders($this->template_penutup, $data),
        ];
        
        return $result;
    }

    /**
     * Replace placeholders dalam text
     * Format: {field_name} atau {relasi.field}
     */
    public function replacePlaceholders($text, $data = [])
    {
        if (!$text) return '';
        
        // Replace dengan data yang diberikan
        foreach ($data as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $text = str_replace('{' . $key . '}', $value, $text);
            }
        }
        
        // Replace dengan setting
        $text = preg_replace_callback('/\{setting\.([a-z_]+)\}/', function ($matches) {
            return setting($matches[1], '');
        }, $text);
        
        // Replace tanggal
        $text = str_replace('{tanggal_sekarang}', format_tanggal(now()), $text);
        $text = str_replace('{tahun}', date('Y'), $text);
        $text = str_replace('{bulan}', date('m'), $text);
        $text = str_replace('{bulan_romawi}', $this->bulanRomawi(date('n')), $text);
        
        return $text;
    }

    /**
     * Convert bulan ke romawi
     */
    protected function bulanRomawi($bulan)
    {
        $romawi = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $romawi[$bulan - 1] ?? '';
    }

    /**
     * Get available placeholders untuk template ini
     */
    public function getAvailablePlaceholders()
    {
        $placeholders = [];
        
        // Dari fields yang didefinisikan
        foreach ($this->fields as $field) {
            $placeholders[] = [
                'kode' => $field->kode_field,
                'label' => $field->label,
                'contoh' => '{' . $field->kode_field . '}',
            ];
        }
        
        // Placeholder sistem
        $sistem = [
            ['kode' => 'tanggal_sekarang', 'label' => 'Tanggal Sekarang', 'contoh' => '{tanggal_sekarang}'],
            ['kode' => 'tahun', 'label' => 'Tahun', 'contoh' => '{tahun}'],
            ['kode' => 'bulan', 'label' => 'Bulan (angka)', 'contoh' => '{bulan}'],
            ['kode' => 'bulan_romawi', 'label' => 'Bulan (Romawi)', 'contoh' => '{bulan_romawi}'],
            ['kode' => 'setting.institution_name', 'label' => 'Nama Institusi', 'contoh' => '{setting.institution_name}'],
            ['kode' => 'setting.kota_institusi', 'label' => 'Kota Institusi', 'contoh' => '{setting.kota_institusi}'],
        ];
        
        return array_merge($placeholders, $sistem);
    }
}
