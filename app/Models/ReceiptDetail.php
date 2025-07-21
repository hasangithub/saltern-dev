<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptDetail extends Model
{
    protected $fillable = [
        'receipt_id',
        'entry_type',
        'entry_id',
        'amount',
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    public function getOwnerAttribute()
    {
        if ($this->entry_type === 'weighbridge') {
            $entry = WeighbridgeEntry::find($this->entry_id);
            return $entry->owner;
        }

        if ($this->entry_type === 'loan') {
            $entry = OwnerLoanRepayment::find($this->entry_id);
            return $entry->ownerLoan->membership->owner;
        }

        return null;
    }
}
