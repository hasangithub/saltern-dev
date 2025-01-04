<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherIncome extends Model
{
    protected $fillable = [
        'received_date',
        'income_category_id',
        'amount',
        'name',
        'description',
    ];

    public function incomeCategory()
    {
        return $this->belongsTo(IncomeCategory::class);
    }
}
