<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    protected $fillable = [
        'saltern_id',
        'owner_id',
        'membership_date',
        'membership_no',
        'owner_signature',
        'representative_signature',
        'representative_authorised_date',
        'is_active',
    ];

    public function owner(){
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function saltern(){
        return $this->belongsTo(Saltern::class);
    }

    public function representative()
    {
        return $this->hasOne(Representative::class);
    }

    public function ownerLoans()
    {
        return $this->hasMany(OwnerLoan::class);
    }
}
