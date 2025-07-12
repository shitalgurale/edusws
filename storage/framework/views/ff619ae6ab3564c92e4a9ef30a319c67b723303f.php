<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Employee Attendance Report - <?php echo e($filters['month']); ?> <?php echo e($filters['year']); ?></title>
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
    <h4>Employee Attendance Report for <?php echo e($filters['month']); ?> <?php echo e($filters['year']); ?></h4>
    <div class="report-summary">
        <p><strong>Role:</strong> <?php echo e($role_name); ?></p>
        <p>Last Updated At:
            <?php if(isset($employeeAttendances) && $employeeAttendances->isNotEmpty()): ?>
                <?php echo e(date('d-M-Y', strtotime($employeeAttendances->max('updated_at')))); ?>

            <?php else: ?>
                Not Updated Yet
            <?php endif; ?>
        </p>
    </div>
</div>

<?php if(isset($employeeAttendances) && $employeeAttendances->isNotEmpty()): ?>
    <?php
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
    ?>

    <?php $__currentLoopData = $employeeSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleName => $employees): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <h4>Summary Report (<?php echo e($roleName); ?>)</h4>
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
                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bioid => $summary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($summary['employee_name']); ?></td>
                        <td><?php echo e($bioid); ?></td>
                        <td><?php echo e($summary['total_days']); ?></td>
                        <td><?php echo e($summary['present_days']); ?></td>
                        <td><?php echo e($summary['absent_days']); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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
            <?php $__currentLoopData = $employeeAttendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($attendance->role_name ?? 'N/A'); ?></td>
                    <td><?php echo e($attendance->employee_name ?? 'N/A'); ?></td>
                    <td><?php echo e($attendance->emp_bioid); ?></td>
                    <td><?php echo e($attendance->school_id); ?></td>
                    <td><?php echo e($attendance->device_id ?? 'N/A'); ?></td>
                    <td><?php echo e($attendance->session_id); ?></td>
                    <td><?php echo e($attendance->status ? 'Present' : 'Absent'); ?></td>
                    <td><?php echo e(date('d-M-Y', strtotime($attendance->created_at))); ?></td>
                    <!--<td><?php echo e(date('h:i A', strtotime($attendance->emp_intime ?? '09:30 AM'))); ?></td>
                    <td><?php echo e(date('h:i A', strtotime($attendance->emp_outtime ?? '05:30 PM'))); ?></td>-->
                    <td><?php echo e($attendance->emp_intime ? date('H:i:s', strtotime($attendance->emp_intime)) : '00:00:00'); ?></td>
                    <td><?php echo e($attendance->emp_outtime ? date('H:i:s', strtotime($attendance->emp_outtime)) : '00:00:00'); ?></td>

                    
                    <td><?php echo e(empty($attendance->device_id) ? 'Manual' : 'Automatic'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No attendance records found for <?php echo e($filters['month']); ?> <?php echo e($filters['year']); ?> and selected role.</p>
<?php endif; ?>

</body>
</html><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/attendance/employee_attendance_report.blade.php ENDPATH**/ ?>