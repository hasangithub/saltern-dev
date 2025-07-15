<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherIncome extends Model
{
    protected $fillable = [
        'received_date',
        'income_category_id',
        'buyer_id',
        'amount',
        'name',
        'description',
        'status',
    ];

    public function incomeCategory()
    {
        return $this->belongsTo(Ledger::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    public function receipt()
    {
        return $this->hasOneThrough(
            \App\Models\Receipt::class,
            \App\Models\ReceiptDetail::class,
            'entry_id',       // FK on ReceiptDetails
            'id',             // FK on Receipts
            'id',             // Local key on WeighbridgeEntry
            'receipt_id'      // Local key on ReceiptDetails
        )->where('entry_type', 'other_income');
    }
}
