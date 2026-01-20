<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmsLogController extends Controller
{
    // 1. View Logs
    public function index()
    {
        return inertia('Communication/SmsLogs', [
            'logs' => SmsLog::with('sender')->latest()->paginate(20)
        ]);
    }

    // 2. Send Custom SMS (To Specific Number)
    public function sendCustom(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:160'
        ]);

        // Asli API Integration (JazzCash/Twilio)
        // \App\Services\SmsService::send($request->phone, $request->message);

        // Log Entry
        Log::info("Custom SMS to {$request->phone}: {$request->message}");

        SmsLog::create([
            'receiver_number' => $request->phone,
            'message_body' => $request->message,
            'type' => 'SMS',
            'status' => 'Sent',
            'sent_by' => auth()->id()
        ]);

        return back()->with('success', 'Message Sent!');
    }
}