<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OwnerLoan extends Model
{
    protected $fillable = ['membership_id', 'requested_amount', 'purpose', 'approved_amount', 'approval_comments' , 'approved_by' , 'approval_date', 'status', 'voucher_id', 'is_migrated', 'created_by'];

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function ownerLoanRepayment()
    {
        return $this->hasMany(OwnerLoanRepayment::class);
    }
 
    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->created_at)->format('Y-m-d');
    }
}
