<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Yahai extends Model
{
    protected $table = 'yahai';
    protected $fillable = ['name'];

    public function salterns(){
        return $this->hasMany(Saltern::class);
    }
}
