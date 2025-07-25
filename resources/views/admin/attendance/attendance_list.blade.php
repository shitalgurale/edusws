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



@if ($loaddata == 1)
    @if (count($attendance_of_students) > 0)
        <!-- ✅ Header Section -->
        <h5>
            <div class="att-report-banner d-flex justify-content-center justify-content-md-between align-items-center flex-wrap">
                <div class="att-report-summary order-1">
                    @php
                        $attendanceMonthYear = $page_data['month'] . ' ' . $page_data['year'];
                    @endphp
                    <h4 class="title">{{ get_phrase('Attendance Report') . ' ' . get_phrase('of') . ' ' . $attendanceMonthYear }}</h4>

                    @if(auth()->user()->role_id == 2)
                        <p class="summary-item">{{ get_phrase('Class') }}: <span>{{ $class_name ?? 'N/A' }}</span></p>
                        <p class="summary-item">{{ get_phrase('Section') }}: <span>{{ $section_name ?? 'N/A' }}</span></p>
                    @else
                        <p class="summary-item">{{ get_phrase('Name') }}: <span>{{ $userName['name'] ?? 'N/A' }}</span></p>
                    @endif

                    <h5>
                        @php
                            $last_record = DailyAttendances::latest('updated_at')->first();
                        @endphp
                        {{ get_phrase('Last updated at') }}: 
                        {{ !empty($last_record?->updated_at) ? date('d-M-Y', strtotime($last_record->updated_at)) : get_phrase('Not updated yet') }}
                    </h5>
                </div>
                <div class="att-banner-img order-0 order-md-1">
                    <img src="{{ asset('assets/images/attendance-report-banner.png') }}" alt="" />
                </div>
            </div>
        </h5>

        @php
            $uniqueStudents = [];
            foreach ($attendance_of_students as $record) {
                $uniqueStudents[$record['student_id']] = $record['student_id'];
            }

            $month = date('m', strtotime('01 ' . $page_data['month'] . ' ' . $page_data['year']));
            $year = $page_data['year'];
            $number_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        @endphp

        <!-- ✅ Attendance Table -->
        <div class="attendance-table-container" id="pdf_table">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th class="sticky">Student Name</th>
                        @for ($i = 1; $i <= $number_of_days; $i++)
                            @php $day = date("D", strtotime("$year-$month-$i")); @endphp
                            <th>{{ $i }}<br><span style="font-size:12px; color:grey;">{{ substr($day, 0, 1) }}</span></th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach ($uniqueStudents as $studentId)
                        @php
                            $studentDetailsObj = (new CommonController)->get_student_details_by_id($studentId);
                            $studentDetails = is_object($studentDetailsObj) ? (array) $studentDetailsObj : $studentDetailsObj;

                            if (empty($studentDetails['name']) || empty($studentDetails['class_id'])) continue;
                        @endphp
                        <tr>
                            <td class="sticky" style="text-align:left;">
                                {{ htmlspecialchars($studentDetails['name']) }}
                            </td>

                            @for ($i = 1; $i <= $number_of_days; $i++)
                                @php
                                    $date_str = date('Y-m-d', strtotime("$year-$month-$i"));
                                    $attendance_by_id = DailyAttendances::where('student_id', $studentId)
                                        ->where('school_id', auth()->user()->school_id)
                                        ->where(function($query) use ($date_str) {
                                            $query->whereDate('stu_intime', $date_str)
                                                  ->orWhereDate('timestamp', $date_str);
                                        })
                                        ->latest('timestamp')
                                        ->first();
                                @endphp
                                <td>
                                    @if (date('w', strtotime($date_str)) == 0)
                                        <span class="att-mark">H</span> <!-- Sunday -->
                                    @elseif ($attendance_by_id)
                                        @if ($attendance_by_id->status == 1)
                                            <span class="att-mark">&#10003;</span> <!-- ✔ Present -->
                                        @elseif ($attendance_by_id->status == 0)
                                            <span class="att-mark">&#10007;</span> <!-- ✖ Absent -->
                                        @endif
                                    @else
                                        <span class="att-mark"></span> <!-- No Record -->
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty_box" style="text-align:center;">
            <img src="{{ asset('assets/images/empty_box.png') }}" width="150" class="mb-3" alt="" />
            <br>
            <span>{{ get_phrase('No data found') }}</span>
        </div>
    @endif
@endif


