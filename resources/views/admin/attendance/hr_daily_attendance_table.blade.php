<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Employee Attendance Report</title>
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
    <h2>Employee Attendance Report</h2>
    <p><strong>Date:</strong> {{ date('d-m-Y', strtotime($date)) }}</p>
    <p><strong>Role:</strong> {{ $role_name }}</p>
@endif

<div id="attendanceTableWrapper">
    <table>
        <thead>
            <tr>
                <th style="width: 16%;">Employee Name</th>
                <th style="width: 16%;">Bio ID</th>
                <th style="width: 16%;">Device ID</th>
                <th style="width: 16%;">In Time</th>
                <th style="width: 16%;">Out Time</th>
                <th style="width: 16%;">Remark</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
                <tr>
                    <td>{{ $record->name }}</td>
                    <td>
                        {{ (isset($record->bioid) && $record->bioid !== null && $record->bioid !== '' && strtoupper($record->bioid) !== 'N/A') 
                            ? $record->bioid 
                            : '-' 
                        }}
                    </td>
                    <td>{{ $record->device_id ?? '0' }}</td>
                    <td>{{ $record->emp_intime ? date('H:i:s', strtotime($record->emp_intime)) : '-' }}</td>
                    <td>{{ $record->emp_outtime ? date('H:i:s', strtotime($record->emp_outtime)) : '-' }}</td>
                    <td>{{ $record->remark }}</td>
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
<form id="exportPdfForm" method="GET" action="{{ route('admin.attendance.export_daily_pdf') }}" target="_blank">
    @csrf
    <input type="hidden" name="role_id" id="export_role_id">
    <input type="hidden" name="date" id="export_date">
</form>

<script>
function submitExportForm() {
    document.getElementById('export_role_id').value = document.getElementById('role_id_filter').value;
    document.getElementById('export_date').value = document.getElementById('date_filter').value;
    document.getElementById('exportPdfForm').submit();
}
</script>
@endif

</body>
</html>
