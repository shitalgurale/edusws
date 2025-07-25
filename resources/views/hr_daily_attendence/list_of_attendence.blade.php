<?php
use App\Models\User;
use App\Models\Role;
use App\Models\Addon\Hr_roles;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\Addon\HrController;
use App\Models\Addon\HrDailyAttendence;
?>

@extends($roleName)

@section('content')

<style>
   .custom_cs{
    padding: 0.375rem 5.75rem;

   }
   .att-custom_div {

     background-color: white !important;

}
.bdr{

    height: 21px !important;

}


</style>


<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
                @if(auth()->user()->role_id == 2)
                <h4>
                    {{ get_phrase('Monthly Attendance') }}
                </h4>
                @else
                <h4>
                    {{ get_phrase('Attendance Report') }}
                </h4>
                @endif
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Human Resource') }}</a></li>
              <li><a href="#">{{ get_phrase('Attendence') }}</a></li>
            </ul>
          </div>
          <div class="export-btn-area">
            @if(auth()->user()->role_id == 2)
            <a href="javascript:;" class="export_btn"  onclick="rightModal('{{ route('hr.show_take_attendence_modal') }}','{{ get_phrase('Mark Attendance') }}')">{{ get_phrase('Mark Attendance') }}</a>

            @endif


          </div>
        </div>
      </div>
    </div>
</div>





<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <div class="row mt-3 d-print-none">

                <div class="col-md-2 mb-1"></div>
                <div class="col-md-2 mb-1">
                    <select name="month" id="month" class="form-select eForm-select eChoice-multiple-with-remove" required>
                        <option value="">
                            {{ get_phrase('Select a month') }}
                        </option>
                        <option value="Jan" {{ date('M') =='Jan' ? 'selected' :'' }}>
                            {{ get_phrase('January') }}
                        </option>
                        <option value="Feb" {{ date('M') =='Feb' ? 'selected' :'' }}>
                            {{ get_phrase('February') }}
                        </option>
                        <option value="Mar" {{ date('M') =='Mar' ? 'selected' :'' }}>
                            {{ get_phrase('March') }}
                        </option>
                        <option value="Apr" {{ date('M') =='Apr' ? 'selected' :'' }}>
                            {{ get_phrase('April') }}
                        </option>
                        <option value="May" {{ date('M') =='May' ? 'selected' :'' }}>
                            {{ get_phrase('May') }}
                        </option>
                        <option value="Jun" {{ date('M') =='Jun' ? 'selected' :'' }}>
                            {{ get_phrase('June') }}
                        </option>
                        <option value="Jul" {{ date('M') =='Jul' ? 'selected' :'' }}>
                            {{ get_phrase('July') }}
                        </option>
                        <option value="Aug" {{ date('M') =='Aug' ? 'selected' :'' }}>
                            {{ get_phrase('August') }}
                        </option>
                        <option value="Sep" {{ date('M') =='Sep' ? 'selected' :'' }}>
                            {{ get_phrase('September') }}
                        </option>
                        <option value="Oct" {{ date('M') =='Oct' ? 'selected' :'' }}>
                            {{ get_phrase('October') }}
                        </option>
                        <option value="Nov" {{ date('M') =='Nov' ? 'selected' :'' }}>
                            {{ get_phrase('November') }}
                        </option>
                        <option value="Dec" {{ date('M') =='Dec' ? 'selected' :'' }}>
                            {{ get_phrase('December') }}
                        </option>
                    </select>
                </div>
                <div class="col-md-2 mb-1">
                    <select name="year" id="year" class="form-select eForm-select eChoice-multiple-with-remove" required>
                        <option value="">
                            {{ get_phrase('Select a year') }}
                        </option>
                        <?php for($year = 2015; $year <= date('Y'); $year++){ ?>
                        <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' :'' }}>
                            {{ $year }}
                        </option>
                        <?php } ?>

                    </select>
                </div>

                @php
                if (auth()->user()->role_id == 2):
                @endphp

                <div class="col-md-2 mb-1">
                    <select name="role_id" id="role_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                        <option value="">
                            {{ get_phrase('Select a role') }}
                        </option>
                        <option value="All">
                            {{ get_phrase('All Roles') }}
                        </option>
                        <?php $roles =  Hr_roles::where('school_id', auth()->user()->school_id)->get()->toArray();?>
                        <?php foreach ($roles as $role): ?>
                        <option value="{{ $role['id'] }}">
                            {{ ucfirst($role['name']) }}
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                @else
                <input type="hidden" id="role_id" name="role_id" value="{{ $user_role }}">

                @php
                endif;
                @endphp


                <div class="col-md-2">
                    <button class="btn btn-block btn-secondary"  onclick="filter_attendance()">

                        {{ get_phrase('Filter') }}
                    </button>
                </div>

                <div class="col-md-2">
                    <div class="position-relative">
                        <button
                          class="eBtn-3 dropdown-toggle"
                          type="button"
                          id="defaultDropdown"
                          data-bs-toggle="dropdown"
                          data-bs-auto-close="true"
                          aria-expanded="false"
                        >
                          <span class="pr-10">
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              width="12.31"
                              height="10.77"
                              viewBox="0 0 10.771 12.31"
                            >
                              <path
                                id="arrow-right-from-bracket-solid"
                                d="M3.847,1.539H2.308a.769.769,0,0,0-.769.769V8.463a.769.769,0,0,0,.769.769H3.847a.769.769,0,0,1,0,1.539H2.308A2.308,2.308,0,0,1,0,8.463V2.308A2.308,2.308,0,0,1,2.308,0H3.847a.769.769,0,1,1,0,1.539Zm8.237,4.39L9.007,9.007A.769.769,0,0,1,7.919,7.919L9.685,6.155H4.616a.769.769,0,0,1,0-1.539H9.685L7.92,2.852A.769.769,0,0,1,9.008,1.764l3.078,3.078A.77.77,0,0,1,12.084,5.929Z"
                                transform="translate(0 12.31) rotate(-90)"
                                fill="#00a3ff"
                              />
                            </svg>
                          </span>
                          Export
                        </button>
                        <ul
                          class="dropdown-menu dropdown-menu-end eDropdown-menu-2">
                          <li>
                            <button class="dropdown-item" href="#" onclick="download_csv()" >CSV</button>
                          </li>
                          <li>
                            <button class="dropdown-item" href="#" onclick="exportEmployeeAttendancePDF()" >PDF</button>
                          </li>
                        </ul>
                      </div>
                </div>



            </div>
            <div class="card-body attendance_content">

                <div class="empty_box text-center">
                    <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                    <br>
                    <span class="">
                        {{ get_phrase('Search Attendance Report') }}
                    </span>
                </div>
            </div>

            @if($no_user == 0)
                <div class="empty_box text-center">
                    <img class="mb-3 " width="150px" src="{{ asset('assets/images/empty_box.png') }}" />

                    <br>
                    <span class="">
                        {{ get_phrase('You are not registered yet') }} 
                     </span>
                </div>
    @endif






        </div>
    </div>
