@php
use App\Http\Controllers\CommonController;
use App\Models\Enrollment;
use App\Models\User;

$active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

// Get students/parents enrollment if applicable
$enrols = collect();
if (in_array($receiver, ['student', 'parent']) && $class_id && $section_id) {
    $enrols = Enrollment::where([
        'class_id'   => $class_id,
        'section_id' => $section_id,
        'school_id'  => auth()->user()->school_id,
        'session_id' => $active_session
    ])->get();
}
@endphp

<style>
    .checkbox_cursor {
        cursor: pointer;
    }
    .custom-control-input {
        cursor: pointer;
        margin-right: 5px;
    }
</style>

@if ($receiver == 'parent')
    <span>
        <a href="javascript:void(0);" style="color: rgba(11, 11, 200, 0.757); font-size: 14px; font-weight: 500;" onclick="checkAll()">
            {{ get_phrase('Check all') }}
        </a>
    </span>
    <table class="table eTable table-bordered">
        <thead>
            <tr>
                <th>{{ get_phrase('Name') }}</th>
                <th>{{ get_phrase('Phone') }}</th>
                <th>{{ get_phrase('Students Name') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($enrols as $key => $enroll)
                @php
                    $parent_details = (new CommonController)->get_student_details_by_id($enroll->user_id);
                @endphp
                <tr>
                    <td>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="parent{{ $key }}" name="phones[]" value="{{ $parent_details->parent_phone }}">
                            <label class="custom-control-label checkbox_cursor" for="parent{{ $key }}">
                                {{ $parent_details->parent_name }}
                            </label>
                        </div>
                        <input type="hidden" class="messages-to-send" name="messages[]" value="">
                    </td>
                    <td>{{ $parent_details->parent_phone }}</td>
                    <td>{{ $parent_details->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

@elseif ($receiver == 'student')
    <div class="table-responsive-sm">
        <table class="table eTable table-bordered">
            <thead>
                <tr>
                    <th>{{ get_phrase('Name') }}</th>
                    <th>
                        {{ get_phrase('Phone') }}
                        <span style="float: right">
                            <a href="javascript:void(0);" onclick="checkAll()">{{ get_phrase('Check all') }}</a>
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrols as $key => $enroll)
                    @php
                        $student_details = (new CommonController)->get_student_details_by_id($enroll->user_id);
                    @endphp
                    <tr>
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="student{{ $key }}" name="phones[]" value="{{ $student_details->phone }}">
                                <label class="custom-control-label checkbox_cursor" for="student{{ $key }}">
                                    {{ $student_details->name }}
                                </label>
                            </div>
                            <input type="hidden" class="messages-to-send" name="messages[]" value="">
                        </td>
                        <td>{{ $student_details->phone }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@elseif ($receiver == 'teacher')
    @php
        $teachers = User::where('role_id', 3)
                        ->where('school_id', auth()->user()->school_id)
                        ->get();
    @endphp
    <table class="table eTable table-bordered">
        <thead>
            <tr>
                <th>{{ get_phrase('Name') }}</th>
                <th>
                    {{ get_phrase('Phone') }}
                    <span style="float: right">
                        <a href="javascript:void(0);" onclick="checkAll()">{{ get_phrase('Check all') }}</a>
                    </span>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($teachers as $key => $teacher)
                @php
                    $info = json_decode($teacher->user_information);
                @endphp
                <tr>
                    <td>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="teacher{{ $key }}" name="phones[]" value="{{ $info->phone }}">
                            <label class="custom-control-label checkbox_cursor" for="teacher{{ $key }}">
                                {{ $teacher->name }}
                            </label>
                        </div>
                        <input type="hidden" class="messages-to-send" name="messages[]" value="">
                    </td>
                    <td>{{ $info->phone }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

<script type="text/javascript">
    function checkAll() {
        $('input:checkbox').prop('checked', true);
    }
</script>
