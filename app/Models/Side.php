<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Side extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function yahais()
    {
        return $this->hasMany(Yahai::class);
    }
}
