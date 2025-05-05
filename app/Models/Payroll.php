<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{

    protected $fillable = [
        'user_id',
        'month',
        'total_days',
        'present_days',
        'leave_days',
        'half_days',
        'no_pay_days',
        'basic_salary',
        'net_salary',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
