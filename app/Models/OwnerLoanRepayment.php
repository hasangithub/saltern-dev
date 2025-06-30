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
}
