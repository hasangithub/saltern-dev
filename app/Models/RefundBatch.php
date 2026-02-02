<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundBatch extends Model
{
    protected $table = 'refund_batches';

    protected $fillable = [
        'name',
        'refund_percentage',
        'date_from',
        'date_to',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'remarks',
    ];

    protected $casts = [
        'date_from'   => 'date',
        'date_to'     => 'date',
        'approved_at' => 'datetime',
    ];

    /* ==========================
     | Relationships
     ========================== */

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function serviceChargeRefunds()
    {
        return $this->hasMany(ServiceChargeRefund::class);
    }

    /* ==========================
     | Helpers
     ========================== */

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
