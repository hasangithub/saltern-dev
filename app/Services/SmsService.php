<?php

namespace App\Services;

use App\Models\SmsSetting;
use Illuminate\Support\Facades\Http;

class SmsService
{
    protected $settings;

    public function __construct()
    {
        // Retrieve SMS settings from the database
        $this->settings = SmsSetting::first();
    }

    /**
     * Send an SMS using the third-party API.
     *
     * @param string $phoneNumber
     * @param string $message
     * @return bool
     */
    public function sendSms($phoneNumber, $message)
    {

        // Check if SMS is enabled
        if (!$this->settings || !$this->settings->sms_enabled) {
            return false;
        }
           
        
        $apiUrl = 'https://www.textit.biz/sendmsg/';
        $params = [
            'id' => $this->settings->sender_id, // Your phone number (API key)
            'pw' => '3257', // Your password (API secret)
            'to' => $phoneNumber, // Recipient's phone number
            'text' => urlencode($message), // URL-encoded message
        ];

        try {
            // Send the HTTP GET request to the TextIt.biz API
            $response = Http::get($apiUrl, $params);
           
            // Check if the request was successful
            if ($response->successful()) {
                return true; // SMS sent successfully
            } else {
                return false; // SMS sending failed
            }
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * Test the SMS functionality.
     *
     * @param string $phoneNumber
     * @return bool
     */
    public function testSms($phoneNumber)
    {
        $message = 'This is a test SMS from the system.';
        return $this->sendSms($phoneNumber, $message);
    }
}