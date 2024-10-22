<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'credit_limit',
        'service_out',
        'address_1',
        'address_2',
        'phone_no',
    ];
}
