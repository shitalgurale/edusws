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
<?php if(isset($pdf) && $pdf == 1): ?>
    <h2>Employee Attendance Report</h2>
    <p><strong>Date:</strong> <?php echo e(date('d-m-Y', strtotime($date))); ?></p>
    <p><strong>Role:</strong> <?php echo e($role_name); ?></p>
<?php endif; ?>

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
            <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($record->name); ?></td>
                    <td>
                        <?php echo e((isset($record->bioid) && $record->bioid !== null && $record->bioid !== '' && strtoupper($record->bioid) !== 'N/A') 
                            ? $record->bioid 
                            : '-'); ?>

                    </td>
                    <td><?php echo e($record->device_id ?? '0'); ?></td>
                    <td><?php echo e($record->emp_intime ? date('H:i:s', strtotime($record->emp_intime)) : '-'); ?></td>
                    <td><?php echo e($record->emp_outtime ? date('H:i:s', strtotime($record->emp_outtime)) : '-'); ?></td>
                    <td><?php echo e($record->remark); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center">No records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>





<?php if(!isset($pdf)): ?>
<form id="exportPdfForm" method="GET" action="<?php echo e(route('admin.attendance.export_daily_pdf')); ?>" target="_blank">
    <?php echo csrf_field(); ?>
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
<?php endif; ?>

</body>
</html>
<?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/admin/attendance/hr_daily_attendance_table.blade.php ENDPATH**/ ?>