<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Employee Attendance Report - {{ $filters['month'] }} {{ $filters['year'] }}</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 12px;
      margin: 20px;
      padding: 0;
    }
    .report-header {
      width: 100%;
      text-align: center;
      padding-bottom: 10px;
      margin-bottom: 10px;
      border-bottom: 3px solid #000;
    }
    .report-header h4 {
      margin: 5px 0;
      font-size: 18px;
      font-weight: bold;
      text-transform: uppercase;
    }
    .report-summary {
      text-align: center;
      font-size: 14px;
      margin-bottom: 10px;
    }
    .report-summary p {
      margin: 4px 0;
      font-weight: bold;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
      margin-bottom: 15px;
    }
    th, td {
      border: 1px solid #000;
      padding: 6px;
      text-align: center;
      word-wrap: break-word;
    }
    th {
      background-color: #f0f0f0;
      font-weight: bold;
    }
  </style>
</head>
<body>

<div class="report-header">
    <h4>Employee Attendance Report for {{ $filters['month'] }} {{ $filters['year'] }}</h4>
    <div class="report-summary">
        <p><strong>Role:</strong> {{ $role_name }}</p>
        <p>Last Updated At:
            @if(isset($employeeAttendances) && $employeeAttendances->isNotEmpty())
                {{ date('d-M-Y', strtotime($employeeAttendances->max('updated_at'))) }}
            @else
                Not Updated Yet
            @endif
        </p>
    </div>
</div>

@if(isset($employeeAttendances) && $employeeAttendances->isNotEmpty())
    @php
        $employeeSummary = $employeeAttendances->groupBy('role_name')->map(function($records) {
            return $records->groupBy('emp_bioid')->map(function($employeeRecords) {
                return [
                    'employee_name' => $employeeRecords->first()->employee_name ?? 'N/A',
                    'total_days'    => $employeeRecords->count(),
                    'present_days'  => $employeeRecords->where('status', 1)->count(),
                    'absent_days'   => $employeeRecords->where('status', 0)->count(),
                ];
            });
        });
    @endphp

    @foreach($employeeSummary as $roleName => $employees)
        <h4>Summary Report ({{ $roleName }})</h4>
        <table>
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Employee BioID</th>
                    <th>Total Days</th>
                    <th>Present Days</th>
                    <th>Absent Days</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $bioid => $summary)
                    <tr>
                        <td>{{ $summary['employee_name'] }}</td>
                        <td>{{ $bioid }}</td>
                        <td>{{ $summary['total_days'] }}</td>
                        <td>{{ $summary['present_days'] }}</td>
                        <td>{{ $summary['absent_days'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <h4>Detailed Attendance</h4>
    <table>
        <thead>
            <tr>
                <th>Role</th>
                <th>Employee Name</th>
                <th>Employee BioID</th>
                <th>School ID</th>
                <th>Device ID</th>
                <th>Session</th>
                <th>Status</th>
                <th>Date</th>
                <th>In Time</th>
                <th>Out Time</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employeeAttendances as $attendance)
                <tr>
                    <td>{{ $attendance->role_name ?? 'N/A' }}</td>
                    <td>{{ $attendance->employee_name ?? 'N/A' }}</td>
                    <td>{{ $attendance->emp_bioid }}</td>
                    <td>{{ $attendance->school_id }}</td>
                    <td>{{ $attendance->device_id ?? 'N/A' }}</td>
                    <td>{{ $attendance->session_id }}</td>
                    <td>{{ $attendance->status ? 'Present' : 'Absent' }}</td>
                    <td>{{ date('d-M-Y', strtotime($attendance->created_at)) }}</td>
                    <!--<td>{{ date('h:i A', strtotime($attendance->emp_intime ?? '09:30 AM')) }}</td>
                    <td>{{ date('h:i A', strtotime($attendance->emp_outtime ?? '05:30 PM')) }}</td>-->
                    <td>{{ $attendance->emp_intime ? date('H:i:s', strtotime($attendance->emp_intime)) : '00:00:00' }}</td>
                    <td>{{ $attendance->emp_outtime ? date('H:i:s', strtotime($attendance->emp_outtime)) : '00:00:00' }}</td>

                    
                    <td>{{ empty($attendance->device_id) ? 'Manual' : 'Automatic' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>No attendance records found for {{ $filters['month'] }} {{ $filters['year'] }} and selected role.</p>
@endif

</body>
</html>