<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffLoan extends Model
{
    protected $fillable = ['user_id', 'requested_amount', 'purpose', 'approved_amount', 'approval_comments' , 'approval_date', 'status'];
}
