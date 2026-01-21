<!DOCTYPE html>
<html>

<head>
    <style>
        .border {
            border: 5px double #000;
            padding: 50px;
            text-align: center;
            height: 90%
        }
    </style>
</head>

<body>
    <div class="border">
        <h1>{{ $school_name }}</h1>
        <h3>{{ ucfirst($certificate->certificate_type) }} Certificate</h3>
        <br><br>
        <p>This is to certify that <strong>{{ $student->full_name }}</strong> (Adm No: {{ $student->admission_no }})</p>
        <p>has been a student of this school. His/Her conduct was:</p>
        <h3>{{ $certificate->remarks }}</h3>
        <br><br><br>
        <p>____________________<br>Principal Signature</p>
    </div>
</body>

</html>