<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\CivilStatus;
use App\Enums\Gender;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Owner extends Authenticatable
{
    use HasFactory, Notifiable;

    // Specify the table associated with the model if not following naming conventions
    protected $table = 'owners';

    // Define the fillable fields
    protected $fillable = [
        'full_name', 'name_with_initial', 'gender', 'civil_status', 'date_of_birth', 'nic',
        'phone_number', 'whatsapp_number', 'email', 'password', 'address_line_1', 'profile_picture',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'civil_status' => CivilStatus::class,
        'gender' => Gender::class,
    ];

    public function complaints()
    {
        return $this->hasMany(OwnerComplaint::class);
    }
}
