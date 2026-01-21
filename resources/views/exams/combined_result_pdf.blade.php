<!DOCTYPE html>
<html>

<head>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center
        }

        th {
            background: #eee
        }
    </style>
</head>

<body>
    <h2 style="text-align:center">Combined Progress Report</h2>
    <p><strong>Name:</strong> {{ $student->full_name }} | <strong>Class:</strong>
        {{ $student->schoolClass->class_name }}</p>
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Breakdown</th>
                <th>Total</th>
                <th>%</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $res)
                <tr>
                    <td>{{ $res['subject'] }}</td>
                    <td>
                        @foreach($res['exams_breakdown'] as $break) <small>{{ $break }}</small><br> @endforeach
                    </td>
                    <td>{{ $res['total_obtained'] }} / {{ $res['total_max'] }}</td>
                    <td>{{ round($res['percentage']) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>