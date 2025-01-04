<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeighbridgeEntry extends Model
{
    protected $fillable = [
        'vehicle_id',
        'initial_weight',
        'tare_weight',
        'transaction_date',
        'owner_id',
        'membership_id',
        'buyer_id',
        'net_weight',
        'bags_count',
        'bag_price',
        'total_amount',
        'status'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'source');
    }

    protected static function booted()
    {
        static::saving(function ($entry) {
            if ($entry->isDirty('tare_weight')) { // Only update if tare_weight is changed
                $entry->net_weight = $entry->tare_weight - $entry->initial_weight;
            }

            // Calculate bags_count and total_amount based on updated net_weight
            if (!is_null($entry->net_weight)) {
                $entry->bags_count = (int) floor($entry->net_weight / 50); // Net weight divided by 50
                $entry->total_amount = $entry->bags_count * $entry->bag_price; // Bags count multiplied by price
            }
        });
    }

    public function getFormattedTotalAmountAttribute()
    {
        return number_format($this->total_amount, 2) . ' LKR';
    }

    public function getFormattedInitialWeightAttribute()
    {
        return number_format($this->initial_weight, 2) . ' kg';
    }

    public function getFormattedTareWeightAttribute()
    {
        return number_format($this->tare_weight, 2) . ' kg';
    }

    public function getFormattedNetWeightAttribute()
    {
        return number_format($this->net_weight, 2) . ' kg';
    }
}
