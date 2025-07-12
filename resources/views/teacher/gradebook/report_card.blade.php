
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Report Card</title>
  <style>
    body {
      font-family: 'DejaVu Sans', sans-serif;
      background-color: #ffffff;
      padding: 20px;
      color: #000;
    }

    .report-container {
      max-width: 900px;
      margin: auto;
      background: #ffffff;
      padding: 25px;
      border-radius: 10px;
      border: 2px solid #000;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .report-title {
      color: #000;
      text-align: center;
      font-size: 32px;
      font-weight: bold;
      text-transform: uppercase;
      margin-bottom: 20px;
    }

    .header-container {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
    }

    .logo {
      max-width: 100px;
      margin-right: 20px;
    }

    .school-info {
      font-size: 22px;
      font-weight: bold;
      color: #000;
    }

    .academic-session {
      font-size: 16px;
      font-weight: 600;
      color: #000;
    }

    .details {
      font-size: 14px;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 1px solid #000;
      text-align: center;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th, td {
      border: 1px solid #000;
      padding: 10px;
      font-size: 14px;
      text-align: center;
    }

    th {
      background-color: #f0f0f0;
      color: #000;
      text-transform: uppercase;
    }

    .result-container {
      margin-top: 20px;
      padding-top: 15px;
      font-size: 15px;
      border-top: 1px solid #000;
    }

    .signatures {
      margin-top: 30px;
      display: flex;
      justify-content: space-between;
      font-size: 14px;
      color: #000;
    }

    .feedback {
      font-size: 14px;
      margin-top: 15px;
    }
  </style>
</head>
<body>
  

  <div class="report-container">
    <div class="report-title">Report Card</div>

    <!-- Header -->
    <div class="header-container" style="text-align: center;">
        @if($school_logo)
            <img src="file://{{ $school_logo }}" alt="School Logo" class="logo" style="max-width: 100px; display: block; margin: 0 auto 10px auto;">
        @endif 
        <div class="school-info">{{ $school_name ?? 'School Name' }}</div>
        <div class="academic-session">Academic Session: {{ $session_title ?? 'N/A' }}</div>
    </div>

    <!-- Student Info -->
 <div class="details">
  <p>
    <strong>Name:</strong> {{ $student_name }} &nbsp; | &nbsp;
    @php
        $className = \App\Models\Classes::find($gradebook->class_id)->name ?? 'N/A';
        $sectionName = \App\Models\Section::find($gradebook->section_id)->name ?? 'N/A';
    @endphp
    
    <strong>Class:</strong> {{ $className }} &nbsp; | &nbsp;
    <strong>Section:</strong> {{ $sectionName }} &nbsp; | &nbsp;
    <strong>Exam:</strong> {{ $exam_name }} &nbsp; | &nbsp;
    <strong>Date:</strong> {{ $current_date }}
  </p>
</div>
    <!-- Marks Table -->
    <table>
      <thead>
        <tr>
          <th>Subject</th>
          <th>Marks Obtained</th>
          <th>Total Marks</th>
        </tr>
      </thead>
      <tbody>
        @php $fail_status = false; @endphp
        @foreach($marks as $subject_id => $mark)
          @php $subject = \App\Models\Subject::find($subject_id); @endphp
          <tr>
            <td>{{ $subject->name ?? 'Unknown Subject' }}</td>
            <td>{{ $mark }}</td>
            <td>{{ $subject_total_marks[$subject_id] ?? '100' }}</td>
          </tr>
          <!--@if($mark < 35)
            @php $fail_status = true; @endphp
          @endif-->
        @endforeach
      </tbody>
    </table>

    <!-- Result Summary -->
    <div class="result-container">
    <p>
      <strong>Total:</strong> {{ $total_marks_obtained }} / {{ $total_marks }} &nbsp; | &nbsp;
      <strong>Percentage:</strong> {{ $percentage }}% &nbsp; | &nbsp;
      <strong>Grade:</strong> {{ $grade }} &nbsp; | &nbsp;
      <strong>Status:</strong> {{ $percentage >= 35 ? 'Pass' : 'Fail' }}
    </p>
    </div>

    <br />

    <!-- Footer Signatures -->
    <div>
      <p style="text-align: center; font-size: 14px; color: #000;">
          <strong>Class Teacher Signature</strong> &nbsp; | &nbsp;
          <strong>Parent Signature</strong> &nbsp; | &nbsp;
          <strong>Principal Signature</strong>
      </p>
    </div>
  </div>

</body>
</html>
