<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    protected $fillable = ['sub_account_group_id', 'name'];

    public function subAccount()
    {
        return $this->belongsTo(SubAccountGroup::class);
    }

    public function subLedgers()
    {
        return $this->hasMany(SubLedger::class);
    }

}