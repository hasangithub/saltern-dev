<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingsHelper
{
    protected static $settings = null;

    public static function get($key, $default = null)
    {
        if (self::$settings === null) {
            self::$settings = Setting::all()->pluck('value', 'key')->toArray();
        }
        return self::$settings[$key] ?? $default;
    }
}
