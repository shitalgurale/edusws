<?php

use App\Http\Controllers\CommonController;
use App\Models\Enrollment;
use App\Models\DailyAttendances;
use App\Models\User;

$active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
$enrols = Enrollment::where(['class_id' => $page_data['class_id'], 'section_id' => $page_data['section_id'], 'school_id' => auth()->user()->school_id, 'session_id' => $active_session])->get();

?>
<style>
    .checkbox_cursor{
        cursor: pointer;
    }
    .custom-control-input{
        cursor: pointer;
        margin-right: 5px;
    }
</style>

 @if ($page_data['receiver'] == 'parent')
{{--  <form action="{{route('admin.sms_center.sms_sending')}}"  class="studentAjaxForm" method="post" enctype="multipart/form-data">  --}}
    <span> <a href="javascript:void(0);" style="color:rgba(11, 11, 200, 0.757); font-size:14px; font-weight: 500;" onclick="checkAll()"><?php echo get_phrase('Check all'); ?></a> </span>
        <table class="table eTable table-bordered">
            <thead>
                <tr>
                    <th>{{ get_phrase('Name') }}</th>
                    <th>{{ get_phrase('Phone') }} </th>
                    <th>{{ get_phrase('Students Name') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrols as $key => $enroll)
                <?php $parent_details = (new CommonController)->get_student_details_by_id($enroll->user_id);
                ?>
                  <tr>
                    <td>
                        <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input " id="<?php echo $key; ?>" name="phones[]" value="{{($parent_details->parent_phone) }}">
                            <label class="custom-control-label checkbox_cursor" for="<?php echo $key; ?>">
                                {{($parent_details->parent_name) }}
                        </div>
                        <input type="hidden" class="messages-to-send" name="messages[]" value="">
                    </td>
                    <td>
                        <label class="custom-control-label checkbox_cursor" for="<?php echo $key; ?>">
                            {{($parent_details->parent_phone) }}
                    </td>
                    <td>
                        <label class="custom-control-label checkbox_cursor" for="<?php echo $key; ?>">
                            {{ $parent_details->name }}
                    </td>
                  </tr>
                  @endforeach
            </tbody>
        </table>
{{--  </form>  --}}

@elseif ($page_data['receiver'] == 'student')

<div class="table-responsive-sm">
    {{--  <form action="{{route('admin.sms_center.sms_sending')}}"  class="studentAjaxForm" method="post" enctype="multipart/form-data">  --}}
        <table class="table eTable table-bordered">
            <thead>
                <tr>
                    <th>{{ get_phrase('Name') }}</th>
                    <th>{{ get_phrase('Phone') }}  <span style="float: right"> <a href="javascript:void(0);" onclick="checkAll()"><?php echo get_phrase('Check all'); ?></a> </span></th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrols as $key => $enroll)
                <?php $student_details = (new CommonController)->get_student_details_by_id($enroll->user_id);
                ?>
                  <tr>
                    <td>
                        <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input " id="<?php echo $key; ?>" name="phones[]" value="{{ $student_details['phone'] }}">
                            <label class="custom-control-label checkbox_cursor" for="<?php echo $key; ?>">
                        {{ $student_details['name'] }}
                        </div>
                        <input type="hidden" class="messages-to-send" name="messages[]" value="">
                    </td>
                    <td>
                        <label class="custom-control-label checkbox_cursor" for="<?php echo $key; ?>">
                            {{ $student_details['phone'] }}
                    </td>
                  </tr>
                  @endforeach
            </tbody>
        </table>
    {{--  </form>  --}}
</div>
@elseif ($page_data['receiver'] == 'teacher')
@php
$teachers = User::where('role_id', 3)->where('school_id', auth()->user()->school_id)->get();
@endphp
{{--  <form action="{{route('admin.sms_center.sms_sending')}}"  class="studentAjaxForm" method="post" enctype="multipart/form-data">  --}}
        <table class="table eTable table-bordered">
            <thead>
                <tr>
                    <th>{{ get_phrase('Name') }}</th>
                    <th>{{ get_phrase('Phone') }}  <span style="float: right"> <a href="javascript:void(0);" onclick="checkAll()"><?php echo get_phrase('Check all'); ?></a> </span></th>
                </tr>
            </thead>
            <tbody>
                @foreach($teachers as $key => $teacher)
                    <?php
                    $info = json_decode($teacher->user_information);
                    ?>
                  <tr>
                    <td>
                        <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input " id="<?php echo $key; ?>" name="phones[]" value="{{ $info->phone }}">
                            <label class="custom-control-label checkbox_cursor" for="<?php echo $key; ?>">
                                {{ $teacher->name }}
                        </div>
                        <input type="hidden" class="messages-to-send" name="messages[]" value="">
                    </td>
                    <td>
                        <label class="custom-control-label checkbox_cursor" for="<?php echo $key; ?>">
                            {{ $info->phone }}
                    </td>
                  </tr>
                  @endforeach
            </tbody>
        </table>
{{--  </form>  --}}
@endif



<script type="text/javascript">

    $(".parentAjaxForm").submit(function(e) {
        var form = $(this);
        ajaxSubmit(e, form, doNothing);
      });

      $(".studentAjaxForm").submit(function(e) {
        var form = $(this);
        ajaxSubmit(e, form, doNothing);
      });

      $(".teacherAjaxForm").submit(function(e) {
        var form = $(this);
        ajaxSubmit(e, form, doNothing);
      });

      function checkAll() {
        $('input:checkbox').prop('checked', "checked");
      }

      function doNothing() {

      }

</script>

