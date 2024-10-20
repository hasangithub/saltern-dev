<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeighbridgeEntry extends Model
{
    protected $fillable = [
        'vehicle_id',
        'initial_weight',
        'tare_weight',
        'transaction_date',
        'owner_id',
        'buyer_id',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }
}
