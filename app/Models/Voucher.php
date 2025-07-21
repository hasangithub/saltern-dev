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
        'bank_sub_ledger_id',
        'ledger_id',
        'sub_ledger_id',
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
        return $this->belongsTo(SubLedger::class, 'bank_sub_ledger_id');
    }

    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }
}
