<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bonafide Certificate</title>
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

        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .italic { font-style: italic; }
        .underline { text-decoration: underline; }
        .maroon { color: #800000; }

        .school-header { font-size: 24px; margin-bottom: 5px; }
        .subtext { font-size: 15px; }

        .meta {
            margin-top: 20px;
            margin-bottom: 30px;
        }

        .footer {
            margin-top: 80px;
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
    <!-- Certificate Title -->
    <div class="center">
        <h3 class="underline bold">BONAFIDE CERTIFICATE</h3>
    </div>

    <!-- Main Content -->
    <p style="text-align: justify;">
        This is to certify that <span class="italic bold">{{ $gender_title ?? 'Miss/Master' }} {{ $student_name }}</span>,
        D/o <span class="bold">{{ $father_name }}</span> & <span class="bold">{{ $mother_name }}</span>,
        studied <span class="italic">in {{ $class ?? 'N/A' }}-{{ $section ?? 'N/A' }} </span> in our school for the year
        <span class="bold">{{ $session }}</span>. Her date of birth is
        <span class="bold">{{ $dob }}</span> as per our records and her conduct was found
        <span class="italic bold">Good</span>.
    </p>

    <!-- Signature -->
    <div class="footer">PRINCIPAL</div>

</body>
</html>
