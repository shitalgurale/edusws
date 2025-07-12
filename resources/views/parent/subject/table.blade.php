<?php
use App\Models\Classes;
?>

@if(!empty($subjects) && count($subjects) > 0)
<table id="basic-datatable" class="table eTable">
    <thead>
        <tr>
            <th>{{ get_phrase('Subjects') }}</th>
            <th>{{ get_phrase('Class') }}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                @php $subjectCount = count($subjects); @endphp
                @foreach($subjects as $key => $subject)
                    {{ $subject['name'] }}@if($key != $subjectCount-1), @endif
                @endforeach
            </td>
            <td>
                @php
                    $className = '-';
                    $firstSubject = $subjects[0] ?? null;
                    if ($firstSubject && isset($firstSubject['class_id'])) {
                        $class = Classes::find($firstSubject['class_id']);
                        if ($class) {
                            $className = $class->name;
                        }
                    }
                @endphp
                {{ $className }}
            </td>
        </tr>
    </tbody>
</table>
@else
<div class="empty_box center">
    <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
    <br>
    <span>{{ get_phrase('No data found') }}</span>
</div>
@endif
