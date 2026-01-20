<?php

namespace App\Http\Controllers;

use App\Models\LeaveApplication;
use Illuminate\Http\Request;

class LeaveApplicationController extends Controller
{
    // 1. Admin View (All Requests)
    public function index()
    {
        return inertia('Communication/Leaves', [
            'leaves' => LeaveApplication::with('student.schoolClass')->latest()->get()
        ]);
    }

    // 2. Parent Apply (API or Form)
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'required|string'
        ]);

        LeaveApplication::create([
            'student_id' => $request->student_id,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Leave Request Sent!');
    }

    // 3. Admin Action (Approve/Reject)
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:approved,rejected']);

        $leave = LeaveApplication::findOrFail($id);
        $leave->update([
            'status' => $request->status,
            'admin_remark' => $request->remark,
            'approved_by' => auth()->id()
        ]);

        // Logic: Agar approve hua, to Attendance Table mein 'Leave' (Status 3) mark kar do?
        // Spec mein ye automated nahi likha, magar yeh "Smart Feature" hai.
        // Filhal hum strict spec par rehte hain (Sirf Status Update).

        return back()->with('success', 'Leave Status Updated');
    }
}