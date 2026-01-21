<!DOCTYPE html>
<html>

<head>
    <title>Fee Challan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .container {
            width: 100%;
            display: flex;
        }

        .copy {
            width: 32%;
            float: left;
            border: 1px dashed #000;
            padding: 10px;
            margin-right: 1%;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .total {
            font-weight: bold;
            border-top: 1px solid #000;
            margin-top: 5px;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    @foreach(['BANK COPY', 'SCHOOL COPY', 'STUDENT COPY'] as $type)
        <div class="copy">
            <div class="header">
                <h3>{{ $school_name }}</h3>
                <p>{{ $type }}</p>
            </div>
            <div class="row"><strong>Challan No:</strong> {{ $invoice->id }}</div>
            <div class="row"><strong>Due Date:</strong> {{ $due_date_formatted }}</div>
            <div class="row"><strong>Student:</strong> {{ $student->full_name }}</div>
            <div class="row"><strong>Adm No:</strong> {{ $student->admission_no }}</div>
            <div class="row"><strong>Class:</strong> {{ $class->class_name }}</div>
            <hr>
            <div class="row">
                <span>{{ $invoice->feeType->fee_title }}</span>
                <span>Rs. {{ number_format($invoice->total_amount) }}</span>
            </div>
            @if($invoice->discount_amount > 0)
                <div class="row">
                    <span>Discount</span>
                    <span>-{{ number_format($invoice->discount_amount) }}</span>
                </div>
            @endif
            <div class="row total">
                <span>Total Payable:</span>
                <span>Rs. {{ number_format($grandTotal) }}</span>
            </div>
            <br><br>
            <div style="text-align: center;">_________________<br>Cashier Signature</div>
        </div>
    @endforeach
</body>

</html>