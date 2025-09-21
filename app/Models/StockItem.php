<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    protected $fillable = ['name', 'opening_balance'];

    public function transactions() {
        return $this->hasMany(StockTransaction::class);
    }

    public function getCurrentBalanceAttribute() {
        $purchases = $this->transactions()->where('type', 'purchase')->sum('quantity');
        $issues = $this->transactions()->where('type', 'issue')->sum('quantity');
        return $this->opening_balance + $purchases - $issues;
    }
}
