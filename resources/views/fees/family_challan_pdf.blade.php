<!DOCTYPE html>
<html>

<head>
    <title>Family Voucher</title>
    <style>
        body {
            font-family: sans-serif;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #eee;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center;">{{ $school_name }}</h2>
    <h4 style="text-align: center;">Family Consolidated Challan - {{ $month }}</h4>

    <p><strong>Parent:</strong> {{ $parent->name }} ({{ $parent->cnic }})</p>
    <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($due_date)->format('d-M-Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Class</th>
                <th>Fee Type</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $inv)
                <tr>
                    <td>{{ $inv->student->full_name }}</td>
                    <td>{{ $inv->student->schoolClass->class_name }}</td>
                    <td>{{ $inv->feeType->fee_title }}</td>
                    <td>{{ number_format($inv->total_amount - $inv->discount_amount) }}</td>
                </tr>
            @endforeach
            <tr style="font-weight: bold; background: #ddd;">
                <td colspan="3">GRAND TOTAL</td>
                <td>Rs. {{ number_format($grandTotal) }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html><!DOCTYPE html>
<html>

<head>
    <title>Family Voucher</title>
    <style>
        body {
            font-family: sans-serif;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #eee;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>{{ $school_name }}</h2>
        <h4>Family Consolidated Challan - {{ $month }}</h4>
        <p><strong>Parent:</strong> {{ $parent->name }} (CNIC: {{ $parent->cnic }})</p>
        <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($due_date)->format('d-M-Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Class</th>
                <th>Fee Type</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $inv)
                <tr>
                    <td style="text-align: left;">{{ $inv->student->full_name }}</td>
                    <td>{{ $inv->student->schoolClass->class_name }}</td>
                    <td>{{ $inv->feeType->fee_title }}</td>
                    <td>{{ number_format($inv->total_amount - $inv->discount_amount) }}</td>
                </tr>
            @endforeach
            <tr style="font-weight: bold; background: #ddd;">
                <td colspan="3" style="text-align: right;">GRAND TOTAL</td>
                <td>Rs. {{ number_format($grandTotal) }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 40px; text-align: center;">
        <p>This is a computer-generated document. No signature required.</p>
    </div>
</body>

</html>