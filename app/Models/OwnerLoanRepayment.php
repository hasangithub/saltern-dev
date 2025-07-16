<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OwnerLoanRepayment extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'owner_loan_id', // References the loan
        'buyer_id',
        'weighbridge_entry_id',
        'amount',          // Repayment amount
        'repayment_date',  // Date of repayment
        'payment_method',  // Method of payment (e.g., cash, bank transfer)
        'notes',           // Additional notes
        'status',
    ];

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    public function ownerLoan()
    {
        return $this->belongsTo(OwnerLoan::class, 'owner_loan_id');
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
        )->where('entry_type', 'loan');
    }
}
