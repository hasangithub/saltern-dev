<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalDetail extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = ['journal_id', 'ledger_id','sub_ledger_id', 'debit_amount', 'credit_amount', 'description', 'deleted_by'];

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
