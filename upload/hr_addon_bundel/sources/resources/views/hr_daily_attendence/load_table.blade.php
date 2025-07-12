<?php
use App\Models\User;
use App\Models\Role;
use App\Http\Controllers\CommonController;
use App\Models\Addon\HrDailyAttendence;
use App\Models\Addon\Hr_roles;
use App\Http\Controllers\Addon\HrController;
?>

<style>
    .table_cap {
        caption-side: top;

    }
</style>

@php
if ($loaddata == 1):
@endphp



{{-- // report open --}}

<?php if(count($attendance_of_students) > 0): ?>

<h5>


    <div class="att-report-banner d-flex justify-content-center justify-content-md-between align-items-center flex-wrap">
        <div class="att-report-summary order-1">
            <h4 class="title">
                {{ get_phrase('Attendance report').' '.get_phrase('Of').' '.date('F', $page_data['attendance_date']) }}
            </h4>
            <?php $roles =  Hr_roles::where('id',$role_id)->first();  ?>

            @if(auth()->user()->role_id == 2)
            <p class="summary-item">{{ get_phrase('Designation') }} : <span>
                    {{  ucfirst($roles['name']) }}
                </span></p>
            @else
            <p class="summary-item">{{ get_phrase('Name') }} : <span>
                    {{ $userName['name'] }}
                </span></p>
            @endif




            <h5>
                {{ get_phrase('Last updated at') }} :
                <?php if ($attendance_of_students[0]['created_at'] == ""): ?>
                {{ get_phrase('Not updated yet') }}
                <?php else: ?>
                    {{ date('d-M-Y', $attendance_of_students[0]['created_at']) }} <br>
                <?php endif; ?>
            </h5>

        </div>


        <div class="att-banner-img order-0 order-md-1">
            <img src="{{ asset('assets/images/attendance-report-banner.png') }}" alt="" />
        </div>

    </div> <!-- end card-body-->
    </div>
    </div>


    </div>



    <!-- Attendance table -->

    <div class="att-table" id="pdf_table">

        <div class="att-title">
            <h4 class="att-title-header">
                {{ ucfirst($roles['name']) }} /
                {{ get_phrase('Date') }}
            </h4>
            <ul class="att-stuName-items">

                <?php

                    $student_id_count = 0;


                foreach(array_slice($attendance_of_students, 0, $no_of_users) as $attendance_of_student )     :  ?>
                <?php $user_details = (new HrController)->get_user_by_id_from_hr_userlist_table($attendance_of_student['user_id']); ?>
                <?php if(date('m', $page_data['attendance_date']) == date('m', $attendance_of_student['created_at'])): ?>
                <?php if($student_id_count != $attendance_of_student['user_id']): ?>


                <li class="att-stuName-item">
                    <a href="#">
                        {{ $user_details['name'] }}
                    </a>
                </li>


                <?php endif; ?>
                <?php $student_id_count = $attendance_of_student['user_id']; ?>
                <?php endif; ?>
                <?php endforeach;  ?>






            </ul>
        </div>


        <div class="att-content">


            <div class="att-dayWeek">
                <div class="att-wDay d-flex">
                    <?php
                            $number_of_days = date('m', $page_data['attendance_date']) == 2 ? (date('Y', $page_data['attendance_date']) % 4 ? 28 : (date('m', $page_data['attendance_date']) % 100 ? 29 : (date('m', $page_data['attendance_date']) % 400 ? 28 : 29))) : ((date('m', $page_data['attendance_date']) - 1) % 7 % 2 ? 30 : 31);
                            $month_year='-'.date('m', $page_data['attendance_date']).'-'.date('Y', $page_data['attendance_date']);

                            for ($i = 1; $i <= $number_of_days; $i++):
                            $weekname=$i.$month_year;

                            $day=date("l", strtotime($weekname));?>

                    <div>
                        <p>
                            {{ substr($day,0,1) }}
                        </p>
                    </div>
                    <?php endfor; ?>


                </div>
                <div class="att-date d-flex">
                    <?php
                            $number_of_days = date('m', $page_data['attendance_date']) == 2 ? (date('Y', $page_data['attendance_date']) % 4 ? 28 : (date('m', $page_data['attendance_date']) % 100 ? 29 : (date('m', $page_data['attendance_date']) % 400 ? 28 : 29))) : ((date('m', $page_data['attendance_date']) - 1) % 7 % 2 ? 30 : 31);
                            for ($i = 1; $i <= $number_of_days; $i++): ?>
                    <div>
                        <p>
                            {{ $i }}
                        </p>
                    </div>
                    <?php endfor; ?>

                </div>

            </div>

            <ul class="att-count-items">


                <?php

                            $student_id_count = 0;

                            foreach(array_slice($attendance_of_students, 0, $no_of_users) as $attendance_of_student )     :  ?>

                <li class="att-count-item">
                    <div class="att-count-stu d-flex">

                        <?php $user_details = (new HrController)->get_user_by_id_from_hr_userlist_table($attendance_of_student['user_id']); ?>


                        <?php if(date('m', $page_data['attendance_date']) == date('m', $attendance_of_student['created_at'])): ?>


                        <?php if($student_id_count != $attendance_of_student['user_id']): ?>


                        <?php for ($i = 1; $i <= $number_of_days; $i++): ?>
                        <?php $page_data['date'] = $i.' '.$page_data['month'].' '.$page_data['year']; ?>
                        <?php $timestamp = strtotime($page_data['date']); ?>

                        <?php $attendance_by_id = HrDailyAttendence::where([ 'user_id' => $attendance_of_student['user_id'], 'school_id' => auth()->user()->school_id, 'created_at' => $timestamp])->first();  ?>
                        <?php if(isset($attendance_by_id->status) && $attendance_by_id->status == 1): ?>
                        <div class="present bdr"></div>
                        <?php elseif(isset($attendance_by_id->status) && $attendance_by_id->status == 0): ?>

                        <div class="absent bdr"></div>

                        <?php else: ?>
                        <div class="att-custom_div"></div>
                        <?php endif; ?>





                        <?php endfor; ?>


                        <?php endif; ?>




                        <?php $student_id_count = $attendance_of_student['user_id']; ?>
                        <?php endif; ?>
                    </div>
                </li>

                <?php endforeach;  ?>

            </ul>


        </div>

    </div>



    <?php else: ?>

    <div class="empty_box text-center">
        <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
        <br>
        <span class="">
            {{ get_phrase('No data found') }}
        </span>
    </div>
    <?php endif; ?>

    {{-- // report close --}}




    @php
    endif;
    @endphp


    <script src="{{ asset('assets/pdfjavascript/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/pdfjavascript/html2canvas.min.js') }}"></script>
    <script type="text/javascript">
        "use strict";

    function Export() {
        html2canvas(document.getElementById('pdf_table'), {
            onrendered: function(canvas) {
                var data = canvas.toDataURL();
                var docDefinition = {
                    content: [{
                        image: data,
                        width: 500
                    }]
                };
                pdfMake.createPdf(docDefinition).download("AttendenceReport.pdf");
            }
        });
    }
    </script>
