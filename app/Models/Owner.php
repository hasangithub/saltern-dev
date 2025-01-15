<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\CivilStatus;
use App\Enums\Gender;

class Owner extends Model
{
    use HasFactory;

    // Specify the table associated with the model if not following naming conventions
    protected $table = 'owners';

    // Define the fillable fields
    protected $fillable = [
        'full_name', 'name_with_initial', 'gender', 'civil_status', 'date_of_birth', 'nic',
        'phone_number', 'whatsapp_number', 'email', 'address_line_1', 'profile_picture',
    ];

    protected $casts = [
        'civil_status' => CivilStatus::class,
        'gender' => Gender::class,
    ];
}
