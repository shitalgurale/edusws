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
<?php if(isset($pdf) && $pdf == 1): ?>
    <h2>Student Attendance Report</h2>
    <?php if(isset($date)): ?>
        <p><strong>Date:</strong> <?php echo e(date('d-m-Y', strtotime($date))); ?></p>
    <?php endif; ?>
    <?php if(isset($class_name) && isset($section_name)): ?>
        <p><strong>Class:</strong> <?php echo e($class_name); ?> &nbsp;&nbsp; <strong>Section:</strong> <?php echo e($section_name); ?></p>
    <?php endif; ?>
<?php endif; ?>

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
            <?php $__empty_1 = true; $__currentLoopData = $attendanceData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($data['name']); ?></td>
                  <td>
    <?php echo e((isset($data['bioid']) && $data['bioid'] !== null && $data['bioid'] !== '' && strtoupper($data['bioid']) !== 'N/A') 
        ? $data['bioid'] 
        : '-'); ?>

</td>
                    <td><?php echo e($data['device_id'] ?? '0'); ?></td>
                    <td><?php echo e($data['in_time'] ? date('H:i:s', strtotime($data['in_time'])) : '-'); ?></td>
                    <td><?php echo e($data['out_time'] ? date('H:i:s', strtotime($data['out_time'])) : '-'); ?></td>
                    <td><?php echo e($data['remark']); ?></td>
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
<form id="exportPdfForm" method="POST" action="<?php echo e(route('admin.attendance.exportStudentDailyPDF')); ?>" target="_blank">
    <?php echo csrf_field(); ?>
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
<?php endif; ?>
</body>
</html>
<?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/admin/attendance/student_daily_attendance_table.blade.php ENDPATH**/ ?>