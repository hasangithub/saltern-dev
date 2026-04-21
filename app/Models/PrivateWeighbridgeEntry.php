<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrivateWeighbridgeEntry extends Model
{
    protected $table = 'private_weighbridge_entries';

    use SoftDeletes;

    protected $dates = ['deleted_at'];

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
        'deleted_by'
    ];

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    public function receipt()
    {
        return $this->hasOneThrough(
            \App\Models\Receipt::class,
            \App\Models\ReceiptDetail::class,
            'entry_id',
            'id',
            'other_income_id',
            'receipt_id'
        )->where('entry_type', 'other_income');
    }
}