</div>
@endsection
<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta4/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/vfs_fonts.js"></script>

<script>

"use strict";

$(document).ready(function () {
    let role = '{{ $user_role }}';

    console.log("User Role: ", role);

    if (role != 1) {
        let month = $('#month').val();
        let year = $('#year').val();
        let role_id = $('#role_id').val();

        if (role_id !== "" && month !== "" && year !== "") {
            filter_attendance();
        }
    }
});

var if_table_loaded = 0;

function filter_attendance() {
    var month = $('#month').val();
    var year = $('#year').val();
    var role_id = $('#role_id').val();

    if (role_id !== "" && month !== "" && year !== "") {
        getHrDailyAttendance(); // Load report on the page
        updateEmployeeAttendanceData(); // Update attendance data in DB
    } else {
        toastr.error('{{ get_phrase('Please select the required fields') }}');
    }
}

// ✅ **Function 1: Load attendance report dynamically**
function getHrDailyAttendance() {
    var month = $('#month').val();
    var year = $('#year').val();
    var role_id = $('#role_id').val();

    if (role_id !== "" && month !== "" && year !== "") {
        $.ajax({
            url: '{{ route('hr.hr_daily_attendance.filter') }}', // Replace with actual backend route
            type: "GET",
            data: { month: month, year: year, role_id: role_id },
            success: function (response) {
                if_table_loaded = 1;
                $('.attendance_content').html(response);
            },
            error: function () {
                toastr.error('Failed to load attendance report.');
            }
        });
    }
}

// ✅ **Function 2: Update attendance data in the database**
function updateEmployeeAttendanceData() {
    var month = $('#month').val();
    var year = $('#year').val();
    var role_id = $('#role_id').val();

    $.ajax({
        url: "/attendance/update-employee-attendance-data", // Replace with actual backend route
        type: "POST",
        headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
        data: { month: month, year: year, role_id: role_id },
        success: function (response) {
            console.log('Attendance data updated:', response);
        },
        error: function () {
            toastr.error('Failed to update attendance data.');
        }
    });
}

// ✅ **Function 3: Export PDF only after updating attendance data**
function exportEmployeeAttendancePDF() {
    if (if_table_loaded == 1) {
        var month = $('#month').val();
        var year = $('#year').val();
        var role_id = $('#role_id').val();

        var url = "/attendance/employee/export-pdf?month=" + encodeURIComponent(month) + "&year=" + encodeURIComponent(year) + "&role_id=" + encodeURIComponent(role_id);
        window.location.href = url;
    } else {
        toastr.error('{{ get_phrase('No data Found') }}');
    }
}
function download_csv() {
    if (if_table_loaded == 1) {
        var month = $('#month').val();
        var year = $('#year').val();
        var role_id = $('#role_id').val();

        var url = "{{ route('attendance.employee.export_csv') }}?month=" + encodeURIComponent(month)
                  + "&year=" + encodeURIComponent(year)
                  + "&role_id=" + encodeURIComponent(role_id);

        window.open(url, '_blank'); // ✅ correct
    } else {
        toastr.error('{{ get_phrase('No data Found') }}');
    }
}




</script>

