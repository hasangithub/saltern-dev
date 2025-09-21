<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransaction extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['stock_item_id', 'type', 'quantity', 'transaction_date', 'department', 'description'];

    public function item() {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }
}
