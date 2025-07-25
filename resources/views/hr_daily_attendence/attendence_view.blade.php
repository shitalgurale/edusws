@php
use App\Models\Session;
$active_session = Session::where('status', 1)
    ->where('school_id', auth()->user()->school_id)
    ->first();
@endphp

<style>
  .margn {
    margin-bottom: 10px;
    width: 100%;
  }
  .tbl {
    padding-right: 0px;
  }
</style>

<div class="row margn">
    <div class="col-6">
        <a href="javascript:" class="btn btn-sm btn-secondary" onclick="present_all()">
            {{ get_phrase('Present All') }}
        </a>
    </div>
    <div class="col-6">
        <a href="javascript:" class="btn btn-sm btn-secondary float-right" onclick="absent_all()">
            {{ get_phrase('Absent All') }}
        </a>
    </div>
</div>

<div class="table-responsive-sm row col-md-12">
    <table class="table eTable table-bordered">
        <thead>
            <tr>
                <th>{{ get_phrase('Name') }}</th>
                <th>{{ get_phrase('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                @php
                    $row = $existing_attendance[$user['id']] ?? null;
                @endphp
                <tr>
                    <td>{{ $user['name'] }}</td>
                    <td>
                        <input type="hidden" name="user_id[]" value="{{ $user['id'] }}">
                        <input type="hidden" name="attendance_id[]" value="{{ $row->id ?? '' }}">

                        <div class="custom-control custom-radio">
                            <input type="radio" name="status-{{ $user['id'] }}" value="1"
                                class="present"
                                {{ isset($row) && $row->status == 1 ? 'checked' : '' }} required>
                            {{ get_phrase('present') }} &nbsp;

                            <input type="radio" name="status-{{ $user['id'] }}" value="0"
                                class="absent"
                                {{ isset($row) && $row->status != 1 ? 'checked' : (!isset($row) ? 'checked' : '') }}
                                required>
                            {{ get_phrase('absent') }}
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
        $(".absent").prop('checked', true);
    }
</script>
