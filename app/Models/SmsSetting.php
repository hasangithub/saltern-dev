<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsSetting extends Model
{
    protected $table = 'sms_settings';
    protected $fillable = [
        'api_key',
        'sender_id',
        'sms_enabled',
    ];
}
