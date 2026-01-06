<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateDokumenField extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'template_dokumen_fields';

    protected $fillable = [
        'template_dokumen_id',
        'kode_field',
        'label',
        'tipe',
        'opsi',
        'nilai_default',
        'sumber_data',
        'wajib',
        'urutan',
        'aktif',
    ];

    protected $casts = [
        'opsi' => 'array',
        'wajib' => 'boolean',
        'aktif' => 'boolean',
    ];

    /**
     * Tipe field yang tersedia
     */
    public const TIPE = [
        'text' => 'Teks Singkat',
        'textarea' => 'Teks Panjang',
        'number' => 'Angka',
        'date' => 'Tanggal',
        'select' => 'Pilihan',
        'radio' => 'Radio Button',
        'checkbox' => 'Checkbox',
        'file' => 'File Upload',
        'hidden' => 'Hidden (Auto)',
    ];

    /**
     * Sumber data yang umum digunakan
     */
    public const SUMBER_DATA = [
        // Mahasiswa
        'mahasiswa.nama' => 'Nama Mahasiswa',
        'mahasiswa.nim' => 'NIM',
        'mahasiswa.tempat_lahir' => 'Tempat Lahir Mahasiswa',
        'mahasiswa.tanggal_lahir' => 'Tanggal Lahir Mahasiswa',
        'mahasiswa.alamat' => 'Alamat Mahasiswa',
        'mahasiswa.no_hp' => 'No HP Mahasiswa',
        'mahasiswa.email' => 'Email Mahasiswa',
        'mahasiswa.program_studi.nama' => 'Program Studi',
        'mahasiswa.program_studi.fakultas.nama' => 'Fakultas',
        'mahasiswa.angkatan' => 'Angkatan',
        'mahasiswa.semester' => 'Semester',
        'mahasiswa.ipk' => 'IPK',
        
        // Dosen
        'dosen.nama' => 'Nama Dosen',
        'dosen.nidn' => 'NIDN',
        'dosen.nip' => 'NIP Dosen',
        'dosen.jabatan_fungsional' => 'Jabatan Fungsional',
        'dosen.program_studi.nama' => 'Prodi Dosen',
        
        // Pegawai
        'pegawai.nama' => 'Nama Pegawai',
        'pegawai.nip' => 'NIP Pegawai',
        'pegawai.jabatan' => 'Jabatan Pegawai',
        'pegawai.unit_kerja.nama' => 'Unit Kerja',
    ];

    /**
     * Relasi ke template
     */
    public function template()
    {
        return $this->belongsTo(TemplateDokumen::class, 'template_dokumen_id');
    }

    /**
     * Scope aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Get nilai dari sumber data
     */
    public function getNilaiDariSumber($model)
    {
        if (!$this->sumber_data || !$model) {
            return $this->nilai_default;
        }

        // Parse sumber data (contoh: mahasiswa.program_studi.nama)
        $parts = explode('.', $this->sumber_data);
        $value = $model;

        foreach ($parts as $index => $part) {
            // Skip bagian pertama jika sama dengan tipe model
            if ($index === 0) {
                continue;
            }
            
            if (is_object($value) && isset($value->$part)) {
                $value = $value->$part;
            } elseif (is_array($value) && isset($value[$part])) {
                $value = $value[$part];
            } else {
                return $this->nilai_default;
            }
        }

        // Format tanggal jika tipe date
        if ($this->tipe === 'date' && $value instanceof \Carbon\Carbon) {
            return format_tanggal($value);
        }

        return $value ?? $this->nilai_default;
    }

    /**
     * Render field sebagai input HTML
     */
    public function renderInput($value = null)
    {
        $value = $value ?? $this->nilai_default;
        $required = $this->wajib ? 'required' : '';
        $name = "fields[{$this->kode_field}]";
        
        switch ($this->tipe) {
            case 'textarea':
                return "<textarea name=\"{$name}\" class=\"form-control\" rows=\"3\" {$required}>{$value}</textarea>";
            
            case 'number':
                return "<input type=\"number\" name=\"{$name}\" class=\"form-control\" value=\"{$value}\" {$required}>";
            
            case 'date':
                $dateValue = $value ? date('Y-m-d', strtotime($value)) : '';
                return "<input type=\"date\" name=\"{$name}\" class=\"form-control\" value=\"{$dateValue}\" {$required}>";
            
            case 'select':
                $options = '<option value="">-- Pilih --</option>';
                foreach ($this->opsi ?? [] as $key => $label) {
                    $selected = $value == $key ? 'selected' : '';
                    $options .= "<option value=\"{$key}\" {$selected}>{$label}</option>";
                }
                return "<select name=\"{$name}\" class=\"form-select\" {$required}>{$options}</select>";
            
            case 'radio':
                $html = '';
                foreach ($this->opsi ?? [] as $key => $label) {
                    $checked = $value == $key ? 'checked' : '';
                    $html .= "<div class=\"form-check\"><input type=\"radio\" name=\"{$name}\" value=\"{$key}\" class=\"form-check-input\" {$checked} {$required}><label class=\"form-check-label\">{$label}</label></div>";
                }
                return $html;
            
            case 'checkbox':
                $checked = $value ? 'checked' : '';
                return "<div class=\"form-check\"><input type=\"checkbox\" name=\"{$name}\" value=\"1\" class=\"form-check-input\" {$checked}><label class=\"form-check-label\">{$this->label}</label></div>";
            
            case 'hidden':
                return "<input type=\"hidden\" name=\"{$name}\" value=\"{$value}\">";
            
            default: // text
                return "<input type=\"text\" name=\"{$name}\" class=\"form-control\" value=\"{$value}\" {$required}>";
        }
    }
}
