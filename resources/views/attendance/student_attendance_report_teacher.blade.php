<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Student Attendance Report</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 12px;
      margin: 20px;
    }
    .report-header {
      width: 100%;
      text-align: center;
      padding-bottom: 10px;
      margin-bottom: 20px;
      border-bottom: 3px solid #000;
    }
    .report-header h4 {
      margin: 5px 0;
      font-size: 22px;
      font-weight: bold;
      text-transform: uppercase;
    }
    .report-summary p {
      margin: 3px 0;
      font-size: 14px;
      font-weight: bold;
    }
    h4.section-title {
      font-size: 18px;
      margin-top: 30px;
      margin-bottom: 10px;
      text-transform: uppercase;
      border-bottom: 1px solid #aaa;
      padding-bottom: 5px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 25px;
    }
    th, td {
      border: 1px solid #000;
      padding: 6px;
      text-align: center;
    }
    th {
      background-color: #f0f0f0;
      font-size: 14px;
      font-weight: bold;
    }
    .student-name-header {
      background-color: #ccc;
      font-weight: bold;
      font-size: 15px;
      text-align: left;
    }
  </style>
</head>
<body>

<!-- ✅ Top Report Header -->
<div class="report-header">
  <h4>Attendance Report for {{ $selectedMonthAbbrev }} {{ $selectedYear }}</h4>
  <div class="report-summary">
    <p><strong>Class:</strong> {{ $class_name ?? 'N/A' }}</p>
    <p><strong>Section:</strong> {{ $section_name ?? 'N/A' }}</p>

    @php
    use App\Models\DailyAttendances;
    $last_record = DailyAttendances::latest('updated_at')->first();
    @endphp

    <p><strong>Last updated at:</strong>
      {{ !empty($last_record) && isset($last_record->updated_at)
          ? date('d-M-Y', strtotime($last_record->updated_at))
          : 'Not updated yet' }}
    </p>
  </div>
</div>

<!-- ✅ Summary Section -->
<h4 class="section-title">Summary Report</h4>
<table>
  <thead>
    <tr>
      <th>Student Name</th>
      <th>Student Bio ID</th>
      <th>Present Days</th>
      <th>Leave Days</th>
    </tr>
  </thead>
  <tbody>
    @foreach($summary as $name => $data)
      <tr>
        <td>{{ $name }}</td>
        <td>{{ $data['bio_id'] }}</td>
        <td>{{ $data['present'] }}</td>
        <td>{{ $data['leave'] }}</td>
      </tr>
    @endforeach
  </tbody>
</table>

<!-- ✅ Detailed Section -->
<h4 class="section-title">Detailed Attendance</h4>
@foreach($groupedAttendances as $name => $records)
  <table>
    <thead>
      <tr>
        <th colspan="6" class="student-name-header">{{ $name }}</th>
      </tr>
      <tr>
        <th>Bio ID</th>
        <th>Date</th>
        <th>Status</th>
        <th>In Time</th>
        <th>Out Time</th>
        <th>Remark</th>
      </tr>
    </thead>
    <tbody>
      @foreach($records as $record)
        <tr>
          <td>{{ $record->stu_bioid }}</td>
          <td>{{ date('d-M-Y', strtotime($record->stu_intime ?? $record->timestamp)) }}</td>
 
          <td>{{ $record->status ? 'Present' : 'Absent' }}</td>
          <td>{{ $record->stu_intime ? date('h:i A', strtotime($record->stu_intime)) : '-' }}</td>
          <td>{{ $record->stu_outtime ? date('h:i A', strtotime($record->stu_outtime)) : '-' }}</td>
          <td>{{ $record->device_id ? 'Automatic' : 'Manual' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endforeach

</body>
</html>
