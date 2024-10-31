<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saltern extends Model
{
    protected $fillable = ['name','owner_id', 'yahai_id'];

    public function owner(){
        return $this->belongsTo(Owner::class);
    }

    public function yahai(){
        return $this->belongsTo(Yahai::class);
    }
}
