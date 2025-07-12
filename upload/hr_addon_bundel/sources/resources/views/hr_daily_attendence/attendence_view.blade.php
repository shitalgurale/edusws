<?php


use App\Http\Controllers\CommonController;
use App\Models\Enrollment;
use App\Models\Session;
use App\Models\DailyAttendances;
use App\Models\Addon\HrDailyAttendence;

$active_session = Session::where('status', 1)->where('school_id', auth()->user()->school_id)->first();


?>
<style>
  .margn{
    margin-bottom: 10px; width: 100%;
  }
  .tbl{
    padding-right: 0px;
  }
</style>




<div class="row margn" >
    <div class="col-6"><a href="javascript:" class="btn btn-sm btn-secondary" onclick="present_all()">
            {{ get_phrase('Present All') }}
        </a></div>
    <div class="col-6"><a href="javascript:" class="btn btn-sm btn-secondary float-right" onclick="absent_all()">
            {{ get_phrase('Absent All') }}
        </a></div>
</div>

<div class="table-responsive-sm row col-md-12">
    <table class="table eTable table-bordered">
        <thead>
            <tr>
                <th>
                    {{ get_phrase('Name') }}
                </th>
                <th>
                    {{ get_phrase('Status') }}
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)

            <tr>
                <td>
                    {{ $user['name'] }}
                </td>
                <td>
                    <input type="hidden" name="user_id[]" value="{{ $user['id'] }}">
                    <div class="custom-control custom-radio">
                        <?php $update_attendance = HrDailyAttendence::where(['created_at' => strtotime($date), 'role_id' => $role_id, 'school_id' => auth()->user()->school_id, 'session_id' => $active_session->id, 'user_id' => $user['id']]);
                        $count_row = $update_attendance->get();
                        ?>
                        <?php if($count_row->count() > 0): ?>
                        <?php $row = $update_attendance->first(); ?>
                        <input type="hidden" name="attendance_id[]" value="{{ $row->id }}">
                        <input type="radio" id="" name="status-{{ $user['id'] }}" value="1" class="present" {{ $row->status == 1 ? 'checked':'' }} required>
                        {{ get_phrase('present') }} &nbsp;
                        <input type="radio" id="" name="status-{{ $user['id'] }}" value="0" class="absent" {{ $row->status != 1 ? 'checked':'' }} required>
                        {{ get_phrase('absent') }}


                        <?php else: ?>
                        <input type="radio" id="" name="status-{{ $user['id'] }}" value="1" class="present" required>
                        {{ get_phrase('present') }} &nbsp;
                        <input type="radio" id="" name="status-{{ $user['id'] }}" value="0" class="absent" checked required>
                        {{ get_phrase('absent') }}


                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script type="text/javascript">

    function present_all() {
        $(".present").prop('checked', true);
    }

    function absent_all() {
        $(".absent").prop('checked',true);
    }
</script>
