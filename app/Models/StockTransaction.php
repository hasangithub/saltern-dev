<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = ['stock_item_id', 'type', 'quantity', 'transaction_date'];

    public function item() {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }
}
