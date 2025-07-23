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

    public function entry()
    {
        return match ($this->entry_type) {
            'weighbridge' => $this->belongsTo(\App\Models\WeighbridgeEntry::class, 'entry_id'),
            'other_income' => $this->belongsTo(\App\Models\OtherIncome::class, 'entry_id'),
            'loan' => $this->belongsTo(\App\Models\OwnerLoanRepayment::class, 'entry_id'),
            default => null,
        };
    }
}
