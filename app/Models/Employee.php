<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','person_id', 'designation', 'base_salary', 'join_date', 'employment_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
