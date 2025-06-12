<?php

namespace App\Http\Controllers;

use App\Models\SmsSetting;
use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Show the form to update SMS settings.
     *
     * @return \Illuminate\View\View
     */
    public function showSettings()
    {
        $settings = SmsSetting::first() ?? new SmsSetting();

        return view('sms.settings', compact('settings'));
    }

    /**
     * Update SMS settings.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'sender_id' => 'nullable|string',
            'sms_enabled' => 'boolean',
        ]);
    
        // Update or create the record
        SmsSetting::updateOrCreate(
            [], // No conditions, so it will always update the first record or create a new one
            [
                'api_key' => $request->api_key,
                'sender_id' => $request->sender_id,
                'sms_enabled' => $request->sms_enabled ?? false,
            ]
        );
    
        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Test the SMS functionality.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function testSms(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
        ]);

        $phoneNumber = $request->phone_number;

        if ($this->smsService->testSms($phoneNumber)) {
            return redirect()->back()->with('success', 'Test SMS sent successfully.');
        }

        return redirect()->back()->with('error', 'Failed to send test SMS.');
    }

    /**
     * Send an SMS.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendSms(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'message' => 'required|string',
        ]);

        $phoneNumber = $request->phone_number;
        $message = $request->message;

        if ($this->smsService->sendSms($phoneNumber, $message)) {
            return redirect()->back()->with('success', 'SMS sent successfully.');
        }

        return redirect()->back()->with('error', 'Failed to send SMS.');
    }
}