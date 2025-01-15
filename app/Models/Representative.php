<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\CivilStatus;
use App\Enums\Gender;

class Representative extends Model
{
    protected $fillable = [
        'name_with_initial', 'nic', 'phone_number', 'membership_id', 'relationship',
    ];

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    protected $casts = [
        'civil_status' => CivilStatus::class,
        'gender' => Gender::class,
    ];
}
