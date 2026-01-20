<?php

namespace App\Http\Controllers;

use App\Models\TimeTable;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class TimeTableController extends Controller
{
    // 1. View Time Table (Filter by Class)
    public function index(Request $request)
    {
        $query = TimeTable::with(['schoolClass', 'subject', 'teacher']);

        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        return inertia('Academic/TimeTable', [
            'routines' => $query->orderBy('day')->orderBy('start_time')->get(),
            'classes' => SchoolClass::all(),
            'subjects' => Subject::all(),
            // Sirf 'teacher' role wale users load karo
            'teachers' => User::where('role', 'teacher')->get()
        ]);
    }

    // 2. Add Class Period
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required',
            'subject_id' => 'required',
            'teacher_id' => 'required', // Teacher assign karna zaroori hai
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        // Optional: Check Teacher Clash (Agar teacher usi waqt kisi aur class mein ho)
        $clash = TimeTable::where('teacher_id', $request->teacher_id)
            ->where('day', $request->day)
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time]);
            })->exists();

        if ($clash) {
            return back()->withErrors(['error' => 'Teacher is busy in another class at this time!']);
        }

        TimeTable::create($request->all());
        return redirect()->back()->with('success', 'Period Added to Routine');
    }

    public function destroy($id)
    {
        TimeTable::findOrFail($id)->delete();
        return back()->with('success', 'Period Removed');
    }
}