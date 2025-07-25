<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt</title>
    <style>
        @page {
            size: landscape;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh; /* Ensure everything fits within the screen */
        }
        .receipt-container {
            width: 90%;
            max-width: 800px;
            border: 1px solid #000;
            padding: 10px;
            font-size: 14px; /* Reduce font size for compactness */
        }
        .school-info {
            text-align: center;
            margin-bottom: 5px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px; /* Reduce font size for better fit */
        }
        .table td, .table th {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
            vertical-align: middle;
        }
        .bold {
            font-weight: bold;
        }
        .receipt-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 16px; /* Adjust heading size */
        }
        .original-copy {
            font-size: 12px;
            font-weight: bold;
            color: #555;
        }
        .stamp {
            text-align: right;
            margin-top: 10px;
            font-size: 12px; /* Reduce size */
        }
        
        /* ✅ Print Button - Right Aligned */
        .print-btn-container {
            text-align: right;
            margin-top: 5px;
            width: 100%;
        }
        .print_invoice_btn {
            display: inline-flex;
            align-items: center;
            background-color: #0096FF; /* Bright blue */
            color: #fff;
            border: none;
            padding: 8px 15px;
            font-size: 14px; /* Smaller button */
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease-in-out;
            cursor: pointer;
        }
        .print_invoice_btn span {
            display: flex;
            align-items: center;
        }
        .print_invoice_btn svg {
            margin-right: 6px;
            fill: #fff;
        }
        .print_invoice_btn:hover {
            background-color: #007BFF;
        }
    </style>
</head>
<body>

    <div class="receipt-container" id="printableDiv">
        <div class="school-info">
            @if($school_logo)
                <img src="{{ $school_logo }}" alt="School Logo" style="max-width: 80px; display:block; margin:auto;">
            @endif
            <h2 style="font-size: 16px; margin: 5px 0;">{{ $school_name }}</h2>
            <p style="font-size: 12px; margin: 2px 0;">{{ $school_address }}</p>
            <p style="font-size: 12px; margin: 2px 0;"><b>Phone:</b> {{ $school_phone }} | <b>Email:</b> {{ $school_email }}</p>
        </div>

        <!-- ✅ Receipt Title -->
        <div class="receipt-title">
            <h3 style="font-size: 16px;">FEE RECEIPT ( Original Copy )</h3>
        </div>

        <!-- ✅ Student Info -->
        @if(!empty($student_details) && is_array($student_details) && isset($student_details['user_id']))
            <table class="table">
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Class-Section</th>
                    <th>Parent Name</th>
                    <th>Receipt No</th>
                    <th>Receipt Date</th>
                </tr>
                <tr>
                    <td>{{ $student_details['user_id'] ?? 'N/A' }}</td>
                    <td>{{ $student_details['name'] ?? 'N/A' }}</td>
                    <td>{{ $student_details['class_name'] ?? '' }} - {{ $student_details['section_name'] ?? '' }}</td>
                    <td>{{ $student_details['parent_name'] ?? 'N/A' }}</td>
                    <td>{{ $invoice_details['id'] ?? 'N/A' }}</td>
                    <td>{{ date('d/m/Y') }}</td>
                </tr>
            </table>
        @else
            <div class="alert alert-warning" style="font-size: 14px; margin: 10px 0;">
                Student details are missing or incomplete. This receipt will not show full student information.
            </div>
        @endif

        <!-- ✅ Installment History -->
        @php
            use App\Models\FeeInstallment;
            $installments = FeeInstallment::where('invoice_id', $invoice_details['id'])->orderBy('paid_at')->get();
        @endphp

        @if($installments->count() > 0)
            <h4 style="font-size: 14px; margin: 5px 0;">Installment History</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Installment #</th>
                        <th>Date</th>
                        <th>Amount Paid</th>
                        <th>Payment Method</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($installments as $index => $inst)
                        <tr>
                            <td>{{ ordinal($index + 1) }} Installment</td>
                            <td>{{ date('d-M-Y', strtotime($inst->paid_at)) }}</td>
                            <td>{{ school_currency($inst->amount_paid) }}</td>
                            <td>{{ ucfirst($inst->payment_method) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- ✅ Amount in Words -->
        <p style="font-size: 14px; margin: 5px 0;"><strong>Amount in Words:</strong> 
            {{ \App\Helpers\NumberToWordsHelper::convertNumberToWords($invoice_details['paid_amount']) }} Rupees Only
        </p>

        <br />

        <!-- ✅ Footer -->
        <div class="stamp">
            <p>Authorized Signature & Stamp</p>
            <p class="text-center"><b>"This is a computer-generated receipt"</b></p>
        </div>
    </div>

    <!-- ✅ Print Button -->
    <div class="print-btn-container">
        <a href="javascript:0" onclick="printableDiv('printableDiv')" class="print_invoice_btn" id="printPageButton">
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16.202 16.202">
                    <path id="print-solid"
                        d="M14.176,6.076H2.025A2.026,2.026,0,0,0,0,8.1v3.038a1.013,1.013,0,0,0,1.013,1.013H2.025v3.038A1.013,1.013,0,0,0,3.038,16.2H13.164a1.013,1.013,0,0,0,1.013-1.013V12.151h1.013A1.013,1.013,0,0,0,16.2,11.139V8.1A2.027,2.027,0,0,0,14.176,6.076Zm-2.025,8.1H4.05V11.139h8.1Z"
                        fill="#fff" />
                </svg>
            </span>
            <span>Print Receipt</span>
        </a>
    </div>

    <!-- ✅ Print Script -->
    <script type="text/javascript">
        "use strict";
        function printableDiv(printableAreaDivId) {
            var printContents = document.getElementById(printableAreaDivId).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>

</body>

</html>
