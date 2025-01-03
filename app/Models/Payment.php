<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'source_type',      // Type of source (e.g., 'WeighbridgeEntry', 'LoanRequest')
        'source_id',        // The ID of the related source (e.g., Weighbridge entry ID, Loan request ID)
        'amount',           // Amount of the payment
        'payment_date',     // Date the payment was made
        'payment_method',   // Method of payment (e.g., 'Cash', 'Bank Transfer')
        'description',      // Optional description of the payment
        'notes',            // Optional notes for the payment
    ];
    
    public function source()
    {
        return $this->morphTo();
    }
}
