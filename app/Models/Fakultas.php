<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Fakultas extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'fakultas';

    protected $fillable = [
        'kode',
        'nama',
        'singkatan',
        'dekan',
        'alamat',
        'telepon',
        'email',
        'website',
        'akreditasi',
        'tanggal_akreditasi',
        'tanggal_berdiri',
        'sk_pendirian',
        'visi',
        'misi',
        'logo',
    ];

    protected $casts = [
        'tanggal_akreditasi' => 'date',
        'tanggal_berdiri' => 'date',
    ];

    public function programStudi()
    {
        return $this->hasMany(ProgramStudi::class);
    }

    /**
     * Get akreditasi badge color
     */
    public function getAkreditasiBadgeAttribute()
    {
        return match($this->akreditasi) {
            'A', 'Unggul' => 'success',
            'B', 'Baik Sekali' => 'primary',
            'C', 'Baik' => 'warning',
            default => 'secondary'
        };
    }
}
