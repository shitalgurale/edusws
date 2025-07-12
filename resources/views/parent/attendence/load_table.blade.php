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
</style>

@if(count($attendance_of_students) > 0)
<div class="row">
  <div class="col-md-4"></div>
  <div class="col-md-4">
    <div class="card bg-secondary text-white">
      <div class="card-body">
        <div class="text-center">
          <h4>
            {{ get_phrase('Attendance report') . ' ' . get_phrase('of') . ' ' . date('F', strtotime($page_data['attendance_date'])) }}
          </h4>

          <h5>
            {{ get_phrase('Name') }} :
            {{ $userName['name'] }}
          </h5>

          <h5>
            {{ get_phrase('Last updated at') }} :
            @if (!empty($attendance_of_students[0]['timestamp']))
              {{ date('d-M-Y', (int)$attendance_of_students[0]['timestamp']) }}
            @else
              {{ get_phrase('Not updated yet') }}
            @endif
          </h5>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4"></div>
</div>

<div class="table-responsive">
  <table class="table table-bordered table-sm" id="attendence_report">
    <caption class="table_cap">
      <h4>{{ get_phrase('Attendance Report') }}</h4>
    </caption>

    <thead class="thead-dark">
      <tr>
        <th width="40px">{{ get_phrase('Date') }} <i class="mdi mdi-arrow-right"></i></th>
        @php
          $month = date('m', strtotime($page_data['attendance_date']));
          $year = date('Y', strtotime($page_data['attendance_date']));
          $number_of_days = $month == 2
              ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29)))
              : ((($month - 1) % 7 % 2) ? 30 : 31);
        @endphp
        @for ($i = 1; $i <= $number_of_days; $i++)
          <th>{{ $i }}</th>
        @endfor
      </tr>
    </thead>

    <tbody>
      @php $student_id_count = 0; @endphp
      @foreach($attendance_of_students as $attendance_of_student)
        @php
          $user_details = (new CommonController)->get_user_by_id_from_user_table($attendance_of_student['student_id']);
        @endphp

        @if(date('m', strtotime($page_data['attendance_date'])) == date('m', (int)$attendance_of_student['timestamp']))
          @if($student_id_count != $attendance_of_student['student_id'])
            <tr>
              <td>{{ $user_details['name'] }}</td>
              @for ($i = 1; $i <= $number_of_days; $i++)
                @php
                  $day_string = sprintf('%04d-%02d-%02d', $year, $month, $i);
                  $attendance_by_id = DailyAttendances::where('student_id', $attendance_of_student['student_id'])
                      ->where('school_id', auth()->user()->school_id)
                      ->whereDate('timestamp', $day_string)
                      ->first();
                @endphp

                <td class="text-center">
                  @if(isset($attendance_by_id->status))
                    @if($attendance_by_id->status == 1)
                      <i class="text-success"><b>P</b></i>
                    @elseif($attendance_by_id->status == 0)
                      <i class="text-danger"><b>A</b></i>
                    @endif
                  @else
                    <div class="att-custom_div"></div>
                  @endif
                </td>
              @endfor
            </tr>
          @endif
          @php $student_id_count = $attendance_of_student['student_id']; @endphp
        @endif
      @endforeach
    </tbody>
  </table>

  <button class="btn btn-custom" onclick="Export()">{{ get_phrase('PDF') }}</button>
</div>
@else
<div class="empty_box text-center">
  <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
  <br>
  <span>{{ get_phrase('No data found') }}</span>
</div>
@endif

<script type="text/javascript">
  "use strict";

  function Export() {
    html2canvas(document.getElementById('attendence_report')).then(function (canvas) {
      var data = canvas.toDataURL();
      var docDefinition = {
        content: [{
          image: data,
          width: 500
        }]
      };
      pdfMake.createPdf(docDefinition).download("attendance_report.pdf");
    });
  }
</script>
