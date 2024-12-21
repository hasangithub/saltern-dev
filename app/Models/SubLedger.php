<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubLedger extends Model
{
    protected $fillable = ['ledger_id', 'name'];

    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }
    
}
