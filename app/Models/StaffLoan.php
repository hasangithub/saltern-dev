<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class StaffLoan extends Model
{
    protected $fillable = ['user_id', 'requested_amount', 'purpose', 'approved_amount', 'approval_comments' , 'approval_date', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staffLoanRepayment()
    {
        return $this->hasMany(StaffLoanRepayment::class);
    }
 
    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->created_at)->format('Y-m-d');
    }
}
