<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundHistory extends Model
{
    use HasFactory;

    protected $table = 'refund_history';

    protected $fillable = [
        'refund_id',
        'status_lama',
        'status_baru',
        'keterangan',
        'user_id',
    ];

    // Relations
    public function refund()
    {
        return $this->belongsTo(Refund::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor
    public function getStatusLamaLabelAttribute()
    {
        return Refund::STATUS_LIST[$this->status_lama] ?? $this->status_lama;
    }

    public function getStatusBaruLabelAttribute()
    {
        return Refund::STATUS_LIST[$this->status_baru] ?? $this->status_baru;
    }
}
