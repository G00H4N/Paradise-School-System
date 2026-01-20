<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DiaryController extends Controller
{
    // 1. View Diaries
    public function index(Request $request)
    {
        $query = Diary::with(['schoolClass', 'subject', 'teacher']);

        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->date) {
            $query->whereDate('diary_date', $request->date);
        }

        return inertia('Academic/Diary', [
            'diaries' => $query->latest()->get(),
            'classes' => SchoolClass::all(),
            'subjects' => Subject::all()
        ]);
    }

    // 2. Add Homework & Send SMS
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'diary_date' => 'required|date',
            'description' => 'required|string',
            'send_sms' => 'boolean' // Checkbox from frontend
        ]);

        $diary = Diary::create([
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'diary_date' => $request->diary_date,
            'description' => $request->description,
            'added_by' => auth()->id()
        ]);

        // --- SPECIFICATION: SMS Trigger ---
        if ($request->send_sms) {
            $this->broadcastDiarySMS($diary);
        }

        return redirect()->back()->with('success', 'Homework Added Successfully!');
    }

    // Helper: SMS Logic
    private function broadcastDiarySMS($diary)
    {
        // Class ke saare students nikalo
        $students = Student::where('class_id', $diary->class_id)->whereNotNull('phone')->get();

        $subjectName = $diary->subject->subject_name;
        $msg = "Homework: {$subjectName} - {$diary->description}. Date: {$diary->diary_date}";

        foreach ($students as $student) {
            // Real SMS API yahan lagegi (JazzCash/Twilio)
            // Filhal Log mein save kar rahe hain taake error na aye
            Log::info("SMS TO {$student->phone}: {$msg}");
        }
    }
}