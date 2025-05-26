<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherIncome extends Model
{
    protected $fillable = [
        'received_date',
        'income_category_id',
        'buyer_id',
        'amount',
        'name',
        'description',
        'status',
    ];

    public function incomeCategory()
    {
        return $this->belongsTo(Ledger::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }
}
