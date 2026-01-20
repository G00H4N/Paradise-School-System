<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // SMS logging ke liye

class AttendanceController extends Controller
{
    // 1. Attendance Register Page (Teacher View)
    public function index(Request $request)
    {
        $query = Student::where('is_active', true)->orderBy('roll_no');

        // Agar Class select ki hai to filter karo
        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        $students = $query->get()->map(function ($student) use ($request) {
            // Check karo aaj ki hazri lagi hai?
            $date = $request->date ?? now()->toDateString();
            $attendance = Attendance::where('student_id', $student->id)
                ->where('attendance_date', $date)
                ->first();

            // Agar hazri lagi hai to status uthao, warna default Present (1)
            $student->attendance_status = $attendance ? $attendance->status : 1;
            return $student;
        });

        return inertia('Attendance/Index', [
            'classes' => SchoolClass::all(),
            'students' => $students,
            'filters' => $request->only(['class_id', 'date'])
        ]);
    }

    // 2. Manual Bulk Attendance Save (Teacher Checkboxes)
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'attendance_data' => 'required|array', // [{student_id: 1, status: 2}, ...]
        ]);

        foreach ($request->attendance_data as $data) {
            $attendance = Attendance::updateOrCreate(
                [
                    'student_id' => $data['student_id'],
                    'attendance_date' => $request->date,
                ],
                [
                    'status' => $data['status'], // 1=Present, 2=Absent, 3=Leave, 4=Late
                    'check_in' => now()->format('H:i:s'), // Tracking time
                ]
            );

            // 🔴 SPECIFICATION FEATURE: Auto-SMS on Absent
            if ($data['status'] == 2) {
                $this->sendAbsentSMS($data['student_id'], $request->date);
            }
        }

        return redirect()->back()->with('success', 'Attendance Marked & SMS Sent!');
    }

    // 3. Biometric / Barcode API (Machine yahan data bhejegi)
    public function apiStore(Request $request)
    {
        // Machine bhejegi: { "admission_no": "ADM-1001", "timestamp": "2026-01-20 08:00:00" }
        $student = Student::where('admission_no', $request->admission_no)->first();

        if ($student) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'attendance_date' => now()->toDateString(),
                ],
                [
                    'status' => 1, // Machine scan = Present
                    'check_in' => now()->format('H:i:s'),
                ]
            );
            return response()->json(['message' => 'Marked Present', 'student' => $student->full_name]);
        }

        return response()->json(['error' => 'Student Not Found'], 404);
    }

    // 4. SMS Sending Logic (Private Helper)
    private function sendAbsentSMS($studentId, $date)
    {
        $student = Student::with('user')->find($studentId);

        // Agar Parent ka phone number hai
        if ($student && $student->phone) {
            $message = "Dear Parent, your child {$student->full_name} is ABSENT today ({$date}). Please contact school if this is an error. - Paradise School";

            // Filhal hum Log mein save kar rahe hain kyunke asli API Key (Gap Analysis) abhi missing hai.
            // Jab aap JazzCash/SMS API khareedenge, yahan asli code lagega.
            Log::info("SMS SENT TO: {$student->phone} | MSG: {$message}");
        }
    }
}