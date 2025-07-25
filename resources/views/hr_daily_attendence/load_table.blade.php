<?php
use App\Models\User;
use App\Models\Addon\HrDailyAttendence;
use App\Models\Addon\Hr_roles;
use App\Http\Controllers\Addon\HrController;
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
    .attendance-table th, 
    .attendance-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }
    .attendance-table thead {
        background-color: #f1f3f5;
    }
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

<?php
if ($loaddata == 1 && count($attendance_of_students) > 0) {
    $selectedRole = Hr_roles::find($role_id);

    $uniqueUsers = [];
    foreach ($attendance_of_students as $record) {
        $uniqueUsers[$record['user_id']] = $record['user_id'];
    }

    // $selectedDate = strtotime($page_data['attendance_date']);
        //$month = date('m', $selectedDate);
        //$year = date('Y', $selectedDate);
        $month = date('m', strtotime('01 ' . $page_data['month'] . ' ' . $page_data['year']));
        $year = $page_data['year'];
    $number_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    $holidays = [];
?>

<!-- ✅ Header Section -->
<div class="att-report-banner d-flex justify-content-center justify-content-md-between align-items-center flex-wrap">
    <div class="att-report-summary order-1">
    <?php
        $attendanceMonthYear = $page_data['month'] . ' ' . $page_data['year'];

    ?>
        <h4 class="title">
                    <?php echo get_phrase('Attendance report') . ' ' . get_phrase('Of') . ' ' . $attendanceMonthYear; ?>
                </h4>
        <p class="summary-item">
            <?php echo get_phrase('Role'); ?>: 
            <span><?php echo $selectedRole->name ?? 'All Roles'; ?></span> 
        </p>
        <h5>
            <?php echo get_phrase('Last updated at'); ?>:
            <?php 
            $last_record = end($attendance_of_students);
            echo empty($attendance_of_students[0]['created_at']) ? get_phrase('Not updated yet') : date('d-M-Y', strtotime($last_record['updated_at']));
            ?>
        </h5>
    </div>
    <div class="att-banner-img order-0 order-md-1">
        <img src="<?php echo asset('assets/images/attendance-report-banner.png'); ?>" alt="" />
    </div>
</div>

<!-- ✅ Attendance Table -->
<div class="attendance-table-container" id="pdf_table">
    <table class="attendance-table">
        <thead>
            <tr>
                <th class="sticky">Employee Name</th>
                <?php for ($i = 1; $i <= $number_of_days; $i++): ?>
                    <?php $day = date("D", strtotime("$year-$month-$i")); ?>
                    <th><?php echo $i . '<br><span style="font-size:12px; color:grey;">' . substr($day, 0, 1) . '</span>'; ?></th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($uniqueUsers as $userId): ?>
                <?php 
                    $userDetails = (new HrController)->get_user_by_id_from_hr_userlist_table($userId);
                ?>
                <tr>
                    <td class="sticky" style="text-align:left;"><?php echo $userDetails['name']; ?></td>
                    <?php for ($i = 1; $i <= $number_of_days; $i++): ?>
                        <?php 
                            $date_str = date('Y-m-d', strtotime("$year-$month-$i"));
                            $day_of_week = date('D', strtotime($date_str));

                            // Fetch attendance record
                            $attendance = HrDailyAttendence::where('user_id', $userId)
                                ->where('school_id', auth()->user()->school_id)
                                ->where(function($query) use ($date_str) {
                                    $query->whereDate('emp_intime', $date_str)
                                          ->orWhere(function($subquery) use ($date_str) {
                                              $subquery->whereNull('emp_intime')
                                                       ->orwhereDate('created_at', $date_str);
                                          });
                                })
                                ->latest('created_at')
                                ->first();

                            // Determine status
                            if ($day_of_week == 'Sun' || in_array($date_str, $holidays)) {
                                $mark = "H"; // Sunday or holiday
                            } elseif ($attendance && isset($attendance->status)) {
                                $mark = ($attendance->status == 1) ? "P" : "A";
                            } else {
                                $mark = ""; // No record
                            }
                        ?>
                        <td>
                            <span class="att-mark"><?php echo $mark; ?></span>
                        </td>
                    <?php endfor; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
} else {
?>
<div class="empty_box text-center">
    <img src="<?php echo asset('assets/images/empty_box.png'); ?>" width="150" class="mb-3" alt="" />
    <br>
    <span><?php echo get_phrase('No data found'); ?></span>
</div>
<?php
}
?>
