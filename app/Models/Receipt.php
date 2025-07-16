<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = [
        'buyer_id',
        'payment_method_id',
        'bank_sub_ledger_id',
        'cheque_no',
        'cheque_date',
        'receipt_date',
        'total_amount',
        'created_by',
    ];

    // Relationships
    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details()
    {
        return $this->hasMany(ReceiptDetail::class);
    }
    public function bank()
    {
        return $this->belongsTo(SubLedger::class, 'bank_sub_ledger_id');
    }
}
