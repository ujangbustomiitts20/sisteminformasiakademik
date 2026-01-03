<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SliderPmb extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'slider_pmb';

    protected $fillable = [
        'judul',
        'deskripsi',
        'gambar',
        'link',
        'button_text',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }

    public function getGambarUrlAttribute()
    {
        if ($this->gambar && file_exists(public_path('storage/' . $this->gambar))) {
            return asset('storage/' . $this->gambar);
        }
        return asset('images/slider-default.jpg');
    }
}
