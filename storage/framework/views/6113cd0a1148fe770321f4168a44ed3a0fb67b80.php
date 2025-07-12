<?php
use App\Models\User;
use App\Models\Role;
use App\Http\Controllers\CommonController;
use App\Models\DailyAttendances;



?>
<style>
    .table_cap {
        caption-side: top;
    }
    .att-mark {
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        display: inline-block;
        width: 20px;
    }
    /* Attendance table horizontal scrolling */
    .attendance-table-container {
        overflow-x: auto;
        display: block;
        width: 100%;
    }
    .attendance-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1200px;
    }
    .attendance-table th, .attendance-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }
    .attendance-table thead {
        background-color: #f1f3f5;
    }
    /* Sticky first column for Student Name */
    .attendance-table th.sticky, 
    .attendance-table td.sticky {
        position: sticky;
        left: 0;
        background: #fff;
        z-index: 2;
        text-align: left;
    }
    .attendance-table th.sticky {
        z-index: 3;
    }
</style>



<?php if($loaddata == 1): ?>
    <?php if(count($attendance_of_students) > 0): ?>
        <!-- ✅ Header Section -->
        <h5>
            <div class="att-report-banner d-flex justify-content-center justify-content-md-between align-items-center flex-wrap">
                <div class="att-report-summary order-1">
                    <?php
                        $attendanceMonthYear = $page_data['month'] . ' ' . $page_data['year'];
                    ?>
                    <h4 class="title"><?php echo e(get_phrase('Attendance Report') . ' ' . get_phrase('of') . ' ' . $attendanceMonthYear); ?></h4>

                    <?php if(auth()->user()->role_id == 2): ?>
                        <p class="summary-item"><?php echo e(get_phrase('Class')); ?>: <span><?php echo e($class_name ?? 'N/A'); ?></span></p>
                        <p class="summary-item"><?php echo e(get_phrase('Section')); ?>: <span><?php echo e($section_name ?? 'N/A'); ?></span></p>
                    <?php else: ?>
                        <p class="summary-item"><?php echo e(get_phrase('Name')); ?>: <span><?php echo e($userName['name'] ?? 'N/A'); ?></span></p>
                    <?php endif; ?>

                    <h5>
                        <?php
                            $last_record = DailyAttendances::latest('updated_at')->first();
                        ?>
                        <?php echo e(get_phrase('Last updated at')); ?>: 
                        <?php echo e(!empty($last_record?->updated_at) ? date('d-M-Y', strtotime($last_record->updated_at)) : get_phrase('Not updated yet')); ?>

                    </h5>
                </div>
                <div class="att-banner-img order-0 order-md-1">
                    <img src="<?php echo e(asset('assets/images/attendance-report-banner.png')); ?>" alt="" />
                </div>
            </div>
        </h5>

        <?php
            $uniqueStudents = [];
            foreach ($attendance_of_students as $record) {
                $uniqueStudents[$record['student_id']] = $record['student_id'];
            }

            $month = date('m', strtotime('01 ' . $page_data['month'] . ' ' . $page_data['year']));
            $year = $page_data['year'];
            $number_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        ?>

        <!-- ✅ Attendance Table -->
        <div class="attendance-table-container" id="pdf_table">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th class="sticky">Student Name</th>
                        <?php for($i = 1; $i <= $number_of_days; $i++): ?>
                            <?php $day = date("D", strtotime("$year-$month-$i")); ?>
                            <th><?php echo e($i); ?><br><span style="font-size:12px; color:grey;"><?php echo e(substr($day, 0, 1)); ?></span></th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $uniqueStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $studentId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $studentDetailsObj = (new CommonController)->get_student_details_by_id($studentId);
                            $studentDetails = is_object($studentDetailsObj) ? (array) $studentDetailsObj : $studentDetailsObj;

                            if (empty($studentDetails['name']) || empty($studentDetails['class_id'])) continue;
                        ?>
                        <tr>
                            <td class="sticky" style="text-align:left;">
                                <?php echo e(htmlspecialchars($studentDetails['name'])); ?>

                            </td>

                            <?php for($i = 1; $i <= $number_of_days; $i++): ?>
                                <?php
                                    $date_str = date('Y-m-d', strtotime("$year-$month-$i"));
                                    $attendance_by_id = DailyAttendances::where('student_id', $studentId)
                                        ->where('school_id', auth()->user()->school_id)
                                        ->where(function($query) use ($date_str) {
                                            $query->whereDate('stu_intime', $date_str)
                                                  ->orWhereDate('timestamp', $date_str);
                                        })
                                        ->latest('timestamp')
                                        ->first();
                                ?>
                                <td>
                                    <?php if(date('w', strtotime($date_str)) == 0): ?>
                                        <span class="att-mark">H</span> <!-- Sunday -->
                                    <?php elseif($attendance_by_id): ?>
                                        <?php if($attendance_by_id->status == 1): ?>
                                            <span class="att-mark">&#10003;</span> <!-- ✔ Present -->
                                        <?php elseif($attendance_by_id->status == 0): ?>
                                            <span class="att-mark">&#10007;</span> <!-- ✖ Absent -->
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="att-mark"></span> <!-- No Record -->
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty_box" style="text-align:center;">
            <img src="<?php echo e(asset('assets/images/empty_box.png')); ?>" width="150" class="mb-3" alt="" />
            <br>
            <span><?php echo e(get_phrase('No data found')); ?></span>
        </div>
    <?php endif; ?>
<?php endif; ?>


<?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/admin/attendance/attendance_list.blade.php ENDPATH**/ ?>