<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;

    // Specify the table associated with the model if not following naming conventions
    protected $table = 'owners';

    // Define the fillable fields
    protected $fillable = [
        'full_name',
        'dob',
        'nic',
        'address',
        'mobile_no',
    ];

    public function salterns(){
        return $this->hasMany(Saltern::class);
    }
}
