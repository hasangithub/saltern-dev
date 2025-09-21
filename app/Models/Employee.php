<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'sort_order','user_id','person_id', 'designation', 'employment_type' , 'department', 'base_salary', 'epf_number', 'join_date', 'employment_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function designations()
    {
        return [
            'Manager',
            'Accountant',
            'Cashier',
            'Management Assistant',
            'Clerk',
            'HR Officer',
            'Field Officer',
            'Supervisor',
            'Storekeeper',
            'Technician',
            'Driver',
            'Security',
        ];
    }

    public function staffLoans()
    {
        return $this->hasMany(StaffLoan::class, 'user_id', 'user_id');
    }

}
