<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public static function send($phone, $message)
    {
        // Yahan Branding SMS ya WhatsApp API ka code ayega
        // Example: Twilio, Ultramsg, or Local Pakistani Gateway

        // Simulation for now (as per Spec Gap Analysis recommendation)
        Log::info("SERVICE: Sending SMS to $phone -> $message");

        // Real implementation example:
        /*
        Http::post('https://api.sms-gateway.com/send', [
            'api_key' => env('SMS_API_KEY'),
            'to' => $phone,
            'message' => $message
        ]);
        */

        return true;
    }
}