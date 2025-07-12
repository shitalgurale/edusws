<?php 

use App\Http\Controllers\CommonController;
use App\Models\Enrollment;
use App\Models\DailyAttendances;
use App\Models\User; // <-- Important

$school_id = auth()->user()->school_id;
$active_session = get_school_settings($school_id)->value('running_session');

// Fetch enrolled students
$enrols = Enrollment::where([
    'class_id' => $page_data['class_id'],
    'section_id' => $page_data['section_id'],
    'school_id' => $school_id,
    'session_id' => $active_session
])->get();

// 🛠 Bulk load all student users in 1 query
$student_ids = $enrols->pluck('user_id')->toArray();
$students = User::whereIn('id', $student_ids)->get()->keyBy('id');

// Fetch all attendance records for selected date/class/section
$attendance_records = DailyAttendances::whereDate('timestamp', $page_data['attendance_date'])
    ->where('class_id', $page_data['class_id'])
    ->where('section_id', $page_data['section_id'])
    ->where('school_id', $school_id)
    ->where('session_id', $active_session)
    ->get()
    ->keyBy('student_id');
?>

<div class="row mb-2">
    <div class="col-6"><a href="javascript:" class="btn btn-sm btn-secondary" onclick="present_all()"><?php echo e(get_phrase('Present All')); ?></a></div>
    <div class="col-6"><a href="javascript:" class="btn btn-sm btn-secondary float-right" onclick="absent_all()"><?php echo e(get_phrase('Absent All')); ?></a></div>
</div>

<div class="table-responsive-sm row col-md-12">
    <table class="table eTable table-bordered">
        <thead>
            <tr>
                <th><?php echo e(get_phrase('Name')); ?></th>
                <th><?php echo e(get_phrase('Status')); ?></th>
            </tr>
        </thead>
        <tbody>

        <?php $__currentLoopData = $enrols; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enroll): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $student_id = $enroll->user_id;
                $student_details = $students[$student_id] ?? null;
            ?>

            <?php if(!$student_details): ?>
                <?php continue; ?>
            <?php endif; ?>

            <?php
                $existingAttendance = $attendance_records[$student_id] ?? null;
            ?>

            <tr>
                <td><?php echo e($student_details->name); ?></td>
                <td>
                    <input type="hidden" name="student_id[]" value="<?php echo e($student_id); ?>">
                    <?php if($existingAttendance): ?>
                        <input type="hidden" name="attendance_id[]" value="<?php echo e($existingAttendance->id); ?>">
                    <?php endif; ?>

                    <div class="custom-control custom-radio">
                        <input type="radio" name="status-<?php echo e($student_id); ?>" value="1" class="present" 
                            <?php echo e($existingAttendance && $existingAttendance->status == 1 ? 'checked' : ''); ?> required>
                        <?php echo e(get_phrase('present')); ?> &nbsp;

                        <input type="radio" name="status-<?php echo e($student_id); ?>" value="0" class="absent" 
                            <?php echo e((!$existingAttendance || $existingAttendance->status == 0) ? 'checked' : ''); ?> required>
                        <?php echo e(get_phrase('absent')); ?>

                    </div>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </tbody>
    </table>
</div>

<script type="text/javascript">
"use strict";

function present_all() {
    $(".present").prop('checked', true);
}

function absent_all() {
    $(".absent").prop('checked', true);
}
</script>
<?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/admin/attendance/student.blade.php ENDPATH**/ ?>