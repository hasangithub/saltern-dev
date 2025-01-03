<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = ['bank_name'];

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }
}
