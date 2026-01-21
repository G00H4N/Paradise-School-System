<!DOCTYPE html>
<html>

<head>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center;">Tabulation Sheet - {{ $class->class_name }}</h2>
    <h4 style="text-align: center;">{{ $exam->exam_title }}</h4>

    <table>
        <thead>
            <tr>
                <th>Roll No</th>
                <th>Name</th>
                @foreach($subjects as $sub)
                    <th>{{ $sub->subject_name }}</th>
                @endforeach
                <th>Total</th>
                <th>%</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $std)
                        @php 
                                        $total = $std->marks->sum('marks_obtained');
                            $max = $std->marks->sum('total_marks');
                            $perc = $max > 0 ? round(($total / $max) * 100, 1) : 0;
                        @endphp
                    <tr>
                        <td>{{ $std->roll_no }}</td>
                            <td>{{ $std->full_name }}</td>
                            @foreach($subjects as $sub)
                                @php $m = $std->marks->where('subject_id', $sub->id)->first(); @endphp
                                <td>{{ $m ? $m->marks_obtained : '-' }}</td>
                            @endforeach
                            <td>{{ $total }}</td>
                            <td>{{ $perc }}%</td>
                            <td>{{ $perc >= 40 ? 'Pass' : 'Fail' }}</td>

                 </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>