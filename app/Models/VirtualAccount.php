<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class VirtualAccount extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'virtual_account';

    protected $fillable = [
        'mahasiswa_id',
        'bank_code',
        'va_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const BANKS = [
        'BCA' => 'Bank Central Asia (BCA)',
        'BNI' => 'Bank Negara Indonesia (BNI)',
        'BRI' => 'Bank Rakyat Indonesia (BRI)',
        'MANDIRI' => 'Bank Mandiri',
        'BSI' => 'Bank Syariah Indonesia (BSI)',
        'PERMATA' => 'Bank Permata',
        'CIMB' => 'CIMB Niaga',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function getBankNameAttribute()
    {
        return self::BANKS[$this->bank_code] ?? $this->bank_code;
    }

    public function getFormattedVaAttribute()
    {
        // Format VA number with spaces for readability
        return chunk_split($this->va_number, 4, ' ');
    }

    public static function generateVA($mahasiswaId, $bankCode)
    {
        $mahasiswa = Mahasiswa::find($mahasiswaId);
        if (!$mahasiswa) {
            return null;
        }

        // Format: Bank prefix (3) + NIM (last 10 digits)
        $bankPrefix = match($bankCode) {
            'BCA' => '888',
            'BNI' => '889',
            'BRI' => '890',
            'MANDIRI' => '891',
            'BSI' => '892',
            default => '899',
        };

        $nimPart = substr(preg_replace('/[^0-9]/', '', $mahasiswa->nim), -10);
        $nimPart = str_pad($nimPart, 10, '0', STR_PAD_LEFT);

        return $bankPrefix . $nimPart;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
