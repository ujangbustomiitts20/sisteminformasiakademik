<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekonsiliasi extends Model
{
    use HasFactory;

    protected $table = 'rekonsiliasi';

    protected $fillable = [
        'akun_bank_id',
        'nomor_rekonsiliasi',
        'periode_awal',
        'periode_akhir',
        'saldo_awal_bank',
        'saldo_akhir_bank',
        'saldo_sistem',
        'selisih',
        'total_mutasi',
        'total_matched',
        'total_unmatched',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'catatan',
    ];

    protected $casts = [
        'periode_awal' => 'date',
        'periode_akhir' => 'date',
        'saldo_awal_bank' => 'decimal:2',
        'saldo_akhir_bank' => 'decimal:2',
        'saldo_sistem' => 'decimal:2',
        'selisih' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_APPROVED = 'approved';

    // Relations
    public function akunBank()
    {
        return $this->belongsTo(AkunBank::class, 'akun_bank_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function detailRekonsiliasi()
    {
        return $this->hasMany(DetailRekonsiliasi::class, 'rekonsiliasi_id');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    // Methods
    public function hitungSelisih()
    {
        $this->selisih = $this->saldo_akhir_bank - $this->saldo_sistem;
        $this->save();

        return $this->selisih;
    }

    public function updateStatistik()
    {
        $details = $this->detailRekonsiliasi;
        
        $this->total_mutasi = $details->count();
        $this->total_matched = $details->where('status', 'matched')->count();
        $this->total_unmatched = $details->whereIn('status', ['unmatched', 'manual', 'ignored'])->count();
        $this->save();

        return $this;
    }

    public function complete()
    {
        $this->status = self::STATUS_COMPLETED;
        $this->updateStatistik();
        $this->hitungSelisih();
        $this->save();

        return $this;
    }

    public function approve($userId = null)
    {
        $this->status = self::STATUS_APPROVED;
        $this->approved_by = $userId ?? auth()->id();
        $this->approved_at = now();
        $this->save();

        return $this;
    }

    public function getPersentaseMatchedAttribute()
    {
        if ($this->total_mutasi == 0) return 0;
        return round(($this->total_matched / $this->total_mutasi) * 100, 2);
    }

    public function isEditable()
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_IN_PROGRESS]);
    }

    public static function generateNomorRekonsiliasi()
    {
        $prefix = 'RKN-' . date('Ym') . '-';
        $lastRekon = self::where('nomor_rekonsiliasi', 'like', $prefix . '%')
            ->orderBy('nomor_rekonsiliasi', 'desc')
            ->first();
        
        if ($lastRekon) {
            $lastNumber = (int) substr($lastRekon->nomor_rekonsiliasi, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    // Alias for detailRekonsiliasi
    public function details()
    {
        return $this->detailRekonsiliasi();
    }
}
