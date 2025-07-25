<?php

use App\Models\Subject;
use App\Models\Session;
use App\Models\Gradebook;

$active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
$index = 0;

?>
<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <div class="view_mark" id="mark_report">
                <table class="table eTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ get_phrase('Subject Name') }}</th>
                            @foreach($exam_categories as $exam_category)
                                <th>{{ $exam_category->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjects as $subject)
                            <tr>
                                <td>{{ ++$index }}</td>
                                <td>{{ $subject->name }}</td>
                                @foreach($exam_categories as $exam_category)
                                    <td>
                                        @php
                                            $exam_marks = \App\Models\Gradebook::where('exam_category_id', $exam_category->id)
                                                ->where('class_id', $student_details->class_id)
                                                ->where('section_id', $student_details->section_id)
                                                ->where('student_id', $student_details->user_id)
                                                ->where('school_id', auth()->user()->school_id)
                                                ->where('session_id', $active_session)
                                                ->first();

                                            $subject_list = $exam_marks && $exam_marks->marks
                                                ? json_decode($exam_marks->marks, true)
                                                : [];
                                        @endphp
                                        {{ $subject_list[$subject->id] ?? '-' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                        {{-- Report Card Row --}}
                        <tr>
                            <td>{{ ++$index }}</td>
                            <td>{{ get_phrase('Report Card') }}</td>
                            @foreach($exam_categories as $exam_category)
                                <td>
                                    <a href="{{ route('parent.download_report_card', ['student_id' => $student_details->user_id, 'exam_category_id' => $exam_category->id]) }}"
                                       class="btn btn-primary btn-sm">
                                        {{ get_phrase('Report Card') }}
                                    </a>
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
