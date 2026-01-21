<!DOCTYPE html>
<html>

<head>
    <style>
        .card {
            width: 320px;
            height: 200px;
            border: 1px solid #000;
            float: left;
            margin: 10px;
            padding: 10px;
            border-radius: 8px;
        }

        .header {
            background: #0d47a1;
            color: #fff;
            padding: 5px;
            text-align: center;
            border-radius: 5px;
        }

        .photo {
            width: 60px;
            height: 70px;
            border: 1px solid #333;
            float: left;
            margin-top: 10px;
        }

        .details {
            float: right;
            width: 220px;
            font-size: 12px;
            margin-top: 10px;
        }

        .footer {
            clear: both;
            text-align: center;
            font-size: 10px;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    @foreach($students as $student)
        <div class="card">
            <div class="header"><strong>{{ $school_name }}</strong></div>
            <div class="photo">
                <img src="{{ public_path('storage/' . $student->profile_photo_path) }}" style="width:100%; height:100%;"
                    onerror="this.style.display='none'">
            </div>
            <div class="details">
                <b>Name:</b> {{ $student->full_name }}<br>
                <b>F.Name:</b> {{ $student->father_name }}<br>
                <b>Class:</b> {{ $student->schoolClass->class_name }}<br>
                <b>Adm No:</b> {{ $student->admission_no }}<br>
                <b>Route:</b> {{ $student->transport ? $student->transport->route_title : 'Self' }}
            </div>
            <div class="footer">
                <p>Session: {{ $session }} | Emergency: 0300-1234567</p>
                {{-- Barcode Logic (Optional): <img
                    src="data:image/png;base64,{{ DNS1D::getBarcodePNG($student->admission_no, 'C39') }}" /> --}}
            </div>
        </div>
    @endforeach
</body>

</html>