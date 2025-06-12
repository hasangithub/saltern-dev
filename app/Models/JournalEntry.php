<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = ['journal_date', 'description', 'is_reversal'];

    public function details()
    {
        return $this->hasMany(JournalDetail::class, 'journal_id');
    }
}
