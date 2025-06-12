<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Yahai extends Model
{
    protected $table = 'yahai';
    protected $fillable = ['name', 'side_id'];

    public function side()
    {
        return $this->belongsTo(Side::class);
    }

    public function salterns()
    {
        return $this->hasMany(Saltern::class);
    }
}
