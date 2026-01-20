<?php

namespace App\Http\Controllers;

use App\Models\GeneratedCertificate;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function index()
    {
        return inertia('Academic/Certificates', [
            'history' => GeneratedCertificate::with('student')->latest()->get()
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'certificate_type' => 'required|in:character,leaving',
            'remarks' => 'nullable|string'
        ]);

        $student = Student::with('schoolClass')->findOrFail($request->student_id);
        $serial = 'CERT-' . strtoupper($request->certificate_type) . '-' . time();

        // 1. Save Record
        $cert = GeneratedCertificate::create([
            'student_id' => $student->id,
            'certificate_type' => $request->certificate_type,
            'serial_number' => $serial,
            'issue_date' => now(),
            'remarks' => $request->remarks ?? 'Satisfactory',
            'issued_by' => auth()->id()
        ]);

        // 2. Generate PDF
        $data = [
            'certificate' => $cert,
            'student' => $student,
            'school_name' => 'Paradise Public Girls Elementary School'
        ];

        $viewName = $request->certificate_type === 'leaving'
            ? 'certificates.leaving_pdf'
            : 'certificates.character_pdf';

        $pdf = Pdf::loadView($viewName, $data)->setPaper('a4', 'landscape');
        return $pdf->stream($serial . '.pdf');
    }
}