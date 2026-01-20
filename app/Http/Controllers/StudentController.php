<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\ParentWallet;
use App\Models\SchoolClass;
use App\Models\TransportRoute; // Added Model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;

class StudentController extends Controller
{
    public function create()
    {
        return inertia('Students/Create', [
            'classes' => SchoolClass::all(),
            'routes' => TransportRoute::all() // Spec Module 6
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validation (Added Transport Validation)
        $request->validate([
            'full_name' => 'required|string',
            'admission_no' => 'required|unique:students,admission_no',
            'class_id' => 'required|exists:school_classes,id',
            'gender' => 'required|in:Male,Female',
            'birthday' => 'required|date',
            'father_name' => 'required|string',
            'father_cnic' => 'required|string', // Critical for Family Link
            'father_phone' => 'required|string',
            'transport_route_id' => 'nullable|exists:transport_routes,id', // Module 6
        ]);

        DB::beginTransaction();
        try {
            // STEP A: PARENT HANDLING (Family Logic)
            $parentUser = User::firstOrCreate(
                ['cnic' => $request->father_cnic],
                [
                    'name' => $request->father_name,
                    'email' => $request->father_cnic . '@parent.com', // Unique fake email
                    'password' => Hash::make('12345678'),
                    'role' => 'parent',
                    'phone' => $request->father_phone,
                ]
            );

            // Create Wallet if new parent
            if ($parentUser->wasRecentlyCreated) {
                ParentWallet::create(['parent_user_id' => $parentUser->id]);
            }

            // STEP B: STUDENT ACCOUNT
            $studentUser = User::create([
                'name' => $request->full_name,
                'email' => $request->admission_no . '@student.com',
                'password' => Hash::make('12345678'),
                'role' => 'student',
                'phone' => $request->father_phone,
            ]);

            // STEP C: PHOTO HANDLING
            $photoPath = null;
            if ($request->student_photo) {
                // Logic already correct in your code
                $image_parts = explode(";base64,", $request->student_photo);
                if (count($image_parts) > 1) {
                    $image_base64 = base64_decode($image_parts[1]);
                    $fileName = 'student_' . time() . '.png';
                    $photoPath = 'students/' . $fileName;
                    Storage::disk('public')->put($photoPath, $image_base64);
                }
            }

            // STEP D: SAVE PROFILE
            Student::create([
                'user_id' => $studentUser->id,
                'parent_id' => $parentUser->id,
                'class_id' => $request->class_id,
                'admission_no' => $request->admission_no,
                'roll_no' => $request->roll_no,
                'full_name' => $request->full_name,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'caste' => $request->caste,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'religion' => $request->religion ?? 'Islam',
                'blood_group' => $request->blood_group,
                'address' => $request->address,
                'phone' => $request->father_phone,
                'admission_date' => now(),
                'profile_photo_path' => $photoPath,
                'transport_route_id' => $request->transport_route_id,
            ]);

            DB::commit();
            return redirect()->route('students.index')->with('success', 'Admission Successful!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function index(Request $request)
    {
        $query = Student::with(['schoolClass', 'user', 'transport']); // Eager Load Transport

        if ($request->search) {
            $query->where('full_name', 'like', "%{$request->search}%")
                ->orWhere('admission_no', 'like', "%{$request->search}%");
        }

        return inertia('Students/Index', [
            'students' => $query->latest()->paginate(10),
            'classes' => SchoolClass::all()
        ]);
    }

    public function import(Request $request)
    {
        $request->validate(['excel_file' => 'required|mimes:xlsx,xls,csv|max:2048']);
        try {
            Excel::import(new StudentsImport, $request->file('excel_file'));
            return redirect()->route('students.index')->with('success', 'Bulk Admission Complete!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Import Failed: ' . $e->getMessage()]);
        }
    }
    // --- NEW ADDITION: Student Promotion (Next Class mein bhejna) ---
    public function promoteStudents(Request $request)
    {
        $request->validate([
            'from_class_id' => 'required|exists:school_classes,id',
            'to_class_id' => 'required|exists:school_classes,id',
            'student_ids' => 'required|array' // Checkbox selection
        ]);

        if ($request->from_class_id == $request->to_class_id) {
            return back()->withErrors(['error' => 'Source and Target class cannot be same!']);
        }

        DB::beginTransaction();
        try {
            Student::whereIn('id', $request->student_ids)
                ->where('class_id', $request->from_class_id)
                ->update([
                    'class_id' => $request->to_class_id,
                    // Optional: Roll No reset logic can go here
                ]);

            DB::commit();
            return redirect()->back()->with('success', 'Students Promoted Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // --- NEW ADDITION: Bulk ID Card Generation ---
    public function generateIdCards(Request $request)
    {
        $request->validate(['class_id' => 'required']);

        $students = Student::where('class_id', $request->class_id)
            ->with('transport') // Bus Route on ID Card
            ->get();

        $data = [
            'students' => $students,
            'school_name' => 'Paradise Public Girls Elementary School',
            'session' => '2026-2027'
        ];

        $pdf = Pdf::loadView('students.id_card_pdf', $data);
        return $pdf->stream("IDCards.pdf");
    }
}