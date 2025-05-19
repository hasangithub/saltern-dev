<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalDetail extends Model
{
    protected $fillable = ['journal_id', 'ledger_id','sub_ledger_id', 'debit_amount', 'credit_amount', 'description'];

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_id');
    }

    public function subLedger()
    {
        return $this->belongsTo(SubLedger::class, 'sub_ledger_id');
    }

    public function ledger()
    {
        return $this->belongsTo(Ledger::class, 'ledger_id');
    }
}
