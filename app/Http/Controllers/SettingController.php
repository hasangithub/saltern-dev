<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $manualEnabled = Setting::get('weighbridge_manual_enable', 0);
        return view('settings.index', compact('manualEnabled'));
    }

    // Update settings
    public function update(Request $request)
    {
        $manualEnabled = $request->has('weighbridge_manual_enable') ? 1 : 0;
        Setting::set('weighbridge_manual_enable', $manualEnabled);

        return back()->with('success', 'Settings updated successfully!');
    }
}
