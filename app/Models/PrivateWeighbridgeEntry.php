<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateWeighbridgeEntry extends Model
{
    protected $table = 'private_weighbridge_entries';

    protected $fillable = [
        'vehicle_id',
        'transaction_date',
        'first_weight',
        'first_weight_time',
        'second_weight',
        'second_weight_time',
        'customer_name',
        'buyer_id',
        'amount',
        'is_paid',
        'other_income_id',
        'created_by',
        'updated_by',
        'status',
    ];

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }
}
