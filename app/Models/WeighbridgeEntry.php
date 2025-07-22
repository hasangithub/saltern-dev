<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Setting;


class WeighbridgeEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'culture',
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
        'owner_share_percentage',
        'status',
        'is_service_charge_paid',
        'created_by',
        'updated_by',
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

    public function receipt()
    {
        return $this->hasOneThrough(
            \App\Models\Receipt::class,
            \App\Models\ReceiptDetail::class,
            'entry_id',       // FK on ReceiptDetails
            'id',             // FK on Receipts
            'id',             // Local key on WeighbridgeEntry
            'receipt_id'      // Local key on ReceiptDetails
        )->where('entry_type', 'weighbridge');
    }

    public function loanRepayments()
    {
        return $this->hasMany(OwnerLoanRepayment::class, 'weighbridge_entry_id');
    }


    protected $casts = [
        'bags_count' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($entry) {
            $ownerSharePercentage = Setting::where('key', 'owner_share_percentage')->value('value') ?? 30;
            $bagPerWeight = Setting::where('key', 'bag_per_weight')->value('value') ?? 30;
            // Calculate net_weight when a new record is created
            $entry->net_weight = bcsub($entry->tare_weight, $entry->initial_weight, 2);
        
            // Calculate bags_count and total_amount based on net_weight
            if (!is_null($entry->net_weight)) {
                $entry->bags_count = bcdiv($entry->net_weight, $bagPerWeight, 2); // Divide net_weight by 50, keep 2 decimal places
                $entry->total_amount = bcmul($entry->bags_count, $entry->bag_price, 2);
            }

            $entry->created_by = auth('web')->id();
            $entry->updated_by = auth('web')->id();
            $entry->owner_share_percentage = $ownerSharePercentage;

            $entry->turn_no = static::whereDate('transaction_date', $entry->transaction_date)->max('turn_no') + 1 ?? 1;
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
