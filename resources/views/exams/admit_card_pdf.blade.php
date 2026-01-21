<!DOCTYPE html>
<html>

<head>
    <style>
        .slip {
            border: 2px solid #333;
            padding: 20px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .title {
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
        }
    </style>
</head>

<body>
    @foreach($students as $student)
        <div class="slip">
            <h2 style="text-align: center; margin: 0;">{{ $school_name }}</h2>
            <p class="title">ROLL NUMBER SLIP - {{ $exam->exam_title }}</p>

            <table width="100%">
                <tr>
                    <td><strong>Name:</strong> {{ $student->full_name }}</td>
                    <td><strong>Roll No:</strong> {{ $student->roll_no }}</td>
                </tr>
                <tr>
                    <td><strong>Class:</strong> {{ $student->schoolClass->class_name }}</td>
                    <td><strong>Father:</strong> {{ $student->father_name }}</td>
                </tr>
            </table>
            <br>
            <p><strong>Instructions:</strong> Please bring this slip daily during exams.</p>
        </div>
    @endforeach
</body>

</html>