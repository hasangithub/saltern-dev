<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffLoanRepayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'staff_loan_id', // References the loan
        'payroll_id',
        'amount',          // Repayment amount
        'repayment_date',  // Date of repayment
        'payment_method',  // Method of payment (e.g., cash, bank transfer)
        'notes',           // Additional notes
        'status',
        'deleted_by',
    ];

    public function staffLoan()
    {
        return $this->belongsTo(StaffLoan::class, 'staff_loan_id');
    }
}
