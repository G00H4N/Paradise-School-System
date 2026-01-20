<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Result Card - {{ $student->full_name }}</title>
    <style>
        body {
            font-family: sans-serif;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .school-name {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1a202c;
        }

        .exam-tag {
            background: #eee;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
            display: inline-block;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 5px;
            font-size: 14px;
        }

        .label {
            font-weight: bold;
            width: 130px;
        }

        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .marks-table th,
        .marks-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
            font-size: 13px;
        }

        .marks-table th {
            background-color: #f8f9fa;
        }

        .subject-col {
            text-align: left;
            padding-left: 10px;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            width: 100%;
            text-align: center;
        }

        .signature {
            display: inline-block;
            width: 30%;
            border-top: 1px solid #000;
            padding-top: 5px;
            font-size: 12px;
        }

        /* Graph & Summary Layout */
        .row {
            width: 100%;
            overflow: hidden;
            margin-top: 20px;
        }

        .col-graph {
            float: left;
            width: 60%;
        }

        .col-summary {
            float: right;
            width: 35%;
        }

        .summary-box {
            border: 2px solid #444;
            padding: 10px;
            border-radius: 5px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dashed #ccc;
            padding: 5px 0;
        }

        .pass {
            color: green;
            font-weight: bold;
        }

        .fail {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="school-name">{{ $school_name }}</div>
        <div>School Management System Generated Report</div>
        <div class="exam-tag">{{ $exam->exam_title }} ({{ $exam->session_year }})</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Student Name:</td>
            <td>{{ $student->full_name }}</td>
            <td class="label">Admission No:</td>
            <td>{{ $student->admission_no }}</td>
        </tr>
        <tr>
            <td class="label">Father Name:</td>
            <td>{{ $student->father_name }}</td>
            <td class="label">Class:</td>
            <td>{{ $student->schoolClass->class_name }}</td>
        </tr>
        <tr>
            <td class="label">Roll No:</td>
            <td>{{ $student->roll_no }}</td>
            <td class="label">Date:</td>
            <td>{{ now()->format('d M, Y') }}</td>
        </tr>
    </table>

    <table class="marks-table">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Total Marks</th>
                <th>Obtained</th>
                <th>Grade</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($marks as $mark)
                <tr>
                    <td class="subject-col">{{ $mark->subject->subject_name }}</td>
                    <td>{{ $mark->total_marks }}</td>
                    <td>{{ $mark->marks_obtained }}</td>
                    <td>{{ $mark->grade }}</td>
                    <td>{{ $mark->teacher_comment ?? '-' }}</td>
                </tr>
            @endforeach
            <tr style="background: #eee; font-weight: bold;">
                <td class="subject-col">GRAND TOTAL</td>
                <td>{{ $grandTotalMax }}</td>
                <td>{{ $grandTotalObtained }}</td>
                <td>{{ $finalGrade }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="row">
        <div class="col-graph">
            @php
                // Graph Generation Logic (Specification Requirement)
                $labels = implode("','", $marks->pluck('subject.subject_name')->toArray());
                $data = implode(",", $marks->pluck('marks_obtained')->toArray());
                $chartUrl = "https://quickchart.io/chart?c={type:'bar',data:{labels:['$labels'],datasets:[{label:'Marks',backgroundColor:'rgba(54, 162, 235, 0.5)',data:[$data]}]}}&width=300&height=180";
            @endphp
            <div style="text-align: center;">
                <strong>Performance Chart</strong><br>
                <img src="{{ $chartUrl }}" style="width: 100%; margin-top: 10px;">
            </div>
        </div>

        <div class="col-summary">
            <div class="summary-box">
                <div style="text-align: center; font-weight: bold; margin-bottom: 10px; text-decoration: underline;">
                    RESULT SUMMARY</div>

                <div class="summary-item">
                    <span>Percentage:</span> <span>{{ $percentage }}%</span>
                </div>
                <div class="summary-item">
                    <span>Class Position:</span> <span>{{ $position }}</span>
                </div>
                <div class="summary-item">
                    <span>Attendance:</span> <span>{{ $attendance }}%</span>
                </div>
                <div class="summary-item" style="border: none;">
                    <span>Status:</span>
                    <span class="{{ $percentage >= 40 ? 'pass' : 'fail' }}">
                        {{ $percentage >= 40 ? 'PASS' : 'FAIL' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="signature">Class Teacher</div>
        <div class="signature">Principal</div>
        <div class="signature">Parent</div>
    </div>

</body>

</html>