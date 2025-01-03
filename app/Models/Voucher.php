<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'name',
        'address',
        'description',
        'payment_method_id',
        'bank_id',
        'cheque_no',
        'cheque_date',
        'amount',
        'note',
        'status'
    ];

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
