<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptDetail extends Model
{
    protected $fillable = [
        'receipt_id',
        'entry_type',
        'entry_id',
        'amount',
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }
}
