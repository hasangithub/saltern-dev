<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\CivilStatus;
use App\Enums\Gender;

class Representative extends Model
{
    protected $fillable = [
        'full_name', 'gender', 'civil_status', 'date_of_birth', 'nic',
        'phone_number', 'secondary_phone_number', 'email', 'address_line_1',
        'address_line_2', 'profile_picture', 'membership_id',
    ];

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    protected $casts = [
        'civil_status' => CivilStatus::class,
        'gender' => Gender::class,
    ];
}
