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
            font-size: 18px;
        }

        table {
            width: 100%;
            margin-top: 10px;
        }

        td {
            padding: 5px;
        }
    </style>
</head>

<body>
    @foreach($students as $student)
        <div class="slip">
            <h2 style="text-align: center; margin: 0;">{{ $school_name }}</h2>
            <p class="title">ROLL NUMBER SLIP - {{ $exam->exam_title }}</p>

            <table>
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
            <p><strong>Note:</strong> Students must bring this slip to the examination hall.</p>
            <br><br>
            <div style="text-align: right;">____________________<br>Principal</div>
        </div>
    @endforeach
</body>

</html>