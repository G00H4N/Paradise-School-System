<?php

namespace App\Http\Controllers;

use App\Models\OnlineClass;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;

class OnlineClassController extends Controller
{
    public function index(Request $request)
    {
        $query = OnlineClass::with(['schoolClass', 'subject', 'teacher']);

        // Agar Student hai to sirf apni class ka dekhe
        if (auth()->user()->role === 'student') {
            $student = \App\Models\Student::where('user_id', auth()->id())->first();
            if ($student) {
                $query->where('class_id', $student->class_id);
            }
        }

        return inertia('Academic/OnlineClasses', [
            'classes' => $query->latest()->get(),
            'schoolClasses' => SchoolClass::all(),
            'subjects' => Subject::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required',
            'subject_id' => 'required',
            'topic' => 'required',
            'meeting_link' => 'required|url',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        OnlineClass::create([
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'teacher_id' => auth()->id(),
            'topic' => $request->topic,
            'meeting_platform' => 'Google Meet', // Or dropdown
            'meeting_link' => $request->meeting_link,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'scheduled'
        ]);

        return back()->with('success', 'Class Scheduled!');
    }
}