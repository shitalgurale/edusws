@extends('teacher.navigation')

@section('content')

<?php 
use App\Http\Controllers\CommonController;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Session;
use App\Models\DailyAttendances;

$class_name = Classes::find($page_data['class_id'])->name;
$section_name = Section::find($page_data['section_id'])->name;
?>

<style>
    .att-mark { font-size: 16px; font-weight: bold; text-align: center; display: inline-block; width: 20px; }
    .attendance-table-container { overflow-x: auto; display: block; width: 100%; }
    .attendance-table { width: 100%; border-collapse: collapse; min-width: 1200px; }
    .attendance-table th, .attendance-table td { border: 1px solid #ddd; padding: 8px; text-align: center; }
    .attendance-table thead { background-color: #f1f3f5; }
    .attendance-table th.sticky, .attendance-table td.sticky { position: sticky; left: 0; background: #fff; z-index: 2; text-align: left; }
    .attendance-table th.sticky { z-index: 3; }
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
        <div class="export-btn-area">
          <a href="#" class="export_btn" onclick="rightModal('{{ route('teacher.take_attendance.open_modal') }}', '{{ get_phrase('Take Attendance') }}')">{{ get_phrase('Take Attendance') }}</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="eSection-wrap-2">

      <!-- Filter Form -->
      <form method="GET" class="d-block ajaxForm" action="{{ route('teacher.daily_attendance.filter') }}">
        <div class="att-filter d-flex flex-wrap">
          <!-- Month Filter -->
          <div class="att-filter-option">
            <select name="month" id="month" class="form-select eForm-select eChoice-multiple-with-remove" required>
              <option value="">{{ get_phrase('Select a month') }}</option>
              @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $m)
                <option value="{{ $m }}" {{ $page_data['month'] == $m ? 'selected' : '' }}>{{ get_phrase(date('F', strtotime("01 $m"))) }}</option>
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
          <!-- Class Filter -->
          <div class="att-filter-option">
            <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" onchange="classWiseSection(this.value)" required>
              <option value="">{{ get_phrase('Select a class') }}</option>
              @foreach($classes as $class)
                <option value="{{ $class['id'] }}" {{ $page_data['class_id'] == $class['id'] ? 'selected' : '' }}>{{ $class['name'] }}</option>
              @endforeach
            </select>
          </div>
          <!-- Section Filter -->
          <div class="att-filter-option">
            <select name="section_id" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
              <option value="">{{ get_phrase('Select a section') }}</option>
              @foreach(\App\Models\Section::where('class_id', $page_data['class_id'])->get() as $section)
                <option value="{{ $section->id }}" {{ $page_data['section_id'] == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="att-filter-btn">
            <button class="eBtn eBtn btn-secondary" type="submit">{{ get_phrase('Filter') }}</button>
          </div>
          <div class="col-md-2">
            <div class="position-relative">
              <button class="eBtn-3 dropdown-toggle" type="button" id="defaultDropdown" data-bs-toggle="dropdown"
                data-bs-auto-close="true" aria-expanded="false">
                <span class="pr-10">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12.31" height="10.77" viewBox="0 0 10.771 12.31">
                    <path id="arrow-right-from-bracket-solid"
                      d="M3.847,1.539H2.308a.769.769,0,0,0-.769.769V8.463a.769.769,0,0,0,.769.769H3.847a.769.769,0,0,1,0,1.539H2.308A2.308,2.308,0,0,1,0,8.463V2.308A2.308,2.308,0,0,1,2.308,0H3.847a.769.769,0,1,1,0,1.539Zm8.237,4.39L9.007,9.007A.769.769,0,0,1,7.919,7.919L9.685,6.155H4.616a.769.769,0,0,1,0-1.539H9.685L7.92,2.852A.769.769,0,0,1,9.008,1.764l3.078,3.078A.77.77,0,0,1,12.084,5.929Z"
                      transform="translate(0 12.31) rotate(-90)" fill="#00a3ff" />
                  </svg>
                </span>
                Export
              </button>
              <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2">
                <li>
                  <button class="dropdown-item" onclick="exportStudentAttendanceCSVTeacher()">CSV</button>
                </li>
                <li>
                  <button class="dropdown-item" onclick="exportStudentAttendancePDFTeacher()">PDF</button>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </form>

      <!-- Attendance Report -->
      @if(count($attendance_of_students) > 0)
        @php
          $selectedDate = strtotime("01 {$page_data['month']} {$page_data['year']}");
          $month = date('m', $selectedDate);
          $year = date('Y', $selectedDate);
          $number_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
          $student_ids = $attendance_of_students->pluck('student_id')->unique();
          $last_row = $attendance_of_students->last();
        @endphp

        <div class="att-report-banner d-flex justify-content-center justify-content-md-between align-items-center flex-wrap">
          <div class="att-report-summary order-1">
            <h4 class="title">{{ get_phrase('Attendance Report of').' '.date('F', $selectedDate).' '.$year }}</h4>
            <p class="summary-item">{{ get_phrase('Class') }}: <span>{{ $class_name }}</span></p>
            <p class="summary-item">{{ get_phrase('Section') }}: <span>{{ $section_name }}</span></p>
            <p class="summary-item">{{ get_phrase('Last Update at') }}: 
              <span>
                {{ $last_row ? date('d-M-Y', strtotime($last_row->updated_at)) : get_phrase('Not updated yet') }}
              </span>
            </p>
          </div>
          <div class="att-banner-img order-0 order-md-1">
            <img src="{{ asset('assets/images/attendance-report-banner.png') }}" alt=""/>
          </div>
        </div>

        <!-- Attendance Table -->
        <div class="attendance-table-container" id="pdf_table">
          <table class="attendance-table">
            <thead>
              <tr>
                <th><h4 class="att-title-header">{{ ucfirst('Student') }} / {{ get_phrase('Date') }}</h4></th>
                @for ($i = 1; $i <= $number_of_days; $i++)
                  @php $day = date("D", strtotime("$year-$month-$i")); @endphp
                  <th>{{ $i }}<br><span style="font-size:12px; color:grey;">{{ substr($day, 0, 1) }}</span></th>
                @endfor
              </tr>
            </thead>
            <tbody>
              @foreach ($student_ids as $studentId)
                  @php
                      $studentObject = (new CommonController)->get_student_details_by_id($studentId);
                      if (!$studentObject) continue; // Skip if object is null
                
                      $studentDetails = (array) $studentObject;
                      if (empty($studentDetails) || !isset($studentDetails['name'])) continue; // Skip if data is empty or name not set
                  @endphp
                  <tr>
                    <td class="sticky" style="text-align:left;">{{ $studentDetails['name'] }}</td>
                  @for ($i = 1; $i <= $number_of_days; $i++)
                    @php
                      $date_str = date('Y-m-d', strtotime("$year-$month-$i"));
                      $day_of_week = date('w', strtotime($date_str)); // 0 = Sunday
                      $attendance_by_id = DailyAttendances::where('student_id', $studentId)
                          ->where('school_id', auth()->user()->school_id)
                          ->whereDate('timestamp', $date_str)
                          ->latest('timestamp')
                          ->first();
                    @endphp
                    <td>
                      @if ($day_of_week == 0)
                        <span class="att-mark">H</span>
                      @elseif ($attendance_by_id && isset($attendance_by_id->status))
                        @if ($attendance_by_id->status == 1)
                          <span class="att-mark">&#10003;</span>
                        @elseif ($attendance_by_id->status == 0)
                          <span class="att-mark">&#10007;</span>
                        @endif
                      @else
                        <span class="att-mark"></span>
                      @endif
                    </td>
                  @endfor
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="empty_box center">
          <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
          <br>
          <span class="">{{ get_phrase('No data found') }}</span>
        </div>
      @endif
    </div>
  </div>
</div>

<script>
"use strict";
function classWiseSection(classId) {
    let url = "{{ route('admin.class_wise_sections', ['id' => ':classId']) }}".replace(":classId", classId);
    fetch(url)
        .then(response => response.text())
        .then(data => document.getElementById('section_id').innerHTML = data)
        .catch(error => console.error("Error loading sections:", error));
}

function updateStudentAttendanceDataTeacher(month, year, class_id, section_id) {
    let url = "{{ route('teacher.update_student_attendance_data') }}"; // Ensure this route matches
    let formData = new FormData();
    formData.append('month', month);
    formData.append('year', year);
    formData.append('class_id', class_id);
    formData.append('section_id', section_id);

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Student attendance data updated successfully for teacher', data);
    })
    .catch(error => {
        console.error("AJAX Error:", error);
        toastr.error('Failed to update student attendance data.');
    });
}

function exportStudentAttendancePDFTeacher() {
    let month = document.getElementById('month').value;
    let year = document.getElementById('year').value;
    let class_id = document.getElementById('class_id').value;
    let section_id = document.getElementById('section_id').value;

    let url = `{{ route('teacher.export_student_pdf') }}?month=${month}&year=${year}&class_id=${class_id}&section_id=${section_id}`;
     // Use window.open instead of window.location.href
     window.open(url, '_blank'); // Open in a new tab for download
}

function exportStudentAttendanceCSVTeacher() {
    let month = document.getElementById('month').value;
    let year = document.getElementById('year').value;
    let class_id = document.getElementById('class_id').value;
    let section_id = document.getElementById('section_id').value;

    let url = `{{ route('teacher.export_student_csv') }}?month=${month}&year=${year}&class_id=${class_id}&section_id=${section_id}`;
    window.open(url, '_blank'); // open in new tab
}



</script>

@endsection
