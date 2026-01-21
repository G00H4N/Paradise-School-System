<!DOCTYPE html>
<html>

<head>
    <style>
        .card {
            width: 320px;
            height: 190px;
            border: 2px solid #000;
            float: left;
            margin: 10px;
            padding: 10px;
            border-radius: 10px;
            page-break-inside: avoid;
        }

        .header {
            background: #1e3a8a;
            color: #fff;
            padding: 5px;
            text-align: center;
            border-radius: 5px 5px 0 0;
            font-weight: bold;
        }

        .photo {
            width: 70px;
            height: 80px;
            border: 1px solid #333;
            float: left;
            margin-top: 10px;
            background: #eee;
        }

        .details {
            float: right;
            width: 210px;
            font-size: 13px;
            margin-top: 10px;
            line-height: 1.4;
        }

        .footer {
            clear: both;
            text-align: center;
            font-size: 10px;
            margin-top: 10px;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    @foreach($students as $student)
        <div class="card">
            <div class="header">{{ $school_name }}</div>
            <div class="photo">
                @if($student->profile_photo_path)
                    <img src="{{ public_path('storage/' . $student->profile_photo_path) }}" style="width:100%; height:100%;">
                @else
                    <div style="text-align:center; padding-top:30px;">No Pic</div>
                @endif
            </div>
            <div class="details">
                <b>Name:</b> {{ $student->full_name }}<br>
                <b>Father:</b> {{ $student->father_name }}<br>
                <b>Class:</b> {{ $student->schoolClass->class_name }}<br>
                <b>Adm No:</b> {{ $student->admission_no }}<br>
                <b>Route:</b> {{ $student->transport ? $student->transport->route_title : 'Self' }}
            </div>
            <div class="footer">
                <span>Valid for Session: {{ $session }}</span>
            </div>
        </div>
    @endforeach
</body>

</html>