<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Student Attendance Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #000000;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #000000;
            padding: 6px;
            text-align: center;
            color: #000000;
            word-wrap: break-word;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        h2, p {
            color: #000000;
            text-align: center;
            margin: 6px 0;
        }
    </style>
</head>
<body>
@if(isset($pdf) && $pdf == 1)
    <h2>Student Attendance Report</h2>
    @if(isset($date))
        <p><strong>Date:</strong> {{ date('d-m-Y', strtotime($date)) }}</p>
    @endif
    @if(isset($class_name) && isset($section_name))
        <p><strong>Class:</strong> {{ $class_name }} &nbsp;&nbsp; <strong>Section:</strong> {{ $section_name }}</p>
    @endif
@endif

<div id="attendanceTableWrapper">
    <table>
        <thead>
            <tr>
                <th style="width: 16%;">Student Name</th>
                <th style="width: 16%;">Bio ID</th>
                <th style="width: 16%;">Device ID</th>
                <th style="width: 16%;">In Time</th>
                <th style="width: 16%;">Out Time</th>
                <th style="width: 16%;">Remark</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendanceData as $data)
                <tr>
                    <td>{{ $data['name'] }}</td>
                  <td>
    {{ (isset($data['bioid']) && $data['bioid'] !== null && $data['bioid'] !== '' && strtoupper($data['bioid']) !== 'N/A') 
        ? $data['bioid'] 
        : '-' 
    }}
</td>
                    <td>{{ $data['device_id'] ?? '0' }}</td>
                    <td>{{ $data['in_time'] ? date('H:i:s', strtotime($data['in_time'])) : '-' }}</td>
                    <td>{{ $data['out_time'] ? date('H:i:s', strtotime($data['out_time'])) : '-' }}</td>
                    <td>{{ $data['remark'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(!isset($pdf))
<form id="exportPdfForm" method="POST" action="{{ route('admin.attendance.exportStudentDailyPDF') }}" target="_blank">
    @csrf
    <input type="hidden" name="class_id" id="export_class_id">
    <input type="hidden" name="section_id" id="export_section_id">
    <input type="hidden" name="date" id="export_date">
</form>

<script>
function submitExportForm() {
    document.getElementById('export_class_id').value = document.getElementById('class_id_on_taking_attendance').value;
    document.getElementById('export_section_id').value = document.getElementById('section_id_on_taking_attendance').value;
    document.getElementById('export_date').value = document.getElementById('date_on_taking_attendance').value;
    document.getElementById('exportPdfForm').submit();
}
</script>
@endif
</body>
</html>
