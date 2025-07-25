<?php
use App\Models\Subject;
use App\Models\Classes;
?>

@if(isset($syllabus) && count($syllabus) > 0)
    <table id="basic-datatable" class="table eTable">
        <thead>
            <tr>
                <th>{{ get_phrase('Title') }}</th>
                <th>{{ get_phrase('Syllabus') }}</th>
                <th>{{ get_phrase('Subject') }}</th>
                <th>{{ get_phrase('Class') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($syllabus as $subject_details)
                @php
                    $subject = Subject::find($subject_details['subject_id']);
                    $class = Classes::find($subject_details['class_id']);
                @endphp
                <tr>
                    <td>{{ $subject_details['title'] ?? '-' }}</td>
                    <td>
                        @if(!empty($subject_details['file']))
                            <a href="{{ asset('assets/uploads/syllabus/' . $subject_details['file']) }}"
                               class="btn btn-primary btn-sm bi bi-download" download>
                               {{ get_phrase('Download') }}
                            </a>
                        @else
                            <span class="text-muted">{{ get_phrase('No File') }}</span>
                        @endif
                    </td>
                    <td>{{ $subject->name ?? 'Unknown Subject' }}</td>
                    <td>{{ $class->name ?? 'Unknown Class' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="empty_box center">
        <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
        <br>
        {{ get_phrase('No data found') }}
    </div>
@endif
