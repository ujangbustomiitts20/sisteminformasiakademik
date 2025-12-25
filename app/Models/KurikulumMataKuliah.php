<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KurikulumMataKuliah extends Model
{
    use HashidsTrait;
    protected $table = 'kurikulum_mata_kuliah';

    protected $fillable = [
        'kurikulum_id',
        'mata_kuliah_id',
        'semester_rekomendasi',
        'kategori',
    ];

    public function kurikulum(): BelongsTo
    {
        return $this->belongsTo(Kurikulum::class);
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class);
    }

    // Accessor untuk badge kategori
    public function getKategoriBadgeAttribute(): string
    {
        return match($this->kategori) {
            'Wajib' => 'danger',
            'Wajib Prodi' => 'warning',
            'Pilihan' => 'info',
            'Pilihan Prodi' => 'secondary',
            'MKU' => 'primary',
            default => 'light',
        };
    }
}
