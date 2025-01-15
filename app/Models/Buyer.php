<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_name',
        'business_registration_number',
        'full_name',
        'credit_limit',
        'service_out',
        'address_1',
        'phone_number',
        'secondary_phone_number',
        'whatsapp_number'
    ];
}
