<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $fillable = ['name', 'description'];

    // Relation to inventories
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
