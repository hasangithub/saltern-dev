<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saltern extends Model
{
    protected $fillable = ['name', 'yahai_id'];

    public function yahai(){
        return $this->belongsTo(Yahai::class);
    }
}
