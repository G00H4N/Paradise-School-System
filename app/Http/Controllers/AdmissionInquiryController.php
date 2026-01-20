<?php

namespace App\Http\Controllers;

use App\Models\AdmissionInquiry;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\Student;
use App\Models\ParentWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdmissionInquiryController extends Controller
{
    // 1. List Inquiries
    public function index()
    {
        return inertia('Admissions/Inquiries', [
            'inquiries' => AdmissionInquiry::with('schoolClass')->latest()->get(),
            'classes' => SchoolClass::all()
        ]);
    }

    // 2. Store New Lead
    public function store(Request $request)
    {
        $request->validate([
            'student_name' => 'required|string',
            'father_name' => 'required|string',
            'phone' => 'required|string',
            'class_id' => 'required|exists:school_classes,id',
            'status' => 'required|in:pending,converted,rejected'
        ]);

        AdmissionInquiry::create($request->all());
        return redirect()->back()->with('success', 'Inquiry Saved Successfully!');
    }

    // 3. Convert to Student (The "One-Click" Feature)
    public function promote($id)
    {
        $inquiry = AdmissionInquiry::findOrFail($id);

        if ($inquiry->status === 'converted') {
            return back()->withErrors(['error' => 'Already Converted!']);
        }

        DB::beginTransaction();
        try {
            // A. Create Parent User (Agar nahi hai)
            // Note: Inquiry mein CNIC nahi hota, is liye hum Phone ko temporary key bana rahe hain
            // Asal admission form par CNIC update karna parega.
            $parentUser = User::firstOrCreate(
                ['phone' => $inquiry->phone],
                [
                    'name' => $inquiry->father_name,
                    'email' => $inquiry->phone . '@parent.com',
                    'password' => Hash::make('12345678'),
                    'role' => 'parent',
                    'cnic' => 'TEMP-' . $inquiry->phone // Placeholder until updated
                ]
            );

            if ($parentUser->wasRecentlyCreated) {
                ParentWallet::create(['parent_user_id' => $parentUser->id]);
            }

            // B. Create Student User
            $admissionNo = 'ADM-' . time(); // Auto-generate
            $studentUser = User::create([
                'name' => $inquiry->student_name,
                'email' => $admissionNo . '@student.com',
                'password' => Hash::make('12345678'),
                'role' => 'student',
                'phone' => $inquiry->phone,
            ]);

            // C. Create Student Profile
            Student::create([
                'user_id' => $studentUser->id,
                'parent_id' => $parentUser->id,
                'class_id' => $inquiry->class_id,
                'admission_no' => $admissionNo,
                'full_name' => $inquiry->student_name,
                'father_name' => $inquiry->father_name,
                'gender' => 'Male', // Default, edit later
                'birthday' => now()->subYears(5), // Default, edit later
                'phone' => $inquiry->phone,
                'admission_date' => now(),
            ]);

            // D. Update Inquiry Status
            $inquiry->update(['status' => 'converted']);

            DB::commit();
            return redirect()->route('students.index')->with('success', 'Inquiry Converted to Student!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}