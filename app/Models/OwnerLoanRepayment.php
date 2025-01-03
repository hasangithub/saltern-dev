<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnerLoanRepayment extends Model
{
    protected $fillable = [
        'owner_loan_id', // References the loan
        'amount',          // Repayment amount
        'repayment_date',  // Date of repayment
        'payment_method',  // Method of payment (e.g., cash, bank transfer)
        'notes',           // Additional notes
    ];
}
