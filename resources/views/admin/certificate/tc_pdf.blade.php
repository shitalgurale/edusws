<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Transfer Certificate</title>
    <style>
        @if($is_pdf)
        @page {
            margin: 10px;
            size: A4;
        }
        body {
            font-family: "Times New Roman", serif;
            font-size: 16px;
            line-height: 1.6;
            color: #000;
            padding: 20px;
        }
        @else
        body {
            font-family: "Times New Roman", serif;
            font-size: 16px;
            line-height: 1.6;
            color: #000;
            margin: 20px;
        }
        @endif

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .logo {
            width: 80px;
            height: auto;
        }
        .school-info {
            flex-grow: 1;
            text-align: center;
            margin-left: -80px;
        }
        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        td, th {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header">
       
    @if($is_pdf)
        @if($school_logo && file_exists($school_logo))
            <div style="text-align: center; width: 100%;">
                <img src="file://{{ $school_logo }}" class="logo" alt="Logo">
            </div>
        @endif
    @else
    
    
        @if($school_logo)
            <div style="text-align: left;">
                <img src="{{ asset('assets/uploads/school_logo/' . basename($school_logo)) }}" class="logo" alt="Logo">
            </div>
        @endif
    @endif
    

        <div class="school-info">
            <div style="font-size: 18px; font-weight: bold;">{{ $school_name }}</div>
            <div class="subtext">School Code:0000{{ $school_id ?? 'N/A' }}</div>
            <div>{{ $school_address ?? 'N/A' }}</div>
            <div>Phone: {{ $school_phone ?? 'N/A' }} | Email: {{ $school_email ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="title">TRANSFER CERTIFICATE</div>

    <table>
        <tr><th>Student Name</th><td>{{ $student_name ?? 'N/A' }}</td></tr>
        <tr><th>Gender</th><td>{{ $gender ?? 'N/A' }}</td></tr>
        <tr><th>Date of Birth</th><td>{{ $dob ?? 'N/A' }}</td></tr>
        <tr><th>DOB in Words</th><td>{{ $dob_words ?? 'N/A' }}</td></tr>
        <tr><th>Father's Name</th><td>{{ $father_name ?? 'N/A' }}</td></tr>
        <tr><th>Mother's Name</th><td>{{ $mother_name ?? 'N/A' }}</td></tr>
        <tr><th>Nationality</th><td>{{ $nationality ?? 'N/A' }}</td></tr>
        <tr><th>Caste</th><td>{{ $caste ?? 'N/A' }}</td></tr>
        <tr><th>Phone Number</th><td>{{ $phone ?? 'N/A' }}</td></tr>
        <tr><th>Address</th><td>{{ $address ?? 'N/A' }}</td></tr>
        <tr><th>Blood Group</th><td>{{ $blood_group ?? 'N/A' }}</td></tr>
        <tr><th>Class</th><td>{{ $class ?? 'N/A' }}</td></tr>
        <tr><th>Section</th><td>{{ $section ?? 'N/A' }}</td></tr>
        <tr><th>Session</th><td>{{ $session ?? 'N/A' }}</td></tr>
        <tr><th>Admission No (Ref No)</th><td>{{ $admission_no ?? 'N/A' }}</td></tr>
        <tr><th>Student ID</th><td>{{ $student_id ?? 'N/A' }}</td></tr>
        <tr><th>Biometric ID</th><td>{{ $stu_bioid ?? 'N/A' }}</td></tr>
        <tr><th>Total Working Days</th><td>{{ $working_days ?? 'N/A' }}</td></tr>
        <tr><th>Days Present</th><td>{{ $present_days ?? 'N/A' }}</td></tr>
        <tr><th>Issue Date</th><td>{{ $issue_date ?? 'N/A' }}</td></tr>
    </table>

    <div class="footer">PRINCIPAL</div>

</body>
</html>