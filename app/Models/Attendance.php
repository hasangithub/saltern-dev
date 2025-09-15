<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{

    protected $fillable = [
        'user_id',
        'attendance_date',
        'status',
        'punch_times',
        'worked_hours'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
