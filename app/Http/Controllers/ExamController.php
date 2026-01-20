<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Mark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // PDF Library Import

class ExamController extends Controller
{
    // 1. Exam Dashboard & Creation
    public function index()
    {
        return inertia('Exams/Index', [
            'exams' => Exam::orderBy('start_date', 'desc')->get(),
            'classes' => SchoolClass::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'exam_title' => 'required|string',
            'start_date' => 'required|date',
            'session_year' => 'required|string' // e.g. 2026-2027
        ]);

        Exam::create($request->all());
        return redirect()->back()->with('success', 'Exam Created Successfully!');
    }

    // 2. Marks Entry Page (Teacher View)
    public function marksEntry(Request $request)
    {
        // Teacher Class aur Subject select karega
        $students = [];

        // Agar filters selected hain, to students fetch karo
        if ($request->class_id && $request->subject_id) {
            $students = Student::where('class_id', $request->class_id)
                ->where('is_active', true)
                ->orderBy('roll_no')
                ->get()
                ->map(function ($student) use ($request) {
                    // Check karo agar marks pehle se lagay huay hain
                    $mark = Mark::where('student_id', $student->id)
                        ->where('exam_id', $request->exam_id)
                        ->where('subject_id', $request->subject_id)
                        ->first();

                    // Frontend ko data bhejo
                    $student->obtained = $mark ? $mark->marks_obtained : '';
                    $student->comment = $mark ? $mark->teacher_comment : '';
                    return $student;
                });
        }

        return inertia('Exams/MarksEntry', [
            'exams' => Exam::all(),
            'classes' => SchoolClass::all(),
            'subjects' => Subject::when($request->class_id, function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            })->get(),
            'students' => $students,
            'filters' => $request->all()
        ]);
    }

    // 3. Save Marks Logic (Bulk Save)
    public function saveMarks(Request $request)
    {
        $request->validate([
            'exam_id' => 'required',
            'subject_id' => 'required',
            'marks_data' => 'required|array' // [{student_id: 1, obtained: 45}, ...]
        ]);

        $subject = Subject::find($request->subject_id);

        DB::beginTransaction();
        try {
            foreach ($request->marks_data as $data) {
                // Grade Calculate karo
                $percentage = ($data['obtained'] / $subject->total_marks) * 100;
                $grade = $this->calculateGrade($percentage);

                // Update or Create Mark
                Mark::updateOrCreate(
                    [
                        'student_id' => $data['student_id'],
                        'exam_id' => $request->exam_id,
                        'subject_id' => $request->subject_id,
                    ],
                    [
                        'marks_obtained' => $data['obtained'],
                        'total_marks' => $subject->total_marks,
                        'grade' => $grade,
                        'teacher_comment' => $data['comment'] ?? null,
                    ]
                );
            }
            DB::commit();
            return redirect()->back()->with('success', 'Marks Saved Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // 4. Generate Result Card (PDF)
    // Yeh function Specification Module 5 ko cover karta hai
    public function generateResultCard($exam_id, $student_id)
    {
        $exam = Exam::findOrFail($exam_id);
        $student = Student::with('schoolClass')->findOrFail($student_id);

        // A. Is exam mein is bache ke saare marks nikalo
        $marks = Mark::with('subject')
            ->where('exam_id', $exam_id)
            ->where('student_id', $student_id)
            ->get();

        if ($marks->isEmpty()) {
            return back()->withErrors(['error' => 'No marks found for this student.']);
        }

        // B. Totals Calculate karo
        $grandTotalObtained = $marks->sum('marks_obtained');
        $grandTotalMax = $marks->sum('total_marks');
        $percentage = ($grandTotalMax > 0) ? ($grandTotalObtained / $grandTotalMax) * 100 : 0;
        $finalGrade = $this->calculateGrade($percentage);

        // C. Position Calculation Logic (Class Rank)
        // Step 1: Class ke tamam bachon ke marks sum karo
        $classStandings = Mark::select('student_id', DB::raw('SUM(marks_obtained) as total'))
            ->where('exam_id', $exam_id)
            ->whereHas('student', function ($q) use ($student) {
                $q->where('class_id', $student->class_id);
            })
            ->groupBy('student_id')
            ->orderByDesc('total') // Highest marks upar
            ->get();

        // Step 2: Apne bache ka index dhoondo
        $position = $classStandings->search(function ($item) use ($student_id) {
            return $item->student_id == $student_id;
        });
        // Index 0 se start hota hai is liye +1 kiya
        $position = ($position !== false) ? $position + 1 : '-';

        // D. Attendance Summary (Video Requirement)
        // Phase 7 mein jo Model update kiya tha, wo yahan use ho raha hai
        $totalDays = \App\Models\Attendance::where('student_id', $student_id)->count();
        $presentDays = \App\Models\Attendance::where('student_id', $student_id)->where('status', 1)->count();
        $attendancePerc = ($totalDays > 0) ? round(($presentDays / $totalDays) * 100) : 0;

        // Data for PDF View
        $data = [
            'exam' => $exam,
            'student' => $student,
            'marks' => $marks,
            'grandTotalObtained' => $grandTotalObtained,
            'grandTotalMax' => $grandTotalMax,
            'percentage' => round($percentage, 2),
            'finalGrade' => $finalGrade,
            'position' => $position, // Class Rank
            'totalStudents' => $classStandings->count(),
            'attendance' => $attendancePerc,
            'school_name' => 'Paradise Public Girls Elementary School',
        ];

        $pdf = Pdf::loadView('exams.result_card_pdf', $data);
        return $pdf->stream("Result-{$student->roll_no}.pdf");
    }

    // Helper: Grade Calculation Formula
    private function calculateGrade($percentage)
    {
        if ($percentage >= 90)
            return 'A+';
        if ($percentage >= 80)
            return 'A';
        if ($percentage >= 70)
            return 'B';
        if ($percentage >= 60)
            return 'C';
        if ($percentage >= 50)
            return 'D';
        return 'F';
    }
    // --- NEW ADDITION: Tabulation Sheet (Poori Class ka Result) ---
    public function tabulationSheet(Request $request)
    {
        $request->validate([
            'exam_id' => 'required',
            'class_id' => 'required'
        ]);

        $exam = Exam::findOrFail($request->exam_id);
        $class = SchoolClass::findOrFail($request->class_id);
        $subjects = Subject::where('class_id', $class->id)->get();

        // Sab students aur unke marks fetch karein
        $students = Student::where('class_id', $class->id)
            ->with([
                'marks' => function ($q) use ($exam) {
                    $q->where('exam_id', $exam->id);
                }
            ])
            ->orderBy('roll_no')
            ->get();

        $data = [
            'exam' => $exam,
            'class' => $class,
            'subjects' => $subjects,
            'students' => $students,
            'school_name' => 'Paradise Public Girls Elementary School',
        ];

        // PDF Landscape mode mein hona chahiye kyunke columns zyada honge
        $pdf = Pdf::loadView('exams.tabulation_pdf', $data)->setPaper('a4', 'landscape');
        return $pdf->stream("Tabulation-{$class->class_name}.pdf");
    }

    // --- NEW ADDITION: Bulk Admit Cards (Roll No Slips) ---
    public function generateAdmitCards(Request $request)
    {
        $request->validate(['exam_id' => 'required', 'class_id' => 'required']);

        $exam = Exam::findOrFail($request->exam_id);
        $students = Student::where('class_id', $request->class_id)
            ->where('is_active', true)
            ->orderBy('roll_no')
            ->get();

        $data = [
            'exam' => $exam,
            'students' => $students,
            'school_name' => 'Paradise Public Girls Elementary School',
        ];

        // A4 page par 2-3 cards print honge
        $pdf = Pdf::loadView('exams.admit_card_pdf', $data);
        return $pdf->stream("AdmitCards-{$request->class_id}.pdf");
    }
    // --- NEW: Combined Result Logic (Mid + Final) ---
    public function combinedResult(Request $request)
    {
        $request->validate([
            'exam_ids' => 'required|array', // e.g. [1, 2] (Mid ID, Final ID)
            'student_id' => 'required'
        ]);

        $student = Student::findOrFail($request->student_id);

        // Dono exams ke marks fetch karein
        $marks = \App\Models\Mark::with(['subject', 'exam'])
            ->whereIn('exam_id', $request->exam_ids)
            ->where('student_id', $student->id)
            ->get()
            ->groupBy('subject_id');

        // Logic: Marks ko merge karna
        $combined = [];
        foreach ($marks as $subjectId => $subjectMarks) {
            $totalObtained = $subjectMarks->sum('marks_obtained');
            $totalMax = $subjectMarks->sum('total_marks');

            $combined[] = [
                'subject' => $subjectMarks->first()->subject->subject_name,
                'exams_breakdown' => $subjectMarks->map(fn($m) => $m->exam->exam_title . ': ' . $m->marks_obtained),
                'total_obtained' => $totalObtained,
                'total_max' => $totalMax,
                'percentage' => ($totalMax > 0) ? ($totalObtained / $totalMax) * 100 : 0
            ];
        }

        // PDF Generation
        $pdf = Pdf::loadView('exams.combined_result_pdf', [
            'student' => $student,
            'results' => $combined
        ]);
        return $pdf->stream('CombinedResult.pdf');
    }
}