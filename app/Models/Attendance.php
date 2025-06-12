<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{

    protected $fillable = [
        'user_id',
        'attendance_date',
        'status', // values: present, leave, half_day, no_pay
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
