<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saltern extends Model
{
    protected $fillable = ['name', 'yahai_id'];

    public function yahai(){
        return $this->belongsTo(Yahai::class);
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function activeMembership()
    {
        return $this->hasOne(Membership::class)->where('is_active', '1');
    }
}
