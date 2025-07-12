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
  <h4>Attendance Report for <?php echo e($selectedMonthAbbrev); ?> <?php echo e($selectedYear); ?></h4>
  <div class="report-summary">
    <p><strong>Class:</strong> <?php echo e($class_name ?? 'N/A'); ?></p>
    <p><strong>Section:</strong> <?php echo e($section_name ?? 'N/A'); ?></p>
    
    <?php
    use App\Models\DailyAttendances;

    $last_record = DailyAttendances::latest('updated_at')->first();
?>

<p><strong>Last updated at:</strong>
    <?php echo e(!empty($last_record) && isset($last_record->updated_at)
        ? date('d-M-Y', strtotime($last_record->updated_at))
        : get_phrase('Not updated yet')); ?>

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
    <?php $__currentLoopData = $summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <tr>
        <td><?php echo e($name); ?></td>
        <td><?php echo e($data['bio_id']); ?></td>
        <td><?php echo e($data['present']); ?></td>
        <td><?php echo e($data['leave']); ?></td>
      </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </tbody>
</table>
<!-- ✅ Detailed Section -->
<h4 class="section-title">Detailed Attendance</h4>
<?php $__currentLoopData = $groupedAttendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $records): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <table>
    <thead>
      <tr>
        <th colspan="6" class="student-name-header"><?php echo e($name); ?></th>
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
      <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td><?php echo e($record->stu_bioid); ?></td>
          <td><?php echo e(date('d-M-Y', strtotime($record->stu_intime ?? $record->timestamp))); ?></td>
          <td><?php echo e($record->status ? 'Present' : 'Absent'); ?></td>
        <!--  <td><?php echo e($record->stu_intime ? date('h:i A', strtotime($record->stu_intime)) : '-'); ?></td>
          <td><?php echo e($record->stu_outtime ? date('h:i A', strtotime($record->stu_outtime)) : '-'); ?></td>-->
          
          
         <td><?php echo e($record->stu_intime ? date('H:i:s', strtotime($record->stu_intime)) : '-'); ?></td>
        <td><?php echo e($record->stu_outtime ? date('H:i:s', strtotime($record->stu_outtime)) : '-'); ?></td>

          <td><?php echo e($record->device_id ? 'Automatic' : 'Manual'); ?></td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</body>
</html>
<?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/attendance/student_attendance_report.blade.php ENDPATH**/ ?>