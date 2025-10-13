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
            'text' => $message, // URL-encoded message
        ];

        $url = $apiUrl . '?' . http_build_query($params);

        try {
            // Send request
            $response = file_get_contents($url);
    
            // Example response format: "OK: 12345" or "ERROR: Invalid number"
            if ($response === false) {
               // \Log::error('TextIt: No response from server');
                return false;
            }
    
            // Parse response
            if (strpos($response, 'OK') !== false) {
                // \Log::info("TextIt SMS sent successfully: {$response}");
                return true;
            } else {
                // \Log::error("TextIt SMS failed: {$response}");
                return false;
            }
    
        } catch (\Exception $e) {
            // \Log::error('TextIt SMS Exception: ' . $e->getMessage());
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