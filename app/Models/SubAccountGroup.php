<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubAccountGroup extends Model
{
    protected $fillable = ['account_group_id', 'name'];

    public function ledgers()
    {
        return $this->hasMany(Ledger::class);
    }

    public function accountGroup()
    {
        return $this->belongsTo(AccountGroup::class);
    }
}
