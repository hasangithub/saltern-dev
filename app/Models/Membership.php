<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    protected $fillable = [
        'saltern_id',
        'owner_id',
        'membership_date',
        'owner_signature',
        'representative_name',
        'representative_signature',
        'is_active',
    ];

    public function owner(){
        return $this->belongsTo(Owner::class);
    }

    public function saltern(){
        return $this->belongsTo(Saltern::class);
    }

    public function representatives()
    {
        return $this->hasMany(Representative::class);
    }
}
