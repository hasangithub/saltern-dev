<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = ['journal_date', 'description', 'is_reversal', 'deleted_by'];

    public function details()
    {
        return $this->hasMany(JournalDetail::class, 'journal_id');
    }
}
