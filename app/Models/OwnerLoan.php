<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnerLoan extends Model
{
    protected $fillable = ['membership_id', 'requested_amount', 'purpose', 'approved_amount', 'approval_comments' , 'approval_date', 'status'];

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function ownerLoanRepayment()
    {
        return $this->hasMany(OwnerLoanRepayment::class);
    }
}
