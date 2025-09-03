<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'place',
        'amount',
        'voucher_id',
        'description',
        'status',
        'replaced_id',
        'created_by',
    ];

    public function replacedInventory()
    {
        return $this->belongsTo(Inventory::class, 'replaced_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
