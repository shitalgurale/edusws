@extends('parent.navigation')

@section('content')

<?php
use App\Models\DailyAttendances;
use App\Http\Controllers\CommonController;
?>

<style>
 .custom_cs { padding: 0.375rem 5.75rem; }
 .att-table { min-width: 1600px; }
 .att-table-scroll { overflow-x: auto; width: 100%; }
 .att-custom_div { background-color: white !important; height: 20px; width: 20px; }
 .att-mark { font-weight: bold; font-size: 16px; text-align: center; display: inline-block; width: 20px; }
</style>

<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Daily Attendance') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Academic') }}</a></li>
                        <li><a href="#">{{ get_phrase('Daily Attendance') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap-2">
            <div class="filter-sec">
                <form method="GET" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('parent.daily_attendance.filter') }}">
                    <div class="att-filter d-flex flex-wrap">
                        <!-- Month Filter -->
                        <div class="att-filter-option">
                            <select name="month" id="month" class="form-select eForm-select eChoice-multiple-with-remove" required>
                                <option value="">{{ get_phrase('Select a month') }}</option>
                                @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $mon)
                                    <option value="{{ $mon }}" {{ $page_data['month'] == $mon ? 'selected' : '' }}>{{ get_phrase(date('F', strtotime("01 $mon"))) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Year Filter -->
                        <div class="att-filter-option">
                            <select name="year" id="year" class="form-select eForm-select eChoice-multiple-with-remove" required>
                                <option value="">{{ get_phrase('Select a year') }}</option>
                                @for($year = 2015; $year <= date('Y'); $year++)
                                    <option value="{{ $year }}" {{ $page_data['year'] == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                        <!-- Student Filter -->
                        <div class="att-filter-option">
                            <select name="student_id" id="student_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                                <option value="">{{ get_phrase('Select student') }}</option>
                                @foreach($child_data as $each_student)
                                    <option value="{{ $each_student['user_id'] }}" {{ isset($student_data->user_id) && $student_data->user_id == $each_student['user_id'] ? 'selected' : '' }}>{{ $each_student['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Filter Button -->
                        <div class="att-filter-btn">
                            <button class="eBtn eBtn btn-secondary" type="submit">{{ get_phrase('Filter') }}</button>
                        </div>
                    </div>
                </form>
            </div>

            @if(count($attendance_of_students) > 0)
            @php
                $month_str = $page_data['month'];
                $year = $page_data['year'];
                $month_num = date('m', strtotime("01 $month_str $year"));
                $number_of_days = cal_days_in_month(CAL_GREGORIAN, $month_num, $year);
            @endphp
            <div class="att-report-banner d-flex justify-content-between align-items-center flex-wrap">
                <div class="att-report-summary order-1">
                    <h4 class="title">{{ get_phrase('Attendance Report of') }} {{ date('F', strtotime("01 $month_str $year")) }} {{ $year }}</h4>
                    <p class="summary-item">{{ get_phrase('Class') }}: <span>{{ $student_data->class_name }}</span></p>
                    <p class="summary-item">{{ get_phrase('Section') }}: <span>{{ $student_data->section_name }}</span></p>
                    @php $last_record = DailyAttendances::latest('updated_at')->first(); @endphp
                    <p class="summary-item">{{ get_phrase('Last Update At') }}: 
                        <span>{{ $last_record ? date('d-M-Y', strtotime($last_record->updated_at)) : get_phrase('Not updated yet') }}</span>
                    </p>
                </div>
                <div class="att-banner-img order-0 order-md-1">
                    <img src="{{ asset('assets/images/attendance-report-banner.png') }}" alt="" />
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="att-table-scroll">
                <div class="att-table" id="pdf_table">
                    <div class="att-title">
                        <h4 class="att-title-header">{{ ucfirst('Student') }} / {{ get_phrase('Date') }}</h4>
                        <ul class="att-stuName-items">
                            <li class="att-stuName-item">
                                <h4 class="att-title-header">{{ $student_data->name }}</h4>
                            </li>
                        </ul>
                    </div>

                    <div class="att-content">
                        <div class="att-dayWeek">
                            <div class="att-wDay d-flex">
                                @for ($i = 1; $i <= $number_of_days; $i++)
                                    @php
                                        $date_str = date('Y-m-d', strtotime("$year-$month_num-$i"));
                                        $day = date("l", strtotime($date_str));
                                    @endphp
                                    <div><p>{{ substr($day, 0, 1) }}</p></div>
                                @endfor
                            </div>
                            <div class="att-date d-flex">
                                @for ($i = 1; $i <= $number_of_days; $i++)
                                    <div><p>{{ $i }}</p></div>
                                @endfor
                            </div>
                        </div>

                        <ul class="att-count-items">
                            <li class="att-count-item">
                                <div class="att-count-stu d-flex">
                                    @for($i = 1; $i <= $number_of_days; $i++)
                                        @php
                                            $date_str = date('Y-m-d', strtotime("$year-$month_num-$i"));
                                            $day_name = date('l', strtotime($date_str));
                                            $attendance = $attendance_of_students->first(function ($att) use ($date_str) {
                                                return \Carbon\Carbon::parse($att->timestamp)->format('Y-m-d') === $date_str;
                                            });
                                        @endphp

                                        @if ($day_name == 'Sunday')
                                            <span class="att-mark">H</span> <!-- Holiday -->
                                        @elseif ($attendance && isset($attendance->status))
                                            @if ($attendance->status == 1)
                                                <span class="att-mark">&#10003;</span> <!-- ✔ Present -->
                                            @elseif ($attendance->status == 0)
                                                <span class="att-mark">&#10007;</span> <!-- ✖ Absent -->
                                            @endif
                                        @else
                                            <div class="att-custom_div"></div> <!-- No data -->
                                        @endif
                                    @endfor
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            @else
            <div class="empty_box center">
                <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                <br>
                <span>{{ get_phrase('No data found') }}</span>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
"use strict";

function Export() {
    var pdfElement = document.getElementById('pdf_table');
    html2canvas(pdfElement, { scale: 2, useCORS: true, allowTaint: true }).then(function(canvas) {
        var data = canvas.toDataURL();
        var docDefinition = { content: [{ image: data, width: 500 }] };
        pdfMake.createPdf(docDefinition).download("AttendanceReport.pdf");
    });
}
</script>

@endsection
