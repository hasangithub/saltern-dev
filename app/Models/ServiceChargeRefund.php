<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceChargeRefund extends Model
{
    protected $fillable = [
        'refund_batch_id',
        'membership_id',
        'total_service_charge',
        'refund_amount',
        'from_date',
        'to_date',
        'voucher_id',
        'created_by',
    ];

    public function memberships()
    {
        return $this->belongsTo(Membership::class, 'membership_id');
    }

    // Relationship to staff (optional)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Link to voucher (optional)
    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }

    // Weighbridge entries connected to this refund
    public function entries()
    {
        return $this->hasMany(WeighbridgeEntry::class, 'refund_id');
    }
}
